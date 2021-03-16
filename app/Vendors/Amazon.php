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

class Amazon
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
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Amazon";
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
        if(Str::contains(Str::lower($this->plainText), Str::lower("Order Confirmation"))){ //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo(){
        $order_no = null;
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));
        $order_no = array_search('Order Confirmation', $textArray);
        if($order_no) {
            $order_no = $textArray[$order_no + 1];
        }
        
        $this->order_no = trim(str_replace('Order #','', $order_no));
    }

    private function transactionExists(){
        $transaction_exists = Transaction::where('order_no', $this->order_no)->exists();
        if($transaction_exists){
            return true;
        }
        return false;
    }

    private function getDiscountIndex(){
        return array_search('Promotion Applied:', $this->textArray);
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
            $date_text = Str::between($this->htmlBody, "auto-confirm@amazon.ca</a>&gt;", "Subject:");
            
            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
            $date_text = strip_tags(Str::after($date_text, "Date:"));
            
            
            $this->emailDate =  Carbon::parse($date_text);
            
            //If string contains fails then use the default email address
            if(Str::contains($this->htmlBody, 'Amazon.ca</b> <span dir="ltr">&lt;')){
                $vendorEmailStr = Str::between($this->htmlBody, 'Amazon.ca</b> <span dir="ltr">&lt;', "@amazon.ca");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@amazon.ca';
            } else{
                $this->vendor_email = "auto-confirm@amazon.ca";
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
        if(Str::contains($this->plainText, "Order Details")){
            $prods_text = null;

            //TODO: Check with discounted email
            $prods_text = Str::between($this->plainText, "Order Details", "Item Subtotal:");
            if($this->getDiscountIndex()){
                $prods_text = Str::between($this->plainText, "Order Details", "Item Subtotal:");
            }

            $prods_array = explode('--tagend--', $prods_text);
            $prods_array = array_map('trim', $prods_array);
            $prods_array = array_values(array_filter($prods_array));

            $prods = [];
            foreach ($prods_array as $key => $value){
                if(Str::contains($value, '$')){

                    $prods[] = [
                        'name' => $prods_array[$key - 3],
                        'description' => $prods_array[$key - 4],
                        'price' => $value
                    ];
                }
            }
            
            $this->products = $prods;
        } 
    }

    private function setExtraInfo(){

        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));
        
        $shipping = array_search('Shipping & Handling:', $textArray);
        
        $promotion = array_search('Promotion Applied:', $textArray);
        
        $gst_hst = array_search('Estimated Tax (GST/HST):', $textArray);
        
        $pst_qst =  array_search('Estimated Tax (PST/QST):', $textArray);
        
        $ex_info = [];

        if($shipping) {
            $shipping = $textArray[$shipping + 1];
            $ex_info[] = [
                'label' => "Shipping & Handling",
                'value' => $shipping,
                'key' => 'shipping_handling',
                'type' => 'amount'
            ];
        }

        if($promotion) {
            $promotion = $textArray[$promotion + 1];
            $ex_info[] = [
                'label' => "Promotion Applied",
                'value' => $promotion,
                'key' => 'promotion_applied',
                'type' => 'amount'
            ];
        }

        if($gst_hst) {
            $gst_hst = $textArray[$gst_hst + 1];
            $ex_info[] = [
                'label' => "Estimated Tax (GST/HST)",
                'value' => $gst_hst,
                'key' => 'gst_hst',
                'type' => 'amount'
            ];
        }

        if($pst_qst) {
            $pst_qst =  $textArray[$pst_qst + 1];
            $ex_info[] = [
                'label' => "Estimated Tax (PST/QST)",
                'value' => $pst_qst,
                'key' => 'pst_qst',
                'type' => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction(){
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));
        $sub_total_label_index = array_search('Item Subtotal:', $textArray);
        $total_label_index = array_search('Order Total:', $textArray);
        $tax_label_index = array_search('Estimated Tax (GST/HST):', $textArray);
        

        if($sub_total_label_index)
        {   
            $this->sub_total = $textArray[$sub_total_label_index + 1];
        }
        if($total_label_index) {
            $this->total = $textArray[$total_label_index + 1];
        }
        if($tax_label_index) {
            $this->tax_amount = $textArray[$tax_label_index + 1];
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
