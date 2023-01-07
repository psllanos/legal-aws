<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PaytmWallet;
use App\Models\InvoicePayment;
use App\Models\Invoice;

class PaytmPaymentController extends Controller
{
    public $currancy;

    public function __construct()
    {
        $this->middleware('XSS');
    }

    public function planPayWithPaytm(Request $request){

        $payment = $this->paymentSetting();

        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan           = Plan::find($planID);
        $authuser       = Auth::user();
        $coupons_id ='';
        if($plan)
        {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->{$request->paytm_payment_frequency . '_price'};
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
                        return Utility::error_res( __('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
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
                            'payment_type' => 'Paytm',
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


         

            $call_back = route('plan.paytm',[$request->plan_id,'coupon_id='.$coupons_id]);
            $payment = PaytmWallet::with('receive');
            $payment->prepare(
                [
                    'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                    'user' => Auth::user()->id,
                    'mobile_number' => $request->mobile,
                    'email' => Auth::user()->email,
                    'amount' => $price,
                    'plan' => $plan->id,
                    'callback_url' => $call_back
                ]
            );
            return $payment->receive();
        }
        else
        {
            return Utility::error_res( __('Plan is deleted.'));
        }
    }

    public function getPaymentStatus(Request $request,$plan){

        // $payment  = $this->paymentSetting();

        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan           = Plan::find($planID);
        $price                  = $plan->{$request->paytm_payment_frequency . '_price'};

        $user = Auth::user();
        $orderID = time();
      
        if($plan)
        {
            try
            {
                $transaction = PaytmWallet::with('receive');
                $response = $transaction->response();
                if($transaction->isSuccessful())
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
                    $order->price          = $price;
                    $order->price_currency = $this->currancy;
                    $order->txn_id         = isset($request->TXNID) ? $request->TXNID : '';
                    $order->payment_type   = __('Paytm');
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

    public function invoicePayWithPaytm(Request $request){

       // $this->paymentSetting();

        $validatorArray = [
            'amount' => 'required',
            'invoice_id' => 'required',
            'mobile' => 'required'
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
            
             $this->paymentSetting();
        }else{

            $admin_payment_setting = Utility::non_auth_payment_settings($user->id);

        $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        config(
            [
                'services.paytm-wallet.env' => isset($admin_payment_setting['paytm_mode'])?$admin_payment_setting['paytm_mode']:'',
                'services.paytm-wallet.merchant_id' => isset($admin_payment_setting['paytm_merchant_id'])?$admin_payment_setting['paytm_merchant_id']:'',
                'services.paytm-wallet.merchant_key' =>  isset($admin_payment_setting['paytm_merchant_key'])?$admin_payment_setting['paytm_merchant_key']:'',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' =>isset($admin_payment_setting['paytm_industry_type'])?$admin_payment_setting['paytm_industry_type']:'',
            ]
        );
        }


        if($invoice->getDue() < $request->amount){
            return Utility::error_res('not correct amount');
        }

        $call_back = route('invoice.paytm',[encrypt($request->invoice_id)]);

       
        $payment = PaytmWallet::with('receive');
        $payment->prepare(
            [
                'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                'user' => $user->id,
                'mobile_number' => $request->mobile,
                'email' => $user->email,
                'amount' => $request->amount,
                'invoice_id' => $request->invoice_id,
                'callback_url' => $call_back,
            ]
        );
        return $payment->receive();
    }

    public function getInvociePaymentStatus(Request $request,$invoice_id){

        //$this->paymentSetting();

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
            if(\Auth::check())
            {
                
                 $this->paymentSetting();
            }else{

                $admin_payment_setting = Utility::non_auth_payment_settings($user->id);

            $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
            config(
                [
                    'services.paytm-wallet.env' => isset($admin_payment_setting['paytm_mode'])?$admin_payment_setting['paytm_mode']:'',
                    'services.paytm-wallet.merchant_id' => isset($admin_payment_setting['paytm_merchant_id'])?$admin_payment_setting['paytm_merchant_id']:'',
                    'services.paytm-wallet.merchant_key' =>  isset($admin_payment_setting['paytm_merchant_key'])?$admin_payment_setting['paytm_merchant_key']:'',
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' =>isset($admin_payment_setting['paytm_industry_type'])?$admin_payment_setting['paytm_industry_type']:'',
                ]
            );
            }

            if($invoice)
            {
                $invoice_data =  $request->session()->get('invoice_data') ;

                try
                {
                    $transaction = PaytmWallet::with('receive');
                    $response = $transaction->response();
                    if($transaction->isSuccessful())
                    {
                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user);
                        $invoice_payment->invoice_id     = $invoice_id;
                        $invoice_payment->amount         = isset($request->TXNAMOUNT) ? $request->TXNAMOUNT : 0;
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

                        if(\Auth::check())
                        {
                            return redirect()->route('invoices.show', $invoice_id)->with('success', __('Payment added Successfully'));
                        }
                        else
                        {
                            return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('success', __('Payment added Successfully'));
                        }

                    }else
                    {
                        if(\Auth::check())
                        {
                             return redirect()->route('invoices.show',$invoice_id)->with('error', __('Transaction has been failed!'));
                        }
                        else
                        {
                            return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('error', __('Transaction has been failed!'));
                        }
                       
                    }
                }
                catch(\Exception $e){

                    if(\Auth::check())
                        {
                             return redirect()->route('invoices.show',$invoice_id)->with('error', __('Transaction has been failed!'));
                        }
                        else
                        {
                            return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('error', __('Transaction has been failed!'));
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
        // if(\Auth::user()->type == 'Owner')
        // {
        $admin_payment_setting = Utility::payment_settings();
        $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        config(
            [
                'services.paytm-wallet.env' => isset($admin_payment_setting['paytm_mode'])?$admin_payment_setting['paytm_mode']:'',
                'services.paytm-wallet.merchant_id' => isset($admin_payment_setting['paytm_merchant_id'])?$admin_payment_setting['paytm_merchant_id']:'',
                'services.paytm-wallet.merchant_key' =>  isset($admin_payment_setting['paytm_merchant_key'])?$admin_payment_setting['paytm_merchant_key']:'',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' =>isset($admin_payment_setting['paytm_industry_type'])?$admin_payment_setting['paytm_industry_type']:'',
            ]
        );
        }
    // }
}