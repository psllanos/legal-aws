<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractType;
use App\Models\ContractAttechment;
use App\Models\ContractComment;
use App\Models\ContractNote;
use App\Models\Utility;
use App\Models\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth'
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
        if(\Auth::user()->can('Manage Contracts'))
        {
            if(\Auth::user()->type == 'Owner'){
                $contracts   = Contract::where('created_by', '=', \Auth::user()->ownerId())->get();
                $curr_month  = Contract::where('created_by', '=', \Auth::user()->ownerId())->whereMonth('start_date', '=', date('m'))->get();
                $curr_week   = Contract::where('created_by', '=', \Auth::user()->ownerId())->whereBetween(
                    'start_date', [
                                    \Carbon\Carbon::now()->startOfWeek(),
                                    \Carbon\Carbon::now()->endOfWeek(),
                                ]
                )->get();
                $last_30days = Contract::where('created_by', '=', \Auth::user()->ownerId())->whereDate('start_date', '>', \Carbon\Carbon::now()->subDays(30))->get();

                // Contracts Summary
                $cnt_contract                = [];
                $cnt_contract['total']       = \App\Models\Contract::getContractSummary($contracts);
                $cnt_contract['this_month']  = \App\Models\Contract::getContractSummary($curr_month);
                $cnt_contract['this_week']   = \App\Models\Contract::getContractSummary($curr_week);
                $cnt_contract['last_30days'] = \App\Models\Contract::getContractSummary($last_30days);

                return view('contracts.index', compact('contracts', 'cnt_contract'));
            }

            elseif(\Auth::user()->type == 'Client'){
                $contracts   = Contract::where('client_name', '=', \Auth::user()->id)->get();
                $curr_month  = Contract::where('client_name', '=', \Auth::user()->id)->whereMonth('start_date', '=', date('m'))->get();
                $curr_week   = Contract::where('client_name', '=', \Auth::user()->id)->whereBetween(
                    'start_date', [
                                    \Carbon\Carbon::now()->startOfWeek(),
                                    \Carbon\Carbon::now()->endOfWeek(),
                                ]
                )->get();
                $last_30days = Contract::where('client_name', '=', \Auth::user()->id)->whereDate('start_date', '>', \Carbon\Carbon::now()->subDays(30))->get();

                // Contracts Summary
                $cnt_contract                = [];
                $cnt_contract['total']       = \App\Models\Contract::getContractSummary($contracts);
                $cnt_contract['this_month']  = \App\Models\Contract::getContractSummary($curr_month);
                $cnt_contract['this_week']   = \App\Models\Contract::getContractSummary($curr_week);
                $cnt_contract['last_30days'] = \App\Models\Contract::getContractSummary($last_30days);

                return view('contracts.index', compact('contracts', 'cnt_contract'));
            }
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
        if(\Auth::user()->can('Create Contract'))
        {
            $client       = User::where('type', '=', 'Client')->get()->pluck('name', 'id');
            $contractType = ContractType::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');

            return view('contracts.create', compact('contractType', 'client'));
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
        if(\Auth::user()->can('Create Contract'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'subject' => 'required',
                                   'value' => 'required',
                                   'type' => 'required',
                                   'date' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('contract.index')->with('error', $messages->first());
            }

            $date = explode(' to ', $request->date);

            $contract              = new Contract();
            $contract->name        = $request->name;
            $contract->client_name = $request->client_name;
            $contract->subject     = $request->subject;
            $contract->value       = $request->value;
            $contract->type        = $request->type;
            $contract->start_date  = $date[0];
            $contract->end_date    = $date[1];
            $contract->notes       = $request->notes;
            $contract->created_by  = \Auth::user()->ownerId();
            // $contract->status      = $request->status;
            $contract->save();

            $settings  = \Utility::settings(\Auth::user()->ownerId());

            if(isset($settings['contract_notification']) && $settings['contract_notification'] ==1){
                $msg = 'New Invoice '.Auth::user()->contractNumberFormat($this->contractNumber()).'  created by  '.\Auth::user()->name.'.';

                \Utility::send_slack_msg($msg);
            }
            if(isset($settings['telegram_contract_notification']) && $settings['telegram_contract_notification'] ==1){
                $resp = 'New  Invoice '.Auth::user()->contractNumberFormat($this->contractNumber()).'  created by  '.\Auth::user()->name.'.';
                \Utility::send_telegram_msg($resp);
            }

            return redirect()->route('contract.index')->with('success', __('Contract successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function contract_status_edit(Request $request, $id)
    {
        $contract = Contract::find($id);
        $contract->status   = $request->status;
        $contract->save();

    }

    function contractNumber()
    {
        $latest = Contract::where('created_by', '=', \Auth::user()->ownerId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->id + 1;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Contract $contract
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contract = Contract::find($id);

        if($contract->created_by == \Auth::user()->ownerId())
        {
            $client   = $contract->client;

            return view('contracts.show', compact('contract', 'client'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Contract $contract
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contract $contract)
    {
        if(\Auth::user()->can('Edit Contract'))
        {
            if($contract->created_by == \Auth::user()->ownerId())
            {
                $client       = User::where('type', '=', 'Client')->get()->pluck('name', 'id');
                $contractType = ContractType::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $date         = $contract->start_date . ' - ' . $contract->end_date;
                unset($contract->start_date);
                unset($contract->end_date);
                $contract->setAttribute('date', $date);

                return view('contracts.edit', compact('contract', 'contractType', 'client'));
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
     * @param \App\Contract $contract
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contract $contract)
    {
        if(\Auth::user()->can('Edit Contract'))
        {
            if($contract->created_by == \Auth::user()->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'subject' => 'required',
                                       'value' => 'required',
                                       'type' => 'required',
                                       'date' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('contract.index')->with('error', $messages->first());
                }

                $date = explode(' to ', $request->date);

                $contract->name        = $request->name;
                $contract->client_name = $request->client_name;
                $contract->subject     = $request->subject;
                $contract->value       = $request->value;
                $contract->type        = $request->type;
                $contract->start_date  = $date[0];
                $contract->end_date    = $date[1];
                $contract->notes       = $request->notes;
                // $contract->status      = $request->status;
                $contract->save();

                return redirect()->route('contract.index')->with('success', __('Contract successfully updated!'));
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
     * @param \App\Contract $contract
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->can('Delete Contract'))
        {
            $contract =Contract::find($id);
            if($contract->created_by == \Auth::user()->ownerId())
            {

                $attechments = $contract->ContractAttechment()->get()->each;

                foreach($attechments->items as $attechment){
                    if (\Storage::exists('contract_attechment/'.$attechment->files)) {
                            unlink('storage/contract_attechment/'.$attechment->files);
                    }
                    $attechment->delete();
                }

                $contract->ContractComment()->get()->each->delete();
                $contract->ContractNote()->get()->each->delete();
                $contract->delete();

                return redirect()->route('contract.index')->with('success', __('Contract successfully deleted!'));
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




    public function descriptionStore($id, Request $request)
    {
        if(\Auth::user()->type == 'Owner')
        {
            $contract        =Contract::find($id);
            $contract->description = $request->description;
            $contract->save();
            return redirect()->back()->with('success', __('Descripation successfully saved.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }


    public function fileUpload($id, Request $request)
    {
        $contract = Contract::find($id);
        if($contract->created_by == \Auth::user()->ownerId())
        {
            $request->validate(['file' => 'required']);
            // $files = $request->file->getClientOriginalName();
            // $request->file->storeAs('contract_attechment', $files);



            $dir = 'contract_attechment/';
            $files = $request->file->getClientOriginalName();
            $path = Utility::upload_file($request,'file',$files,$dir,[]);
                if($path['flag'] == 1){
                    $file = $path['url'];
                }
                else{
                    return redirect()->back()->with('error', __($path['msg']));
                }


            // $request->file->storeAs('contract_attechment', $files);

            $file                 = ContractAttechment::create(
                [
                    'contract_id' => $request->contract_id,
                    'user_id' => \Auth::user()->id,
                    'files' => $files,
                ]
            );
            $return               = [];
            $return['is_success'] = true;
            $return['download']   = route(
                'contracts.file.download', [
                                            $contract->id,
                                            $file->id,
                                        ]
            );
            $return['delete']     = route(
                'contracts.file.delete', [
                                        $contract->id,
                                        $file->id,
                                    ]
            );

            // ActivityLog::create(
            //     [
            //         'user_id' => \Auth::user()->id,
            //         'contract_id' => $contract->id,
            //         'log_type' => 'Upload File',
            //         'remark' => json_encode(['file_name' => $file_name]),
            //     ]
            // );

            return response()->json(
                [
                    'is_success' => true,
                    'success' => __('Status successfully updated!'),
                ], 200
            );
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }

    }

    public function fileDownload($id, $file_id)
    {
            $contract = Contract::find($id);
            if($contract->created_by == \Auth::user()->ownerId())
            {
                $file = ContractAttechment::find($file_id);
                if($file)
                {
                    $file_path = storage_path('contract_attechment/' . $file->files);

                    // $files = $file->files;

                    return \Response::download(
                        $file_path, $file->files, [
                                      'Content-Length: ' . filesize($file_path),
                                  ]
                    );
                }
                else
                {
                    return redirect()->back()->with('error', __('File is not exist.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }

    }


    public function fileDelete($id, $file_id)
    {

            $contract = Contract::find($id);
            $file = ContractAttechment::find($file_id);
            if($file)
            {
                $path = storage_path('contract_attechment/' . $file->files);
                if(file_exists($path))
                {
                    \File::delete($path);
                }
                $file->delete();

                return redirect()->back()->with('success', __('Attechment successfully delete.'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('File is not exist.'),
                    ], 200
                );
            }

    }





    public function commentStore(Request $request ,$id)
    {
        if(\Auth::user()->can('Create Comment'))
        {
            $contract              = new ContractComment();
            $contract->comment     = $request->comment;
            $contract->contract_id = $id;
            $contract->user_id     = \Auth::user()->id;
            $contract->save();


            return redirect()->back()->with('success', __('comments successfully created!') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''))->with('status', 'comments');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function commentDestroy($id)
    {
        if(\Auth::user()->can('Delete Comment'))
        {
            if(\Auth::user()->type == 'Owner' || \Auth::user()->type == 'Client')
            {
                $contract = ContractComment::find($id);
                $contract->delete();

                return redirect()->back()->with('success', __('Comment successfully deleted!'));
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

    public function noteStore($id, Request $request)
    {
        if(\Auth::user()->can('Create Note'))
        {
            if(\Auth::user()->type == 'Owner' || \Auth::user()->type == 'Client')
            {
                $contract              = Contract::find($id);
                $notes                 = new ContractNote();
                $notes->contract_id    = $contract->id;
                $notes->note           = $request->note;
                $notes->user_id        = \Auth::user()->id;
                $notes->created_by     = \Auth::user()->ownerId();
                $notes->save();
                return redirect()->back()->with('success', __('Note successfully saved.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied'));
        }

    }


    public function noteDestroy($id)
    {
        if(\Auth::user()->can('Delete Note'))
        {
            $contract = ContractNote::find($id);
            if($contract->created_by == \Auth::user()->ownerId())
            {
                $contract->delete();

                return redirect()->back()->with('success', __('Note successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }





    public function copycontract($id)
    {
        if(\Auth::user()->can('Create Contract'))
        {
            $contract = Contract::find($id);
            $client       = User::where('type', '=', 'Client')->get()->pluck('name', 'id');
                $contractType = ContractType::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $date         = $contract->start_date . ' to ' . $contract->end_date;
                unset($contract->start_date);
                unset($contract->end_date);
                $contract->setAttribute('date', $date);

                return view('contracts.copy', compact('contract', 'contractType', 'client'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }


    public function copycontractstore(Request $request)
    {
        if(\Auth::user()->can('Create Contract'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'subject' => 'required',
                                   'value' => 'required',
                                   'type' => 'required',
                                   'date' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('contract.index')->with('error', $messages->first());
            }

            $date = explode(' to ', $request->date);

            $contract              = new Contract();
            $contract->name        = $request->name;
            $contract->client_name = $request->client_name;
            $contract->subject     = $request->subject;
            $contract->value       = $request->value;
            $contract->type        = $request->type;
            $contract->start_date  = $date[0];
            $contract->end_date    = $date[1];
            $contract->notes       = $request->notes;
            $contract->created_by  = \Auth::user()->ownerId();
            $contract->save();

            $settings  = \Utility::settings(\Auth::user()->ownerId());

            if(isset($settings['contract_notification']) && $settings['contract_notification'] ==1){
                $msg = 'New Invoice '.Auth::user()->contractNumberFormat($this->contractNumber()).'  created by  '.\Auth::user()->name.'.';

                \Utility::send_slack_msg($msg);
            }
            if(isset($settings['telegram_contract_notification']) && $settings['telegram_contract_notification'] ==1){
                $resp = 'New  Invoice '.Auth::user()->contractNumberFormat($this->contractNumber()).'  created by  '.\Auth::user()->name.'.';
                \Utility::send_telegram_msg($resp);
            }

            return redirect()->route('contract.index')->with('success', __('Contract successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }





    public function printContract($id)
    {
        $contract  = Contract::findOrFail($id);
        $settings = Utility::settings();
        $client   = $contract->client_name;
        //Set your logo
        // $logo         = asset(\Storage::url('logo/'));
        $logo=\App\Models\Utility::get_file('logo/');

            $dark_logo    = Utility::get_superadmin_logo('dark_logo');
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));

        return view('contracts.contract_view', compact('contract','client','img','settings'));

    }


    public function pdffromcontract($contract_id)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($contract_id);

        $contract  = Contract::findOrFail($id);


            if(\Auth::check())
            {
                $usr=\Auth::user();
            }
            else
            {

                $usr=User::where('id',$contract->created_by)->first();

            }
            // $logo         = asset(\Storage::url('logo/'));
            $logo=\App\Models\Utility::get_file('logo/');

            $dark_logo    = Utility::getValByName('dark_logo');
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));


        return view('contracts.template', compact('contract','usr','img'));

    }




    public function signature($id)
    {
        $contract = Contract::find($id);

        return view('contracts.signature', compact('contract'));
    }


    public function signatureStore(Request $request)
    {
        $contract              = Contract::find($request->contract_id);

        if(\Auth::user()->type == 'Owner'){
            $contract->owner_signature       = $request->owner_signature;
        }
        if(\Auth::user()->type == 'Client'){
            $contract->client_signature       = $request->client_signature;
        }

        $contract->save();

        return response()->json(
            [
                'Success' => true,
                'message' => __('Contract Signed successfully'),
            ], 200
        );

    }


    public function sendmailContract($id,Request $request)
    {

        if(\Auth::user()->type == 'Owner')
        {
            $contract              = Contract::find($id);
            $contractArr = [
                'contract_id' => $contract->id,
            ];
            $client = User::find($contract->client_name);
            $estArr = [
                'email' => $client->email,
                'contract_subject' => $contract->subject,
                'contract_client' => $client->name,
                'contract_start_date' => $contract->start_date,
                'contract_end_date' =>$contract->end_date ,
            ];
            // Send Email
            $resp = Utility::sendEmailTemplate('contract', [$client->id => $client->email], $estArr);
            return redirect()->route('contract.show', $contract->id)->with('success', __('Mail Send successfully!') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }



}

