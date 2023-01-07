@extends('layouts.admin')
@section('title')
    {{ __("Estimation Detail") }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page"><a href="{{route('estimations.index')}}">{{__('Estimate')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Estimation Detail')}}</li>
@endsection

@section('action-button')

        @if(Auth::user()->type == 'Owner')
                <a href="#" class="btn btn-sm btn-primary btn-icon cp_link" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Click to copy Estimation link')}}" data-link="{{route('pay.estimation',\Illuminate\Support\Facades\Crypt::encrypt($estimation->estimation_id))}}" data-toggle="tooltip" data-original-title="{{__('Click to copy Estimation link')}}"><i class="ti ti-file-text text-white"></i></a>

        @endif
        @can('Edit Estimation')
                 <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Estimate')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit Estimate')}}" data-url="{{ URL::to('estimations/'.$estimation->id.'/edit') }}"><i class="ti ti-pencil text-white"></i></a>
        @endcan

        @can('View Estimation')
               <a href="{{ route('get.estimation',$estimation->id) }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Print Estimation')}}" target="_blanks"><i class="ti ti-printer text-white"></i></a>
        @endcan
@endsection



@push('script')
    <script>
         $('.cp_link').on('click', function () {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            show_toastr('Success', '{{__('Link Copy on Clipboard')}}', 'success')
        });
    </script>
@endpush

@section('content')
    <div class="row">
            <!-- [ Invoice ] start -->
        <div class="container">
            <div>
                <div class="card" id="printTable">
                    <div class="card-body">
                        <div class="row ">
                            <div class="col-md-12 invoice-contact">
                                <div class="invoice-box row">
                                    <div class="col-sm-2">
                                        <table class="table table-responsive invoice-table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td><h4>{{ Auth::user()->estimateNumberFormat($estimation->estimation_id) }}</h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{!! DNS2D::getBarcodeHTML(route('pay.estimation',\Illuminate\Support\Facades\Crypt::encrypt($estimation->estimation_id)), "QRCODE",2,2) !!}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-sm-6 invoice-client-info">
                                        <h6>{{__('From')}} :</h6>
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
                                    <div class="col-sm-4 text-end">
                                        @if($client)
                                            <h6 class="m-b-20">{{__('To')}} :</h6>
                                            <p class="m-0 m-t-10">{{$client->name}}</p>
                                            <p class="m-0 m-t-10">{{$client->email}}</p>
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        {{-- <div class="row invoive-info d-print-inline-flex">
                            <div class="col-sm-4 invoice-client-info">
                                <h6>{{__('From')}} :</h6>
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
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-4 text-end">
                                @if($client)
                                    <h6 class="m-b-20">{{__('To')}} :</h6>
                                    <p class="m-0 m-t-10">{{$client->name}}</p>
                                    <p class="m-0 m-t-10">{{$client->email}}</p>
                                @endif
                            </div>
                        </div> --}}
                         <div class="row invoive-info d-print-inline-flex">
                            <div class="col-sm-4 invoice-client-info">
                                <h6>{{__('Status')}} :</h6>
                               @if($estimation->status == 0)
                                    <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                @elseif($estimation->status == 1)
                                    <span class="badge rounded p-2 px-3 bg-danger">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                @elseif($estimation->status == 2)
                                    <span class="badge rounded p-2 px-3 bg-warning">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                @elseif($estimation->status == 3)
                                    <span class="badge rounded p-2 px-3 bg-success">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                @elseif($estimation->status == 4)
                                    <span class="badge rounded p-2 px-3 bg-info">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                @endif
                            </div>
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-4 text-end">
                                <h6 class="m-b-20">{{__('Issue Date')}}:</h6>
                                <p class="m-0 m-t-10">{{ Auth::user()->dateFormat($estimation->issue_date) }}</p>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <h4 class="h4 font-weight-400 float-left">{{__('Order Summary')}}</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                @can('Estimation Add Product')
                                    <div class="btn btn-sm btn-primary btn-icon mt-1">

                                        <a href="#" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Estimation Product')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Add Estimation Product')}}" data-url="{{ route('estimations.products.add',$estimation->id) }}"><i class="ti ti-plus text-white"></i></a>

                                    </div>
                                @endcan
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table invoice-detail-table">
                                        <thead>
                                            <tr class="thead-default">
                                                <th style="width: 250px;">{{__('Action')}}</th>
                                                <th style="width: 250px;">{{__('#')}}</th>
                                                <th style="width: 250px;">{{__('Item')}}</th>
                                                <th style="width: 250px;">{{__('Price')}}</th>
                                                <th style="width: 250px;">{{__('Quantity')}}</th>
                                                <th style="width: 250px;" class="text-end">{{__('Totals')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i=0;
                                            @endphp
                                            @foreach($estimation->getProducts as $product)
                                                <tr>
                                                    <td class="Action">
                                                    <span>
                                                        @can('Estimation Edit Product')
                                                            <div class="action-btn btn-info ms-2">
                                                                <a href="#" data-size="lg" data-url="{{ route('estimations.products.edit',[$estimation->id,$product->pivot->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Estimation Product')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Estimation Product')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Estimation Delete Product')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['estimations.products.delete', $estimation->id,$product->pivot->id]]) !!}
                                                                    <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Estimation Product')}}">
                                                                       <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    </span>
                                                    </td>
                                                    <td class="invoice-order">{{++$i}}</td>
                                                    <td class="small-order">{{$product->name}}</td>
                                                    <td class="small-order">{{Auth::user()->priceFormat($product->pivot->price)}}</td>
                                                    <td class="small-order">{{$product->pivot->quantity}}</td>
                                                    @php
                                                        $price = $product->pivot->price * $product->pivot->quantity;
                                                    @endphp
                                                    <td class="invoice-order text-end">{{Auth::user()->priceFormat($price)}}</td>
                                                </tr>
                                            @endforeach
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
                                                @php
                                                    $subTotal = $estimation->getSubTotal();
                                                @endphp
                                                <th>{{__('Subtotal')}} :</th>
                                                <td>{{Auth::user()->priceFormat($subTotal)}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{__('Discount')}} :</th>
                                                <td>{{Auth::user()->priceFormat($estimation->discount)}}</td>
                                            </tr>
                                            <tr>
                                                @php
                                                    $tax = $estimation->getTax();
                                                @endphp
                                                <th>{{$estimation->tax->name}} ({{$estimation->tax->rate}} %) :</th>
                                                <td>{{Auth::user()->priceFormat($tax)}}</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <hr />
                                                    <h5 class="text-primary m-r-10">{{__('Total')}} :</h5>
                                                </td>
                                                <td>
                                                    <hr />
                                                    <h5 class="text-primary">{{Auth::user()->priceFormat($subTotal-$estimation->discount+$tax)}}</h5>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Invoice ] end -->
    </div>



@endsection
