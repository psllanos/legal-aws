<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\User;
use Illuminate\Http\Request;

class TaxController extends Controller
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
        if(\Auth::user()->can('Manage Taxes'))
        {
            $taxes = Tax::where('created_by', '=', \Auth::user()->ownerId())->get();

            return view('taxes.index')->with('taxes', $taxes);
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
        if(\Auth::user()->can('Create Tax'))
        {
            return view('taxes.create');
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
        if(\Auth::user()->can('Create Tax'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'rate' => 'required|numeric',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('users')->with('error', $messages->first());
            }

            $tax             = new Tax();
            $tax->name       = $request->name;
            $tax->rate       = $request->rate;
            $tax->created_by = \Auth::user()->ownerId();
            $tax->save();

            return redirect()->route('taxes.index')->with('success', __('Tax rate successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Tax $tax
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Tax $tax)
    {
        return redirect()->route('taxes.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Tax $tax
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Tax $tax)
    {
       
        if(\Auth::user()->can('Edit Tax'))
        {
            if($tax->created_by == \Auth::user()->ownerId())
            {
                return view('taxes.edit', compact('tax'));
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
     * @param \App\Tax $tax
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tax $tax)
    {
        if(\Auth::user()->can('Edit Tax'))
        {

            if($tax->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'rate' => 'required|numeric',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users')->with('error', $messages->first());
                }

                $tax->name = $request->name;
                $tax->rate = $request->rate;
                $tax->save();

                return redirect()->route('taxes.index')->with('success', __('Tax rate successfully updated!'));
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
     * @param \App\Tax $tax
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tax $tax)
    {
        if(\Auth::user()->can('Delete Tax'))
        {
            if($tax->created_by == \Auth::user()->ownerId())
            {
                $tax->delete();

                return redirect()->route('taxes.index')->with('success', __('Tax rate successfully deleted!'));
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
