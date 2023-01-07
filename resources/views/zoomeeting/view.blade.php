<div class="modal-body">
    <div class="tab-content tab-bordered">
    <div class="tab-pane fade show active" id="tab-1" role="tabpanel">
        <div class="row">
            <div class="col-lg-8">
            <div class="card">
                <div class="card-header card-body">
                    <dl class="row">
                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                        <dd class="col-sm-8"><span class="text-sm">{{ $zoommeeting-> title }}</span></dd>


                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Meeting Id')}}</span></dt>
                        <dd class="col-sm-8"><span class="text-sm">{{$zoommeeting->meeting_id}}</span></dd>

                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Lead')}}</span></dt>
                        <dd class="col-sm-8"><span class="text-sm">{{ !empty($zoommeeting->lead_name)?$zoommeeting->lead_name:'-' }}</span></dd>

                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Client')}}</span></dt>
                        <dd class="col-sm-8"><span class="text-sm">{{!empty($zoommeeting->client_name)?$zoommeeting->client_name:'-'}}</span></dd>

                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Start Date')}}</span></dt>
                        <dd class="col-sm-8"><span class="text-sm">{{ \Auth::user()->dateFormat($zoommeeting->start_date) }}</span></dd>

                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Duration')}}</span></dt>
                        <dd class="col-sm-8"><span class="text-sm">{{ $zoommeeting->duration }}</span></dd>

                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Start URl')}}</span></dt>
                        <dd class="col-sm-8"><span class="text-sm">@if($zoommeeting->created_by == \Auth::user()->id && $zoommeeting->checkDateTime())
                                <a href="{{$zoommeeting->start_url}}" target="_blank"> {{__('Start meeting')}} <i class="fas fa-external-link-square-alt "></i></a>
                                @elseif($zoommeeting->checkDateTime())
                                    <a href="{{$zoommeeting->join_url}}" target="_blank"> {{__('Join meeting')}} <i class="fas fa-external-link-square-alt "></i></a>
                                @else
                                    -
                                @endif</span></dd>

                                <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Status')}}</span></dt>
                                <dd class="col-sm-8">@if($zoommeeting->checkDateTime())
                                    @if($zoommeeting->status == 'waiting')
                                        <span class="badge rounded p-2 px-3 bg-info">{{ucfirst($zoommeeting->status)}}</span>
                                    @else
                                        <span class="badge rounded p-2 px-3 bg-success">{{ucfirst($zoommeeting->status)}}</span>
                                    @endif
                                @else
                                    <span class="badge rounded p-2 px-3 bg-danger">{{__("End")}}</span>
                                @endif
                                </dd>

                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-header card-footer ">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Assigned Client')}}</span></dt>
                                        <dd class="col-sm-12"><span class="text-sm">{{ !empty($zoommeeting->client_name)?$zoommeeting->client_name:''}}</span></dd>

                                        <dt class="col-sm-12"><span class="h6 text-sm mb-0">Created</span></dt>
                                        <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($zoommeeting->created_at)}}</span></dd>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
</div>
 



