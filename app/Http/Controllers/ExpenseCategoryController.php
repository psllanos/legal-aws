<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
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
            $expense_categories = ExpenseCategory::where('created_by', '=', \Auth::user()->ownerId())->get();

            return view('expense_categories.index')->with('expense_categories', $expense_categories);
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
        if(\Auth::user()->can('Create Expense Category'))
        {
            return view('expense_categories.create');
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
        if(\Auth::user()->can('Create Expense Category'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'description' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('expense_categories.index')->with('error', $messages->first());
            }

            $expense_category              = new ExpenseCategory();
            $expense_category->name        = $request->name;
            $expense_category->description = $request->description;
            $expense_category->created_by  = \Auth::user()->ownerId();
            $expense_category->save();

            return redirect()->route('expense_categories.index')->with('success', __('Expense Category successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ExpenseCategory $expenseCategory
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        return redirect()->route('expense_categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ExpenseCategory $expenseCategory
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenseCategory $expenseCategory)
    {
        if(\Auth::user()->can('Edit Expense Category'))
        {
            if($expenseCategory->created_by == \Auth::user()->ownerId())
            {
                return view('expense_categories.edit', compact('expenseCategory'));
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
     * @param \App\ExpenseCategory $expenseCategory
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        if(\Auth::user()->can('Edit Expense Category'))
        {
            if($expenseCategory->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('expense_categories.index')->with('error', $messages->first());
                }

                $expenseCategory->name        = $request->name;
                $expenseCategory->description = $request->description;
                $expenseCategory->save();

                return redirect()->route('expense_categories.index')->with('success', __('Expense Category successfully updated!'));
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
     * @param \App\ExpenseCategory $expenseCategory
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        if(\Auth::user()->can('Delete Expense Category'))
        {
            if($expenseCategory->created_by == \Auth::user()->ownerId())
            {
                $expenseCategory->delete();

                return redirect()->route('expense_categories.index')->with('success', __('Expense Category successfully deleted!'));
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
