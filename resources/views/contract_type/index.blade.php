@extends('layouts.admin')

@section('title')
    {{__('Manage Contract Type')}}
@endsection

@section('action-button')
    
    <div class="row align-items-center m-1">
        @can('Create Contract Type')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Contract')}}" data-url="{{route('contract_type.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
 
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Setup')}}</li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Contract Type')}}</li>
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
                                    <th>{{__('Contract Type')}}</th>
                                    <th width="250px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contractTypes as $contractType)
                                <tr>
                                    <td>{{ $contractType->name }}</td>
                                    <td class="Action">
                                        <span>
                                        @can('Edit Contract Type')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="md" data-url="{{ URL::to('contract_type/'.$contractType->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Contract Type')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('Delete Contract Type')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['contract_type.destroy', $contractType->id]]) !!}
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
