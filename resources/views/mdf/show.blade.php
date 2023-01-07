@extends('layouts.admin')

@section('title')
    {{__('Market Development Funds')}}
@endsection

@push('script')
    <script>
        var sub_type = '{{$mdf->sub_type}}';
        $(document).ready(function () {
            $("select[name=request_type]").trigger('change');
        });
        $(document).on("change", "select[name=request_type]", function () {
            fillEventType($(this).val(), sub_type);
        });

        function fillEventType(request_type, selected = 0) {
            $.ajax({
                url: '{{route('mdf.event.json')}}',
                data: {request_type: request_type, _token: $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                success: function (data) {
                    $('#event_type').select2();
                    if (data != '') {
                        $("#event_type").html('<option value="0" selected="selected">{{__('Select Event Type')}}</option>');
                        $.each(data, function (key, data) {
                            var sel = '';
                            if (key == selected) {
                                sel = 'selected';
                            }
                            $("#event_type").append('<option value="' + key + '" ' + sel + '>' + data + '</option>');
                        });
                    } else {
                        $("#event_type").html('<option value="" selected="selected">{{__('Select Event Type')}}</option>');
                    }

                    $('#event_type').select2({
                        placeholder: "{{__('Select Event Type')}}"
                    });
                }
            });
        }

        $(document).on('change', 'input[type=radio][name=product_type]', function () {
            changeProduct(this.value)
        });

        function changeProduct(val) {
            if (val == 'product_service') {
                $('#product_service').removeClass('d-none');
                $('#product_service').addClass('d-block');

                $('#others').removeClass('d-block');
                $('#others').addClass('d-none');
            } else if (val == 'others') {
                $('#others').removeClass('d-none');
                $('#others').addClass('d-block');

                $('#product_service').removeClass('d-block');
                $('#product_service').addClass('d-none');
            }
        }
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('mdf.index')}}">{{__('MDF')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Funds')}}</li>
@endsection
@section('action-button')

                @can('View MDF')
                        <a href="{{ route('get.mdf',$mdf->id) }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Print MDF')}}" title="{{__('Print MDF')}}" target="_blanks">
                            <span><i class="fa fa-print text-white"></i> </span>
                        </a>
                @endcan
                @if($mdf->is_complete == '0')
                     @can('Edit MDF')
                            <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit MDF')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit MDF')}}" data-url="{{ route('mdf.edit',$mdf->id) }}"><i class="ti ti-pencil text-white"></i></a>
                    @endcan
                    @can('Create MDF Payment')
                            <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Fund')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Add Fund')}}" data-url="{{ route('mdf.payments.approved',[$mdf->id,'fund'])}}"><i class="ti ti-plus text-white"></i></a>
                    @endcan


                @endif
@endsection

