@extends('layouts.admin')

@section('title')
    {{__('Manage Contract')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Contract')}}</li>
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        @can('Create Contract')
            <div class="btn btn-sm btn-primary btn-icon">
                <a href="#" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Contract')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Contract')}}" data-url="{{route('contract.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Contracts')}}</h6>
                            <h3 class="text-primary">{{ $cnt_contract['total'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-success text-white"></i>
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
                            <h6 class="m-b-20">{{__('This Month Total Contracts')}}</h6>
                            <h3 class="text-info">{{ $cnt_contract['this_month'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-info text-white"></i>
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
                            <h6 class="m-b-20">{{__('This Week Total Contracts')}}</h6>
                            <h3 class="text-warning">{{ $cnt_contract['this_week'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-warning text-white"></i>
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
                            <h6 class="m-b-20">{{__('Last 30 Days Total Contracts')}}</h6>
                            <h3 class="text-danger">{{ $cnt_contract['last_30days'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
                <div class="card table-card">
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table mb-0 pc-dt-simple">
                                <thead>
                                    <tr>
                                        <th width="60px">{{__('Contract')}}</th>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Client Name')}}</th>
                                        <th>{{__('Value')}}</th>
                                        <th>{{__('Type')}}</th>
                                        <th>{{__('Start Date')}}</th>
                                        <th>{{__('End Date')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th width="250px">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contracts as $contract)
                                        <tr>
                                            <td class="Id">
                                                    <a href="{{route('contract.show',$contract->id)}}" class="btn btn-outline-primary">{{ Auth::user()->contractNumberFormat($contract->id) }}</a>
                                            </td>
                                            <td>{{ $contract->name }}</td>
                                            <td>{{ $contract->client->name }}</td>
                                            <td>{{ Auth::user()->priceFormat($contract->value) }}</td>
                                            <td>{{ $contract->contract_type->name }}</td>
                                            <td>{{ Auth::user()->dateFormat($contract->start_date) }}</td>
                                            <td>{{ Auth::user()->dateFormat($contract->end_date) }}</td>
                                            <td>
                                                @if($contract->status == 'accept')
                                                    <span class="status_badge badge bg-primary  p-2 px-3 rounded">{{__('Accept')}}</span>
                                                @elseif($contract->status == 'decline')
                                                    <span class="status_badge badge bg-danger p-2 px-3 rounded">{{ __('Decline') }}</span>
                                                @elseif($contract->status == 'pending')
                                                    <span class="status_badge badge bg-warning p-2 px-3 rounded">{{ __('Pending') }}</span>
                                                @endif
                                            </td>
                                            <td class="Action">
                                                <span>
                                                    @can('Create Contract')
                                                        @if(\Auth::user()->type == 'Owner' && $contract->status == 'accept')
                                                            <div class="action-btn btn-primary ms-2">
                                                                <a href="#" data-size="lg" data-url="{{route('contracts.copy',$contract->id)}}"data-ajax-popup="true" data-title="{{__('Duplicate')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Duplicate')}}" ><i class="ti ti-copy text-white"></i></a>
                                                            </div>
                                                        @endif
                                                    @endcan
                                                        @if(\Auth::user()->type=='Owner' || \Auth::user()->type=='Client')
                                                            <div class="action-btn btn-warning ms-2">
                                                                <a href="{{route('contract.show',$contract->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}" ><i class="ti ti-eye text-white"></i></a>
                                                            </div>
                                                        @endif
                                                    @can('Edit Contract')
                                                        <div class="action-btn btn-info ms-2">
                                                            <a href="#" data-size="lg" data-url="{{ URL::to('contract/'.$contract->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Contract')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Contract')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete Contract')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['contract.destroy', $contract->id]]) !!}
                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Contract')}}">
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
