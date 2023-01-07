<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Validator;

class RazorpayPaymentController extends Controller
{
    //
    public $secret_key;
    public $public_key;
    public $is_enabled;
    public $currancy;

    public function __construct()
    {
        $this->middleware('XSS');
    }

    public function planPayWithRazorpay(Request $request){

        $this->paymentSetting();

        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan           = Plan::find($planID);
        $authuser       = Auth::user();
        $coupon_id = '';
        if($plan)
        {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->{$request->razorpay_payment_frequency . '_price'};

            if(isset($request->coupon) && !empty($request->coupon))
            {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if(!empty($coupons))
                {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;

                    if($usedCoupun >= $coupons->limit)
                    {
                        return Utility::error_res( __('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
                    $coupon_id = $coupons->id;
                }
                else
                {
                    return Utility::error_res( __('This coupon code is invalid or has expired.'));
                }
            }

            if($price <= 0)
            {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id);

                if($assignPlan['is_success'] == true && !empty($plan))
                {
                    if(!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '')
                    {
                        try
                        {
                            $authuser->cancel_subscription($authuser->id);
                        }
                        catch(\Exception $exception)
                        {
                            \Log::debug($exception->getMessage());
                        }
                    }

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price==null?0:$price,
                            'price_currency' => !empty($this->currancy) ? $this->currancy : 'usd',
                            'txn_id' => '',
                            'payment_type' => 'Paystack',
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg'] = __("Plan successfully upgraded.");
                    $res['flag'] = 2;
                    return $res;
                }
                else
                {
                    return Utility::error_res( __('Plan fail to upgrade.'));
                }
            }

            $res_data['email'] = Auth::user()->email;
            $res_data['total_price'] = $price;
            $res_data['currency'] = $this->currancy;
            $res_data['flag'] = 1;
            $res_data['payment_frequency'] = $request->razorpay_payment_frequency;
            $res_data['coupon'] = $coupon_id;
            return $res_data;
        }
        else
        {
            return Utility::error_res( __('Plan is deleted.'));
        }

    }
    public function getPaymentStatus(Request $request,$pay_id,$plan){

        $payment = $this->paymentSetting();
        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan           = Plan::find($planID);
        $user = Auth::user();
        if($plan)
        {
            try
            {
                $orderID = time();
                $ch = curl_init('https://api.razorpay.com/v1/payments/' . $pay_id . '');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_USERPWD, $this->public_key . ':' . $this->secret_key); // Input your Razorpay Key Id and Secret Id here
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = json_decode(curl_exec($ch));
                // check that payment is authorized by razorpay or not

                if($response->status == 'authorized')
                {


                    if($request->has('coupon_id') && $request->coupon_id != '')
                    {
                        $coupons = Coupon::find($request->coupon_id);
                        if(!empty($coupons))
                        {
                            $userCoupon            = new UserCoupon();
                            $userCoupon->user   = $user->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order  = $orderID;
                            $userCoupon->save();


                            $usedCoupun = $coupons->used_coupon();
                            if($coupons->limit <= $usedCoupun)
                            {
                                $coupons->is_active = 0;
                                $coupons->save();
                            }
                        }
                    }

                    $order                 = new Order();
                    $order->order_id       = $orderID;
                    $order->name           = $user->name;
                    $order->card_number    = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year  = '';
                    $order->plan_name      = $plan->name;
                    $order->plan_id        = $plan->id;
                    $order->price          = isset($response->amount) ? $response->amount/100 : 0;
                    $order->price_currency = $this->currancy;
                    $order->txn_id         = isset($response->id) ? $response->id : $pay_id;
                    $order->payment_type   = __('Razorpay');
                    $order->payment_status = 'success';
                    $order->receipt        = '';
                    $order->user_id        = $user->id;
                    $order->save();

                    $assignPlan = $user->assignPlan($plan->id, $request->payment_frequency);

                    if($assignPlan['is_success'])
                    {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Plan not found!'));
            }
        }
    }

    public function invoicePayWithRazorpay(Request $request){


        $validatorArray = [
            'amount' => 'required',
            'invoice_id' => 'required',
        ];
        $validator      = Validator::make(
            $request->all(), $validatorArray
        )->setAttributeNames(
            ['invoice_id' => 'Invoice']
        );
        if($validator->fails())
        {
            return Utility::error_res($validator->errors()->first());
        }
        $invoice = Invoice::find($request->invoice_id);
        if(\Auth::check())
        {
             $user = Auth::user();
        }
        else
        {
           $user=User::where('id',$invoice->created_by)->first();
        }
        if(\Auth::check())
        {
             $user = Auth::user();
             $this->paymentSetting();
        }else{

           $payment_setting = Utility::non_auth_payment_settings($user->id);

        $this->currancy = isset($payment_setting['currency'])?$payment_setting['currency']:'';
        $this->secret_key = isset($payment_setting['razorpay_secret_key'])?$payment_setting['razorpay_secret_key']:'';
        $this->public_key = isset($payment_setting['razorpay_public_key'])?$payment_setting['razorpay_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_razorpay_enabled'])?$payment_setting['is_razorpay_enabled']:'off';
            
        }
        if($invoice->getDue() < $request->amount){
            return Utility::error_res('not currect amount');
        }

        $res_data['email'] = $user->email;
        $res_data['total_price'] = $request->amount;
        $res_data['currency'] = $this->currancy;
        $res_data['flag'] = 1;
        $res_data['invoice_id'] = $invoice->id;
        $request->session()->put('invoice_data', $res_data);
        $this->pay_amount =$request->amount;
        return $res_data;
    }


    public function getInvociePaymentStatus($pay_id,$invoice_id,Request $request){
       
         $invoice_id = decrypt($invoice_id);
            $invoice    = Invoice::find($invoice_id);

         $user=User::where('id',$invoice->created_by)->first();


         if(\Auth::check())
        {
             $user = Auth::user();
             $this->paymentSetting();
        }else{

           $payment_setting = Utility::non_auth_payment_settings($user->id);

        $this->currancy = isset($payment_setting['currency'])?$payment_setting['currency']:'';
        $this->secret_key = isset($payment_setting['razorpay_secret_key'])?$payment_setting['razorpay_secret_key']:'';
        $this->public_key = isset($payment_setting['razorpay_public_key'])?$payment_setting['razorpay_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_razorpay_enabled'])?$payment_setting['is_razorpay_enabled']:'off';
            
        }

        $invoice_data =  $request->session()->get('invoice_data') ;


        if(!empty($invoice_id) && !empty($pay_id))
        {

            

            if($invoice && !empty($invoice_data))
            {


                try
                {

                    $orderID = time();
                $ch      = curl_init('https://api.razorpay.com/v1/payments/' . $pay_id . '');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_USERPWD, $this->public_key . ':' . $this->secret_key); // Input your Razorpay Key Id and Secret Id here
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = json_decode(curl_exec($ch));

                   
                   
               

                    if(isset($response->status) && $response->status == 'authorized')
                    {

                        //$paydata = $response->data;

                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user);
                        $invoice_payment->invoice_id     = $invoice_id;
                        $invoice_payment->amount         = isset($invoice_data['total_price'])?$invoice_data['total_price']:0;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->payment_id     = 0;
                        $invoice_payment->payment_type   =  __('Razorpay');
                        $invoice_payment->client_id      =  $user->id;
                        $invoice_payment->notes          = '';
                        $invoice_payment->save();

                        if(($invoice->getDue() - $invoice_payment->amount) == 0)
                        {
                            $invoice->status = 'paid';
                            $invoice->save();
                            
                        }
                       
                        $settings  = Utility::settings($invoice->created_by);
                        
                        if(isset($settings['payment_notification']) && $settings['payment_notification'] ==1){
                            $msg = ucfirst($user->name) .' paid '.$invoice_data['total_price'].'.'; 
                            Utility::send_slack_msg($msg);
                        }
                        if(isset($settings['telegram_payment_notification']) && $settings['telegram_payment_notification'] ==1){
                            $resp =ucfirst($user->name) .' paid '.$invoice_data['total_price'].'.';
                            \Utility::send_telegram_msg($resp);    
                        }


                        if(\Auth::check())
                        {
                            return redirect()->route('invoices.show', $invoice_id)->with('success', __('Payment added Successfully'));
                        }
                        else
                        {
                            return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('success', __('Payment added Successfully'));
                        }
                        
                    }else{
                        if(\Auth::check())
                        {
                             return redirect()->route('invoices.show',$invoice_id)->with('error', __('Transaction fail'));
                        }   
                        else
                        {
                            return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('error', __('Transaction fail'));
                        }
                       
                    }
                }
                catch(\Exception $e)
                {
                    if(\Auth::check())
                    {
                         return redirect()->route('plans.index')->with('error', __('Invoice not found.'));
                    }   
                    else
                    {
                        return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('error', __('Invoice not found.'));
                    }
                   
                }
            }else{
                if(\Auth::check())
                {
                     return redirect()->route('invoices.show',$invoice_id)->with('error', __('Invoice not found.'));
                }
                else
                {
                    return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('error', __('Invoice not found.'));
                }
               
            }
        }else{
            if(\Auth::check())
                {
                     return redirect()->route('invoices.show',$invoice_id)->with('error', __('Invoice not found.'));
                }
                else
                {
                    return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('error', __('Invoice not found.'));
                }
        }
    }

    public function paymentSetting()
    {

        $payment_setting = Utility::payment_settings();
        $this->currancy = isset($payment_setting['currency'])?$payment_setting['currency']:'';
        $this->secret_key = isset($payment_setting['razorpay_secret_key'])?$payment_setting['razorpay_secret_key']:'';
        $this->public_key = isset($payment_setting['razorpay_public_key'])?$payment_setting['razorpay_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_razorpay_enabled'])?$payment_setting['is_razorpay_enabled']:'off';
    }
}
