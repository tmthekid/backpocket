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

class Uber
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
    private $message_id;

    private $tax_label_index = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Uber";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();
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

    private function arraySearchIndex($string){
        $element = array_filter($this->textArray, function($text) use ($string) {
            return preg_match("/\b$string\b/i", $text);
        });
        $index = array_key_first($element);

        return $index;
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
        if(Str::contains(Str::lower($this->plainText), Str::lower("Thanks for ordering"))){
            $this->vendor_name = "Uber Eats";
            $this->order_no = "UBE" . date('Ymdhis') . mt_rand(100, 999);
            $this->tax_label_index = array_search('Tax', $this->textArray);
            return true;
        }
        elseif(Str::contains(Str::lower($this->plainText), Str::lower("Thanks for riding"))){
            $this->vendor_name = "Uber Trip";
            $this->order_no = "UBT" . date('Ymdhis') . mt_rand(100, 999);
            $this->tax_label_index = array_search('HST', $this->textArray);
            return true;
        } else{ //If it is not order/invoice then skip it.
            return false;
        }
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Forwarded message"))) {
            //TODO: check if there are more than forwarded messages
            $date_text = Str::between($this->htmlBody, "@uber.com", "Subject");

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));

            $date_text = strip_tags(Str::after($date_text, "Date:"));

            $this->emailDate =  Carbon::parse($date_text);

            //If string contains fails then use the default email address
            if(Str::contains($this->htmlBody, 'Uber Receipts &lt;')){
                $vendorEmailStr = Str::between($this->htmlBody, "Uber Receipts &lt;", "@uber.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@uber.com';
            } else{
                $this->vendor_email = "uber.canada@uber@uber.com";
            }

        }

        if(Str::contains($this->plainText, "Forgot password")){
            $this->vendor_address = str_replace('--tagend--', '', Str::between($this->plainText, "Forgot password", "Privacy"));
        }
    }

    private function setProducts(){
        /**
         * Products Details
         */
        $prods_text = null;

        $prods = [];

        //TODO: Check with discounted email
        if($this->vendor_name=="Uber Eats"){
            $prods_text = Str::between($this->plainText, "Thanks for ordering", "Subtotal");

            $prods_array = explode('--tagend--', $prods_text);
            $prods_array = $this->filterArray($prods_array);

            $prods_total_index = array_search('Total', $prods_array);
            $this->total = $prods_array[$prods_total_index + 1];

            unset($prods_array[$prods_total_index]);
            unset($prods_array[$prods_total_index+1]);
            $prods_array = array_values(array_filter($prods_array));

            foreach ($prods_array as $key => $value){
                if(Str::contains($value, 'Total') || Str::contains($value, '(CA$')){
                    continue;
                }

                if(Str::contains($value, 'CA$')){
                    $prods[] = [
                        'name' => $prods_array[$key - 1],
                        'price' => $value
                    ];
                }
            }

        } elseif ($this->vendor_name == "Uber Trip"){
            $prods_text = Str::between($this->plainText, "Thanks for riding", "Subtotal");

            $prods_array = explode('--tagend--', $prods_text);
            $prods_array = array_values(array_filter($prods_array));
            $prods_total_index = array_search('Total', $prods_array);
            $this->total = $prods_array[$prods_total_index + 1];

            $trip_fare_index = array_search('Trip Fare', $prods_array);
            $prods[] = [
                'name' => "Trip Fare",
                'price' => $prods_array[$trip_fare_index + 1]
            ];
        }

        $this->products = $prods;
    }

    /**
     * If the label and value in same array element. For Example [30] => "You rode with Dotun"
     * @return array
     */
    private function getDescription($search_str){
        $output = [];
        array_filter($this->textArray, function($text) use ($search_str, &$output) {
            foreach ($search_str as $key => $value){
                if(preg_match("/\b$value\b/i", $text)){
                    $output[$key] = [
                        'label' => $value,
                        'amount' => trim(Str::after(Str::lower($text), Str::lower($value))),
                    ];
                }
            }
        });
        return $output;
    }

    private function setExtraInfo(){

        $special_offer_label_index = $this->arraySearchIndex("Special Offer");
        $promotion_label_index = $this->arraySearchIndex("Promotion");
        if($this->vendor_name == "Uber Trip"){
            $extra_tax_label_index = array_search('Tolls, Surcharges, and Fees', $this->textArray);

            $ex_info = [];
            if($extra_tax_label_index){
                $ex_info[] = [
                    'label' => "Tolls, Surcharges, and Fees",
                    'value' => $this->textArray[$extra_tax_label_index + 1],
                    'key' => 'extra_tax',
                    'type' => 'amount'
                ];
            }

            $search_str = [
                'rider_name' => 'You rode with',
                'license_plate' => 'License Plate',
                'ptc_license' => 'PTC License',
            ];

            $desc = $this->getDescription($search_str);
            if($desc){
                foreach ($desc as $key => $value){
                    $ex_info[] = [
                        'label' => $value['label'],
                        'value' => Str::title($value['amount']),
                        'key' => $key,
                        'type' => 'desc'
                    ];
                }
            }

        }
        elseif($this->vendor_name == "Uber Eats"){
            $service_fee_label_index = $this->arraySearchIndex("Service Fee");
            $delivery_fee_label_index = $this->arraySearchIndex("Delivery Fee");

            if($service_fee_label_index){
                $ex_info[] = [
                    'label' => "Service Fee",
                    'value' => $this->textArray[$service_fee_label_index + 1],
                    'key' => 'service_fee',
                    'type' => 'amount'
                ];
            }

            if($delivery_fee_label_index){
                $ex_info[] = [
                    'label' => "Delivery Fee",
                    'value' => $this->textArray[$delivery_fee_label_index + 1],
                    'key' => 'delivery_fee',
                    'type' => 'amount'
                ];
            }

        }

        if($special_offer_label_index){
            $ex_info[] = [
                'label' => "Special Offer",
                'value' => $this->textArray[$special_offer_label_index + 1],
                'key' => 'special_offer',
                'type' => 'amount'
            ];
        }

        if($promotion_label_index){
            $ex_info[] = [
                'label' => "Promotion",
                'value' => $this->textArray[$promotion_label_index + 1],
                'key' => 'promotion',
                'type' => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction(){
        $sub_total_index = array_search('Subtotal', $this->textArray);

        $card_label_index = $this->arraySearchIndex('Switch');

        $this->payment_method = $this->textArray[$card_label_index - 3];
        $this->payment_ref = substr($this->textArray[$card_label_index - 2], -4);
        $this->sub_total = $this->textArray[$sub_total_index + 1];
        $this->tax_amount = $this->textArray[$this->tax_label_index + 1];

        $this->total = $this->total;
        $this->payment_method = $this->payment_method;
        $this->setExtraInfo();

    }

    public function parseEmail(){
        try{
            if(!$this->isInvoice()) return false;

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
            'payment_ref' => $this->payment_ref,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail(){
        return $this->detail;
    }
}
