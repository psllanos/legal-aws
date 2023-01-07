@extends('layouts.admin')

@section('title')
    {{__('Manage MDF Status')}}
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        @can('Create MDF Status')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create MDF Status')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create MDF Status')}}" data-url="{{route('mdf_status.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Setup')}}</li>
    <li class="breadcrumb-item active" aria-current="page">{{__('MDF Status')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card table-card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{__('Status')}}</th>
                                    <th width="250px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statuses as $status)
                                    <tr>
                                        <td>{{ $status->name }}</td>
                                        <td class="Action">
                                            <span>
                                            @can('Edit MDF Status')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-size="md" data-url="{{ route('mdf_status.edit',$status->id) }}" data-ajax-popup="true" data-title="{{__('Edit Custom Field')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Custom Field')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                    </div>
                                                @endcan
                                                @if(count($statuses) > 1)
                                                    @can('Delete MDF Status')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['mdf_status.destroy', $status->id]]) !!}
                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                            {!! Form::close() !!}
                                                    </div>
                                                    @endcan
                                                @endif
                                            </span>
                                        </td>
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
