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

class Apple
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
    private $tax_no = null;

    private $products = [];

    private $order_no;
    private $emailDate;
    private $discount;
    private $sub_total = 0;
    private $tax_amount = 0;
    private $total = 0;
    private $payment_method = null;
    private $payment_ref = null;
    private $transaction_no = 0;
    private $extra_info = null;
    private $prod_desc = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Apple";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();
        $this->setOrderNo();
        $this->setDiscount();
    }

    private function filterArray($array){
        //removing empty elements from content array

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

        /**
         * Since apple email has two sections i-e 1 for english and other for another language.
         * So we just need the first half and remove the extra elements form array
         */
        $last_index_text = 'All rights reserved';

        $last_element = array_filter($this->textArray, function($text) use ($last_index_text) {
            return preg_match("/\b$last_index_text\b/i", $text);
        });

        $last_index = array_key_first($last_element);

        $this->textArray = array_slice($this->textArray, 0, ($last_index + 2));
//        dd($this->textArray);
        $this->textCollection = collect($this->textArray);

    }

    //TODO: Set configuration, for example start and end point of parsing
    private function isInvoice(){
        if(Str::contains(Str::lower($this->plainText), Str::lower("ORDER ID"))){ //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo(){
        $order_id_index = array_search('ORDER ID', $this->textArray);
        if($order_id_index){
            $this->order_no = $this->textArray[$order_id_index + 1];
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
            $date_text = Str::between($this->htmlBody, "@email.apple.com", "Subject");

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));

            if(Str::contains($date_text, 'Date :')){
                $date_text = strip_tags(Str::after($date_text, "Date :"));
            }
            elseif(Str::contains($date_text, 'Date:')){
                $date_text = strip_tags(Str::after($date_text, "Date:"));
            }

            $this->emailDate =  Carbon::parse($date_text);

            $this->vendor_email = "no_reply@email.apple.com";

        }

        $search_str = "Apple GST";

        array_filter($this->textArray, function($text) use ($search_str, &$tax_no) {
            if(preg_match("/\b$search_str\b/i", $text)){
                $tax_no = trim(Str::after(Str::lower($text), Str::lower($search_str)));
                $tax_no = str_replace('/hst no', '', $tax_no);
            }
        });

        $this->tax_no = $tax_no;

        $this->vendor_address = $this->textCollection->last();

    }

    private function setProducts(){
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        /**
         * Following line means search apple services from first half.
         */
        $prods_text = Str::after(Str::before($this->plainText, 'All rights reserved'), "DOCUMENT NO.");

        $prods_text = Str::before($prods_text, "Subtotal");
        $prods_text = str_replace('PRICE', '', $prods_text);
        if($this->getDiscountIndex()){
            $prods_text = Str::between($this->plainText, "x1", "Discount");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = $this->filterArray($prods_array);
        $prods = collect($prods_array);

        $this->prod_desc = $prods[3] . '<br />' . ($prods[4] ?? '');
        $prods = [
            [
                'name' => $prods[2],
                'description' => $this->prod_desc,
                'price' => $prods->last()
            ]
        ];

        $this->products = $prods;
    }

    private function setExtraInfo(){

        $p_s_t_index = array_search('P.S.T./Q.S.T.', $this->textArray);
        $tax_label_index = array_search('G.S.T./H.S.T.', $this->textArray);
        $ex_info = [];
        if($p_s_t_index) {
            $ex_info[] = [
                'label' => "P.S.T./Q.S.T.",
                'value' => $this->textArray[$p_s_t_index + 1],
                'key' => 'p_s_t',
                'type' => 'amount'
            ];

        }

        if($tax_label_index) {
            $ex_info[] = [
                'label' => "G.S.T./H.S.T.",
                'value' => $this->textArray[$tax_label_index + 1],
                'key' => 'vat',
                'type' => 'amount'
            ];

        }

        $ex_info[] = [
            'label' => "Description",
            'value' => $this->prod_desc,
            'key' => 'description',
            'type' => 'prod_desc'
        ];

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction(){
        $total_label_index = array_search('TOTAL', $this->textArray);
        $tax_label_index = array_search('G.S.T./H.S.T.', $this->textArray);
        $sub_total_index = array_search('Subtotal', $this->textArray);
        $transaction_no_index = array_search('DOCUMENT NO.', $this->textArray);
        $billed_to_index = array_search('BILLED TO', $this->textArray);

        $payment_method = Str::after($this->plainText, 'BILLED TO');
        $payment_method = Str::before($payment_method, '....');
        $payment_method = trim(str_replace('--tagend--', '', $payment_method));

        if($billed_to_index){
            $billed_next = $this->textArray[$billed_to_index + 1];
            $b_exp = explode(' ', $billed_next);
            $this->payment_ref = $b_exp[2] ?? null;
        }

        $this->sub_total = $this->textArray[$sub_total_index + 1];
        $this->tax_amount = $this->textArray[$tax_label_index + 1];
        $this->total = $this->textArray[$total_label_index + 1];
        $this->transaction_no = $this->textArray[$transaction_no_index + 1];
        $this->payment_method = $payment_method;

        $this->setExtraInfo();

    }

    public function parseEmail(){
        // try{
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
        // } catch (Exception $exception){
        //     Log::error("Array Creation Error: " . $exception->getMessage());
        //     return false;
        // }
    }

    public function setDetail(){
        $this->detail = [
            'vendor' => [
                'email' => $this->vendor_email,
                'name' => $this->vendor_name,
                'address' => $this->vendor_address,
                'tax_no' => $this->tax_no
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
            'payment_ref' => $this->payment_ref,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail(){
        return $this->detail;
    }
}
