@extends('layouts.admin')

@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('title')
    {{__('Manage Leads')}} @if($pipeline) - {{$pipeline->name}} @endif
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
    <li class="breadcrumb-item active" aria-current="page">{{__('Leads')}}</li>
@endsection


@section('action-button')

    <div class="row m-1 align-items-center">
        @if($pipeline)
            <div class="col-auto pe-0">
                {{ Form::open(array('route' => 'deals.change.pipeline','id'=>'change-pipeline','class'=>'mr-2')) }}
                {{ Form::select('default_pipeline_id', $pipelines,$pipeline->id, array('class' => 'form-select custom-form-select','id'=>'default_pipeline_id')) }}
                {{ Form::close() }}
            </div>
        @endif

        <div class="col-auto pe-0">
            <a href="{{ route('leads.index') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Kanban View')}}" class="btn btn-sm btn-primary btn-icon m-1"><i class="ti ti-table"></i> </a>
        </div>
         @can('Create Lead')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1 " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Lead')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Lead')}}" data-url="{{route('leads.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>

@endsection

@section('content')
    @if($pipeline)
        <div class="row">
            <div class="col-md-12">
                <div class="card table-card">
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table mb-0 pc-dt-simple">
                                <thead>
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Subject')}}</th>
                                        <th>{{__('Stage')}}</th>
                                        <th>{{__('Users')}}</th>
                                        <th width="300px">{{__('Action')}}</th>
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
                                                        <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="25">
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
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['leads.destroy', $lead->id],'id'=>'delete-form-'.$lead->id]) !!}
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
