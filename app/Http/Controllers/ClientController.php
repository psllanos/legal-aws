<?php

namespace App\Http\Controllers;

use App\Models\ClientDeal;
use App\Models\ClientPermission;
use App\Exports\ClientExport;

// use App\Imports\Import;
use App\Imports\ClientImport;
use App\Models\Contract;
use App\Models\CustomField;
use App\Models\Estimation;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
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

    public function index()
    {
        if(\Auth::user()->can('Manage Clients'))
        {
            $user    = \Auth::user();
            $clients = User::where('created_by', '=', $user->ownerId())->where('type', '=', 'Client')->get();

            return view('clients.index', compact('clients'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create(Request $request)
    {

        if(\Auth::user()->can('Create Client'))
        {
            if($request->ajax)
            {
                return view('clients.createAjax');
            }
            else
            {
                $customFields = CustomField::where('module', '=', 'client')->get();

                return view('clients.create', compact('customFields'));
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('Create Client'))
        {
            $user      = \Auth::user();
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'email' => 'required|email|unique:users',
                                   'password' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                if($request->ajax)
                {
                    return response()->json(['error' => $messages->first()], 401);
                }
                else
                {
                    return redirect()->back()->with('error', $messages->first());
                }
            }

            $role = Role::findByName('Client');

            $client = User::create(
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'job_title' => $request->job_title,
                    'password' => Hash::make($request->password),
                    'type' => 'Client',
                    'lang' => Utility::getValByName('default_language'),
                    'created_by' => $user->ownerId(),
                ]
            );
            $client->assignRole($role);

            $uArr = [
                'email' => $client->email,
                'password' => $request->password,
            ];

            // Send Email
            $resp = Utility::sendEmailTemplate('New User', [$client->id => $client->email], $uArr);

            if($request->customField)
            {
                CustomField::saveData($client, $request->customField);
            }
            if($request->ajax)
            {
                return response()->json(
                    [
                        'success' => __('Client created Successfully!'),
                        'record' => $client,
                        'target' => '#client_id',
                    ], 200
                );
            }
            else
            {
                return redirect()->back()->with('success', __('Client created Successfully!') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }

        }
        else
        {
            if($request->ajax)
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
    }
    
    public function show(User $client)
    {
        $usr = Auth::user();
        if(!empty($client) && $usr->id == $client->ownerId() && $client->id != $usr->id && $client->type == 'Client')
        {
            // For Invoices
            $invoices    = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', $client->id)->where('invoices.created_by', '=', $client->ownerId())->get();
            $curr_month  = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', $client->id)->where('invoices.created_by', '=', $client->ownerId())->whereMonth('invoices.issue_date', '=', date('m'))->get();
            $curr_week   = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', $client->id)->where('invoices.created_by', '=', $client->ownerId())->whereBetween(
                'invoices.issue_date', [
                                         \Carbon\Carbon::now()->startOfWeek(),
                                         \Carbon\Carbon::now()->endOfWeek(),
                                     ]
            )->get();
            $last_30days = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', $client->id)->where('invoices.created_by', '=', $client->ownerId())->whereDate('invoices.issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();

            // Invoice Summary
            $cnt_invoice                = [];
            $cnt_invoice['total']       = Invoice::getInvoiceSummary($invoices);
            $cnt_invoice['this_month']  = Invoice::getInvoiceSummary($curr_month);
            $cnt_invoice['this_week']   = Invoice::getInvoiceSummary($curr_week);
            $cnt_invoice['last_30days'] = Invoice::getInvoiceSummary($last_30days);

            $cnt_invoice['cnt_total']       = $invoices->count();
            $cnt_invoice['cnt_this_month']  = $curr_month->count();
            $cnt_invoice['cnt_this_week']   = $curr_week->count();
            $cnt_invoice['cnt_last_30days'] = $last_30days->count();

            // For Estimations
            $estimations = $client->clientEstimations()->orderByDesc('id')->get();
            $curr_month  = $client->clientEstimations()->whereMonth('issue_date', '=', date('m'))->get();
            $curr_week   = $client->clientEstimations()->whereBetween(
                'issue_date', [
                                \Carbon\Carbon::now()->startOfWeek(),
                                \Carbon\Carbon::now()->endOfWeek(),
                            ]
            )->get();
            $last_30days = $client->clientEstimations()->whereDate('issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();

            // Estimation Summary
            $cnt_estimation                = [];
            $cnt_estimation['total']       = Estimation::getEstimationSummary($estimations);
            $cnt_estimation['this_month']  = Estimation::getEstimationSummary($curr_month);
            $cnt_estimation['this_week']   = Estimation::getEstimationSummary($curr_week);
            $cnt_estimation['last_30days'] = Estimation::getEstimationSummary($last_30days);

            $cnt_estimation['cnt_total']       = $estimations->count();
            $cnt_estimation['cnt_this_month']  = $curr_month->count();
            $cnt_estimation['cnt_this_week']   = $curr_week->count();
            $cnt_estimation['cnt_last_30days'] = $last_30days->count();

            // For Contracts
            $contracts   = $client->clientContracts()->orderByDesc('id')->get();
            $curr_month  = $client->clientContracts()->whereMonth('start_date', '=', date('m'))->get();
            $curr_week   = $client->clientContracts()->whereBetween(
                'start_date', [
                                \Carbon\Carbon::now()->startOfWeek(),
                                \Carbon\Carbon::now()->endOfWeek(),
                            ]
            )->get();
            $last_30days = $client->clientContracts()->whereDate('start_date', '>', \Carbon\Carbon::now()->subDays(30))->get();

            // Contracts Summary
            $cnt_contract                = [];
            $cnt_contract['total']       = Contract::getContractSummary($contracts);
            $cnt_contract['this_month']  = Contract::getContractSummary($curr_month);
            $cnt_contract['this_week']   = Contract::getContractSummary($curr_week);
            $cnt_contract['last_30days'] = Contract::getContractSummary($last_30days);

            $cnt_contract['cnt_total']       = $contracts->count();
            $cnt_contract['cnt_this_month']  = $curr_month->count();
            $cnt_contract['cnt_this_week']   = $curr_week->count();
            $cnt_contract['cnt_last_30days'] = $last_30days->count();

            return view('clients.show', compact('client', 'invoices', 'cnt_invoice', 'estimations', 'cnt_estimation', 'contracts', 'cnt_contract'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit(User $client)
    {
        if(\Auth::user()->can('Edit Client'))
        {
            $user = \Auth::user();
            if($client->created_by == $user->ownerId())
            {
                $client->customField = CustomField::getData($client, 'client');
                $customFields        = CustomField::where('module', '=', 'client')->get();

                return view('clients.edit', compact('client', 'customFields'));
            }
            else
            {
                return response()->json(['error' => __('Invalid Client.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function update(User $client, Request $request)
    {
        if(\Auth::user()->can('Edit Client'))
        {
            $user = \Auth::user();
            if($client->created_by == $user->ownerId())
            {
                $validation = [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $client->id,
                ];

                $post         = [];
                $post['name'] = $request->name;
                if(!empty($request->password))
                {
                    $validation['password'] = 'required';
                    $post['password']       = Hash::make($request->password);
                }

                $validator = \Validator::make($request->all(), $validation);
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $post['email'] = $request->email;

                $client->update($post);

                CustomField::saveData($client, $request->customField);

                return redirect()->back()->with('success', __('Client Updated Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Invalid Client.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy(User $client)
    {
        if(\Auth::user()->can('Delete Client'))
        {
            $user = \Auth::user();
            if($client->created_by == $user->ownerId())
            {
                $estimation = Estimation::where('client_id', '=', $client->id)->first();

                if(empty($estimation))
                {
                    ClientDeal::where('client_id', '=', $client->id)->delete();
                    ClientPermission::where('client_id', '=', $client->id)->delete();
                    $client->delete();

                    return redirect()->back()->with('success', __('Client Deleted Successfully!'));
                }
                else
                {
                    return redirect()->back()->with('error', __('This client has assigned some estimation.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Invalid Client.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function fileImportExport()
    {

       return view('clients.import');
    }

    public function fileImport(Request $request) 
    {
        // Excel::import(new ClientImport, $request->file('file')->store('temp'));
        // return back();


        $rules = [
            'file' => 'required|mimes:csv,txt,xlsx',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $clients = (new ClientImport())->toArray(request()->file('file'))[0];
      
        $totalclient = count($clients) - 1;

        $errorArray    = [];
        for($i = 1; $i <= count($clients) - 1; $i++)
        {
            $client = $clients[$i];

            $clientByEmail = User::where('email', $client[1])->first();


            if(!empty($clientByEmail))
            {
                $clientData = $clientByEmail;
            }
            else
            {
                $clientData = new User();
              
            }


            $clientData->name             = $client[0];
            $clientData->email            = $client[1];
            $clientData->password         = Hash::make($client[2]);
            $clientData->type          = $client[3];
            $clientData->lang        = $client[4];
            $clientData->created_by     = \Auth::user()->ownerId();
            $clientData->is_active  = $client[5];
            $clientData->messenger_color    = $client[6];
           

            if(empty($clientData))
            {
                $errorArray[] = $clientData;
            }
            else
            {
                $clientData->save();
            }
        }

        $errorRecord = [];
        if(empty($errorArray))
        {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        }
        else
        {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalCustomer . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }
    

     public function fileExport() 
    {

        $name = 'client_' . date('Y-m-d i:h:s');
        $data = Excel::download(new ClientExport(), $name . '.xlsx');  ob_end_clean();
        

        return $data;
    } 

    function customerNumber()
    {
        $latest = User::where('created_by', '=', \Auth::user()->ownerId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }


        return $latest->customer_id + 1;
    }

}
