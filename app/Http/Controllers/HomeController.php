<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealTask;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\DealCall;
use App\Models\Stage;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::check())
        {
            $transdate = date('Y-m-d', time());

           /* $roles = Auth::user()->roles;*/
            $calenderTasks = [];
            $chartData     = [];
            $chartcall     = [];
            $dealdata      = [];
            $stagedata     = [];
            $arrCount      = [];
            $arrErr        = [];
            $m             = date("m");
            $de            = date("d");
            $y             = date("Y");
            $format        = 'Y-m-d';
            $user          = \Auth::user();

            if(\Auth::user()->can('View Task'))
            {
                $company_setting = Utility::settings();
            }

            if($user->type == 'Super Admin')
            {
                foreach($user->deals as $deal)
                {
                    foreach($deal->tasks as $task)
                    {
                        $calenderTasks[] = [
                            'title' => $task->name,
                            'start' => $task->date,
                            'url' => route(
                                'deals.tasks.show', [
                                                      $deal->id,
                                                      $task->id,
                                                  ]
                            ),
                            'className' => ($task->status) ? 'event-success border-success' : 'event-warning border-warning',
                        ];
                    }

                    // $calenderTasks[] = [
                    //     'title' => $deal->name,
                    //     'start' => $deal->created_at->format('Y-m-d'),
                    //     'url' => route('deals.show', [$deal->id]),
                    //     'className' => 'deal event-primary border-primary',
                    // ];
                }

                $arrCount['owner']['owner'] = User::where('type', '=', 'Owner')->count();
                $arrCount['owner']['total'] = User::where('type', '!=', 'Super Admin')->where('plan', '!=', 1)->count();
                $arrCount['order']['order'] = Order::count();
                $arrCount['order']['total'] = Order::where('payment_status', '=', 'succeeded')->sum('price');
                $arrCount['plan']['plan']   = Plan::count();
                $arrCount['plan']['total']  = Plan::where(
                    'id', function ($query){
                    $query->select('plan_id')->from('orders')->groupBy('plan_id')->orderBy(\DB::raw('COUNT(plan_id)'))->limit(1);
                }
                )->first();

                $chartData = $this->getOrderChart(['duration' => 'week']);
            }
            elseif($user->type == 'Owner')
            {
                //Handle Custom Error for System Setting
                $err = '';
                if(empty($company_setting['company_name']))
                {
                    $err .= __('Company Name') . ', ';
                }

                if(empty($company_setting['company_email']))
                {
                    $err .= __('System Email') . ', ';
                }

                if(empty($company_setting['company_email_from_name']))
                {
                    if(!empty($err))
                    {
                        $err = rtrim($err, ', ');
                        $err .= ' & ';
                    }
                    $err .= __('Email');
                }
                $arrErr['system'] = $err;

                if($user->getUserCount() == 0)
                {
                    $arrErr['user'] = __('Please create new User');
                }

                if($user->getRoleCount() == 0)
                {
                    $arrErr['role'] = __('Please create new Role');
                }

                foreach($user->deals as $deal)
                {
                    foreach($deal->tasks as $task)
                    {
                        $calenderTasks[] = [
                            'title' => $task->name,
                            'start' => $task->date,
                            'url' => route(
                                'deals.tasks.show', [
                                                      $deal->id,
                                                      $task->id,
                                                  ]
                            ),
                            'className' => ($task->status) ? 'event-success border-success' : 'event-warning border-warning',
                        ];
                    }

                    // $calenderTasks[] = [
                    //     'title' => $deal->name,
                    //     'start' => $deal->created_at->format('Y-m-d'),
                    //     'url' => '#',
                    //     'className' => 'deal event-primary border-primary',
                    // ];
                }

                $arrTemp = [];
                for($i = 0; $i <= 7 - 1; $i++)
                {
                    $date                 = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
                    $arrTemp['date'][]    = __(date('d-M', strtotime($date)));
                    $arrTemp['invoice'][] = Invoice::where('issue_date', '=', $date)->where('created_by', '=', $user->ownerId())->count();
                    $arrTemp['payment'][] = InvoicePayment::join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoice_payments.date', '=', $date)->where('invoices.created_by', '=', $user->ownerId())->count();
                }
                $chartData = $arrTemp;
                $arrCount['client']  = User::where('type', '=', 'Client')->where('created_by', '=', $user->ownerId())->count();
                $arrCount['user']    = User::where('type', '!=', 'Client')->where('created_by', '=', $user->ownerId())->count();
                $arrCount['deal']    = Deal::where('created_by', '=', $user->ownerId())->count();
                $arrCount['invoice'] = Invoice::where('created_by', '=', $user->ownerId())->count();


                $arryTemp = [];
                for($i = 0; $i <= 7 - 1; $i++)
                {
                    $date                 = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
                    $arryTemp['date'][]    = __(date('d-M', strtotime($date)));
                    $arryTemp['dealcall'][] = DealCall::whereDate('created_at',$date)->where('user_id', $user->ownerId())->count();

                }
                $chartcall = $arryTemp;
                $chartcall['user']    = User::where('type', '!=', 'Client')->where('created_by',$user->ownerId())->count();
                $chartcall['deal']    = Deal::where('created_by',$user->ownerId())->count();


                $arry = [];
                for($i = 0; $i <= 7 - 1; $i++)
                {
                    $name               = Stage::all();
                    $arry['stage'][]    = Stage::where('name',$name);
                }
                // dd($name);
                $stagedata = $arry;
                $stagedata['user']    = User::where('type', '!=', 'Client')->where('created_by',$user->ownerId())->count();
                $stagedata['deal']    = Deal::where('created_by',$user->ownerId())->count();


            }
            elseif($user->type == 'Client')
            {
                $arrTemp = [];
                for($i = 0; $i <= 7 - 1; $i++)
                {
                    $date                 = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
                    $arrTemp['date'][]    = __(date('d-M', strtotime($date)));
                    $arrTemp['invoice'][] = Invoice::where('issue_date', '=', $date)->where('created_by', '=', $user->ownerId())->count();
                    $arrTemp['payment'][] = InvoicePayment::join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoice_payments.date', '=', $date)->where('invoices.created_by', '=', $user->ownerId())->count();
                }

                $temp = [];
                for($i = 0; $i <= 7 - 1; $i++)
                {
                    $date                 = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
                    $temp['date'][]    = __(date('d-M', strtotime($date)));
                    $temp['deal'][] = Deal::whereDate('created_at',$date)->where('created_by', $user->ownerId())->count();

                }
                $dealdata = $temp;
                $dealdata['user']    = User::where('type', '!=', 'Client')->where('created_by',$user->ownerId())->count();

                $chartData = $arrTemp;

                foreach($user->clientDeals as $deal)
                {
                    foreach($deal->tasks as $task)
                    {
                        $calenderTasks[] = [
                            'title' => $task->name,
                            'start' => $task->date,
                            'url' => route(
                                'deals.tasks.show', [
                                                      $deal->id,
                                                      $task->id,
                                                  ]
                            ),
                            'className' => ($task->status) ? 'event-success border-success' : 'event-warning border-warning',
                        ];
                    }

                    $calenderTasks[] = [
                        'title' => $deal->name,
                        'start' => $deal->created_at->format('Y-m-d'),
                        'url' => route('deals.show', [$deal->id]),
                        'className' => 'deal event-primary border-primary',
                    ];
                }

                $client_deal         = $user->clientDeals->pluck('id');
                $arrCount['deal']    = $user->clientDeals->count();
                $arrCount['invoice'] = Invoice::join('deals', 'invoices.deal_id', '=', 'deals.id')->where('deals.created_by', '=', $user->ownerId())->count();
                if(!empty($client_deal->first()))
                {
                    $arrCount['task'] = DealTask::whereIn('deal_id', $client_deal)->count();
                }
                else
                {
                    $arrCount['task'] = 0;
                }
            }
            else
            {
                $arrTemp = [];
                for($i = 0; $i <= 7 - 1; $i++)
                {
                    $date                 = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
                    $arrTemp['date'][]    = __(date('d-M', strtotime($date)));
                    $arrTemp['invoice'][] = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('invoices.issue_date', '=', $date)->where('user_deals.user_id', '=', $user->id)->where('invoices.created_by', '=', $user->ownerId())->count();
                    $arrTemp['payment'][] = InvoicePayment::join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('invoice_payments.date', '=', $date)->where('user_deals.user_id', '=', $user->id)->where('invoices.created_by', '=', $user->ownerId())->count();
                }
                $chartData = $arrTemp;


                foreach($user->deals as $deal)
                {
                    foreach($deal->tasks as $task)
                    {
                        $calenderTasks[] = [
                            'title' => $task->name,
                            'start' => $task->date,
                            'url' => route(
                                'deals.tasks.show', [
                                                      $deal->id,
                                                      $task->id,
                                                  ]
                            ),
                            'className' => ($task->status) ? 'event-success border-success' : 'event-warning border-warning',
                        ];
                    }

                    $calenderTasks[] = [
                        'title' => $deal->name,
                        'start' => $deal->created_at->format('Y-m-d'),
                        'url' => route('deals.show', [$deal->id]),
                        'className' => 'deal bg-primary border-primary',
                    ];
                }

                $user_deal           = $user->deals->pluck('id');
                $arrCount['deal']    = $user->deals()->count();
                $arrCount['invoice'] = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->count();
                if(!empty($user_deal->first()))
                {
                    $arrCount['task'] = DealTask::whereIn('deal_id', $user_deal)->count();
                }
                else
                {
                    $arrCount['task'] = 0;
                }
            }

            $invoices   = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', \Auth::user()->id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->get();
            $arrInvoice = [];

            foreach($invoices as $invoice)
            {
                $dueAmount = $invoice->getDue();
                if($dueAmount > 0)
                {
                    $arrInvoice[] = $invoice;
                }
            }

            arsort($arrInvoice);

            $invoices = array_slice($arrInvoice, 0, 5);



            $deals = Deal::where('created_by', '=', \Auth::user()->ownerId())->take(5)->get();



            $deal_stage = Stage::where('created_by', \Auth::user()->id)->orderBy('order', 'ASC')->get();

            $dealStageName = [];
            $dealStageData = [];
            foreach($deal_stage as $deal_stage_data){
                $deal_stage = Deal::where('created_by', \Auth::user()->id)->where('stage_id',$deal_stage_data->id)->orderBy('order', 'ASC')->count();
                $dealStageName[]= $deal_stage_data->name;
                $dealStageData[]= $deal_stage;
            }

            return view('admin.dashboard', compact('calenderTasks','transdate','arrErr', 'arrCount', 'chartData','chartcall','deals', 'invoices','dealdata','dealStageName','dealStageData'));
        }
        else
        {
            if(!file_exists(storage_path() . "/installed"))
            {
                header('location:install');
                die;
            }
            else
            {
                if(Utility::getValByName('enable_landing') == 'yes')
                {

                    return view('layouts.landing');
                }
                else
                {
                    return redirect()->route('login');
                }
            }
        }
    }

    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if($arrParam['duration'])
        {
            if($arrParam['duration'] == 'week')
            {
                $previous_week = strtotime("-1 week +1 day");
                for($i = 0; $i < 7; $i++)
                {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];

        $arrDuration = array_reverse($arrDuration);

        foreach($arrDuration as $date => $label)
        {
            $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = __($label);
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }

    public function check()
    {
        $user = \Auth::user();

        // Check plan trial
        if($user->type == 'Owner' && $user->is_trial_done < 2)
        {
            if($user->is_trial_done == 1 && $user->plan_expire_date < date('Y-m-d'))
            {
                $user->is_trial_done = 2;
                $user->save();
            }
        }

        // Check plan when owner login
        if($user->type == 'Owner' && (empty($user->plan_expire_date) || $user->plan_expire_date < date('Y-m-d')))
        {
            $plan=Plan::where('id',$user->plan)->first();
            if($plan->monthly_price>0 || $plan->annual_price>0)
            {
                $error = $user->is_trial_done ? __('Your Plan is expired.') : ($user->plan_expire_date < date('Y-m-d') ? __('Please upgrade your plan') : '');

                return redirect()->route('plans.index')->with('error', $error);
            }

        }

        return redirect()->route('home');
    }
}
