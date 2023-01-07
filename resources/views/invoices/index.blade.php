@extends('layouts.admin')

@section('title')
    {{__('Manage Invoices')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Invoices')}}</li>
@endsection

@section('action-button')

             <a href="{{route('invoice.export')}}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-original-title="{{__('Export')}}"  >
                <i class="ti ti-file-export text-white"></i>
            </a>
        @can('Create Invoice')
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Invoice')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Invoice')}}" data-url="{{route('invoices.create')}}"><i class="ti ti-plus text-white"></i></a>
        @endcan

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Invoice')}}</h6>
                            <h3 class="text-primary">{{ $cnt_invoice['total'] }}</h3>
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
                            <h3 class="text-info">{{ $cnt_invoice['this_month'] }}</h3>
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
                            <h3 class="text-warning">{{ $cnt_invoice['this_week'] }}</h3>
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
                            <h3 class="text-danger">{{ $cnt_invoice['last_30days'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table pc-dt-simple">
                            <thead>
                                <tr>
                                    <th width="60px">{{__('Invoice')}}</th>
                                    <th>{{__('Deal')}}</th>
                                    <th>{{__('Issue Date')}}</th>
                                    <th>{{__('Due Date')}}</th>
                                    <th>{{__('Value')}}</th>
                                    <th>{{__('Status')}}</th>
                                    @if(Auth::user()->type != 'Client')
                                        <th width="250px">{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="Id">
                                        @can('View Invoice')
                                        <a href="{{route('invoices.show',$invoice->id)}}" class="btn  btn-outline-primary"> <i class="fas fa-file-invoice"></i> {{ Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                        @else
                                        {{ Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                        @endcan
                                    </td>
                                    <td>{{ $invoice->deal->name }}</td>
                                    <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                    <td>{{ Auth::user()->dateFormat($invoice->due_date) }}</td>
                                    <td>{{ Auth::user()->priceFormat($invoice->getTotal()) }}</td>

                                    <td>
                                        @if($invoice->status == 0)

                                            <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Invoice::$statues[0]) }}</span>
                                        @elseif($invoice->status == 1)
                                            <span class="badge rounded p-2 px-3 bg-danger">{{ __(\App\Models\Invoice::$statues[1]) }}</span>
                                        @elseif($invoice->status == 2)
                                            <span class="badge rounded p-2 px-3 bg-warning">{{ __(\App\Models\Invoice::$statues[2]) }}</span>
                                        @elseif($invoice->status == 3)
                                        <span class="badge rounded p-2 px-3 bg-danger">{{ __(\App\Models\Invoice::$statues[3]) }}</span>

                                        @elseif($invoice->status == 4)
                                            <span class="badge rounded p-2 px-3 bg-info">{{ __(\App\Models\Invoice::$statues[4]) }}</span>
                                        @endif

                                    </td>
                                    @if(Auth::user()->type != 'Client')
                                        <td class="text-right">
                                            @can('View Invoice')
                                                <div class="action-btn btn-warning ms-2">
                                                    <a href="{{route('invoices.show',$invoice->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View Invoice')}}" ><i class="ti ti-eye text-white"></i></a>
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
