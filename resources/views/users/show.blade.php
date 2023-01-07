@extends('layouts.admin')

@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('title')
    {{ $user->name.__("'s Detail") }}
@endsection

@push('head')
    <!-- <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}"> -->

@endpush

@push('script')
    <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script>
@endpush

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page"><a href="{{route('users')}}">{{__('User')}}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{__('User Detail')}}</li>
@endsection


@section('content')
    <div class="row">
        <div class="col-12 mb-2">
            <h4 class="h4 font-weight-400 float-left">{{__('Deals')}}</h4>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Deals')}}</h6>
                            <h4 class="text-primary">{{ $cnt_deal['total'] }} / {{$cnt_deal['cnt_total']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rocket bg-success text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('This Month Total Deals')}}</h6>
                            <h4 class="text-info">{{ $cnt_deal['this_month'] }} / {{$cnt_deal['this_month']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rocket bg-info text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('This Week Total Deals')}}</h6>
                            <h4 class="text-warning">{{ $cnt_deal['cnt_this_week'] }} / {{$cnt_deal['cnt_this_week']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rocket bg-warning text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Last 30 Days Total Deals')}}</h6>
                            <h4 class="text-danger">{{ $cnt_deal['cnt_this_week'] }} / {{$cnt_deal['cnt_this_week']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rocket bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card">

                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('Stage')}}</th>
                                    <th>{{__('Tasks')}}</th>
                                    <th>{{__('Users')}}</th>
                                    @if(Gate::check('Edit Deal') ||  Gate::check('Delete Deal'))
                                        <th width="300px">{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($deals as $deal)
                                    <tr>
                                        <td>{{ $deal->name }}</td>
                                        <td>{{\Auth::user()->priceFormat($deal->price)}}</td>
                                        <td>{{ $deal->stage->name }}</td>
                                        <td>{{count($deal->tasks)}}/{{count($deal->complete_tasks)}}</td>
                                        <td>
                                            @foreach($deal->users as $user)
                                                <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" target="_blank">
                                                    <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                </a>
                                            @endforeach
                                        </td>
                                        @if(\Auth::user()->type != 'Client')
                                            <td class="Action">
                                                <span>
                                                @can('View Deal')
                                                        @if($deal->is_active)
                                                            <div class="action-btn bg-warning ms-2">
                                                                <a href="{{route('deals.show',$deal->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{__('View')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
                                                            </div>


                                                        @endif
                                                    @endcan
                                                    @can('Edit Deal')
                                                        <div class="action-btn btn-info ms-2">
                                                            <a href="#" data-size="lg" data-url="{{ URL::to('deals/'.$deal->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Deal')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Deal')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete Deal')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['deals.destroy', $deal->id]]) !!}
                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Deal')}}">
                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endif
                                                </span>
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
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{__('Leads')}}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Subject')}}</th>
                                    <th>{{__('Stage')}}</th>
                                    <th>{{__('Users')}}</th>
                                    @if(Auth::user()->type != 'Client')
                                        <th width="300px">{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($leads) > 0)
                                    @foreach ($leads as $lead)
                                        <tr>
                                            <td>{{ $lead->name }}</td>
                                            <td>{{ $lead->subject }}</td>
                                            <td>{{ $lead->stage->name }}</td>
                                            <td>
                                                @foreach($lead->users as $user)
                                                    <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" target="_blank">
                                                        <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                    </a>
                                                @endforeach
                                            </td>
                                            @if(Auth::user()->type != 'Client')
                                                <td class="Action">
                                                    <span>
                                                    @can('View Lead')
                                                            @if($lead->is_active)
                                                                <div class="action-btn bg-warning ms-2">
                                                                    <a href="{{route('leads.show',$lead->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{__('View')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
                                                                </div>
                                                            @endif
                                                        @endcan
                                                        @can('Edit Lead')
                                                            <div class="action-btn btn-info ms-2">
                                                                <a href="#" data-size="lg" data-url="{{ URL::to('leads/'.$lead->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Lead')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Lead')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete Lead')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leads.destroy', $lead->id]]) !!}
                                                                    <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Lead')}}">
                                                                       <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endif
                                                    </span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="font-style">
                                        <td colspan="5" class="text-center">{{ __('No data available in table') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-2">
            <h4 class="h4 font-weight-400 float-left">{{__('MDF')}}</h4>
        </div>
        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total MDF')}}</h6>
                            <h4 class="text-primary">{{ $cnt_mdf['total'] }} / {{$cnt_mdf['cnt_total']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt bg-success text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('This Month Total MDF')}}</h6>
                            <h4 class="text-info">{{ $cnt_mdf['this_month'] }} / {{$cnt_mdf['this_month']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt bg-info text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('This Week Total MDF')}}</h6>
                            <h4 class="text-warning">{{ $cnt_mdf['this_week'] }} / {{$cnt_mdf['this_week']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt bg-warning text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Last 30 Days Total MDF')}}</h6>
                            <h4 class="text-danger">{{ $cnt_mdf['last_30days'] }} / {{$cnt_mdf['last_30days']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{__('MDF')}}</th>
                                    <th>{{__('Date Created')}}</th>
                                    <th>{{__('Request From')}}</th>
                                    <th>{{__('Requested Amount')}}</th>
                                    <th>{{__('Approved Amount')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Sub Type')}}</th>
                                    @if(\Auth::user()->type != 'Client')
                                        <th>{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mdfs as $mdf)
                                    <tr>
                                        <td class="Id">
                                            @can('View MDF')
                                                @if(!empty($mdf->approvedAmt) || Auth::user()->type == 'Owner')
                                                    <a class="btn  btn-outline-primary" href="{{route('mdf.show',$mdf->id)}}"><i class="fas fa-file-invoice"></i> {{ Auth::user()->mdfNumberFormat($mdf->mdf_id) }}</a>
                                                @else
                                                    {{ Auth::user()->mdfNumberFormat($mdf->mdf_id) }}
                                                @endif
                                            @else
                                                {{ Auth::user()->mdfNumberFormat($mdf->mdf_id) }}
                                            @endcan
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($mdf->created_at) }}</td>
                                        <td>{{ $mdf->user->name }}</td>
                                        <td>{{ \Auth::user()->priceFormat($mdf->amount) }}</td>
                                        <td>{{ \Auth::user()->priceFormat((!empty($mdf->approvedAmt->amount) ? $mdf->approvedAmt->amount : 0)) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($mdf->date) }}</td>
                                        <td>{{ $mdf->statusDetail->name }}</td>
                                        <td>{{ $mdf->typeDetail->name }}</td>
                                        <td>{{!empty( $mdf->subTypeDetail)?$mdf->subTypeDetail->name:''}}</td>
                                        @if(\Auth::user()->type != 'Client')
                                            <td class="Action">
                                                <span>
                                                @if($mdf->is_complete == '0')
                                                        @if(\Auth::user()->can('Create MDF Payment') && \Auth::user()->type == 'Owner')
                                                            @if(!empty($mdf->approvedAmt))
                                                                <div class="action-btn bg-info ms-2">
                                                                    <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{__('Approved')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Approved')}}"><i class="ti ti-circle-check text-white"></i></a>

                                                                </div>
                                                            @else

                                                                <div class="action-btn bg-success ms-2">
                                                                    <a href="#" data-size="lg" data-url="{{ route('mdf.payments.approved',[$mdf->id,'approved'])}}" data-ajax-popup="true" data-title="{{__('Approved Amount')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Approved Amount')}}" ><i class="ti ti-circle-check text-white"></i></a>
                                                                </div>

                                                            @endif
                                                        @endif
                                                    @endif
                                                    @can('View MDF')
                                                        @if(!empty($mdf->approvedAmt) || Auth::user()->type == 'Owner')
                                                            <div class="action-btn bg-warning ms-2">
                                                                <a href="{{route('mdf.show',$mdf->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{ __('View') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
                                                            </div>


                                                        @endif
                                                    @endcan
                                                    @if($mdf->is_complete == '0')
                                                        @can('Edit MDF')
                                                            <div class="action-btn btn-primary ms-2">
                                                                <a href="#" data-size="lg" data-url="{{ route('mdf.edit',$mdf->id) }}" data-ajax-popup="true" data-title="{{__('Edit MDF')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit MDF')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete MDF')
                                                            @if(empty($mdf->approvedAmt) || Auth::user()->type == 'Owner')
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['mdf.destroy', $mdf->id]]) !!}
                                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm">
                                                                           <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endif
                                                        @endcan
                                                    @endif
                                                </span>
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
