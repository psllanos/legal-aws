<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\User;
use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Validator;

class PaymentWallController extends Controller
{

    public function planpay(Request $request)
    {
        $data=$request->all();

         $admin_payment_setting = Utility::payment_settings();
        return view('plans.planpay',compact('data','admin_payment_setting'));
        
    }

    public function invoicepay(Request $request)
    {
        $data=$request->all();
        $invoice=Invoice::find($request->invoice_id);
         $admin_payment_setting =$this->paymentSetting($invoice->created_by);

        return view('invoices.paymentwallpay',compact('data','admin_payment_setting'));
        
    }

    

    public function planerror(Request $request,$flag)
    {
        if($flag == 1){
            return redirect()->route("plans.index")->with('success', __('Plan activated Successfully! '));
        }else{
                return redirect()->route("plans.index")->with('error', __('Transaction has been failed! '));
        } 
    
    }

    public function invoiceerror(Request $request,$flag,$invoice_id)
    {
   
         if(\Auth::check())
        {
            if($flag == 1){
                     return redirect()->route('invoices.show',$invoice_id)->with('success', __('Payment added Successfully')); 
            }else{
                    return redirect()->route('invoices.show',$invoice_id)->with('error', __('Transaction has been failed! '));
            } 
          
        }
        else
        {
            if($flag == 1){
                     return redirect()->route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Payment added Successfully ')); 
            }else{
                    return redirect()->route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Transaction has been failed! '));
            }
        }
       
    }
    



   public function planPayWithPaymentWall(Request $request,$plan_id){
        

        $this->planpaymentSetting();



        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan           = Plan::find($planID);

        $authuser       = Auth::user();
        $coupon_id ='';
        if($plan)
        {

            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  =  $plan->{$request->amount . '_price'};

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
                            'price' => $price,
                            'price_currency' => !empty($this->currancy) ? $this->currancy : 'usd',
                            'txn_id' => '',
                            'payment_type' => 'PaymentWall',
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
            else
            {

                \Paymentwall_Config::getInstance()->set(array(
                  
                    'private_key' => $this->secret_key
                ));

                $parameters = $request->all();

                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $price,
                    'currency' => !empty($this->currancy) ? $this->currancy : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );

                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(),true);
                $response = $charge->getPublicData();
          
                if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                       if($request->has('coupon') && $request->coupon != '')
                        {
                            $coupons = Coupon::find($request->coupon);
                            if(!empty($coupons))
                            {
                                $userCoupon            = new UserCoupon();
                                $userCoupon->user   = $authuser->id;
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
                        $order->name           = $authuser->name;
                        $order->card_number    = '';
                        $order->card_exp_month = '';
                        $order->card_exp_year  = '';
                        $order->plan_name      = $plan->name;
                        $order->plan_id        = $plan->id;
                        $order->price          = isset($paydata['amount']) ? $paydata['amount'] : $price;
                        $order->price_currency = $this->currancy;
                        $order->txn_id         = isset($paydata['txid']) ? $paydata['txid'] : 0;
                        $order->payment_type   = __('PaymentWall');
                        $order->payment_status = 'success';
                        $order->receipt        = '';
                        $order->user_id        = $authuser->id;
                        $order->save();

                        $assignPlan = $authuser->assignPlan($plan->id);

                        if($assignPlan['is_success'])
                        {
                          
                             $res['flag'] = 1;
                             return $res;
                          
                        }
                    } elseif ($charge->isUnderReview()) {
                          $res['flag'] = 2;
                             return $res;
                    }
                
                } else {
                    $errors = json_decode($response, true);

                    $res['flag'] = 2;
                    return $res;
                }
            }
            $res['flag'] = 2;
            return $res;
        }
        else
        {
            $res['flag'] = 2;
            return $res;
        }
    }

    public function invoicePayWithPaymentWall(Request $request,$invoice_id){
        
        $invoice = Invoice::find($invoice_id);
        
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
                 $this->paymentSetting($user->id);
            }else{

                $payment_setting = Utility::non_auth_payment_settings($user->id);
                
                $this->currancy =isset($payment_setting['currency'])?$payment_setting['currency']:'';
                
                $this->secret_key = isset($payment_setting['flutterwave_secret_key'])?$payment_setting['flutterwave_secret_key']:'';
                $this->public_key = isset($payment_setting['flutterwave_public_key'])?$payment_setting['flutterwave_public_key']:'';
                $this->is_enabled = isset($payment_setting['is_flutterwave_enabled'])?$payment_setting['is_flutterwave_enabled']:'off';
            }
            if($invoice->getDue() < $request->amount){
                return Utility::error_res('not currect amount');
            }   
            \Paymentwall_Config::getInstance()->set(array(
              
                'private_key' => $this->secret_key
            ));

            $parameters = $request->all();

            $chargeInfo = array(
                'email' => $parameters['email'],
                'history[registration_date]' => '1489655092',
                'amount' => isset($request['amount'])?$request['amount']:0,
                'currency' => !empty($this->currancy) ? $this->currancy : 'USD',
                'token' => $parameters['brick_token'],
                'fingerprint' => $parameters['brick_fingerprint'],
                'description' => 'Order #123'
            );

            $charge = new \Paymentwall_Charge();
            $charge->create($chargeInfo);
            $responseData = json_decode($charge->getRawResponseData(),true);
            $response = $charge->getPublicData();
      
            if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                if ($charge->isCaptured()) {

                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user);
                        $invoice_payment->invoice_id     = $invoice_id;
                        $invoice_payment->amount         = isset($request['amount'])?$request['amount']:0;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->payment_id     = 0;
                        $invoice_payment->payment_type   = 'paytm';
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
                            $resp = ucfirst($user->name) .' paid '.$invoice_data['total_price'].'.'; 
                            \Utility::send_telegram_msg($resp);    
                        }

                        $res['invoice']=$invoice_id;
                         $res['flag'] = 1;
                         return $res;
            
                } elseif ($charge->isUnderReview()) {
                    $res['invoice']=$invoice_id;
                     $res['flag'] = 2;
                     return $res;
                }
            } 
             else {
                $errors = json_decode($response, true);
                $res['invoice']=$invoice_id;
                 $res['flag'] = 2;
                 return $res;
            }

    }


     
    public function paymentSetting($id)
    {
        $payment_setting = Utility::invoice_payment_settings($id);

        $this->currancy = isset($payment_setting['currency'])?$payment_setting['currency']:'';

        $this->secret_key = isset($payment_setting['paymentwall_private_key'])?$payment_setting['paymentwall_private_key']:'';
        $this->public_key = isset($payment_setting['paymentwall_public_key'])?$payment_setting['paymentwall_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_paymentwall_enabled'])?$payment_setting['is_paymentwall_enabled']:'off';
        return $this;
    }




    public function planpaymentSetting()
    {
        $payment_setting = Utility::payment_settings();

        $this->currancy = isset($payment_setting['currency'])?$payment_setting['currency']:'';

        $this->secret_key = isset($payment_setting['paymentwall_private_key'])?$payment_setting['paymentwall_private_key']:'';
        $this->public_key = isset($payment_setting['paymentwall_public_key'])?$payment_setting['paymentwall_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_paymentwall_enabled'])?$payment_setting['is_paymentwall_enabled']:'off';
        return $this;
    }
}
