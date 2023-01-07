@extends('layouts.admin')

@section('title')
    {{__('Payments')}}
@endsection


@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Payments')}}</li>
@endsection

@section('content')
    <div class="row">

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Payment')}}</h6>
                            <h3 class="text-primary">{{ $cnt_payments['total'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check bg-success text-white"></i>
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
                            <h6 class="m-b-20">{{__('This Month Total Payment')}}</h6>
                            <h3 class="text-info">{{ $cnt_payments['this_month'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check bg-info text-white"></i>
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
                            <h6 class="m-b-20">{{__('This Week Total Payment')}}</h6>
                            <h3 class="text-warning">{{ $cnt_payments['this_week'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check bg-warning text-white"></i>
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
                            <h6 class="m-b-20">{{__('Last 30 Days Total Payment')}}</h6>
                            <h3 class="text-danger">{{ $cnt_payments['last_30days'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check bg-danger text-white"></i>
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
                                    <th>{{__('Transaction ID')}}</th>
                                    <th>{{__('Invoice')}}</th>
                                    <th>{{__('Deal')}}</th>
                                    <th>{{__('Payment Date')}}</th>
                                    <th>{{__('Payment Method')}}</th>
                                    <th>{{__('Payment Type')}}</th>
                                    <th>{{__('Note')}}</th>
                                    <th>{{__('Amount')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($payments as $payment)
                                    <tr>
                                        <td>
                                            {{sprintf("%05d", $payment->transaction_id)}}
                                        </td>
                                        <td class="Id">
                                            @can('View Invoice')
                                                <a class="btn  btn-outline-primary" href="{{route('invoices.show',$payment->invoice->id)}}"><i class="fas fa-file-invoice"></i> {{ Auth::user()->invoiceNumberFormat($payment->invoice->invoice_id) }}</a>
                                            @else
                                                {{ Auth::user()->invoiceNumberFormat($payment->invoice->invoice_id) }}
                                            @endcan
                                        </td>
                                        <td>{{ $payment->invoice->deal->name }}</td>
                                        <td>
                                            {{ Auth::user()->dateFormat($payment->date) }}
                                        </td>
                                        <td>
                                            {{(!empty($payment->payment)?$payment->payment->name:'-')}}
                                        </td>
                                        <td>
                                            {{$payment->payment_type}}
                                        </td>
                                        <td>
                                            {{$payment->notes}}
                                        </td>
                                        <td>
                                            {{Auth::user()->priceFormat($payment->amount)}}
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
