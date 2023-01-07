<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth',
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
        if(\Auth::user()->can('Manage Payments'))
        {
            $payments = Payment::where('created_by', '=', \Auth::user()->ownerId())->get();

            return view('payments.index')->with('payments', $payments);
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
        if(\Auth::user()->can('Create Payment'))
        {
            return view('payments.create');
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
        if(\Auth::user()->can('Create Payment'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('payments.index')->with('error', $messages->first());
            }

            $payment             = new Payment();
            $payment->name       = $request->name;
            $payment->created_by = \Auth::user()->ownerId();
            $payment->save();

            return redirect()->route('payments.index')->with('success', __('Payment successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Payment $payment
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        return redirect()->route('payments.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Payment $payment
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        if(\Auth::user()->can('Edit Payment'))
        {
            if($payment->created_by == \Auth::user()->ownerId())
            {
                return view('payments.edit', compact('payment'));
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
     * @param \App\Payment $payment
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        if(\Auth::user()->can('Edit Payment'))
        {

            if($payment->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('payments.index')->with('error', $messages->first());
                }
                $payment->name = $request->name;
                $payment->save();

                return redirect()->route('payments.index')->with('success', __('Payment successfully updated!'));
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
     * @param \App\Payment $payment
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        if(\Auth::user()->can('Delete Payment'))
        {
            if($payment->created_by == \Auth::user()->ownerId())
            {
                $payment->delete();

                return redirect()->route('payments.index')->with('success', __('Payment successfully deleted!'));
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
}
