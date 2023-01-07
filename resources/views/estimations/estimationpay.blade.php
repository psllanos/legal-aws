@extends('layouts.invoicepayheader')


@section('title')

        {{__('Estimation')}} {{ '('. $estimation->client->name .')' }}

@endsection

@section('action-button')

    <div class="row align-items-center m-1">
        <a href="{{route('estimation.download.pdf',\Crypt::encrypt($estimation->id))}}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Print Estimation')}}" target="_blanks"><i class="ti ti-printer text-white"></i></a>
    </div>

@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Estimation')}}</li>
@endsection


@push('script')



    @if($users->type == 'Client' && $invoice->getDue() > 0 && isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')

        <script src="https://js.paystack.co/v1/inline.js"></script>

        <script type="text/javascript">
            $(document).on("click", "#pay_with_paystack", function () {

                $('#paystack-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;

                        var paystack_callback = "{{ url('/invoice-pay-with-paystack') }}";
                        var order_id = '{{time()}}';
                        var handler = PaystackPop.setup({
                            key: '{{ $payment_setting['paystack_public_key']  }}',
                            email: res.email,
                            amount: res.total_price*100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {
                                console.log(response.reference,order_id);
                                window.location.href = "{{url('/invoice/paystack')}}/"+response.reference+"/{{encrypt($invoice->id)}}";
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    }else if(res.flag == 2){

                    }else{
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>
    @endif

    @if($users->type == 'Client' && $invoice->getDue() > 0 && isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on')

        <script src="https://js.stripe.com/v3/"></script>
        <script type="text/javascript">
            var stripe = Stripe('{{ $payment_setting['stripe_key'] }}');
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '14px',
                    color: '#32325d',
                },
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {style: style});

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                stripe.createToken(card).then(function (result) {
                    if (result.error) {
                        show_toastr('Error', result.error.message, 'error');
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        </script>
    @endif

    @if($users->type == 'Client' && $invoice->getDue() > 0 && isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')

        <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>

        <script type="text/javascript">
            //    Flaterwave Payment
            $(document).on("click", "#pay_with_flaterwave", function () {
                $('#flaterwave-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("{{ isset($payment_setting['flutterwave_public_key'] ) }}"){
                            API_publicKey = "{{$payment_setting['flutterwave_public_key']}}";
                        }
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                        var flutter_callback = "{{ url('/invoice-pay-with-flaterwave') }}";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '{{$users->email}}',
                            amount: res.total_price,
                            currency: '{{$payment_setting['currency']}}',
                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) + 'fluttpay_online-' +
                                {{ date('Y-m-d') }},
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function () {
                            },
                            callback: function (response) {
                                var txref = response.tx.txRef;
                                if(response.tx.chargeResponseCode == "00" || response.tx.chargeResponseCode == "0") {
                                    window.location.href = "{{url('/invoice/flaterwave')}}/"+txref+"/{{encrypt($invoice->id)}}";
                                }else{
                                    // redirect to a failure page.
                                }
                                x.close(); // use this to close the modal immediately after payment.
                            }});
                    }else if(res.flag == 2){

                    }else{
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>

    @endif

    @if($users->type == 'Client' && $invoice->getDue() > 0 && isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')

        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

        <script type="text/javascript">
            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function () {
                $('#razorpay-payment-form').ajaxForm(function (res) {

                    if(res.flag == 1){

                        var razorPay_callback = "{{url('/invoice-pay-with-razorpay')}}";
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("{{isset($payment_setting['razorpay_public_key'])}}"){
                            API_publicKey = "{{$payment_setting['razorpay_public_key']}}";
                        }
                        var options = {
                            "key": API_publicKey, // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Invoice Payment',
                            "currency": '{{$payment_setting['currency']}}',
                            "description": "",
                            "handler": function (response) {
                                window.location.href = "{{url('/invoice/razorpay')}}/"+response.razorpay_payment_id +"/{{encrypt($invoice->id)}}";
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    }else if(res.flag == 2){

                    }else{
                        show_toastr('Error', data.message, 'msg');
                    }
                }).submit();
            });
        </script>
    @endif
@endpush

@section('content')

    <div class="row">
            <!-- [ Invoice ] start -->
        <div class="container">

            <div class="card">
                <div class="card-body">




                    <div class="row ">
                        <div class="col-md-4">
                            <div class="invoice-contact">
                                <div class="invoice-box row">
                                    <div class="col-sm-12">
                                        <h5>{{__('From')}} :</h5>
                                        <table class="">
                                            <tbody>
                                                <tr>
                                                    <th>{{ $company_setting['company_name'] }}</th>
                                                </tr>
                                        <tr>
                                            <td>{{ $company_setting['company_address'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $company_setting['company_city'] }},</td>
                                        </tr>
                                        <tr>
                                            <td>{{$company_setting['company_state']}} - {{ $company_setting['company_zipcode'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $company_setting['company_country'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $company_setting['company_telephone']}}</td>
                                        </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-3 invoice-client-info">
                            <div class="invoice-contact">
                                <div class="invoice-box row">
                                        {{-- <h5>{{ __('To') }}:</h5> --}}
                                        @if($client)
                                            <h6 class="m-b-20">{{__('To')}} :</h6>
                                            <p class="m-0 m-t-10">{{$client->name}}</p>
                                            <p class="m-0 m-t-10">{{$client->email}}</p>
                                        @endif
                                </div>
                            </div>
                        </div>


                        <div class="col-md-3  invoice-client-info">
                            <div class="invoice-contact">

                                <div class="col-sm-12">
                                    <h5>{{__('Description')}} :</h5>
                                    <table class="">
                                        <tbody>
                                            <tr>
                                                <th>{{ __('Issue Date') }} :</th>
                                                <td>{{ Auth::user()->dateFormat($estimation->issue_date) }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Due Date') }} :</th>
                                                <td>{{ Auth::user()->dateFormat($estimation->due_date) }}</td>
                                            </tr>
                                            <tr>
                                                <div>
                                                <th>{{__('Status')}} :</th>
                                                @if($estimation->status == 0)
                                                <td>
                                                    <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                                </td>
                                                @elseif($estimation->status == 1)
                                                <td>
                                                    <span class="badge rounded p-2 px-3 bg-danger">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                                </td>
                                                @elseif($estimation->status == 2)
                                                <td>
                                                    <span class="badge rounded p-2 px-3 bg-warning">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                                </td>
                                                @elseif($estimation->status == 3)
                                                <td>
                                                    <span class="badge rounded p-2 px-3 bg-success">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                                </td>
                                                @elseif($estimation->status == 4)
                                                <td>
                                                    <span class="badge rounded p-2 px-3 bg-info">{{ __(\App\Models\Estimation::$statues[$estimation->status]) }}</span>
                                                </td>
                                                @endif
                                                </div>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 ps-5 mt-5 qr_code">
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

                    </div>

                    <div class="row mb-5">
                        <div class="col-md-12">
                            <h4 class="h4 font-weight-400 float-left">{{__('Order Summary')}}</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table invoice-detail-table">
                                    <thead>
                                        <tr class="thead-default">
                                            <th>{{__('productname')}}</th>
                                            <th>{{__('totalQuantity')}}</th>
                                            <th>{{__('Price')}}</th>
                                            <th>{{__('Description')}}</th>
                                            <th>{{__('Price')}}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalQuantity=0;
                                            $totalRate=0;
                                            $totalAmount=0;
                                            $totalTaxPrice=0;
                                            $totalDiscount=0;
                                            $taxesData=[];
                                        @endphp
                                         @foreach($estimation->getProducts as $estimations)
                                            @php

                                                $taxes=\Utility::tax($estimations->tax);
                                                $totalQuantity+=$estimations->quantity;
                                                $totalRate+=$estimations->price;
                                                $totalDiscount+=$estimations->discount;

                                            @endphp
                                            <tr>
                                                <td>{{$estimations->name}} </td>

                                                <td>{{$estimations->pivot->quantity }}</td>
                                                <td>{{$users->priceFormat($estimations->pivot->price)}} </td>


                                                <td>{{$estimations->description}} </td>
                                                 @php
                                        $price = $estimations->pivot->price * $estimations->pivot->quantity;
                                    @endphp
                                                <td>{{$users->priceFormat($price)}}</td>

                                                @php
                                                    $totalQuantity+=$estimations->quantity;
                                                    $totalRate+=$estimations->price;
                                                    $totalDiscount+=$estimations->discount;
                                                    $totalAmount+=($estimations->price*$estimations->quantity);
                                                @endphp
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
                                            <th>{{__('Sub Total')}} :</th>
                                            <td>{{$users->priceFormat($estimation->getSubTotal())}}</td>
                                        </tr>
                                        @if(!empty($taxesData))
                                            @foreach($taxesData as $taxName => $taxPrice)
                                                @if($taxName != 'No Tax')
                                                    <tr>
                                                        <th>{{$taxName}}</th>
                                                        <td>{{ $users->priceFormat($taxPrice) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif

                                        <tr>
                                            <td>
                                                <h5 class="text-primary m-r-10">{{__('Total value')}} :</h5>
                                            </td>
                                            <td>
                                                <h5 class="text-primary">{{$users->priceFormat($estimation->getTotal())}}</h5>
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
        <!-- [ Invoice ] end -->
    </div>

@endsection

