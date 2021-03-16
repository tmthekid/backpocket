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
use Illuminate\Support\Arr;

class Starbucks
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

    private $products = [];

    private $order_no;
    private $emailDate;
    private $discount;
    private $sub_total = 0;
    private $tax_amount = 0;
    private $total = 0;
    private $payment_method = null;
    private $payment_ref = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Starbucks";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();


        $this->setOrderNo();

        $this->setDiscount();
    }

    private function filterArray($array){

        $tmp = [];

        //removing extra spaces from array
        $array = array_filter($array, function($e){
            return str_replace(' ', '', trim(preg_replace('/\s+/', ' ', $e)));;
        });
        foreach ($array as $key => $value) {
            //removing utf-8 characters like &nbsp;
            $value = str_replace("\xc2\xa0",' ', $value);
            $tmp[] = trim($value);
        }

        //removing empty elements from content array using array_values
        return array_values(array_filter($tmp));
    }

    /**
     *
     */
    private function plainTextToArray(){
        /**
         * Convert plaintext into array
         */
        $tmp_content = explode('--tagend--', $this->plainText);

        $this->textArray = $this->filterArray($tmp_content);

        $this->textCollection = collect($this->textArray);
    }

    private function arraySearchIndex($string){
        $element = array_filter($this->textArray, function($text) use ($string) {
            return preg_match("/\b$string\b/i", $text);
        });
        $index = array_key_first($element);

        return $index;
    }

    //TODO: Set configuration, for example start and end point of parsing
    private function isInvoice(){
        if(Str::contains(Str::lower($this->plainText), Str::lower("Order Number"))){ //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo(){
        //If string contains fails then use the default email address
        if(Str::contains($this->htmlBody, 'Order Number')){
            $this->order_no = strip_tags(Str::between($this->htmlBody, "Order Number:", "Received:"));
            $this->order_no = trim($this->order_no);
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
            $date_text = Str::between($this->htmlBody, "@starbucks.com", "Subject");

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));

            if(Str::contains($date_text, "Date:")){
                $date_text = strip_tags(Str::after($date_text, "Date:"));
            } elseif (Str::contains($date_text, "Date :")){
                $date_text = strip_tags(Str::after($date_text, "Date :"));
            }

            $this->emailDate =  Carbon::parse(trim($date_text));

            //If string contains fails then use the default email address
            if(Str::contains($this->htmlBody, 'Starbucks Coffee Company &lt;')){
                $vendorEmailStr = Str::between($this->htmlBody, "Starbucks Coffee Company &lt;", "@starbucks.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@starbucks.com';
            } else{
                $this->vendor_email = "order@starbucks.com";
            }

        }

        if(Str::contains($this->plainText, "Our mailing address is:")){
            $this->vendor_address = str_replace('--tagend--', '', Str::after($this->plainText, "Our mailing address is:"));
        }
    }

    private function setProducts(){
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        $prods_text = Str::between($this->plainText, "Order Detail:", "Merchandise Subtotal:");
        if($this->getDiscountIndex()){
            $prods_text = Str::between($this->plainText, "Order Detail:", "Discount");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = $this->filterArray($prods_array);

        $prods_array = array_slice($prods_array, 5);
        $prods_array = array_chunk($prods_array, 5);

        $prods = [];
        foreach ($prods_array as $key => $value){
            if(isset($prods_array[$key][0]) &&
                isset($prods_array[$key][2]) &&
                isset($prods_array[$key][1]) &&
                isset($prods_array[$key][4])
            ){
                $prods[$key]['name'] = $prods_array[$key][0] . ' - ' . $prods_array[$key][1];
                $prods[$key]['quantity'] = $prods_array[$key][2];
                $prods[$key]['price'] = $prods_array[$key][4];
            }

        }

        $this->products = $prods;
    }

    private function setTransaction(){
        $total_label_index = array_search('Total Charge:', $this->textArray);
        $tax_label_index = array_search('Estimated Tax:', $this->textArray);
        $sub_total_index = array_search('Merchandise Subtotal:', $this->textArray);

        $this->sub_total = $this->textArray[$sub_total_index + 1];
        $this->tax_amount = $this->textArray[$tax_label_index + 1];
        $this->total = $this->textArray[$total_label_index + 1];
        $this->payment_method = "";
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

            $detail['vendor'] = [
                'email' => $this->vendor_email,
                'name' => $this->vendor_name,
                'address' => $this->vendor_address
            ];

            /**
             * Set Products properties required for DB
             */
            $this->setProducts();  
            $detail['products'] = $this->products; 

            /**
             * Set Transaction properties required for DB
             */
            $this->setTransaction();
            $detail['transaction'] = [
                'order_no' => $this->order_no,
                'transaction_date' => $this->emailDate->format('Y-m-d H:i:s'),
                'sub_total' => $this->sub_total,
                'discount' => $this->discount,
                'total' => $this->total,
                'tax_amount' => $this->tax_amount,
                'payment_method' => $this->payment_method,
                'message_id' => $this->message_id
            ];

            //End Products & Transactions

            $this->setDetail();

            return $this->detail;
        } catch (Exception $exception){
            Log::error("Array Creation Error: " . $this->vendor_name . " - " . $exception->getMessage());
            return false;
        }
    }

    public function setDetail(){
        $this->detail = [
            'vendor' => [
                'email' => $this->vendor_email,
                'name' => $this->vendor_name,
                'address' => $this->vendor_address
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
            'message_id' => $this->message_id
        ];
    }

    public function getDetail(){
        return $this->detail;
    }
}
