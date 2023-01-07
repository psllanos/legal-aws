<?php

namespace App\Http\Controllers;

use App\Models\Estimation;
use App\Models\EstimationProduct;
use App\Models\Product;
use App\Exports\EstimationExport;
use App\Models\Tax;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EstimationController extends Controller
{
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
        if(\Auth::user()->can('Manage Estimations'))
        {
            if(\Auth::user()->type == 'Client')
            {
                $estimations = Estimation::where('client_id', '=', \Auth::user()->id)->where('created_by', '=', \Auth::user()->ownerId())->get();
                $curr_month  = Estimation::where('client_id', '=', \Auth::user()->id)->where('created_by', '=', \Auth::user()->ownerId())->whereMonth('issue_date', '=', date('m'))->get();
                $curr_week   = Estimation::where('client_id', '=', \Auth::user()->id)->where('created_by', '=', \Auth::user()->ownerId())->whereBetween(
                    'issue_date', [
                                    \Carbon\Carbon::now()->startOfWeek(),
                                    \Carbon\Carbon::now()->endOfWeek(),
                                ]
                )->get();
                $last_30days = Estimation::where('client_id', '=', \Auth::user()->id)->where('created_by', '=', \Auth::user()->ownerId())->whereDate('issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }
            else
            {
                $estimations = Estimation::where('created_by', '=', \Auth::user()->ownerId())->get();
                $curr_month  = Estimation::where('created_by', '=', \Auth::user()->ownerId())->whereMonth('issue_date', '=', date('m'))->get();
                $curr_week   = Estimation::where('created_by', '=', \Auth::user()->ownerId())->whereBetween(
                    'issue_date', [
                                    \Carbon\Carbon::now()->startOfWeek(),
                                    \Carbon\Carbon::now()->endOfWeek(),
                                ]
                )->get();
                $last_30days = Estimation::where('created_by', '=', \Auth::user()->ownerId())->whereDate('issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }

            // Estimation Summary
            $cnt_estimation                = [];
            $cnt_estimation['total']       = Estimation::getEstimationSummary($estimations);
            $cnt_estimation['this_month']  = Estimation::getEstimationSummary($curr_month);
            $cnt_estimation['this_week']   = Estimation::getEstimationSummary($curr_week);
            $cnt_estimation['last_30days'] = Estimation::getEstimationSummary($last_30days);

            return view('estimations.index', compact('estimations', 'cnt_estimation'));
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
        if(\Auth::user()->can('Create Estimation'))
        {
            $taxes  = Tax::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
            $client = User::where('type', '=', 'Client')->where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');

            return view('estimations.create', compact('client', 'taxes'));
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
        $usr = \Auth::user();

        if($usr->can('Create Estimation'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'client_id' => 'required',
                                   'issue_date' => 'required|date',
                                   'tax_id' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('estimations.index')->with('error', $messages->first());
            }

            $estimation                = new Estimation();
            $estimation->estimation_id = $this->estimationNumber();
            $estimation->client_id     = $request->client_id;
            $estimation->status        = 0;
            $estimation->issue_date    = $request->issue_date;
            $estimation->discount      = 0;
            $estimation->tax_id        = $request->tax_id;
            $estimation->terms         = $request->terms;
            $estimation->created_by    = \Auth::user()->ownerId();
            $estimation->save();

            $estimationArr = [
                'estimation_id' => $estimation->id,
                'estimation_name' => $usr->estimateNumberFormat($estimation->estimation_id),
                'updated_by' => $usr->id,
            ];

            $client = User::find($request->client_id);

            $estArr = [
                'estimation_name' => $usr->estimateNumberFormat($estimation->estimation_id),
                'estimation_client' => $client->name,
                'estimation_status' => Estimation::$statues[$estimation->status],
            ];

            Utility::sendNotification('assign_estimation', $request->client_id, $estimationArr);

            // Send Email
            $resp = Utility::sendEmailTemplate('Assign Estimation', [$client->id => $client->email], $estArr);

            $settings  = Utility::settings(\Auth::user()->ownerId());

            if(isset($settings['estimate_notification']) && $settings['estimate_notification'] ==1){

                $msg = 'New Estimation created by the '.\Auth::user()->name.'.';

                \Utility::send_slack_msg($msg);
            }
             if(isset($settings['telegram_estimate_notification']) && $settings['telegram_estimate_notification'] ==1){
                $resps = 'New Estimation created by the '.\Auth::user()->name.'.';
                \Utility::send_telegram_msg($resps);
            }

            return redirect()->route('estimations.show', $estimation->id)->with('success', __('Estimation successfully created!') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function estimationNumber()
    {
        $latest = Estimation::where('created_by', '=', \Auth::user()->ownerId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->estimation_id + 1;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Estimation $estimation
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Estimation $estimation)
    {
        if(\Auth::user()->can('View Estimation'))
        {
            if($estimation->created_by == \Auth::user()->ownerId())
            {
                $settings = Utility::settings();
                $client   = $estimation->client;

                return view('estimations.show', compact('estimation', 'settings', 'client'));
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
     * @param \App\Estimation $estimation
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Estimation $estimation)
    {
        if(\Auth::user()->can('Edit Estimation'))
        {
            if($estimation->created_by == \Auth::user()->ownerId())
            {
                $taxes  = Tax::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $client = User::where('type', '=', 'Client')->where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');

                return view('estimations.edit', compact('estimation', 'client', 'taxes'));
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
     * @param \App\Estimation $estimation
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Estimation $estimation)
    {
        if(\Auth::user()->can('Edit Estimation'))
        {
            if($estimation->created_by == \Auth::user()->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'client_id' => 'required',
                                       'status' => 'required',
                                       'issue_date' => 'required|date',
                                       'tax_id' => 'required',
                                       'discount' => 'required|min:0',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('estimations.index')->with('error', $messages->first());
                }

                $estimation->client_id  = $request->client_id;
                $estimation->status     = $request->status;
                $estimation->issue_date = $request->issue_date;
                $estimation->tax_id     = $request->tax_id;
                $estimation->terms      = $request->terms;
                $estimation->discount   = $request->discount;
                $estimation->save();

                return redirect()->back()->with('success', __('Estimation successfully updated!'));
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
     * @param \App\Estimation $estimation
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estimation $estimation)
    {
        if(\Auth::user()->can('Delete Estimation'))
        {
            if($estimation->created_by == \Auth::user()->ownerId())
            {
                $estimation->delete();

                return redirect()->route('estimations.index')->with('success', __('Estimation successfully deleted!'));
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
        if(\Auth::user()->can('Estimation Add Product'))
        {
            $estimation = Estimation::find($id);
            if($estimation->created_by == \Auth::user()->ownerId())
            {
                $products = Product::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $products->prepend(__('Select Products'), '');

                return view('estimations.products', compact('estimation', 'products'));
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
        if(\Auth::user()->can('Estimation Add Product'))
        {
            $estimation = Estimation::find($id);
            if($estimation->created_by == \Auth::user()->ownerId())
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

                    return redirect()->route('estimations.show', $estimation->id)->with('error', $messages->first());
                }

                $product = Product::find($request->product_id);
                EstimationProduct::create(
                    [
                        'estimation_id' => $estimation->id,
                        'product_id' => $product->id,
                        'price' => $product->price,
                        'quantity' => $request->quantity,
                        'description' => $request->description,
                    ]
                );

                return redirect()->route('estimations.show', $estimation->id)->with('success', __('Product successfully added!'));
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
        if(\Auth::user()->can('Estimation Edit Product'))
        {
            $estimation = Estimation::find($id);
            if($estimation->created_by == \Auth::user()->ownerId())
            {
                $product  = EstimationProduct::find($product_id);
                $products = Product::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $products->prepend(__('Select Products'), '');

                return view('estimations.products', compact('estimation', 'products', 'product'));
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
        if(\Auth::user()->can('Estimation Edit Product'))
        {
            $estimation = Estimation::find($id);
            if($estimation->created_by == \Auth::user()->ownerId())
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

                    return redirect()->route('estimations.show', $estimation->id)->with('error', $messages->first());
                }

                $product                        = Product::find($request->product_id);
                $estimationProduct              = EstimationProduct::find($product_id);
                $estimationProduct->product_id  = $product->id;
                $estimationProduct->price       = $product->price;
                $estimationProduct->quantity    = $request->quantity;
                $estimationProduct->description = $request->description;
                $estimationProduct->save();

                return redirect()->route('estimations.show', $estimation->id)->with('success', __('Product successfully updated!'));
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
        if(\Auth::user()->can('Estimation Delete Product'))
        {
            $estimation = Estimation::find($id);

            if($estimation->created_by == \Auth::user()->ownerId())
            {
                $estimationProduct = EstimationProduct::find($product_id);
                $estimationProduct->delete();

                return redirect()->route('estimations.show', $estimation->id)->with('success', __('Product successfully deleted!'));
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

    public function printEstimation($id)
    {
        if(\Auth::user()->can('Manage Estimations'))
        {
            $estimation = Estimation::findOrFail($id);
            $settings   = Utility::settings();
            $client     = User::where('id', '=', $estimation->client_id)->where('type', '=', 'Client')->first();

            //Set your logo
            // $logo            = asset(\Storage::url('logo/'));
            $logo=\App\Models\Utility::get_file('logo/');

            $dark_logo    = Utility::getValByName('dark_logo');

            $estimation_logo = Utility::getValByName('estimation_logo');
            if(isset($estimation_logo) && !empty($estimation_logo))
            {
                $img = \App\Models\Utility::get_file('/') . $estimation_logo;
            }
            else
            {
                $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
            }

            if($estimation)
            {
                $color      = '#' . $settings['estimation_color'];
                $font_color = Utility::getFontColor($color);

                return view('estimations.templates.' . $settings['estimation_template'], compact('estimation', 'color', 'settings', 'client', 'img', 'font_color'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function previewEstimation($template, $color)
    {
        $settings   = Utility::settings();
        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $estimation    = new Estimation();
        $client        = new \stdClass();
        $tax           = new \stdClass();
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

        $estimation->estimation_id = 1;
        $estimation->issue_date    = date('Y-m-d H:i:s');
        $estimation->discount      = 50;
        $estimation->getProducts   = $items;
        $estimation->tax           = $tax;

        //Set your logo
        // $logo            = asset(\Storage::url('logo/'));
        $logo=\App\Models\Utility::get_file('logo/');

        $dark_logo    = Utility::getValByName('dark_logo');
        $estimation_logo = Utility::getValByName('estimation_logo');
        if(isset($estimation_logo) && !empty($estimation_logo))
        {
            $img = \App\Models\Utility::get_file('/'). $estimation_logo;
        }
        else
        {
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));

        }

        return view('estimations.templates.' . $template, compact('estimation', 'preview', 'color', 'settings', 'client', 'img', 'font_color'));
    }

    public function payestimation($estimation_id){

        if(!empty($estimation_id)){

            try {
                $id  = \Illuminate\Support\Facades\Crypt::decrypt($estimation_id);
            } catch(\RuntimeException $e) {
               return redirect()->back()->with('error',__('Blog not avaliable'));
            }
            // $id = \Illuminate\Support\Facades\Crypt::decrypt($estimation_id);

            $estimation = Estimation::where('id',$id)->first();

            if(!is_null($estimation)){

                $settings = Utility::settings();

                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $productname = 0;
                $taxesData     = [];

                foreach($estimation->itemsdata as $item)
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
                $estimation->items         = $items;
                $estimation->totalTaxPrice = $totalTaxPrice;
                $estimation->totalQuantity = $totalQuantity;
                $estimation->totalRate     = $totalRate;

                $estimation->taxesData     = $taxesData;

                $company_setting = Utility::settings();
                $client   = $estimation->client;

                $ownerId = Utility::ownerIdforInvoice($estimation->created_by);

                $payment_setting = Utility::invoice_payment_settings($ownerId);

                $users = User::where('id',$estimation->created_by)->first();

                if(!is_null($users)){
                    \App::setLocale($users->lang);
                }else{
                    $users = User::where('type','owner')->first();
                    \App::setLocale($users->lang);
                }


                return view('estimations.estimationpay',compact('estimation', 'company_setting','users','payment_setting','client'));
            }else{
                return abort('404', 'The Link You Followed Has Expired');
            }
        }else{
            return abort('404', 'The Link You Followed Has Expired');
        }
    }


        public function pdffromestimation($estimation_id)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($estimation_id);

            $estimation = Estimation::findOrFail($id);
            $settings   = Utility::settings();
            $client     = User::where('id', '=', $estimation->client_id)->where('type', '=', 'Client')->first();

            //Set your logo
            $logo=\App\Models\Utility::get_file('logo/');
           $dark_logo    = Utility::getValByName('dark_logo');
            $estimation_logo = Utility::getValByName('estimation_logo');
            if(isset($estimation_logo) && !empty($estimation_logo))
            {
                $img = \App\Models\Utility::get_file('/'). $estimation_logo;
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

                $usr=User::where('id',$estimation->created_by)->first();

            }

            if($estimation)
            {
                $color      = '#' . $settings['estimation_color'];
                $font_color = Utility::getFontColor($color);

                return view('estimations.templates.' . $settings['estimation_template'], compact('estimation', 'color', 'settings', 'client', 'img', 'font_color','usr'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }

    }

     public function fileExport()
    {

        $name = 'estimation_' . date('Y-m-d i:h:s');
        $data = \Excel::download(new EstimationExport(), $name . '.xlsx');  ob_end_clean();


        return $data;
    }
}

