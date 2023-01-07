@extends('layouts.admin')

@section('title')
    {{__('Manage Custom Fields')}}
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        @can('Create Custom Field')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Custom Field')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Custom Field')}}" data-url="{{route('custom_fields.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Setup')}}</li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Custom Fields')}}</li>
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
                                    <th>{{__('Custom Field')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Module')}}</th>
                                    <th width="250px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($custom_fields as $custom_field)
                                <tr>
                                    <td>{{ $custom_field->name }}</td>
                                    <td>{{ ucfirst($custom_field->type) }}</td>
                                    <td>{{ ucfirst($custom_field->module) }}</td>
                                    <td class="Action">
                                        <span>
                                        @can('Edit Custom Field')
                                                <div class="action-btn btn-info  ms-2">
                                                    <a href="#" data-size="md" data-url="{{ URL::to('custom_fields/'.$custom_field->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Custom Field')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Custom Field')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('Delete Custom Field')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['custom_fields.destroy', $custom_field->id]]) !!}
                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                           <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                    {!! Form::close() !!}
                                                </div>
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
