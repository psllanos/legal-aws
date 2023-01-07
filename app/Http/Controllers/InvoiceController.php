<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceProduct;
use App\Exports\InvoiceExport;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Stripe;

class InvoiceController extends Controller
{
    public $currancy;
    public $stripe_secret;

    public function __construct()
    {
        $this->middleware(
            [
                'XSS',
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Invoices'))
        {
            if(\Auth::user()->type == 'Client')
            {
                $invoices    = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->get();
                $curr_month  = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereMonth('invoices.issue_date', '=', date('m'))->get();
                $curr_week   = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereBetween(
                    'invoices.issue_date', [
                                             \Carbon\Carbon::now()->startOfWeek(),
                                             \Carbon\Carbon::now()->endOfWeek(),
                                         ]
                )->get();
                $last_30days = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereDate('invoices.issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }
            else
            {
                $invoices    = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->get();
                $curr_month  = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereMonth('invoices.issue_date', '=', date('m'))->get();
                $curr_week   = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereBetween(
                    'invoices.issue_date', [
                                             \Carbon\Carbon::now()->startOfWeek(),
                                             \Carbon\Carbon::now()->endOfWeek(),
                                         ]
                )->get();
                $last_30days = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereDate('invoices.issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }

            // Invoice Summary
            $cnt_invoice                = [];
            $cnt_invoice['total']       = \App\Models\Invoice::getInvoiceSummary($invoices);
            $cnt_invoice['this_month']  = \App\Models\Invoice::getInvoiceSummary($curr_month);
            $cnt_invoice['this_week']   = \App\Models\Invoice::getInvoiceSummary($curr_week);
            $cnt_invoice['last_30days'] = \App\Models\Invoice::getInvoiceSummary($last_30days);


            return view('invoices.index', compact('invoices', 'cnt_invoice'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('Create Invoice'))
        {
            $taxes = Tax::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
            $deals = \Auth::user()->deals->pluck('name', 'id');

            return view('invoices.create', compact('deals', 'taxes'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('Create Invoice'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'deal_id' => 'required',
                                   'issue_date' => 'required|date',
                                   'due_date' => 'required|date',
                                   'tax_id' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('invoices.index')->with('error', $messages->first());
            }

            $invoice             = new Invoice();
            $invoice->invoice_id = $this->invoiceNumber();
            $invoice->deal_id    = $request->deal_id;
            $invoice->status     = 0;
            $invoice->issue_date = $request->issue_date;
            $invoice->due_date   = $request->due_date;
            $invoice->discount   = 0;
            $invoice->tax_id     = $request->tax_id;
            $invoice->terms      = $request->terms;
            $invoice->created_by = \Auth::user()->ownerId();
            $invoice->save();

            ActivityLog::create(
                [
                    'user_id' => \Auth::user()->id,
                    'deal_id' => $request->deal_id,
                    'log_type' => 'Create Invoice',
                    'remark' => sprintf(__('%s Create new invoice "%s"'), \Auth::user()->name, \Auth::user()->invoiceNumberFormat($invoice->invoice_id)),
                ]
            );

            $settings  = Utility::settings(\Auth::user()->ownerId());

            if(isset($settings['invoice_notification']) && $settings['invoice_notification'] ==1){
                $msg = 'New  Invoice '.Auth::user()->invoiceNumberFormat($this->invoiceNumber()).'  created by  '.\Auth::user()->name.'.';

                \Utility::send_slack_msg($msg);
            }

             if(isset($settings['telegram_invoice_notification']) && $settings['telegram_invoice_notification'] ==1){
                $resp = 'New  Invoice '.Auth::user()->invoiceNumberFormat($this->invoiceNumber()).'  created by  '.\Auth::user()->name.'.';
                \Utility::send_telegram_msg($resp);
            }

            return redirect()->route('invoices.show', $invoice->id)->with('success', __('Invoice successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function invoiceNumber()
    {
        $latest = Invoice::where('created_by', '=', \Auth::user()->ownerId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        if(\Auth::user()->can('View Invoice'))
        {
            if($invoice->created_by == \Auth::user()->ownerId())
            {
                $settings = Utility::settings();
                $payment_setting = Utility::payment_settings();
                $client   = $invoice->deal->clients->first();

                return view('invoices.show', compact('invoice', 'settings', 'client','payment_setting'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        if(\Auth::user()->can('Edit Invoice'))
        {
            if($invoice->created_by == \Auth::user()->ownerId())
            {
                $taxes = Tax::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $deals = \Auth::user()->deals->pluck('name', 'id');

                return view('invoices.edit', compact('invoice', 'deals', 'taxes'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        if(\Auth::user()->can('Edit Invoice'))
        {
            if($invoice->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'deal_id' => 'required',
                                       'status' => 'required',
                                       'issue_date' => 'required|date',
                                       'due_date' => 'required|date',
                                       'tax_id' => 'required',
                                       'discount' => 'required|min:0',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoices.index')->with('error', $messages->first());
                }

                if($invoice->status!=$request->status)
                {
                    $status=1;
                }
                else
                {
                    $status=0;
                }


                $invoice->deal_id    = $request->deal_id;
                $invoice->status     = $request->status;
                $invoice->issue_date = $request->issue_date;
                $invoice->due_date   = $request->due_date;
                $invoice->tax_id     = $request->tax_id;
                $invoice->terms      = $request->terms;
                $invoice->discount   = $request->discount;
                $invoice->save();

            $settings  = Utility::settings(\Auth::user()->ownerId());

          if($status==1)
            {
                if(isset($settings['invoice_status_update_notification']) && $settings['invoice_status_update_notification'] ==1){
                    $msg = 'Invoice '.Auth::user()->invoiceNumberFormat($this->invoiceNumber()).' status successfully updated by '.\Auth::user()->name.'.';

                    \Utility::send_slack_msg($msg);
                }

                if(isset($settings['telegram_invoice_status_update_notification']) && $settings['telegram_invoice_status_update_notification'] ==1){
                    $resp = 'Invoice '.Auth::user()->invoiceNumberFormat($this->invoiceNumber()).' status successfully updated by '.\Auth::user()->name.'.';
                    \Utility::send_telegram_msg($resp);
                }

            }



                return redirect()->back()->with('success', __('Invoice successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        if(\Auth::user()->can('Delete Invoice'))
        {
            if($invoice->created_by == \Auth::user()->ownerId())
            {
                $invoice->delete();

                return redirect()->route('invoices.index')->with('success', __('Invoice successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function productAdd($id)
    {
        if(\Auth::user()->can('Invoice Add Product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == \Auth::user()->ownerId())
            {
                $products = Product::where('created_by', '=', \Auth::user()->ownerId())->whereIn('id', explode(',', $invoice->deal->products))->get()->pluck('name', 'id');


                $products->prepend(__('Select Products'), '');

                return view('invoices.products', compact('invoice', 'products'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function productStore($id, Request $request)
    {
        if(\Auth::user()->can('Invoice Add Product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'product_id' => 'required',
                                       'description' => 'required',
                                       'quantity' => 'required|numeric|min:1',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoices.show', $invoice->id)->with('error', $messages->first());
                }
                $product = Product::find($request->product_id);

                InvoiceProduct::create(
                    [
                        'invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'price' => $product->price,
                        'quantity' => $request->quantity,
                        'description' => $request->description,
                    ]
                );

                $invoice = Invoice::where('id', $id)->first();
                $due     = $invoice->getDue();
                $total   = $invoice->getTotal();

                if($due == 0)
                {
                    $invoice->status = 0;
                    $invoice->save();
                }
                else
                {
                    $invoice->status = 1;
                    $invoice->save();
                }
                return redirect()->route('invoices.show', $invoice->id)->with('success', __('Product successfully added!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function productEdit($id, $product_id)
    {
        if(\Auth::user()->can('Invoice Edit Product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == \Auth::user()->ownerId())
            {
                $product  = InvoiceProduct::find($product_id);
                $products = Product::where('created_by', '=', \Auth::user()->ownerId())->whereIn('id', explode(',', $invoice->deal->products))->get()->pluck('name', 'id');
                $products->prepend(__('Select Products'), '');

                return view('invoices.products', compact('invoice', 'products', 'product'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function productUpdate($id, $product_id, Request $request)
    {
        if(\Auth::user()->can('Invoice Edit Product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'product_id' => 'required',
                                       'quantity' => 'required|numeric|min:1',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoices.show', $invoice->id)->with('error', $messages->first());
                }
                $product                     = Product::find($request->product_id);
                $invoiceProduct              = InvoiceProduct::find($product_id);
                $invoiceProduct->product_id  = $product->id;
                $invoiceProduct->price       = $product->price;
                $invoiceProduct->quantity    = $request->quantity;
                $invoiceProduct->description = $request->description;
                $invoiceProduct->save();

                return redirect()->route('invoices.show', $invoice->id)->with('success', __('Product successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function productDelete($id, $product_id)
    {
        if(\Auth::user()->can('Invoice Delete Product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == \Auth::user()->ownerId())
            {
                $invoiceProduct = InvoiceProduct::find($product_id);
                $invoiceProduct->delete();

                return redirect()->route('invoices.show', $invoice->id)->with('success', __('Product successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function paymentAdd($id)
    {
        if(\Auth::user()->can('Create Invoice Payment'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == \Auth::user()->ownerId())
            {
                $payment_methods = Payment::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $payment_methods->prepend(__('Select Payment Method'), '');

                return view('invoices.payments', compact('invoice', 'payment_methods'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function paymentStore($id, Request $request)
    {
        if(\Auth::user()->can('Create Invoice Payment'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'amount' => 'required|numeric|min:1',
                                       'date' => 'required',
                                       'notes' => 'required',
                                       'payment_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoices.show', $invoice->id)->with('error', $messages->first());
                }
                $objUser=\Auth::user();

                InvoicePayment::create(
                    [
                        'transaction_id' => $this->transactionNumber($objUser),
                        'invoice_id' => $invoice->id,
                        'amount' => $request->amount,
                        'date' => $request->date,
                        'payment_id' => $request->payment_id,
                        'payment_type' => __('MANUAL'),
                        'notes' => $request->notes,
                    ]
                );
                $invoice = Invoice::where('id', $id)->first();
                $due     = $invoice->getDue();
                $total   = $invoice->getTotal();

                if($due <= 0)
                {
                    $invoice->status = 3;
                    $invoice->save();
                }
                else
                {
                    $invoice->status = 2;
                    $invoice->save();
                }

                return redirect()->route('invoices.show', $invoice->id)->with('success', __('Payment successfully added!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function addPayment($id, Request $request)
    {

        $invoice = Invoice::find($id);

        if(\Auth::check())
        {
             $objUser = \Illuminate\Support\Facades\Auth::user();
        }
        else
        {
            $objUser = User::where('id',$invoice->created_by)->first();
        }

        $settings = Utility::settings();

       if(\Auth::check())
        {
             $user = Auth::user();
             $this->paymentSetting();
        }else{

            $payment_settings = Utility::non_auth_payment_settings($objUser->id);
            $this->currancy =isset($payment_settings['currency'])?$payment_settings['currency']:'';
            $this->stripe_secret = isset($payment_settings['stripe_secret'])?$payment_settings['stripe_secret']:'';
            // $this->currancy_symbol,$this->paypal_client_id,$this->currancy,$this->paypal_mode,$this->paypal_secret_key);
        }

        if($invoice)
        {
            if($request->amount > $invoice->getDue())
            {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            else
            {

                try
                {

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $price   = $request->amount;

                    Stripe\Stripe::setApiKey($this->stripe_secret);

                    $data = Stripe\Charge::create(
                        [
                            "amount" => 100 * $price,
                            "currency" => $this->currancy,
                            "source" => $request->stripeToken,
                            "description" => $settings['company_name'] . " - " . $objUser->invoiceNumberFormat($invoice->invoice_id),
                            "metadata" => ["order_id" => $orderID],
                        ]
                    );

                    if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                    {
                        InvoicePayment::create(
                            [
                                'transaction_id' => $this->transactionNumber($objUser),
                                'invoice_id' => $invoice->id,
                                'amount' => $price,
                                'date' => date('Y-m-d'),
                                'payment_id' => 0,
                                'payment_type' => __('STRIPE'),
                                'client_id' => $objUser->id,
                                'notes' => '',
                            ]
                        );

                        if(($invoice->getDue() - $request->amount) == 0)
                        {
                            $invoice->status = 'paid';
                            $invoice->save();
                        }

                        $settings  = Utility::settings($invoice->created_by);

                        if(isset($settings['payment_notification']) && $settings['payment_notification'] ==1){
                            $msg = ucfirst($objUser->name) .' paid '.$price.'.';
                            \Utility::send_slack_msg($msg);
                        }
                        if(isset($settings['telegram_payment_notification']) && $settings['telegram_payment_notification'] ==1){
                            $resp = ucfirst($objUser->name) .' paid '.$price.'.';
                            \Utility::send_telegram_msg($resp);
                        }

                        if(\Auth::check())
                        {
                             return redirect()->back()->with('success', __(' Payment added Successfully'));
                        }
                        else
                        {
                            return redirect()->route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice->id))->with('success', __('Payment successfully added!'));

                        }


                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Transaction has been failed!'));
                    }

                }
                catch(\Exception $e)
                {
                    if(\Auth::check())
                        {
                             return redirect()->route('invoices.show', $invoice->id)->with('error', __($e->getMessage()));
                        }
                        else
                        {
                            return redirect()->route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice->id))->with('error', __($e->getMessage()));

                        }

                }
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function transactionNumber($objUser)
    {
        $latest = InvoicePayment::select('invoice_payments.*')->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoices.created_by', '=',$objUser->ownerId())->latest()->first();
        if($latest)
        {
            return $latest->transaction_id + 1;
        }

        return 1;
    }

    public function payments()
    {
        if(\Auth::user()->can('Manage Invoice Payments'))
        {
            $usr = \Auth::user();

            if($usr->type == 'Client')
            {
                $payments    = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('client_deals', 'client_deals.deal_id', '=', 'invoices.deal_id')->where('client_deals.client_id', '=', $usr->id)->get();
                $curr_month  = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('client_deals', 'client_deals.deal_id', '=', 'invoices.deal_id')->where('client_deals.client_id', '=', $usr->id)->whereMonth('invoice_payments.date', '=', date('m'))->get();
                $curr_week   = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('client_deals', 'client_deals.deal_id', '=', 'invoices.deal_id')->where('client_deals.client_id', '=', $usr->id)->whereBetween(
                    'invoice_payments.date', [
                                               \Carbon\Carbon::now()->startOfWeek(),
                                               \Carbon\Carbon::now()->endOfWeek(),
                                           ]
                )->get();
                $last_30days = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('client_deals', 'client_deals.deal_id', '=', 'invoices.deal_id')->where('client_deals.client_id', '=', $usr->id)->whereDate('invoice_payments.date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }
            else
            {
                $payments    = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('deals', 'deals.id', '=', 'invoices.deal_id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->get();
                $curr_month  = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('deals', 'deals.id', '=', 'invoices.deal_id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereMonth('invoice_payments.date', '=', date('m'))->get();
                $curr_week   = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('deals', 'deals.id', '=', 'invoices.deal_id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereBetween(
                    'invoice_payments.date', [
                                               \Carbon\Carbon::now()->startOfWeek(),
                                               \Carbon\Carbon::now()->endOfWeek(),
                                           ]
                )->get();
                $last_30days = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('deals', 'deals.id', '=', 'invoices.deal_id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->whereDate('invoice_payments.date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }

            // Payment Summary
            $cnt_payments                = [];
            $cnt_payments['total']       = \App\Models\Invoice::getPaymentSummary($payments);
            $cnt_payments['this_month']  = \App\Models\Invoice::getPaymentSummary($curr_month);
            $cnt_payments['this_week']   = \App\Models\Invoice::getPaymentSummary($curr_week);
            $cnt_payments['last_30days'] = \App\Models\Invoice::getPaymentSummary($last_30days);

            return view('invoices.all-payments', compact('payments', 'cnt_payments'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function printInvoice($id)
    {
        // if(\Auth::user()->can('Manage Invoices'))
        // {
            $invoice  = Invoice::findOrFail($id);
            $settings = Utility::settings();
            $client   = $invoice->deal->clients->first();

            //Set your logo
            // $logo         = asset(\Storage::url('logo/'));
            $logo=\App\Models\Utility::get_file('logo/');
            $dark_logo    = Utility::getValByName('dark_logo');
            $invoice_logo = Utility::getValByName('invoice_logo');
            if(isset($invoice_logo) && !empty($invoice_logo))
            {
                $img = \App\Models\Utility::get_file('/') . $invoice_logo;
            }
            else
            {
                $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
            }

            if($invoice)
            {
                $color      = '#' . $settings['invoice_color'];
                $font_color = Utility::getFontColor($color);

                return view('invoices.templates.' . $settings['invoice_template'], compact('invoice', 'color', 'settings', 'client', 'img', 'font_color'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission Denied.'));
        // }
    }

    public function previewInvoice($template, $color)
    {
        $settings   = Utility::settings();
        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $invoice       = new Invoice();
        $deal          = new \stdClass();
        $client        = new \stdClass();
        $tax           = new \stdClass();
        $deal->name    = 'Test Deal';
        $client->name  = 'Client';
        $client->email = 'client@example.com';
        $tax->name     = 'GST';
        $tax->rate     = 10;

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $price           = new \stdClass();
            $price->price    = 100;
            $price->quantity = $i;

            $item       = new \stdClass();
            $item->name = 'Product ' . $i;;
            $item->pivot = $price;
            $items[]     = $item;
        }

        $invoice->invoice_id  = 1;
        $invoice->issue_date  = date('Y-m-d H:i:s');
        $invoice->due_date    = date('Y-m-d H:i:s');
        $invoice->deal        = $deal;
        $invoice->discount    = 50;
        $invoice->getProducts = $items;
        $invoice->tax         = $tax;

        //Set your logo
        // $logo         = asset(\Storage::url('logo/'));
        $logo=\App\Models\Utility::get_file('logo/');

        $dark_logo    = Utility::getValByName('dark_logo');
        $invoice_logo = Utility::getValByName('invoice_logo');
        if(isset($invoice_logo) && !empty($invoice_logo))
        {
            $img = \App\Models\Utility::get_file('/') . $invoice_logo;
        }
        else
        {
            $img = asset($logo . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));

        }

        return view('invoices.templates.' . $template, compact('invoice', 'preview', 'color', 'settings', 'client', 'img', 'font_color'));
    }

    public function paymentSetting()
    {
        $payment_settings = Utility::payment_settings();
        $this->currancy =isset($payment_settings['currency'])?$payment_settings['currency']:'';
        $this->stripe_secret = isset($payment_settings['stripe_secret'])?$payment_settings['stripe_secret']:'';

        return;
    }

     public function payinvoice($invoice_id){

        if(!empty($invoice_id)){

            try {
				$id  = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
			} catch(\RuntimeException $e) {
			   return redirect()->back()->with('error',__('Invoice not avaliable'));
			}
            // $id = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);


            $invoice = Invoice::where('id',$id)->first();

            if(!is_null($invoice)){

                $settings = Utility::settings();

                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $productname = 0;
                $taxesData     = [];

                foreach($invoice->itemsdata as $item)
                {
                    $productname =$item->name;
                    $totalQuantity += $item->quantity;
                    $totalRate     += $item->price;
                  //  $totalDiscount += $item->discount;
                    $taxes         = Utility::tax($item->tax);

                    $itemTaxes = [];

                     foreach($taxes as $tax) {
                    if(!empty($tax)) { $taxPrice            =
                    Utility::taxRate($tax->rate, $item->price,
                    $item->quantity); $totalTaxPrice       += $taxPrice;
                    $itemTax['tax_name'] = $tax->tax_name;



                     $itemTax['tax'] = $tax->tax. '%'; $itemTax['price']    =  $taxPrice; $itemTaxes[]
                        = $itemTax;

                            if(array_key_exists($tax->name, $taxesData))
                            {

                                $taxesData[$itemTax['tax_name']] = $taxesData[$tax->tax_name] + $taxPrice;
                            }
                            else
                            {
                                $taxesData[$tax->tax_name] = $taxPrice;
                            }
                        }
                        else
                        {


                            $taxPrice            = Utility::taxRate(0, $item->price);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = 'No Tax';
                            $itemTax['tax']      = '';
                            $itemTax['price']    = $taxPrice;
                            $itemTaxes[]         = $itemTax;


                            if(array_key_exists('No Tax', $taxesData))
                            {


                                $taxesData[$tax->tax_name] = $taxesData['No Tax'] + $taxPrice;
                            }
                            else
                            {
                                $taxesData['No Tax'] = $taxPrice;
                            }

                        }
                    }
                    $item->itemTax = $itemTaxes;
                    $items[]       = $item;
                }
                $invoice->items         = $items;
                $invoice->totalTaxPrice = $totalTaxPrice;
                $invoice->totalQuantity = $totalQuantity;
                $invoice->totalRate     = $totalRate;

                $invoice->taxesData     = $taxesData;

                $company_setting = Utility::settings();
                $client   = $invoice->deal->clients->first();

                $ownerId = Utility::ownerIdforInvoice($invoice->created_by);

                $payment_setting = Utility::invoice_payment_settings($ownerId);

                $users = User::where('id',$invoice->created_by)->first();

                if(!is_null($users)){
                    \App::setLocale($users->lang);
                }else{
                    $users = User::where('type','owner')->first();
                    \App::setLocale($users->lang);
                }


                return view('invoices.invoicepay',compact('invoice', 'company_setting','users','payment_setting','client'));
            }else{
                return abort('404', 'The Link You Followed Has Expired');
            }
        }else{
            return abort('404', 'The Link You Followed Has Expired');
        }
    }

     public function pdffrominvoice($invoice_id)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);

         $invoice  = Invoice::findOrFail($id);

            $settings = Utility::settings();
            $client   = $invoice->deal->clients->first();

            //Set your logo
            $logo=\App\Models\Utility::get_file('logo/');
            $dark_logo    = Utility::getValByName('dark_logo');
            $invoice_logo = Utility::getValByName('invoice_logo');
            if(isset($invoice_logo) && !empty($invoice_logo))
            {
                $img = \App\Models\Utility::get_file('/'). $invoice_logo;
            }
            else
            {
                $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
            }

            if(\Auth::check())
            {
                $usr=\Auth::user();
            }
            else
            {

                $usr=User::where('id',$invoice->created_by)->first();

            }

            if($invoice)
            {
                $color      = '#' . $settings['invoice_color'];
                $font_color = Utility::getFontColor($color);

                return view('invoices.templates.' . $settings['invoice_template'], compact('invoice', 'color', 'settings', 'client', 'img', 'font_color','usr'));

            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }

     public function fileExport()
    {

        $name = 'invoice_' . date('Y-m-d i:h:s');
        $data = Excel::download(new InvoiceExport(), $name . '.xlsx');  ob_end_clean();


        return $data;
    }
}
