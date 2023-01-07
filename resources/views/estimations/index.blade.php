@extends('layouts.admin')

@section('title')
    {{__('Manage Estimate')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Estimate')}}</li>
@endsection

@section('action-button')

             <a href="{{route('estimation.export')}}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-original-title="{{__('Export')}}"  >
                <i class="ti ti-file-export text-white"></i>
            </a>
        @can('Create Estimation')
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Estimate')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Estimate')}}" data-url="{{route('estimations.create')}}"><i class="ti ti-plus text-white"></i></a>
        @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Estimate')}}</h6>
                            <h3 class="text-primary">{{ $cnt_estimation['total'] }}</h3>
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
                            <h3 class="text-info">{{ $cnt_estimation['this_month'] }}</h3>
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
                            <h3 class="text-warning">{{ $cnt_estimation['this_week'] }}</h3>
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
                            <h3 class="text-danger">{{ $cnt_estimation['last_30days'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table pc-dt-simple">
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
                                            <a class="btn  btn-outline-primary" href="{{route('estimations.show',$estimate->id)}}"> <i class="fas fa-file-estimate"></i> {{ Auth::user()->estimateNumberFormat($estimate->estimation_id) }}</a>
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
                                            <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @elseif($estimate->status == 2)
                                            <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @elseif($estimate->status == 3)
                                            <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @elseif($estimate->status == 4)
                                            <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Estimation::$statues[$estimate->status]) }}</span>
                                        @endif
                                    </td>
                                    @if(Auth::user()->type != 'Client')
                                        <td class="Action">
                                            <span>
                                            @can('View Estimation')
                                                    <div class="action-btn btn-warning ms-2">
                                                        <a href="{{route('estimations.show',$estimate->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Show Estimation')}}" ><i class="ti ti-eye text-white"></i></a>
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
@endsection
