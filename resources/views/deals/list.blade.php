@extends('layouts.admin')

@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('title')
    {{__('Manage Deals')}} @if($pipeline) - {{$pipeline->name}} @endif
@endsection

@push('head')
    <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}">
@endpush

@push('script')
    <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script>
    <script>
        $(document).on("change", "#change-pipeline select[name=default_pipeline_id]", function () {
            $('#change-pipeline').submit();
        });
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Deals')}}</li>
@endsection

@section('action-button')
    <div class="row m-1 align-items-center">
        @if($pipeline)
            <div class="col-auto pe-0">
                {{ Form::open(array('route' => 'deals.change.pipeline','id'=>'change-pipeline','class'=>'mr-2')) }}
                {{ Form::select('default_pipeline_id', $pipelines,$pipeline->id, array('class' => 'form-control custom-form-select','id'=>'default_pipeline_id')) }}
                {{ Form::close() }}
            </div>
        @endif

        <div class="col-auto pe-0">
            <a href="{{ route('deals.index') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Kanban View')}}" class="btn btn-sm btn-primary btn-icon m-1"><i class="ti ti-table"></i> </a>
        </div>
        @can('Create Deal')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1 " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Deal')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Deal')}}" data-url="{{route('deals.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    @if($pipeline)
        <div class="row">
            <div class="col-xl-3 col-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-20">{{__('Total Deals')}}</h6>
                                <h3 class="text-primary">{{ $cnt_deal['total'] }}</h3>
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
                                <h3 class="text-info">{{ $cnt_deal['this_month'] }}</h3>
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
                                <h3 class="text-warning">{{ $cnt_deal['this_week'] }}</h3>
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
                                <h3 class="text-danger">{{ $cnt_deal['last_30days'] }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rocket bg-danger text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card table-card">
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table mb-0 pc-dt-simple">
                                <thead>
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Price')}}</th>
                                        <th>{{__('Stage')}}</th>
                                        <th>{{__('Tasks')}}</th>
                                        <th>{{__('Users')}}</th>
                                        {{-- @if(Gate::check('Edit Deal') ||  Gate::check('Delete Deal')) --}}
                                            <th width="300px">{{__('Action')}}</th>
                                        {{-- @endif --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($deals) > 0)
                                        @foreach ($deals as $deal)
                                            <tr>
                                                <td>{{ $deal->name }}</td>
                                                <td>{{\Auth::user()->priceFormat($deal->price)}}</td>
                                                <td>{{ $deal->stage->name }}</td>
                                                <td>{{count($deal->tasks)}}/{{count($deal->complete_tasks)}}</td>
                                                <td>
                                                    @foreach($deal->users as $user)
                                                    <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" target="_blank">
                                                        <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="25">
                                                    </a>
                                                    @endforeach
                                                </td>
                                                @if(\Auth::user()->type == 'Client' || \Auth::user()->type == 'Owner')
                                                    <td class="Action">
                                                        <span>
                                                        @can('View Deal')
                                                                @if($deal->is_active)
                                                                     <div class="action-btn bg-warning ms-2">
                                                                        <a href="{{route('deals.show',$deal->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{__('View Deal')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View Deal')}}"><i class="ti ti-eye text-white"></i></a>
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
                                    @else
                                        <tr class="font-style">
                                            <td colspan="6" class="text-center">{{ __('No data available in table') }}</td>
                                        </tr>
                                    @endif


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
