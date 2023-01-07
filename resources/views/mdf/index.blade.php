@extends('layouts.admin')

@section('title')
    {{__('Manage MDF (Market Development Funds)')}}
@endsection

@push('script')
    <script>
        function fillEventType(request_type, selected = 0) {
            $.ajax({
                url: '{{route('mdf.event.json')}}',
                data: {request_type: request_type, _token: $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                success: function (data) {
                    $("#event_type").empty();
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

                    $('#event_type').select2();
                }
            });
        }
    </script>
@endpush

@section('action-button')
    @if(\Auth::user()->type != 'Owner')
        <div class="row align-items-center m-1">
            @can('Request MDF')
                <div class="col-auto pe-0">
                    <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('MDF Request')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Make MDF Request')}}" data-url="{{route('mdf.create')}}"><i class="ti ti-plus text-white"></i></a>
                </div>
            @endcan 
        </div>
    @endif
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('MDF')}}</li>
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-12">
                <div class="row">
                    <div class="col-xxl-6">
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="card mdf-card manage-card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-success">
                                            <i class="fas fa-money-check-alt text-white"></i>
                                        </div>

                                        <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p>
                                        <h6 class="mb-3">{{('MDF')}}</h6>
                                        <h4 class="mb-0">{{ $cnt_mdf['total'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card mdf-card manage-card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-info">
                                            <i class="fas fa-money-check-alt  text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm mt-4 mb-2">{{__('This Month')}}</p>
                                         <h6 class="mb-3">{{('Total MDF')}}</h6>
                                        <h4 class="mb-0">{{ $cnt_mdf['this_month'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card mdf-card manage-card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-warning">
                                            <i class="fas fa-money-check-alt text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm mt-4 mb-2">{{__('This Week')}}</p>
                                        <h6 class="mb-3">{{('Total MDF')}}</h6>
                                        <h4 class="mb-0">{{ $cnt_mdf['this_week'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card mdf-card manage-card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-money-check-alt text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm mt-4 mb-2">{{__('Last 30 Days')}}</p>
                                        <h6 class="mb-3">{{('Total MDF')}}</h6>
                                        <h4 class="mb-0">{{ $cnt_mdf['pending_amt'] }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6">
                        <div class="card mdf-card total_amount_card">
                            <div class="card-body">
                                <h4>Total Amount</h4>
                                <div class="row mb-3 card_row mt-5">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-primary">
                                                <i class="fa fa-dollar-sign"></i>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted text-sm mb-0">{{__('Total Approved Amount')}}</p>
                                                <h4 class="mb-0 text-primary">{{ $cnt_mdf['approved_amt'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 my-3 my-sm-0">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-info">
                                                <i class="fa fa-dollar-sign"></i>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted text-sm mb-0">{{__('Total Fund Amount')}}</p>
                                                <h4 class="mb-0 text-info">{{ $cnt_mdf['fund_amt'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-danger">
                                                <i class="fa fa-dollar-sign"></i>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted text-sm mb-0">{{__('Total Pending Amount')}}</p>
                                                <h4 class="mb-0 text-danger">{{ $cnt_mdf['pending_amt'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ sample-page ] end -->
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
                                    <th>{{__('MDF')}}</th>
                                    <th>{{__('Date Created')}}</th>
                                    <th>{{__('Request From')}}</th>
                                    <th>{{__('Requested Amount')}}</th>
                                    <th>{{__('Approved Amount')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Sub Type')}}</th>
                                    @if(\Auth::user()->type != 'Client')
                                        <th>{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($mdfs as $mdf)
                                    <tr>
                                        <td class="Id">
                                            @can('View MDF')
                                                @if(!empty($mdf->approvedAmt) || Auth::user()->type == 'Owner')
                                                    <a href="{{route('mdf.show',$mdf->id)}}" class="btn  btn-outline-primary"> <i class="fas fa-file-invoice"></i> {{ Auth::user()->mdfNumberFormat($mdf->mdf_id) }}</a>
                                                @else
                                                    {{ Auth::user()->mdfNumberFormat($mdf->mdf_id) }}
                                                @endif
                                            @else
                                                {{ Auth::user()->mdfNumberFormat($mdf->mdf_id) }}
                                            @endcan
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($mdf->created_at) }}</td>
                                        <td>{{ $mdf->user->name }}</td>
                                        <td>{{ \Auth::user()->priceFormat($mdf->amount) }}</td>
                                        <td>{{ \Auth::user()->priceFormat((!empty($mdf->approvedAmt->amount) ? $mdf->approvedAmt->amount : 0)) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($mdf->date) }}</td>
                                        <td>{{ $mdf->statusDetail->name }}</td>
                                        <td>{{ $mdf->typeDetail->name }}</td>
                                        <td>{{ isset($mdf->subTypeDetail->name)?($mdf->subTypeDetail->name):'' }}</td>
                                        @if(\Auth::user()->type != 'Client')
                                            <td class="Action">
                                                <span>
                                                @if($mdf->is_complete == '0')
                                                        @if(\Auth::user()->can('Create MDF Payment') && \Auth::user()->type == 'Owner')
                                                            @if(!empty($mdf->approvedAmt))
                                                                <div class="action-btn btn-success ms-2">
                                                                    <a href="#" class="btn btn-icon btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Approved')}}" data-toggle="tooltip" data-original-title="{{__('Approved')}}"><i class="ti ti-circle-check text-white"></i></a>
                                                                </div>
                                                                
                                                            @else
                                                                <div class="action-btn btn-info ms-2">
                                                                    <a href="#" data-size="lg" data-url="{{ route('mdf.payments.approved',[$mdf->id,'approved'])}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Approved Amount')}}" data-ajax-popup="true" data-title="{{__('Approved Amount')}}" data-toggle="tooltip" data-original-title="{{__('Approved Amount')}}" class="btn btn-icon btn-sm"><i class="ti ti-circle-check text-white"></i></a>
                                                                </div>
                                                                
                                                            @endif
                                                        @endif
                                                    @endif
                                                    @can('View MDF')
                                                        @if(!empty($mdf->approvedAmt) || Auth::user()->type == 'Owner')
                                                            <div class="action-btn btn-warning ms-2">
                                                                <a href="{{route('mdf.show',$mdf->id)}}" class="btn btn-icon btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}" data-original-title="{{ __('View') }}"><i class="ti ti-eye text-white"></i></a>
                                                            </div>

                                                            
                                                        @endif
                                                    @endcan
                                                    @if($mdf->is_complete == '0')
                                                        @can('Edit MDF')
                                                            <div class="action-btn btn-info ms-2">
                                                                <a href="#" data-url="{{ route('mdf.edit',$mdf->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit MDF')}}" data-ajax-popup="true" data-title="{{__('Edit MDF')}}" class="btn btn-icon btn-sm" ><i class="ti ti-pencil text-white"></i></a>
                                                            </div>
                                                            
                                                        @endcan
                                                        @can('Delete MDF')
                                                            @if(empty($mdf->approvedAmt) || Auth::user()->type == 'Owner')
                                                                <div class="action-btn btn-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['mdf.destroy', $mdf->id]]) !!}
                                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete MDF')}}">
                                                                           <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                                
                                                            @endif
                                                        @endcan
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
@endsection
