<?php

namespace App\Http\Controllers;

use App\Models\MdfStatus;
use Illuminate\Http\Request;

class MdfStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage MDF Status'))
        {
            $statuses = MdfStatus::where('created_by', '=', \Auth::user()->ownerId())->get();

            return view('mdf_status.index', compact('statuses'));
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
        if(\Auth::user()->can('Create MDF Status'))
        {
            return view('mdf_status.create');
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
        if(\Auth::user()->can('Create MDF Status'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:200',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('mdf_status.index')->with('error', $messages->first());
            }

            $staus             = new MdfStatus();
            $staus->name       = $request->name;
            $staus->created_by = \Auth::user()->ownerId();
            $staus->save();

            return redirect()->route('mdf_status.index')->with('success', __('MDF Status successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\MdfStatus $mdfStatus
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MdfStatus $mdfStatus)
    {
        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\MdfStatus $mdfStatus
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MdfStatus $mdfStatus)
    {
        if(\Auth::user()->can('Edit MDF Status'))
        {
            if($mdfStatus->created_by == \Auth::user()->ownerId())
            {
                return view('mdf_status.edit', compact('mdfStatus'));
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
     * @param \App\MdfStatus $mdfStatus
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MdfStatus $mdfStatus)
    {
        if(\Auth::user()->can('Edit MDF Status'))
        {

            if($mdfStatus->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:200',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('mdf_status.index')->with('error', $messages->first());
                }

                $mdfStatus->name = $request->name;
                $mdfStatus->save();

                return redirect()->route('mdf_status.index')->with('success', __('MDF Status successfully updated!'));
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
     * @param \App\MdfStatus $mdfStatus
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MdfStatus $mdfStatus)
    {
        if(\Auth::user()->can('Delete MDF Status'))
        {
            if($mdfStatus->created_by == \Auth::user()->ownerId())
            {
                //                if(count($mdfStatus->stages) == 0)
                //                {
                $mdfStatus->delete();

                return redirect()->route('mdf_status.index')->with('success', __('MDF Status successfully deleted!'));
                //                }
                //                else
                //                {
                //                    return redirect()->route('mdf_status.index')->with('error', __('There are some MDF using this status, please remove it first!'));
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
