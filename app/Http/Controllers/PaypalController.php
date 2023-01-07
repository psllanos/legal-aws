<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductVariantOption;
use App\Models\Retainer;
use App\Models\RetainerPayment;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\User;
use App\Models\UserCoupon;  
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\PurchasedProducts;

class PaypalController extends Controller
{
    public $paypal_client_id;
    public $paypal_mode;
    public $paypal_secret_key;
    public $currancy_symbol;
    public $currancy;

    public function paymentConfig()
    {
        if(\Auth::check())
        {
            $payment_setting = Utility::payment_settings();    
        }
        else
        {
            $payment_setting = Utility::set_payment_settings($this->invoiceData->created_by);
        }

        config(
            [
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]
        );    
    }

    public function paypalCreate(Request $request, $plan)
    {
        // dd($request->all(),$plan);
        
        $this->paymentConfig();

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->code);
        $plan = Plan::find($planID);
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $payment_frequency = $request->payment_frequency.'_price';
        $get_amount = $plan->$payment_frequency;       
        if ($plan) {
            try
            {
                // dd('fdgfgh');
                $coupon_id = null;
                $price = $plan->$payment_frequency;
                // dd($plan->$payment_frequency);  
                
                if(!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun = $coupons->used_coupon();
                        $discount_value = ($plan->payment_frequency / 100) * $coupons->discount;
                        $price = $plan->payment_frequency - $discount_value;
                   
                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                        $coupon_id = $coupons->id;
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }
                // $this->paymentConfig();
                $paypalToken = $provider->getAccessToken();
               
                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('plan.get.payment.status', [$plan->id, $get_amount]),
                        "cancel_url" => route('plan.get.payment.status', [$plan->id, $get_amount]),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => Utility::getValByName('site_currency'),
                                "value" => $get_amount,
                            ],
                        ],
                    ],
                ]);
              
                if (isset($response['id']) && $response['id'] != null) {

                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                        
                            return redirect()->away($links['href']);
                        }
                    }
                   
                    return redirect()
                        ->route('plans.index')
                        ->with('error', 'Something went wrong.');
                } else {
                   
                    return redirect()
                        ->route('plans.index')
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }
            } catch (\Exception $e) {
                // dd($e);
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPaymentStatus(Request $request, $plan_id, $amount)
    {
        // dd($request->all());
        $this->paymentConfig();
        $user = Auth::user();
        $plan = Plan::find($plan_id);

        if ($plan) {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $payment_id = Session::get('paypal_payment_id');
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

            // $status  = ucwords(str_replace('_', ' ', $result['state']));
            if ($request->has('coupon_id') && $request->coupon_id != '') {
                $coupons = Coupon::find($request->coupon_id);
                if (!empty($coupons)) {
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = $user->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $order_id;
                    $userCoupon->save();
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }
            }
            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                if ($response['status'] == 'COMPLETED') {
                    $statuses = 'success';
                }
                $order = new Order();
                $order->order_id = $order_id;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $amount;
                $order->price_currency = env('CURRENCY');
                $order->txn_id = $payment_id;
                $order->payment_type = __('PAYPAL');
                $order->payment_status = $statuses;
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                $assignPlan = $user->assignPlan($plan->id);
                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }

                return redirect()
                    ->route('plans.index')
                    ->with('success', 'Transaction complete.');
            } else {
                return redirect()
                    ->route('plans.index')
                    ->with('error', $response['message'] ?? 'Something went wrong.');
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function clientPayWithPaypal(Request $request, $invoice_id)
    {
        $this->paymentConfig();
        // dd($request);
        $invoice = Invoice::find($invoice_id);
        if (Auth::check()) {
            $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
            $user = \Auth::user();
            // dd($user);

        } else {
            $user = User::where('id', $invoice->created_by)->first();
            $settings = Utility::settingById($invoice->created_by);
        }

        $get_amount = $request->amount;

        $request->validate(['amount' => 'required|numeric|min:0']);

        $provider = new PayPalClient;
        // dd($provider);

        $provider->setApiCredentials(config('paypal'));

        if ($invoice) {

            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
               
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                // $name = User::invoiceNumberFormat($settings, $invoice->invoice_id);
                $name = Auth::user()->invoiceNumberFormat($invoice->invoice_id);
                

                $paypalToken = $provider->getAccessToken();
                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('client.get.payment.status', [$invoice->id, $get_amount]),
                        "cancel_url" => route('client.get.payment.status', [$invoice->id, $get_amount]),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => Utility::getValByName('site_currency'),
                                "value" => $get_amount,
                            ],
                        ],
                    ],
                ]);

                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            return redirect()->away($links['href']);
                        }
                    }
                    return redirect()
                        ->route('invoices.show', \Crypt::encrypt($invoice->id))
                        ->with('error', 'Something went wrong.');
                } else {
                    return redirect()
                        ->route('invoices.show', \Crypt::encrypt($invoice->id))
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }

                return redirect()->route('invoices.show', \Crypt::encrypt($invoice_id))->back()->with('error', __('Unknown error occurred'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function clientGetPaymentStatus(Request $request, $invoice_id, $amount)
    {
        $this->paymentConfig();
        $invoice = Invoice::find($invoice_id);
        
        if (\Auth::check()) {
            $user = \Auth::user();
        } else {
            $user = User::where('id',$invoice->created_by)->first();
        }
        $payment_id = Session::get('paypal_payment_id');
        Session::forget('paypal_payment_id');

        if (empty($request->PayerID || empty($request->token))) {
            return redirect()->back()->with('error', __('Payment failed'));
        }
        $provider = new PayPalClient;
        $response = $provider->showAuthorizedPaymentDetails($request->PayerID);

        try {
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
            if($order_id)
                {
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user);
                    $invoice_payment->invoice_id     = $invoice->id;
                    $invoice_payment->amount         = $amount;
                    $invoice_payment->date           = date('Y-m-d');
                    $invoice_payment->payment_id     = 0;
                    $invoice_payment->payment_type   = __('PAYPAL');
                    $invoice_payment->client_id      = $user->id;
                    $invoice_payment->notes          = '';
                    $invoice_payment->save();

                    if(($invoice->getDue() - $invoice_payment->amount) == 0)
                    {
                        $invoice->status = 'paid';
                        $invoice->save();
                    }

                    $settings  = Utility::settings($invoice->created_by);
                    if(isset($settings['payment_notification']) && $settings['payment_notification'] ==1){
                        $msg = ucfirst($user->name) .' paid '.$result['transactions'][0]['amount']['total'].'.'; 
                       
                        Utility::send_slack_msg($msg);
                    }
                    if(isset($settings['telegram_payment_notification']) && $settings['telegram_payment_notification'] ==1){
                            $resp = ucfirst($user->name) .' paid '.$result['transactions'][0]['amount']['total'].'.';
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
                   
                }
                else
                {
                    if(\Auth::check())
                    {
                         return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction has been ' . $status));
                    }
                    else
                    {
                        return redirect()->route('pay.invoice',\Crypt::encrypt($invoice_id))->with('error', __('Transaction has been ' . $status));
                    }
                   
                }
            
        } catch (\Exception$e) {
            if (Auth::check()) {
                return redirect()->route('pay.invoice', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
            } else {
                return redirect()->back()->with('success', __('Transaction has been complted.'));
            }
        }
    }

}
