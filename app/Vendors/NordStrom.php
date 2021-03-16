<?php
/**
 * Created by PhpStorm.
 * User: DevEnviroment
 * Date: 2020-06-30
 * Time: 22:31
 */

namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NordStrom
{
    private $htmlBody;
    private $plainText;
    private $sender;
    private $textArray;
    private $textCollection;
    private $detail = [];

    private $vendor_name;
    private $vendor_email;
    private $vendor_address = null;
    private $vendor_store = null;

    private $products = [];

    private $order_no;
    private $emailDate;
    private $discount;
    private $sub_total = 0;
    private $tax_amount = 0;
    private $total = 0;
    private $payment_method = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "NordStrom";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();
        $this->setOrderNo();
        $this->setDiscount();
    }

    /**
     *
     */
    private function plainTextToArray(){
        /**
         * Convert plaintext into array
         */
        $tmp_content = explode('--tagend--', $this->plainText);

        //removing empty elements from content array
        $tmp_content = array_values(array_filter($tmp_content));

        //removing extra spaces from array
        $this->textArray = array_filter($tmp_content, function($e){
            return preg_replace('/\s+/', ' ', $e);
        });
        $this->textCollection = collect($this->textArray);
    }

    //TODO: Set configuration, for example start and end point of parsing
    private function isInvoice(){
        if(Str::contains(Str::lower($this->plainText), Str::lower("Transaction number:"))){ //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo(){
        $order_no = null;
        $order_no = array_search('Transaction number: ', $this->textArray);
        if($order_no)
        {
            $this->order_no = $this->textArray[$order_no + 1];    
        }
    }

    private function transactionExists(){
        $transaction_exists = Transaction::where('order_no', $this->order_no)->exists();
        if($transaction_exists){
            return true;
        }
        return false;
    }

    private function getDiscountIndex(){
        return array_search('Discount', $this->textArray);
    }

    private function setDiscount(){
        if($this->getDiscountIndex()){
            $this->discount = $this->textArray[$this->getDiscountIndex() + 1];
        }
    }

    private function setVendor(){
        //If email is forwarded mail
        if (Str::contains(Str::lower($this->plainText), Str::lower("Forwarded message"))) {
            //TODO: check if there are more than forwarded messages
            $date_text = Str::between($this->htmlBody, "@eml.nordstrom.com", "Subject:");

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
            $date_text = strip_tags(Str::after($date_text, "Date:"));
            $this->emailDate =  Carbon::parse($date_text);

            //If string contains fails then use the default email address
            if(Str::contains($this->htmlBody, 'NORDSTROM STORES &lt;')){
                $vendorEmailStr = Str::between($this->htmlBody, "NORDSTROM STORES &lt;", "@eml.nordstrom.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@eml.nordstrom.com';
            } else{
                $this->vendor_email = "nordstrom@eml.nordstrom.com";
            }

        }

        $store_no = array_search('Store number: ', $this->textArray);
        if($store_no)
        {
            $this->vendor_store = $this->textArray[$store_no + 1];    
        }


        //return $this->plainText;
        $vendor_address = array_search('Nordstrom Sherway Gardens', $this->textArray);
        if($vendor_address)
        {
            $vendor_address = $this->textArray[$vendor_address + 2].' '.$this->textArray[$vendor_address + 3];    
            $this->vendor_address = trim($vendor_address);
        }   
    }

    private function setProducts(){
        /**
         * Products Details
         */
        $prods_text = null;
        //TODO: Check with discounted email
        $prods_text = Str::between($this->plainText, "Returned", "Return subtotal");
        if($this->getDiscountIndex()){
            $prods_text = Str::between($this->plainText, "Returned", "Return subtotal");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));

        $prods = [];
        foreach ($prods_array as $key => $value){
            if(Str::contains($value, '$')){
                $prods[] = [
                    'name' => $prods_array[$key - 5],
                    'price' => str_replace('Price: ', '',$value)
                ];
            }
        }
        $this->products = $prods;
    }

    private function setExtraInfo(){

        $ringing_employee = array_search('Ringing employee: ', $this->textArray);
        $register_number = array_search('Register number: ', $this->textArray);
        
        $ex_info = [];
        if($ringing_employee) {
            $ex_info[] = [
                'label' => "Ringing Employee",
                'value' => $this->textArray[$ringing_employee + 1],
                'key' => 'ringing_employee',
                'type' => 'name'
            ];
        }

        if($register_number) {
            $ex_info[] = [
                'label' => "Register Number",
                'value' => $this->textArray[$register_number + 1],
                'key' => 'register_number',
                'type' => 'number'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();

    }

    private function setTransaction(){
        $sub_total_index = array_search('Return subtotal', $this->textArray);
        $tax_label_index = array_search('GST/HST', $this->textArray);
        $total_label_index = array_search('Return total', $this->textArray);
        $payment_method = array_search('APPL', $this->textArray);

        $this->sub_total = $this->textArray[$sub_total_index + 5];
        $this->tax_amount = $this->textArray[$tax_label_index + 5];
        $this->total = $this->textArray[$total_label_index + 5];
        $this->payment_method = $this->textArray[$payment_method + 1];

        $this->setExtraInfo();
    }

    public function parseEmail(){
        try{

            if(!$this->isInvoice()) return false;

            $this->setOrderNo();

            /**
             * Check if the transaction/order already exists then return false stop further
             * proceeding to avoid any duplication
             */
            if($this->transactionExists()) return false;

            /**
             * Set vendor properties required for DB
             */
            $this->setVendor();

            /**
             * Set Products properties required for DB
             */
            $this->setProducts();

            /**
             * Set Transaction properties required for DB
             */
            $this->setTransaction();

            //End Products & Transactions

            $this->setDetail();

            return $this->detail;
        } catch (Exception $exception){
            Log::error("Array Creation Error: " . $exception->getMessage());
            return false;
        }
    }

    public function setDetail(){
        $this->detail = [
            'vendor' => [
                'email' => $this->vendor_email,
                'name' => $this->vendor_name,
                'address' => $this->vendor_address,
                'store_no' => $this->vendor_store
            ]
        ];

        $this->detail['products'] = $this->products;
        $this->detail['transaction'] = [
            'order_no' => $this->order_no,
            'transaction_date' => $this->emailDate->format('Y-m-d H:i:s'),
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'total' => $this->total,
            'tax_amount' => $this->tax_amount,
            'payment_method' => $this->payment_method,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail(){
        return $this->detail;
    }
}
