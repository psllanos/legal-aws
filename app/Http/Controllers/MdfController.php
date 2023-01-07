<?php

namespace App\Http\Controllers;

use App\Models\Mdf;
use App\Models\MdfFund;
use App\Models\MdfProduct;
use App\Models\MdfStatus;
use App\Models\MdfType;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MdfController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usr = Auth::user();
        if($usr->can('Manage MDFs'))
        {
            if($usr->type == 'Owner')
            {
                $users       = User::where('type', '!=', 'Client')->where('created_by', '=', $usr->ownerId())->get()->pluck('id')->toArray();
                $mdfs        = Mdf::whereIn('user_id', $users)->get();
                $curr_month  = Mdf::whereIn('user_id', $users)->whereMonth('date', '=', date('m'))->get();
                $curr_week   = Mdf::whereIn('user_id', $users)->whereBetween(
                    'date', [
                              \Carbon\Carbon::now()->startOfWeek(),
                              \Carbon\Carbon::now()->endOfWeek(),
                          ]
                )->get();
                $last_30days = Mdf::whereIn('user_id', $users)->whereDate('date', '>', \Carbon\Carbon::now()->subDays(30))->get();

                $mdf_ids = $mdfs->pluck('id')->toArray();

                $total_approved_amt = MdfFund::whereIn('mdf_id', $mdf_ids)->where('type', 'LIKE', 'approved')->get();
                $total_fund_amt     = MdfFund::whereIn('mdf_id', $mdf_ids)->where('type', 'LIKE', 'fund')->get();
            }
            else
            {
                $mdfs        = Mdf::where('user_id', '=', $usr->id)->get();
                $curr_month  = Mdf::where('user_id', '=', $usr->id)->whereMonth('date', '=', date('m'))->get();
                $curr_week   = Mdf::where('user_id', '=', $usr->id)->whereBetween(
                    'date', [
                              \Carbon\Carbon::now()->startOfWeek(),
                              \Carbon\Carbon::now()->endOfWeek(),
                          ]
                )->get();
                $last_30days = Mdf::where('user_id', '=', $usr->id)->whereDate('date', '>', \Carbon\Carbon::now()->subDays(30))->get();

                $mdf_ids = $mdfs->pluck('id')->toArray();

                $total_approved_amt = MdfFund::whereIn('mdf_id', $mdf_ids)->where('type', 'LIKE', 'approved')->get();
                $total_fund_amt     = MdfFund::whereIn('mdf_id', $mdf_ids)->where('type', 'LIKE', 'fund')->get();
            }

            // MDF Summary
            $cnt_mdf                 = [];
            $cnt_mdf['total']        = \App\Models\Mdf::getMdfSummary($mdfs, true);
            $cnt_mdf['this_month']   = \App\Models\Mdf::getMdfSummary($curr_month);
            $cnt_mdf['this_week']    = \App\Models\Mdf::getMdfSummary($curr_week);
            $cnt_mdf['last_30days']  = \App\Models\Mdf::getMdfSummary($last_30days);
            $cnt_mdf['approved_amt'] = \App\Models\Mdf::getMdfSummary($total_approved_amt, true);
            $cnt_mdf['fund_amt']     = \App\Models\Mdf::getMdfSummary($total_fund_amt, true);
            $cnt_mdf['pending_amt']  = $cnt_mdf['total'] - ($cnt_mdf['approved_amt'] + $cnt_mdf['fund_amt']);

            $cnt_mdf['total']        = $usr->priceFormat($cnt_mdf['total']);
            $cnt_mdf['approved_amt'] = $usr->priceFormat($cnt_mdf['approved_amt']);
            $cnt_mdf['fund_amt']     = $usr->priceFormat($cnt_mdf['fund_amt']);
            $cnt_mdf['pending_amt']  = $usr->priceFormat($cnt_mdf['pending_amt']);
            return view('mdf.index', compact('mdfs', 'cnt_mdf'));
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
        $usr = Auth::user();
        if($usr->can('Request MDF'))
        {
            $status = MdfStatus::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id')->toArray();
            $type   = MdfType::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id')->toArray();

            return view('mdf.create', compact('status', 'type'));
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
        $usr = Auth::user();
        if($usr->can('Request MDF') && $usr->type != 'Owner')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   // 'submitter' => 'required',
                                   'status' => 'required',
                                   'request_type' => 'required',
                                   'event_type' => 'required',
                                   'event_date' => 'required',
                                   'amount_requested' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $mdf              = new Mdf();
            $mdf->mdf_id      = $this->mdfNumber();
            $mdf->user_id     = $usr->id;
            $mdf->status      = $request->status;
            $mdf->type        = $request->request_type;
            $mdf->sub_type    = $request->event_type;
            $mdf->date        = $request->event_date;
            $mdf->amount      = $request->amount_requested;
            $mdf->description = $request->description;
            $mdf->created_by  = $usr->ownerId();
            $mdf->save();

            // return redirect()->route('mdf.show', $mdf->id)->with('success', __('MDF successfully created!'));
            return redirect()->back()->with('success', __('MDF successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Mdf $mdf
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Mdf $mdf)
    {
        $usr = Auth::user();
        if($usr->can('View MDF') && (!empty($mdf->approvedAmt) || $usr->type == 'Owner'))
        {
            if($usr->type != 'Owner' && $mdf->user_id != $usr->id)
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }

            if($mdf->user->created_by == $usr->ownerId())
            {
                $settings = Utility::settings();

                return view('mdf.show', compact('mdf', 'settings'));
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
     * @param \App\Mdf $mdf
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Mdf $mdf)
    {
        $usr = Auth::user();
        if($usr->can('Edit MDF'))
        {
            if($mdf->user->created_by == $usr->ownerId())
            {
                $status = MdfStatus::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id')->toArray();
                $type   = MdfType::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id')->toArray();

                return view('mdf.edit', compact('mdf', 'status', 'type'));
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
     * @param \App\Mdf $mdf
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mdf $mdf)
    {
        $usr = Auth::user();
        if($usr->can('Edit MDF'))
        {
            if($mdf->user->created_by == $usr->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       // 'submitter' => 'required',
                                       'status' => 'required',
                                       'request_type' => 'required',
                                       'event_type' => 'required',
                                       'event_date' => 'required',
                                       'amount_requested' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $mdf->status      = $request->status;
                $mdf->type        = $request->request_type;
                $mdf->sub_type    = $request->event_type;
                $mdf->date        = $request->event_date;
                $mdf->amount      = $request->amount_requested;
                $mdf->description = $request->description;
                $mdf->save();

                return redirect()->back()->with('success', __('MDF successfully updated!'));
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
     * @param \App\Mdf $mdf
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mdf $mdf)
    {
        $usr = Auth::user();
        if($usr->can('Delete MDF'))
        {
            if($mdf->user->created_by == $usr->ownerId())
            {
                MdfProduct::where('mdf_id', '=', $mdf->id)->delete();
                MdfFund::where('mdf_id', '=', $mdf->id)->delete();
                $mdf->delete();

                return redirect()->route('mdf.index')->with('success', __('MDF successfully deleted!'));
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

    // Get Type based it's Event(Sub_type)
    public function jsonEvent(Request $request)
    {
        $type  = MdfType::find($request->request_type);
        $event = $type->subType->pluck('name', 'id');

        return response()->json($event, 200);
    }

    // Get MDF Number
    function mdfNumber()
    {
        $latest = Mdf::where('created_by', '=', \Auth::user()->ownerId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->mdf_id + 1;
    }

    public function productAdd($id)
    {
        $usr = Auth::user();
        if($usr->can('MDF Add Expense'))
        {
            $mdf = Mdf::find($id);
            if($mdf->created_by == $usr->ownerId())
            {
                $products = Product::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id');

                $products->prepend(__('Select Products'), '');

                return view('mdf.products', compact('mdf', 'products'));
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
        $usr = Auth::user();
        if($usr->can('MDF Add Expense'))
        {
            $mdf = Mdf::find($id);
            if($mdf->created_by == $usr->ownerId())
            {
                $required = [];

                if($request->product_type == 'product_service')
                {
                    $required = [
                        'product_id' => 'required',
                        'description' => 'required',
                        'quantity' => 'required|numeric|min:1',
                    ];
                }
                else
                {
                    $required = [
                        'title' => 'required',
                        'price' => 'required|numeric|min:1',
                    ];
                }

                $validator = \Validator::make(
                    $request->all(), $required
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('mdf.show', $mdf->id)->with('error', $messages->first());
                }

                // Validate Amount
                if($request->product_type == 'product_service')
                {
                    $product = Product::find($request->product_id);
                    $amount  = $product->price * $request->quantity;
                }
                else
                {
                    $amount = $request->price;
                }

                if($amount > $mdf->getDue())
                {
                    return redirect()->back()->with('error', __("You have not enough amount."));
                }
                // end Validate amount

                if($request->product_type == 'product_service')
                {
                    $post = [
                        'mdf_id' => $mdf->id,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $request->quantity,
                        'description' => $request->description,
                        'type' => $request->product_type,
                    ];
                }
                else
                {
                    $post = [
                        'mdf_id' => $mdf->id,
                        'product_id' => 0,
                        'name' => $request->title,
                        'price' => $request->price,
                        'quantity' => 1,
                        'description' => $request->description,
                        'type' => $request->product_type,
                    ];
                }

                MdfProduct::create($post);

                // Make MDF Complete if Amount is same as approved amount
                $mdf = Mdf::find($id);
                if($mdf->getDue() == 0 || $mdf->getFundAmt() == $mdf->getSubTotal())
                {
                    $mdf->is_complete = 1;
                    $mdf->save();
                }

                return redirect()->route('mdf.show', $mdf->id)->with('success', __('Expense successfully added!'));
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
        $usr = Auth::user();
        if($usr->can('MDF Delete Expense'))
        {
            $mdf = Mdf::find($id);
            if($mdf->created_by == $usr->ownerId())
            {
                $mdfProduct = MdfProduct::find($product_id);
                $mdfProduct->delete();

                return redirect()->route('mdf.show', $mdf->id)->with('success', __('Expense successfully deleted!'));
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

    public function paymentApproved($id, $type)
    {
        $usr = Auth::user();
        if($usr->can('Create MDF Payment') && $usr->type == 'Owner')
        {
            $mdf = Mdf::find($id);
            if($mdf->created_by == $usr->ownerId())
            {
                $payment_methods = Payment::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id');
                $payment_methods->prepend(__('Select Payment Method'), '');

                return view('mdf.approved', compact('mdf', 'payment_methods', 'type'));
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

    public function paymentApprovedStore($id, Request $request)
    {
        $usr = Auth::user();
        if($usr->can('Create MDF Payment'))
        {
            $mdf = Mdf::find($id);
            if($mdf->created_by == $usr->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'amount' => 'required|numeric|min:1',
                                       'date' => 'required',
                                       'payment_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                if($request->amount <= ($mdf->amount - $mdf->getFundAmt()))
                {
                    MdfFund::create(
                        [
                            'mdf_id' => $mdf->id,
                            'amount' => $request->amount,
                            'payment_id' => $request->payment_id,
                            'type' => $request->type,
                            'note' => $request->notes,
                            'date' => $request->date,
                            'created_by' => $usr->id,
                        ]
                    );

                    if($request->type == 'fund')
                    {
                        return redirect()->back()->with('success', __('Fund successfully Added!'));
                    }
                    else
                    {
                        return redirect()->back()->with('success', __('Payment successfully Approved!'));
                    }
                }
                else
                {
                    return redirect()->back()->with('error', __('Invalid Amount.'));
                }
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

    public function printMDF($id)
    {
        if(\Auth::user()->can('Manage MDFs'))
        {
            $mdf      = Mdf::findOrFail($id);
            $settings = Utility::settings();

            //Set your logo
            // $logo         = asset(\Storage::url('logo/'));
            $logo=\App\Models\Utility::get_file('logo/');

            $dark_logo    = Utility::getValByName('dark_logo');
            $mdf_logo     = Utility::getValByName('mdf_logo');
            if(isset($mdf_logo) && !empty($mdf_logo))
            {
                $img = \App\Models\Utility::get_file('/') . $mdf_logo;
            }
            else
            {
                $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
            }

            if($mdf)
            {
                $color      = '#' . $settings['mdf_color'];
                $font_color = Utility::getFontColor($color);

                return view('mdf.templates.' . $settings['mdf_template'], compact('mdf', 'color', 'settings', 'img', 'font_color'));
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

    public function previewMDF($template, $color)
    {
        $settings   = Utility::settings();
        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $mdf     = new Mdf();
        $mdf->id = 1;

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item           = new \stdClass();
            $item->mdf_id   = $mdf->id;
            $item->name     = 'Product ' . $i;
            $item->price    = 100;
            $item->quantity = $i;
            $items[]        = $item;
        }

        $user                = new \stdClass();
        $user->name          = 'User';
        $user->email         = 'user@example.com';
        $statusDetail        = new \stdClass();
        $statusDetail->name  = 'New';
        $typeDetail          = new \stdClass();
        $typeDetail->name    = 'Google';
        $subTypeDetail       = new \stdClass();
        $subTypeDetail->name = 'Google Event';

        $fund         = new \stdClass();
        $fund->mdf_id = $mdf->id;
        $fund->amount = 1000;
        $funds[]      = $fund;

        $mdf->mdf_id        = 1;
        $mdf->date          = date('Y-m-d H:i:s');
        $mdf->user          = $user;
        $mdf->amount        = 1000;
        $mdf->statusDetail  = $statusDetail;
        $mdf->typeDetail    = $typeDetail;
        $mdf->subTypeDetail = $subTypeDetail;
        $mdf->getProducts   = $items;
        $mdf->funds         = $funds;

        //Set your logo
        // $logo         = asset(\Storage::url('logo/'));
        $logo=\App\Models\Utility::get_file('logo/');

        $dark_logo    = Utility::getValByName('dark_logo');
        $mdf_logo     = Utility::getValByName('mdf_logo');
        if(isset($mdf_logo) && !empty($mdf_logo))
        {
            $img = \App\Models\Utility::get_file('/') . $mdf_logo;
        }
        else
        {
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
        }

        return view('mdf.templates.' . $template, compact('mdf', 'preview', 'color', 'settings', 'img', 'font_color'));
    }

    public function changeComplete($id)
    {
        $mdf              = Mdf::find($id);
        $mdf->is_complete = ($mdf->is_complete == 1) ? 0 : 1;
        $mdf->save();

        return redirect()->back();
    }
}
