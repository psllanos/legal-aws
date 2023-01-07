<?php

namespace App\Http\Controllers;

use App\Models\MdfType;
use Illuminate\Http\Request;

class MdfTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage MDF Types'))
        {
            $types = MdfType::where('created_by', '=', \Auth::user()->ownerId())->get();

            return view('mdf_type.index', compact('types'));
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
        if(\Auth::user()->can('Create MDF Type'))
        {
            return view('mdf_type.create');
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
        if(\Auth::user()->can('Create MDF Type'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:200',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('mdf_type.index')->with('error', $messages->first());
            }

            $type             = new MdfType();
            $type->name       = $request->name;
            $type->created_by = \Auth::user()->ownerId();
            $type->save();

            return redirect()->route('mdf_type.index')->with('success', __('MDF Type successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\MdfType $mdfType
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MdfType $mdfType)
    {
        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\MdfType $mdfType
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MdfType $mdfType)
    {
        if(\Auth::user()->can('Edit MDF Type'))
        {
            if($mdfType->created_by == \Auth::user()->ownerId())
            {
                return view('mdf_type.edit', compact('mdfType'));
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
     * @param \App\MdfType $mdfType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MdfType $mdfType)
    {
        if(\Auth::user()->can('Edit MDF Type'))
        {
            if($mdfType->created_by == \Auth::user()->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:200',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('mdf_type.index')->with('error', $messages->first());
                }

                $mdfType->name = $request->name;
                $mdfType->save();

                return redirect()->route('mdf_type.index')->with('success', __('MDF Type successfully updated!'));
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
     * @param \App\MdfType $mdfType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MdfType $mdfType)
    {
        if(\Auth::user()->can('Delete MDF Type'))
        {
            if($mdfType->created_by == \Auth::user()->ownerId())
            {
                //                if(count($mdfType->stages) == 0)
                //                {
                $mdfType->delete();

                return redirect()->route('mdf_type.index')->with('success', __('MDF Type successfully deleted!'));
                //                }
                //                else
                //                {
                //                    return redirect()->route('mdf_type.index')->with('error', __('There are some MDF using this status, please remove it first!'));
                //                }
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
