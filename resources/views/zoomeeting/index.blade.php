@extends('layouts.admin')

@section('title')
    {{__('Manage Zoom Meetings')}}
@endsection

@section('action-button')

            <a href="{{ route('zoommeeting.calender') }}" class="btn btn-sm btn-primary btn-icon"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Calendar View')}}"><i class="ti ti-calendar"></i></a>
        @if(\Auth::user()->type=='Owner')
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Zoom Meeting')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Zoom Meeting')}}" data-url="{{route('zoommeeting.create')}}"><i class="ti ti-plus text-white"></i></a>
        @endif
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Zoom Meetings')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{__('Title')}}</th>
                                    @if(\Auth::user()->type=='Employee' || \Auth::user()->type=='Owner')
                                    <th>{{__('Lead Name')}}</th>
                                    @endif
                                    @if(\Auth::user()->type=='Owner')
                                    <th>{{__('Client')}}</th>
                                    <th>{{__('User')}}</th>
                                    @endif
                                    <th>{{__('Meeting Time')}}</th>
                                    <th>{{__('Duration')}}</th>
                                    <th>{{__('Join URl')}}</th>
                                    <th>{{__('Status')}}</th>
                                    @if(\Auth::user()->type=='Owner')
                                    <th width="150px">{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zoommeetings as $zoommeeting)
                                    <tr>
                                        <td>{{$zoommeeting->title}}</td>
                                        @if(\Auth::user()->type=='Employee' || \Auth::user()->type=='Owner')
                                        <td>{{$zoommeeting->lead_name}}</td>
                                        @endif
                                        @if(\Auth::user()->type=='Owner')
                                        <td>{{$zoommeeting->client_name}}</td>
                                        <td>{{$zoommeeting->user_name}}</td>
                                        @endif
                                        <td>{{$zoommeeting->start_date}}</td>
                                        <td>{{$zoommeeting->duration}} {{__("Minutes")}}</td>

                                        <td>@if($zoommeeting->created_by == \Auth::user()->id && $zoommeeting->checkDateTime())
                                            <a href="{{$zoommeeting->start_url}}" target="_blank"> {{__('Start meeting')}} <i class="fas fa-external-link-square-alt "></i></a>
                                            @elseif($zoommeeting->checkDateTime())
                                                <a href="{{$zoommeeting->join_url}}" target="_blank"> {{__('Join meeting')}} <i class="fas fa-external-link-square-alt "></i></a>
                                            @else

                                            @endif</td>
                                                    <td>
                                                          @if($zoommeeting->checkDateTime())
                                                            @if($zoommeeting->status == 'waiting')
                                                                <span class="badge bg-info p-2 px-2 rounded">{{ucfirst($zoommeeting->status)}}</span>
                                                            @else
                                                                <span class="badge bg-success p-2 px-2 rounded">{{ucfirst($zoommeeting->status)}}</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-danger p-2 px-3 rounded">{{__("End")}}</span>
                                            @endif
                                        </td>
                                    @if(\Auth::user()->type=='Owner')
                                        <td>
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['zoommeeting.destroy', $zoommeeting->id]]) !!}
                                                    <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                       <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                {!! Form::close() !!}
                                            </div>

                                        </td>

                                    @endif
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<!-- <script src="{{url('custom/libs/bootstrap-daterangepicker/daterangepicker.js')}}"></script> -->

@endpush
