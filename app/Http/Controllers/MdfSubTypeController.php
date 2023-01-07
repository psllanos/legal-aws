<?php

namespace App\Http\Controllers;

use App\Models\MdfSubType;
use App\Models\MdfType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MdfSubTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage MDF Sub Types'))
        {
            $types = MdfSubType::where('created_by', '=', \Auth::user()->ownerId())->get();

            return view('mdf_sub_type.index', compact('types'));
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
        if($usr->can('Create MDF Sub Type'))
        {
            $mdfTypes = MdfType::where('created_by', '=', $usr->id)->get()->pluck('name', 'id')->toArray();

            return view('mdf_sub_type.create', compact('mdfTypes'));
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
        if($usr->can('Create MDF Sub Type'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'mdf_type' => 'required',
                                   'name' => 'required|max:200',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('mdf_sub_type.index')->with('error', $messages->first());
            }

            $sub_type             = new MdfSubType();
            $sub_type->mdf_type   = $request->mdf_type;
            $sub_type->name       = $request->name;
            $sub_type->created_by = $usr->ownerId();
            $sub_type->save();

            return redirect()->route('mdf_sub_type.index')->with('success', __('MDF Sub Type successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\MdfSubType $mdfSubType
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MdfSubType $mdfSubType)
    {
        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\MdfSubType $mdfSubType
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MdfSubType $mdfSubType)
    {
        $usr = Auth::user();
        if($usr->can('Edit MDF Sub Type'))
        {
            if($mdfSubType->created_by == \Auth::user()->ownerId())
            {
                $mdfTypes = MdfType::where('created_by', '=', $usr->id)->get()->pluck('name', 'id')->toArray();

                return view('mdf_sub_type.edit', compact('mdfSubType', 'mdfTypes'));
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
     * @param \App\MdfSubType $mdfSubType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MdfSubType $mdfSubType)
    {
        if(\Auth::user()->can('Edit MDF Sub Type'))
        {
            if($mdfSubType->created_by == \Auth::user()->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'mdf_type' => 'required',
                                       'name' => 'required|max:200',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('mdf_sub_type.index')->with('error', $messages->first());
                }

                $mdfSubType->mdf_type = $request->mdf_type;
                $mdfSubType->name     = $request->name;
                $mdfSubType->save();

                return redirect()->route('mdf_sub_type.index')->with('success', __('MDF Sub Type successfully updated!'));
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
     * @param \App\MdfSubType $mdfSubType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MdfSubType $mdfSubType)
    {
        if(\Auth::user()->can('Delete MDF Sub Type'))
        {
            if($mdfSubType->created_by == \Auth::user()->ownerId())
            {
                //                if(count($mdfType->stages) == 0)
                //                {
                $mdfSubType->delete();

                return redirect()->route('mdf_sub_type.index')->with('success', __('MDF Sub Type successfully deleted!'));
                //                }
                //                else
                //                {
                //                    return redirect()->route('mdf_sub_type.index')->with('error', __('There are some MDF Sub using this status, please remove it first!'));
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
