<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use LivePixel\MercadoPago\MP;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Validator;

class MercadoPaymentController extends Controller
{
    public $token;
    public $is_enabled;
    public $currancy;
    public $mode;
 
    
    public function __construct()
    {
        $this->middleware('XSS');
    }

    public function planPayWithMercado(Request $request){

        $this->paymentSetting();

        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan           = Plan::find($planID);
        $authuser       = Auth::user();
        $coupons_id ='';
        if($plan)
        {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->{$request->mercado_payment_frequency . '_price'};
            if(isset($request->coupon) && !empty($request->coupon))
            {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if(!empty($coupons))
                {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;
                    $coupons_id = $coupons->id;
                    if($usedCoupun >= $coupons->limit)
                    {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
                }
                else
                {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if($price <= 0)
            {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id,$request->mercado_payment_frequency);

                if($assignPlan['is_success'] == true && !empty($plan))
                {

                    $orderID = time();
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
                            'payment_type' => 'Mercado Pago',
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


            \MercadoPago\SDK::setAccessToken($this->token);
            try {
                   
                // Create a preference object
                $preference = new \MercadoPago\Preference();
                // Create an item in the preference
                $item = new \MercadoPago\Item();
                $item->title = "Plan : " . $plan->name;
                $item->quantity = 1;
                $item->unit_price = (float)$price;
                $preference->items = array($item);
    
                $success_url = route('plan.mercado',[$request->plan_id,'payment_frequency='.$request->mercado_payment_frequency,'coupon_id='.$coupons_id,'flag'=>'success']);
                $failure_url = route('plan.mercado',[$request->plan_id,'flag'=>'failure']);
                $pending_url = route('plan.mercado',[$request->plan_id,'flag'=>'pending']);
                
                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url
                );
               
                $preference->auto_return = "approved";
                $preference->save();
    
                // Create a customer object
                $payer = new \MercadoPago\Payer();
                // Create payer information
                $payer->name = \Auth::user()->name;
                $payer->email = \Auth::user()->email;
                $payer->address = array(
                    "street_name" => ''
                );   
                if($this->mode =='live'){
                    $redirectUrl = $preference->init_point;
                }else{
                    $redirectUrl = $preference->sandbox_init_point;
                }
                return redirect($redirectUrl);
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
            // callback url :  domain.com/plan/mercado

        }
        else
        {
            return redirect()->back()->with('error', 'Plan is deleted.');
        }
    }

    public function getPaymentStatus(Request $request,$plan){
        
         $this->paymentSetting();
         $planID         = \Illuminate\Support\Facades\Crypt::decrypt($plan);
       $plan           = Plan::find($planID);
       $price                  = $plan->{$request->payment_frequency . '_price'};

       $user = Auth::user();
       $orderID = time();
      
       if($plan)
       {
           try
           {
            
            if($plan && $request->has('status'))
            {
                
                if($request->status == 'approved' && $request->flag =='success')
                {
                       if(!empty($user->payment_subscription_id) && $user->payment_subscription_id != '')
                       {
                           try
                           {
                               $user->cancel_subscription($user->id);
                           }
                           catch(\Exception $exception)
                           {
                               \Log::debug($exception->getMessage());
                           }
                       }

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
                       $order->price          = $price;
                       $order->price_currency = $this->currancy;
                       $order->txn_id         = $request->has('preference_id')?$request->preference_id:'';
                       $order->payment_type   = 'Mercado Pago';
                       $order->payment_status = 'succeeded';
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
                   }else{
                       return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
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

    public function invoicePayWithMercado(Request $request){


        //$this->paymentSetting();

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

            $admin_payment_setting = Utility::non_auth_payment_settings($user->id);
            $this->token = isset($admin_payment_setting['mercado_access_token'])?$admin_payment_setting['mercado_access_token']:'';
        $this->mode = isset($admin_payment_setting['mercado_mode'])?$admin_payment_setting['mercado_mode']:'';
        $this->is_enabled = isset($admin_payment_setting['is_mercado_enabled'])?$admin_payment_setting['is_mercado_enabled']:'off';
        $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        }

        if($invoice->getDue() < $request->amount){
            return Utility::error_res('not correct amount');
        }

        $preference_data       = array(
            "items" => array(
                array(
                    "title" => "Invoice Payment",
                    "quantity" => 1,
                    "currency_id" => $this->currancy,
                    "unit_price" => (float)$request->amount,
                ),
            ),
        );

         \MercadoPago\SDK::setAccessToken($this->token);
        try {
               
            // Create a preference object
            $preference = new \MercadoPago\Preference();
            // Create an item in the preference
            $item = new \MercadoPago\Item();
            $item->title = "Invoice : " . $request->invoice_id;
            $item->quantity = 1;
            $item->unit_price = (float)$request->amount;
            $preference->items = array($item);

            $success_url = route('invoice.mercado',[encrypt($invoice->id),'amount'=>(float)$request->amount,'flag'=>'success']);
            $failure_url = route('invoice.mercado',[encrypt($invoice->id),'flag'=>'failure']);
            $pending_url = route('invoice.mercado',[encrypt($invoice->id),'flag'=>'pending']);
            $preference->back_urls = array(
                "success" => $success_url,
                "failure" => $failure_url,
                "pending" => $pending_url
            );
            $preference->auto_return = "approved";
            $preference->save();

            // Create a customer object
            $payer = new \MercadoPago\Payer();
            // Create payer information
            $payer->name = $user->name;
            $payer->email = $user->email;
            $payer->address = array(
                "street_name" => ''
            );
            
            if($this->mode =='live'){
                $redirectUrl = $preference->init_point;
            }else{
                $redirectUrl = $preference->sandbox_init_point;
            }
            return redirect($redirectUrl);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getInvociePaymentStatus(Request $request,$invoice_id){
    
         if(!empty($invoice_id))
        {
            
            $invoice_id = decrypt($invoice_id);
            $invoice    = Invoice::find($invoice_id);
             if(\Auth::check())
                {
                     $user = Auth::user();
                }
                else
                {
                   $user=User::where('id',$invoice->created_by)->first();
                }
            if($invoice && $request->has('status'))
            {
            
                try
                {

                
                  
                    if($request->status == 'approved' && $request->flag =='success')
                    {
                        

                        $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user);
                    $invoice_payment->invoice_id     = $invoice_id;
                    $invoice_payment->amount         = $request->amount?$request->amount:0;
                    $invoice_payment->date           = date('Y-m-d');
                    $invoice_payment->payment_id     = 0;
                    $invoice_payment->payment_type   = 'Mercado Pago';
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
                            $msg = ucfirst($user->name) .' paid '.$request->amount.'.'; 
                            Utility::send_slack_msg($msg);
                        }
                        if(isset($settings['telegram_payment_notification']) && $settings['telegram_payment_notification'] ==1){
                            $resp = ucfirst($user->name) .' paid '.$request->amount.'.';
                            \Utility::send_telegram_msg($resp);    
                        }
                   
                        if(\Auth::check())
                        {
                              return redirect()->route('invoices.show', $invoice_id)->with('success', __('Invoice paid Successfully!'));
                        }
                        else
                        {
                            return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
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
                    return redirect()->route('invoices.index')->with('error', __('Plan not found!'));
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
                    return redirect()->route('invoices.index')->with('error', __('Invoice not found.'));
            }
            else
            {
                return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('error', __('Invoice not found.'));
            }
           
        }
    }

    public function paymentSetting()
    {
        
        $admin_payment_setting = Utility::payment_settings();
        $this->token = isset($admin_payment_setting['mercado_access_token'])?$admin_payment_setting['mercado_access_token']:'';
        $this->mode = isset($admin_payment_setting['mercado_mode'])?$admin_payment_setting['mercado_mode']:'';
        $this->is_enabled = isset($admin_payment_setting['is_mercado_enabled'])?$admin_payment_setting['is_mercado_enabled']:'off';
        $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        return;
    }
}
