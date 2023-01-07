<?php

namespace App\Http\Controllers;

use App\Models\ZoomMeeting;
use App\Models\User;
use App\Models\Lead;
use App\Models\Utility;
use App\Traits\ZoomMeetingTrait;
use Illuminate\Http\Request;

class ZoomMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    use ZoomMeetingTrait;
    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    const MEETING_URL="https://api.zoom.us/v2/";

    public function index()
    {

            $user=\Auth::user();
           if($user->type=='Owner')
            {
                $zoommeetings    = ZoomMeeting::where('created_by', \Auth::user()->ownerId())->get();
            }
            if($user->type=='Employee')
            {
                $zoommeetings    = ZoomMeeting::where('user_id', \Auth::user()->id)->get();
            }
            if($user->type=='Client')
            {
                $zoommeetings    = ZoomMeeting::where('client_id', \Auth::user()->id)->get();
            }
            // $this->statusUpdate();
            return view('zoomeeting.index',compact('zoommeetings'));



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         if(\Auth::user()->type == 'Owner')
        {
            $user=\Auth::user();
            $clients = User::where('created_by', '=', $user->ownerId())->where('type', '=', 'Client')->get()->pluck('name', 'id');
            $clients->prepend(__('Select Client'), '');

            $users = User::where('created_by', '=', \Auth::user()->ownerId())->where('type', '=', 'Employee')->get()->pluck('name', 'id');
            $users->prepend(__('Select User'), '');

            $Leads = Lead::where('created_by', '=', $user->ownerId())->get()->pluck('name', 'id');
            $Leads->prepend(__('Select Lead'), '');

            return view('zoomeeting.create', compact('clients','Leads','users'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = \Validator::make(
            $request->all(), [
                               'title' => 'required',
                                'start_date' => 'required',
                                'duration'=>'required|integer',

                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

    $settings = Utility::settings();

    if($settings['zoom_api_key']!="" && $settings['zoom_secret_key']!="")
    {
         if(!empty($request->lead_id)|| !empty($request->client_id) || !empty($request->user_id))
        {
            $data['topic'] = $request->title;
            $data['start_time'] = date('y:m:d H:i:s',strtotime($request->start_date));
            $data['duration'] = (int)$request->duration;
            $data['password'] = $request->password;
            $data['host_video'] = 0;
            $data['participant_video'] = 0;
            $meeting_create = $this->createmitting($data);
            \Log::info('Meeting');
            \Log::info((array)$meeting_create);
            if(isset($meeting_create['success']) &&  $meeting_create['success'] == true){
                $meeting_id = isset($meeting_create['data']['id'])?$meeting_create['data']['id']:0;
                $start_url = isset($meeting_create['data']['start_url'])?$meeting_create['data']['start_url']:'';
                $join_url = isset($meeting_create['data']['join_url'])?$meeting_create['data']['join_url']:'';
                $status = isset($meeting_create['data']['status'])?$meeting_create['data']['status']:'';

                    $user      = \Auth::user();

                    $zoomeeting              = new ZoomMeeting();
                    $zoomeeting->title        = $request->title;
                    $zoomeeting->meeting_id       = $meeting_id;
                    if($request->lead_id != '')
                    {
                        $zoomeeting->lead_id     = $request->lead_id;
                    }
                    if($request->client_id != '')
                    {
                        $zoomeeting->client_id     = $request->client_id;
                    }
                    if($request->user_id != '')
                    {
                        $zoomeeting->user_id     = $request->user_id;
                    }

                    $zoomeeting->password = $request->password;
                    $zoomeeting->start_date    = date('y:m:d H:i:s',strtotime($request->start_date));
                    $zoomeeting->duration    = (int)$request->duration;
                    $zoomeeting->start_url=$start_url;
                    $zoomeeting->join_url=$join_url;
                    $zoomeeting->status=$status;
                    $zoomeeting->created_by  = $user->ownerId();
                    $zoomeeting->save();

            return redirect()->back()->with('success', __('ZoomMeeting Successfully create..'));
        }
        }
        else
        {
            return redirect()->back()->with('error', __('Please select Users,Clients or Leads..'));
        }
    }
    else
    {
        return redirect()->back()->with('error', __('Please Add Zoom Settings'));
    }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function show(ZoomMeeting $zoommeeting)
    {
      if($zoommeeting->created_by == \Auth::user()->ownerId())
        {

            return view('zoomeeting.view', compact('zoommeeting'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function edit(ZoomMeeting $zoommeeting)
    {
        if($zoommeeting->created_by == \Auth::user()->ownerId())
            {

                $user=\Auth::user();
                $clients = User::where('created_by', '=', $user->ownerId())->where('type', '=', 'Client')->get()->pluck('name', 'id');
                $clients->prepend(__('Select Client'), '');

                $Leads = Lead::where('created_by', '=', $user->ownerId())->get()->pluck('name', 'id');
                $Leads->prepend(__('Select Lead'), '');


                return view('zoomeeting.edit', compact('zoommeeting','Leads', 'clients'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ZoomMeeting $zoommeeting)
    {

        if($zoommeeting->created_by == \Auth::user()->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'title' => 'required',
                               'meeting_id' => 'required',

                                'start_date' => 'required',
                                'duration'=>'required|integer',
                                'start_url'=>'required',
                                'join_url'=>'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                 $user=\Auth::user();
                $zoommeeting->title        = $request->title;
                $zoommeeting->meeting_id       = $request->meeting_id;
                $zoommeeting->lead_id     = $request->lead_id;
                if($request->lead_id != '')
                {
                    $zoommeeting->lead_id     = $request->lead_id;
                }
                if($request->client_id != '')
                {
                    $zoommeeting->client_id     = $request->client_id;
                }
                if($request->user_id != '')
                {
                    $zoommeeting->user_id     = $request->user_id;
                }

                $zoommeeting->password = $request->password;
                $zoommeeting->start_date    = $request->start_date;
                $zoommeeting->duration    = $request->duration;
                $zoommeeting->start_url=$request->start_url;
                $zoommeeting->join_url=$request->join_url;
                $zoommeeting->created_by  = $user->ownerId();
                $zoommeeting->save();



                return redirect()->back()->with('success', __('Zoom Meeting successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(ZoomMeeting $zoommeeting)
    {
        if($zoommeeting->created_by == \Auth::user()->ownerId())
            {

                $zoommeeting->delete();

                return redirect()->route('zoommeeting.index')->with('success', __('Zoom Meeting successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
    }

     public function calender()
    {
        $user=\Auth::user();
        if($user->type=='Owner')
        {
            $zoommeetings    = ZoomMeeting::where('created_by', \Auth::user()->ownerId())->get();
        }
        if($user->type=='Employee')
        {
            $zoommeetings    = ZoomMeeting::where('user_id', \Auth::user()->id)->get();
        }
        if($user->type=='Client')
        {
            $zoommeetings    = ZoomMeeting::where('client_id', \Auth::user()->id)->get();
        }
        $transdate = date('Y-m-d', time());

        $arrMeeting[]='';

        foreach($zoommeetings as $zoommeeting)
            {

                 $calandar[] = [
                        'id' => $zoommeeting['id'],
                        'title' => $zoommeeting['title'],
                        'start' => $zoommeeting['start_date'],
                        'url' => route(
                            'zoommeeting.show', [
                                                  $zoommeeting['id'],
                                              ]
                        ),
                        'className' => ($zoommeeting['status']) ? 'event-success border-success' : 'event-warning border-warning',
                    ];

            }

          return view('zoomeeting.calendar', compact('calandar','transdate'));
    }

    public function statusUpdate(){
        $meetings = ZoomMeeting::where('status','waiting')->where('created_by',\Auth::user()->id)->pluck('meeting_id');
        foreach($meetings as $meeting){
            $data = $this->get($meeting);
            if(isset($data['data']) && !empty($data['data'])){
                $meeting = ZoomMeeting::where('meeting_id',$meeting)->update(['status'=>$data['data']['status']]);
            }
        }

    }
}
