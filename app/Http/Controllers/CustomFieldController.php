<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
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
        if(\Auth::user()->can('Manage Expense Categories'))
        {
            $custom_fields = CustomField::where('created_by', '=', \Auth::user()->ownerId())->get();

            return view('custom_fields.index')->with('custom_fields', $custom_fields);
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
        if(\Auth::user()->can('Create Custom Field'))
        {
            $types   = CustomField::$fieldTypes;
            $modules = CustomField::$modules;

            return view('custom_fields.create', compact('types', 'modules'));
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
        if(\Auth::user()->can('Create Custom Field'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'type' => 'required',
                                   'module' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('custom_fields.index')->with('error', $messages->first());
            }

            $custom_field             = new CustomField();
            $custom_field->name       = $request->name;
            $custom_field->type       = $request->type;
            $custom_field->module     = $request->module;
            $custom_field->created_by = \Auth::user()->ownerId();
            $custom_field->save();

            return redirect()->route('custom_fields.index')->with('success', __('Custom Field successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CustomField $customField
     *
     * @return \Illuminate\Http\Response
     */
    public function show(CustomField $customField)
    {
        return redirect()->route('custom_fields.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CustomField $customField
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomField $customField)
    {
        if(\Auth::user()->can('Edit Custom Field'))
        {
            if($customField->created_by == \Auth::user()->ownerId())
            {
                $types   = CustomField::$fieldTypes;
                $modules = CustomField::$modules;

                return view('custom_fields.edit', compact('customField', 'types', 'modules'));
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
     * @param \App\CustomField $customField
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomField $customField)
    {
        if(\Auth::user()->can('Edit Custom Field'))
        {

            if($customField->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('custom_fields.index')->with('error', $messages->first());
                }

                $customField->name   = $request->name;
                $customField->save();

                return redirect()->route('custom_fields.index')->with('success', __('Custom Field successfully updated!'));
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
     * @param \App\CustomField $customField
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomField $customField)
    {
        if(\Auth::user()->can('Delete Custom Field'))
        {
            if($customField->created_by == \Auth::user()->ownerId())
            {
                $customField->delete();

                return redirect()->route('custom_fields.index')->with('success', __('Custom Field successfully deleted!'));
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
