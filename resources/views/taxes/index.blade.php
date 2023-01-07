@extends('layouts.admin')

@section('title')
    {{__('Manage Tax Rates')}}
@endsection

@section('action-button')

    <div class="row align-items-center m-1">
        @can('Create Tax')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Tax Rate')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Tax Rate')}}" data-url="{{route('taxes.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Setup')}}</li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Tax Rates')}}</li>
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
                                    <th>{{__('Tax Name')}}</th>
                                    <th>{{__('Rate %')}}</th>
                                    <th width="150px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($taxes as $tax)
                                    <tr>
                                        <td>{{ $tax->name }}</td>
                                        <td>{{ $tax->rate }}</td>
                                        <td class="Action">
                                            <span>
                                            @can('Edit Tax')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="lg" data-url="{{ URL::to('taxes/'.$tax->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Tax Rate')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Tax Rate')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                                @endcan
                                                @can('Delete Tax')

                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['taxes.destroy', $tax->id]]) !!}
                                                            <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Tax Rate')}}">
                                                               <span class="text-white"> <i class="ti ti-trash"></i></span>
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
