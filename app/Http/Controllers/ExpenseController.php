<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Expense;
use App\Models\Utility;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
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
        $usr = \Auth::user();
        if($usr->can('Manage Expenses'))
        {
            if($usr->type == 'Owner')
            {
                $expenses    = Expense::where('created_by', '=', $usr->ownerId())->get();
                $curr_month  = Expense::where('created_by', '=', $usr->ownerId())->whereMonth('date', '=', date('m'))->get();
                $curr_week   = Expense::where('created_by', '=', $usr->ownerId())->whereBetween(
                    'date', [
                              \Carbon\Carbon::now()->startOfWeek(),
                              \Carbon\Carbon::now()->endOfWeek(),
                          ]
                )->get();
                $last_30days = Expense::where('created_by', '=', $usr->ownerId())->whereDate('date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }
            else
            {
                $expenses    = Expense::where('user_id', '=', $usr->id)->get();
                $curr_month  = Expense::where('user_id', '=', $usr->id)->whereMonth('date', '=', date('m'))->get();
                $curr_week   = Expense::where('user_id', '=', $usr->id)->whereBetween(
                    'date', [
                              \Carbon\Carbon::now()->startOfWeek(),
                              \Carbon\Carbon::now()->endOfWeek(),
                          ]
                )->get();
                $last_30days = Expense::where('user_id', '=', $usr->id)->whereDate('date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }

            // Expense Summary
            $cnt_expense                = [];
            $cnt_expense['total']       = \App\Models\Expense::getExpenseSummary($expenses);
            $cnt_expense['this_month']  = \App\Models\Expense::getExpenseSummary($curr_month);
            $cnt_expense['this_week']   = \App\Models\Expense::getExpenseSummary($curr_week);
            $cnt_expense['last_30days'] = \App\Models\Expense::getExpenseSummary($last_30days);

            return view('expenses.index', compact('expenses', 'cnt_expense'));
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
        if(\Auth::user()->can('Create Expense'))
        {
            $category = ExpenseCategory::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
            $deals    = Deal::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
            $deals->prepend(__('Select Deal'), '');

            return view('expenses.create', compact('category', 'deals'));
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
        if(\Auth::user()->can('Create Expense'))
        {

            $rules = [
                'category_id' => 'required',
                'description' => 'required',
                'amount' => 'required',
                'date' => 'required|date',
                'deal_id' => 'required',
                'user_id' => 'required',
            ];
            if($request->attachment)
            {
                $rules['attachment'] = 'required';
            }

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('expenses.index')->with('error', $messages->first());
            }

            $expense              = new Expense();
            $expense->category_id = $request->category_id;
            $expense->description = $request->description;
            $expense->amount      = $request->amount;
            $expense->date        = $request->date;
            $expense->deal_id     = $request->deal_id;
            $expense->user_id     = $request->user_id;
            $expense->created_by  = \Auth::user()->ownerId();
            $expense->save();

            if($request->attachment)
            {

                $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                // $filepath        = $request->file('attachment')->storeAs('attachment', $extension);
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $dir        = 'attachment/';

                $url = '';
                $path = Utility::upload_file($request,'attachment',$filenameWithExt,$dir,[]);

                if($path['flag'] == 1){
                    $url = $path['url'];
                    $expense->attachment = $url;
                }else{
                    return redirect()->route('expenses.index', \Auth::user()->id)->with('error', __($path['msg']));
                }
                $expense->save();
            }

            return redirect()->route('expenses.index')->with('success', __('Expense successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Expense $expense
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        return redirect()->route('expenses.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Expense $expense
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        if(\Auth::user()->can('Edit Expense'))
        {
            if($expense->created_by == \Auth::user()->ownerId())
            {
                $category = ExpenseCategory::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $deals    = Deal::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $deals->prepend(__('Select Deal'), '');

                return view('expenses.edit', compact('expense', 'category', 'deals'));
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
     * @param \App\Expense $expense
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        if(\Auth::user()->can('Edit Expense'))
        {

            if($expense->created_by == \Auth::user()->ownerId())
            {

                $rules = [
                    'category_id' => 'required',
                    'description' => 'required',
                    'amount' => 'required',
                    'date' => 'required|date',
                    'deal_id' => 'required',
                    'user_id' => 'required',
                ];
                if($request->attachment)
                {
                    $rules['attachment'] = 'required';
                }

                $validator = \Validator::make($request->all(), $rules);

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('expenses.index')->with('error', $messages->first());
                }
                $expense->category_id = $request->category_id;
                $expense->description = $request->description;
                $expense->amount      = $request->amount;
                $expense->date        = $request->date;
                $expense->deal_id     = $request->deal_id;
                $expense->user_id     = $request->user_id;
                $expense->save();

                if($request->attachment)
                {
                    $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('attachment')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $dir        = 'attachment/';

                    $url = '';
                    $path = Utility::upload_file($request,'attachment',$filenameWithExt,$dir,[]);

                    if($path['flag'] == 1){
                        $url = $path['url'];
                        $expense->attachment = $url;
                    }else{
                        return redirect()->route('attachment', \Auth::user()->id)->with('error', __($path['msg']));
                    }

                    $expense->save();
                }

                return redirect()->route('expenses.index')->with('success', __('Expense successfully updated!'));
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
     * @param \App\Expense $expense
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        if(\Auth::user()->can('Delete Expense'))
        {
            if($expense->created_by == \Auth::user()->ownerId())
            {
                $expense->delete();

                return redirect()->route('expenses.index')->with('success', __('Expense successfully deleted!'));
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
