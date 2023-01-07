
@extends('layouts.admin')

@section('title')
    {{ $client->name.__("'s Detail") }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('clients.index')}}">{{__('Client')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Client Detail')}}</li>
@endsection

@section('content')
    {{--Invoices--}}
    <div class="row">
        <div class="col-12 mb-2">
            <h4 class="h4 font-weight-400 float-left">{{__('Invoices')}}</h4>
        </div>
        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Invoice')}}</h6>
                            <h4 class="text-primary">{{ $cnt_invoice['total'] }} / {{$cnt_invoice['cnt_total']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice bg-success text-white"></i>
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
                            <h6 class="m-b-20">{{__('This Month Total Invoice')}}</h6>
                            <h4 class="text-info">{{ $cnt_invoice['this_month'] }} / {{$cnt_invoice['cnt_this_month']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice bg-info text-white"></i>
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
                            <h6 class="m-b-20">{{__('This Week Total Invoice')}}</h6>
                            <h4 class="text-warning">{{ $cnt_invoice['this_week'] }} / {{$cnt_invoice['cnt_this_week']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice bg-warning text-white"></i>
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
                            <h6 class="m-b-20">{{__('Last 30 Days Total Invoice')}}</h6>
                            <h4 class="text-danger">{{ $cnt_invoice['last_30days'] }} / {{$cnt_invoice['cnt_last_30days']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice bg-danger text-white"></i>
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
                                    <th width="60px">{{__('Invoice')}}</th>
                                    <th>{{__('Deal')}}</th>
                                    <th>{{__('Issue Date')}}</th>
                                    <th>{{__('Due Date')}}</th>
                                    <th>{{__('Value')}}</th>
                                    <th>{{__('Status')}}</th>
                                    @if(\Auth::user()->type != 'Client')
                                        <th width="250px">{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($invoices as $invoice)
                                    <tr>
                                        <td class="Id">
                                            @can('View Invoice')
                                                <a class="btn  btn-outline-primary" href="{{route('invoices.show',$invoice->id)}}"> <i class="fas fa-file-invoice"></i> {{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                            @else
                                                {{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                            @endcan
                                        </td>
                                        <td>{{ $invoice->deal->name }}</td>
                                        <td>{{ \Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($invoice->due_date) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                        <td>
                                            @if($invoice->status == 0)
                                                <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 1)
                                                <span class="badge rounded p-2 px-3 bg-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 2)
                                                <span class="badge rounded p-2 px-3 bg-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 3)
                                                <span class="badge rounded p-2 px-3 bg-success">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 4)
                                                <span class="badge rounded p-2 px-3 bg-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @endif
                                        </td>
                                        @if(\Auth::user()->type != 'Client')
                                            <td class="Action">
                                                <span>
                                                @can('View Invoice')
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a href="{{route('invoices.show',$invoice->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{__('View')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Edit Invoice')
                                                        <div class="action-btn btn-info ms-2">
                                                            <a href="#" data-size="lg" data-url="{{ URL::to('invoices/'.$invoice->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Invoice')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Invoice')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete Invoice')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id]]) !!}
                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Invoice')}}">
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
    {{--Estimations--}}
    <div class="row">
        <div class="col-12 mb-2">
            <h4 class="h4 font-weight-400 float-left">{{__('Estimations')}}</h4>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Estimate')}}</h6>
                            <h4 class="text-primary">{{ $cnt_estimation['total'] }} / {{$cnt_estimation['cnt_total']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane bg-success text-white"></i>
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
                            <h6 class="m-b-20">{{__('This Month Total Estimate')}}</h6>
                            <h4 class="text-info">{{ $cnt_estimation['this_month'] }} / {{$cnt_estimation['cnt_this_month']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane bg-info text-white"></i>
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
                            <h6 class="m-b-20">{{__('This Week Total Estimate')}}</h6>
                            <h4 class="text-warning">{{ $cnt_estimation['this_week'] }} / {{$cnt_estimation['cnt_this_week']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane bg-warning text-white"></i>
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
                            <h6 class="m-b-20">{{__('Last 30 Days Total Estimate')}}</h6>
                            <h4 class="text-danger">{{ $cnt_estimation['last_30days'] }} / {{$cnt_estimation['cnt_last_30days']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12">
             <div class="col-md-12">
                <div class="card"> 
                <div class="card-header card-body table-border-style">  
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{__('Estimate')}}</th>
                                    <th>{{__('Client')}}</th>
                                    <th>{{__('Issue Date')}}</th>
                                    <th>{{__('Value')}}</th>
                                    <th>{{__('Status')}}</th>
                                    @if(Auth::user()->type != 'Client')
                                        <th width="250px">{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estimations as $estimate)
                                <tr>
                                    <td class="Id">
                                        @can('View Estimation')
                                            <a href="{{route('estimations.show',$estimate->id)}}"> <i class="fas fa-file-estimate"></i> {{ Auth::user()->estimateNumberFormat($estimate->estimation_id) }}</a>
                                        @else
                                            {{ Auth::user()->estimateNumberFormat($estimate->estimation_id) }}
                                        @endcan
                                    </td>
                                    <td>{{ $estimate->client->name }}</td>
                                    <td>{{ Auth::user()->dateFormat($estimate->issue_date) }}</td>
                                    <td>{{ Auth::user()->priceFormat($estimate->getTotal()) }}</td>
                                    <td>
                                        @if($estimate->status == 0)
                                            <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @elseif($estimate->status == 1)
                                            <span class="badge rounded p-2 px-3 bg-danger">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @elseif($estimate->status == 2)
                                            <span class="badge rounded p-2 px-3 bg-warning">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @elseif($estimate->status == 3)
                                            <span class="badge rounded p-2 px-3 bg-success">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @elseif($estimate->status == 4)
                                            <span class="badge rounded p-2 px-3 bg-info">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @endif
                                    </td>
                                    @if(Auth::user()->type != 'Client')
                                        <td class="Action">
                                            <span>
                                            @can('View Estimation')
                                                <div class="action-btn bg-warning ms-2">
                                                    <a href="{{route('estimations.show',$estimate->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{__('View')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
                                                </div>
                                                @endcan
                                                @can('Edit Estimation')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-size="lg" data-url="{{ URL::to('estimations/'.$estimate->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Estimation')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Estimation')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Estimation')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['estimations.destroy', $estimate->id]]) !!}
                                                            <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Estimation')}}">
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
    </div>
    {{--Contracts--}}
    <div class="row">
        <div class="col-12 mb-2">
            <h4 class="h4 font-weight-400 float-left">{{__('Contracts')}}</h4>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Contracts')}}</h6>
                            <h4 class="text-primary">{{ $cnt_contract['total'] }} / {{$cnt_contract['cnt_total']}}</h4>
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
                            <h4 class="text-info">{{ $cnt_contract['total'] }} / {{$cnt_contract['cnt_total']}}</h4>
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
                            <h4 class="text-warning">{{ $cnt_contract['total'] }} / {{$cnt_contract['cnt_total']}}</h4>
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
                            <h4 class="text-danger">{{ $cnt_estimation['last_30days'] }} / {{$cnt_estimation['cnt_last_30days']}}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-danger text-white"></i>
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
                                        <td>{{ $contract->name }}</td>
                                        <td>{{ $contract->client->name }}</td>
                                        <td>{{ Auth::user()->priceFormat($contract->value) }}</td>
                                        <td>{{ $contract->contract_type->name }}</td>
                                        <td>{{ Auth::user()->dateFormat($contract->start_date) }}</td>
                                        <td>{{ Auth::user()->dateFormat($contract->end_date) }}</td>
                                        <td>{{ $contract->status }}</td>
                                        <td class="Action">
                                            <span>
                                            @can('Edit Contract')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="lg" data-url="{{ URL::to('contract/'.$contract->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Contract Type')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Contract Type')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                                @endcan
                                                @can('Delete Contract')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['contract.destroy', $contract->id]]) !!}
                                                            <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Contract Type')}}">
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