@section('content')
    <div class="row">
            <!-- [ Invoice ] start -->
        <div class="container">
            <div>
                <div class="card" id="printTable">
                    <div class="card-body">
                        <div class="row ">
                            <div class="col-md-8 invoice-contact">
                                <div class="invoice-box row">
                                    <div class="col-sm-12">
                                        <table class="table table-responsive invoice-table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td><h4>{{ \Auth::user()->mdfNumberFormat($mdf->mdf_id) }}</h4>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        <div class="row invoive-info d-print-inline-flex">
                            <div class="col-sm-4 invoice-client-info">
                                <h6 class="m-b-20">{{__('From')}} :</h6>
                                <p class="m-0 m-t-10">{{$mdf->user->name}}</p>
                                <p class="m-0 m-t-10">{{$mdf->user->email}}</p>
                            </div>
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-4 text-end">
                                <h6>{{__('To')}} :</h6>
                                <h6 class="m-0">{{$settings['company_name']}}</h6>
                                <p class="m-0 m-t-10">{{$settings['company_address']}}</p>
                                <p class="m-0">{{$settings['company_city']}}
                                    @if(isset($settings['company_city']) && !empty($settings['company_city'])), @endif
                                </p>
                                <p class="m-0">{{$settings['company_state']}} @if(isset($settings['company_zipcode']) && !empty($settings['company_zipcode']))-@endif {{$settings['company_zipcode']}}</p>
                                <p><a class="text-secondary" href="$" target="_top"><span class="__cf_email__"
                                            data-cfemail="6a0e0f07052a0d070b030644090507">{{$settings['company_country']}}</span></a>
                                </p>
                            </div>
                        </div>
                         <div class="row invoive-info d-print-inline-flex">
                            <div class="col-sm-3 invoice-client-info">
                                <h6>{{__('Status')}} :</h6>
                                <p class="m-0 m-t-10">{{ strtoupper($mdf->statusDetail->name) }}</p>
                            </div>
                            <div class="col-sm-3">
                                <h6 class="m-b-20">{{__('Event Date')}} :</h6>
                                <p class="m-0 m-t-10">{{ \Auth::user()->dateFormat($mdf->date) }}</p>
                            </div>
                            <div class="col-sm-3">
                                <h6 class="m-b-20">{{__('Type')}} :</h6>
                                <p class="m-0 m-t-10">{{ $mdf->typeDetail->name }}</p>
                            </div>
                            <div class="col-sm-3 text-end">
                                <h6 class="m-b-20">{{__('Sub Type')}} :</h6>

                                <p class="m-0 m-t-10"> {{!empty($mdf->subTypeDetail)?$mdf->subTypeDetail->name:''}}</p>
                            </div>
                        </div>
                        @if(\Auth::user()->type == 'Owner')
                            <div class="col-12 text-end py-2">
                                @if($mdf->is_complete == 1)
                                    <a href="#" class="badge p-2 px-3 rounded bg-warning" data-toggle="tooltip" data-original-title="{{__('Mark as Pending this MDF')}}" data-confirm="{{__('Are You Sure?')}}|{{__('This action Mark Your MDF as a "Pending"')}}" data-confirm-yes="document.getElementById('change-status-form-{{$mdf->id}}').submit();"><i class="fas fa-clock"></i> <span>{{__('Pending')}}</span></a>
                                @else
                                    <a href="#" class="badge p-2 px-3 rounded bg-success" data-toggle="tooltip" data-original-title="{{__('Mark as Complete this MDF')}}" data-confirm="{{__('Are You Sure?')}}|{{__('This action Mark Your MDF as a "Complete"')}}" data-confirm-yes="document.getElementById('change-status-form-{{$mdf->id}}').submit();"><i class="fas fa-check"></i> <span>{{__('Complete')}}</span></a>
                                @endif
                                {{ Form::open(array('route' => array('mdf.change.complete',$mdf->id),'class'=>'mr-2','id'=>'change-status-form-'.$mdf->id)) }}
                                {{ Form::close() }}
                            </div>
                        @endif

                         <div class="justify-content-between align-items-center d-flex mb-2">
                            <h4 class="h4 font-weight-400 float-left">{{__('Order Summary')}}</h4>
                            @can('Estimation Add Product')

                                    <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add MDF Expense')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Add MDF Expense')}}" data-url="{{ route('mdf.products.add',$mdf->id) }}"><i class="ti ti-plus text-white"></i></a>
                            @endcan

                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-12 ">
                                <div class="table-responsive">
                                    <table class="table invoice-detail-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 250px;">{{__('Action')}}</th>
                                                <th style="width: 250px;">{{__('#')}}</th>
                                                <th style="width: 250px;">{{__('Item')}}</th>
                                                <th style="width: 250px;">{{__('Price')}}</th>
                                                <th style="width: 250px;">{{__('Quantity')}}</th>
                                                <th class="text-end">{{__('Total Expenses')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($mdf->getProducts->count() > 0)
                                                @php
                                                    $i=0;
                                                @endphp
                                                @foreach($mdf->getProducts as $product)
                                                    <tr>
                                                        <td class="Action">
                                                    <span>
                                                        @can('MDF Delete Expense')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['mdf.products.delete', $mdf->id,$product->id]]) !!}
                                                                    <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete MDF')}}">
                                                                       <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    </span>
                                                        </td>
                                                        <td class="invoice-order">{{++$i}}</td>
                                                        <td class="small-order">{{$product->name}}</td>
                                                        <td class="small-order">{{\Auth::user()->priceFormat($product->price)}}</td>
                                                        <td class="small-order">{{$product->quantity}}</td>
                                                        @php
                                                            $price = ($product->quantity > 0) ? $product->price * $product->quantity : $product->price;
                                                        @endphp
                                                        <td class="invoice-order text-right">{{\Auth::user()->priceFormat($price)}}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center"><span>{{__('No Data Found!')}}</span></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="invoice-total">
                                    <table class="table invoice-table ">
                                        <tbody>
                                            <tr>
                                                <th>{{__('Subtotal')}} :</th>
                                                <td>{{\Auth::user()->priceFormat($mdf->getSubTotal())}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{__('Requested Amount')}} :</th>
                                                <td>{{\Auth::user()->priceFormat($mdf->amount)}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{__('Approved Amount')}} :</th>
                                                <td>{{\Auth::user()->priceFormat($mdf->getFundAmt())}}</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                         <!-- @if(!empty($mdf->description)) -->
                            <h4 class="h4 font-weight-400 float-left">{{__('Description')}}</h4>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 text-justify">
                                            <p class="text-sm">{{ $mdf->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- @endif -->
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Invoice ] end -->
    </div>

     <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0">
        <div class="card table-card">
            <div class="card-header">
                <h5>{{ __('Approved Amount') }}</h5>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                           <tr>
                                <th>{{__('Payment Date')}}</th>
                                <th>{{__('Payment Method')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Note')}}</th>
                                <th class="text-right">{{__('Amount')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($mdf->funds->count() > 0)
                                @foreach($mdf->funds as $fund)
                                    <tr>
                                        <td>
                                            {{ \Auth::user()->dateFormat($fund->date) }}
                                        </td>
                                        <td>
                                            {{(!empty($fund->payment)?$fund->payment->name:'-')}}
                                        </td>
                                        <td>
                                            {{ ucfirst($fund->type) }}
                                        </td>
                                        <td>
                                            {{(!empty($fund->notes) ? $fund->notes : '-')}}
                                        </td>
                                        <td class="text-right">
                                            {{\Auth::user()->priceFormat($fund->amount)}}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">{{__('No Data Found!')}}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>





@endsection
