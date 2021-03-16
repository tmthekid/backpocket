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

class PizzaPizza
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
    private $discount = null;
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

        $this->vendor_name = "PizzaPizza";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();
        $this->setOrderNo();
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

        foreach ($this->textArray as $key => $value) {
            if($value==""  || $value==" " || $value=="&nbsp;"){
                unset($this->textArray[$key]);
            } else{
                $this->textArray[$key] = trim($value);
            }
        }
        $this->textArray = array_values($this->textArray);
        // dd($this->textArray);
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
        if(Str::contains(Str::lower($this->plainText), Str::lower("Thank you for ordering with Pizza Pizza"))){ //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo(){
        $this->order_no = date('Ymdhis') . mt_rand(100, 999);
    }

    private function transactionExists(){
        $transaction_exists = Transaction::where('message_id', $this->message_id)->exists();
        if($transaction_exists){
            return true;
        }
        return false;
    }

    private function setVendor(){
        //If email is forwarded mail
        if (Str::contains(Str::lower($this->plainText), Str::lower("Forwarded message"))) { //Plain email body

            if(Str::contains(Str::lower($this->plainText), Str::lower('Your Order Confirmation'))){

                $date_text = Str::between($this->htmlBody, "@pizzapizza.ca", "Subject");

                $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
                $date_text = trim(preg_replace('/\s+/', ' ', $date_text));

                $date_text = strip_tags(Str::after($date_text, "Date : "));
                $date_text_exp = collect(explode(",", $date_text));
                $date_text = trim($date_text_exp->last());
            }
            else{
                $date_text = Str::between($this->htmlBody, "@pizzapizza.ca", "Subject");
                $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
                $date_text = trim(preg_replace('/\s+/', ' ', $date_text));

                if(Str::contains($date_text, "Date:")){
                    $date_text = strip_tags(Str::after($date_text, "Date:"));
                }
                else{
                    $date_text = strip_tags(Str::after($date_text, "Date :"));
                }

            }
            

            $this->emailDate =  Carbon::parse($date_text);

            
            $this->vendor_email = "orderconfirmation@pizzapizza.ca";

        }

    }

    private function setProducts(){
        /**
         * Products Details
         */

        if(Str::contains(Str::lower($this->plainText), Str::lower('Your Order Confirmation'))){
            $prods_text = null;

            $prods_text = Str::after($this->plainText, "Order Summary");
            $prods_text = Str::before($prods_text, "Order Total:");
            
            $prods_array = explode('--tagend--', $prods_text);
            
            foreach ($prods_array as $key => $value) {
                if($value==""  || $value==" " || $value=="&nbsp;" || Str::contains($value, '======')){
                    unset($prods_array[$key]);
                } else{
                    $prods_array[$key] = trim($value);
                }
            }

            $prods_array = array_values(array_filter($prods_array));
            $prods_collect = collect($prods_array);

            $split_delimeter = "$";
            $split_index = $prods_collect->filter(function ($item, $key) use ($split_delimeter) {
                if(Str::contains($item, $split_delimeter)){
                    return $key;
                }
            });

            $prods = [];

            foreach ($split_index as $key => $value) {
                $prods[] = [
                    'name' => $prods_array[$key - 1],
                    'price' => $prods_array[$key],
                    'quantity' => $prods_array[$key - 2],
                ];
            }

        }
        else {
            $prods_text = null;

            $prods_text = Str::between($this->plainText, "Item Total", "Order Summary");
            
            $prods_array = explode('--tagend--', $prods_text);
            foreach ($prods_array as $key => $value) {
                if($value==" " || $value=="&nbsp;"){
                    unset($prods_array[$key]);
                }
            }
            $this->textArray = array_values($this->textArray);
            $prods_array = array_values(array_filter($prods_array));

            $prods_collect = collect($prods_array);

            $split_delimeter = "$";
            $split_index = $prods_collect->filter(function ($item, $key) use ($split_delimeter) {
                if(Str::contains($item, $split_delimeter)){
                    return $key;
                }
            });

            $split_start = 0;
            $new_array = [];
            foreach ($split_index as $key => $value) {
                $key = $key + 1;
                $new_array[] = $prods_collect->slice($split_start, $key);
                $split_start = $key;
            }

            $prods = [];
            foreach ($new_array as $prod_c){
                $prods[] = [
                    'name' => $prod_c->first(),
                    'price' => $prod_c->last()
                ];
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo(){

        if(Str::contains(Str::lower($this->plainText), Str::lower('Your Order Confirmation'))){
            $discount_label_index = array_search('Discount', $this->textArray);
            $dc_label_index = array_search('Delivery Charge: ', $this->textArray);

            $ex_info = [];
            if($discount_label_index) {
                $ex_info[] = [
                    'label' => "Discount",
                    'value' => $this->textArray[$discount_label_index + 1],
                    'key' => 'discount',
                    'type' => 'amount'
                ];
            }

            if($dc_label_index) {
                $ex_info[] = [
                    'label' => "Delivery Charge",
                    'value' => $this->textArray[$dc_label_index + 1],
                    'key' => 'delivery_charge',
                    'type' => 'amount'
                ];
            }
            
        }
        else{
            $pst_label_index = array_search('PST:', $this->textArray);

            $ex_info = [];
            if($pst_label_index) {
                $ex_info[] = [
                    'label' => "PST",
                    'value' => $this->textArray[$pst_label_index + 1],
                    'key' => 'pst',
                    'type' => 'amount'
                ];
            }

            $total_label_index = array_search('Total:', $this->textArray);
            $redeemed_label = $this->textArray[$total_label_index + 2] ?? null;

            if(Str::contains(Str::lower($redeemed_label), Str::lower('Redeemed'))){
                $ex_info[] = [
                    'label' => $redeemed_label,
                    'value' => $this->textArray[$total_label_index + 3],
                    'key' => Str::slug($redeemed_label),
                    'type' => 'amount'
                ];
            }

        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function getTransactionLabelIndex($array, $string){
            $element = array_filter($array, function($text) use ($string) {
                return preg_match("/\b$string\b/i", $text);
            });

            $index = array_key_first($element);
            return $index;
    }

    private function setTransaction(){
        if(Str::contains(Str::lower($this->plainText), Str::lower('Your Order Confirmation'))){
            $trans_start = Str::afterLast($this->plainText, "===================");
            $trans_start = explode('--tagend--', $trans_start);

            foreach ($trans_start as $key => $value) {
                if($value==" " || $value=="&nbsp;"){
                    unset($trans_start[$key]);
                }
            }

            //removing empty elements from content array
            $trans_start = array_values(array_filter($trans_start));

            //removing extra spaces from array
            $trans_start = array_filter($trans_start, function($e){
                return preg_replace('/\s+/', ' ', $e);
            });

            $total_label_index = $this->getTransactionLabelIndex($trans_start, 'Your Order Total is') ;
            $sub_total_index = $this->getTransactionLabelIndex($trans_start, 'Order Total:') ;
            $tax_label_index = $this->getTransactionLabelIndex($trans_start, 'Tax Amount') ;

            $this->sub_total = trim($trans_start[$sub_total_index + 1]);
            $this->tax_amount =  trim($trans_start[$tax_label_index + 1]);
            $this->total =  trim($trans_start[$total_label_index + 1]);
        }
        else{
            $total_label_index = array_search('Total:', $this->textArray);
            $tax_label_index = array_search('GST/HST:', $this->textArray);
            $sub_total_index = array_search('Sub total:', $this->textArray);

            $this->sub_total = trim($this->textArray[$sub_total_index + 1]);
            $this->tax_amount =  trim($this->textArray[$tax_label_index + 1]);
            $this->total =  trim($this->textArray[$total_label_index + 1]);
        }

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
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail(){
        return $this->detail;
    }
}
