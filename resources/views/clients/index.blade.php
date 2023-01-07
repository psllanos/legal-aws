@extends('layouts.admin')

@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('title')
    {{ __('Clients') }}
@endsection

@section('action-button')

        <a href="{{route('client.export')}}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-original-title="{{__('Export')}}"  >
            <i class="ti ti-file-export text-white"></i>
        </a>
        <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Import')}}" data-size="md" data-ajax-popup="true" data-title="{{__('Import client CSV file')}}" data-url="{{route('client.file.import')}}">
            <i class="ti ti-file-import text-white"></i>
        </a>
         @can('Create Client')
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create User')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create User')}}" data-url="{{route('clients.create')}}"><i class="ti ti-plus text-white"></i></a>
        @endcan

@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Client')}}</li>
@endsection



@section('content')
    <div class="row">


        @foreach($clients as $client)
           <div class="col-md-3 col-sm-6 col-md-3">
                <div class="card text-white text-center">
                    @if($client->is_active == 1)
                        <div class="card-header border-0 pb-0">
                            <div class="card-header-right">
                                <div class="btn-group card-option">
                                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{route('clients.show',$client->id)}}" class="dropdown-item">
                                            <i class="ti ti-eye"></i>
                                            <span>{{__('View')}}</span>
                                        </a>
                                        @can('Edit Client')

                                     <!--        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModal" data-url="{{route('clients.edit',$client->id)}}" data-bs-whatever="{{__('Edit Client')}}"><i class="far fa-edit"></i>
                                                <span>{{__('Edit')}}</span></a>  -->

                                            <a href="#" class="dropdown-item" data-url="{{route('clients.edit',$client->id)}}" data-ajax-popup="true" data-title="{{__('Edit Client')}}"><i class="ti ti-pencil"></i>
                                                <span>{{__('Edit')}}</span></a>

                                        @endcan

                                        @can('Delete Client')
                                             {!! Form::open(['method' => 'DELETE', 'route' => ['clients.destroy', $client->id],'id'=>'delete-form-'.$client['id']]) !!}
                                                    <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm">
                                                       <i class="ti ti-archive"></i>
                                                       <span class="ms-1">{{__('Delete')}} </span>
                                                    </a>

                                                {!! Form::close() !!}
                                        @endcan

                                        <a href="#" class="dropdown-item" data-url="{{route('user.reset',\Crypt::encrypt($client->id))}}" data-ajax-popup="true" data-title="{{__('Reset Password')}}" data-size="md" data-toggle="tooltip" data-original-title="{{__('Reset Password')}}"><i class="ti ti-key"></i>
                                            <span>{{__('Reset Password')}}</span>
                                        </a>


                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="#" class="action-item"><i class="fas fa-lock"></i></a>
                    @endif

                    <div class="card-body">
                        {{-- <a @if($client->avatar) href="{{asset('/storage/avatars/'.$client->avatar)}}" @else href="{{asset('/storage/avatars/avatar.png')}}" @endif target="_blank">
                            <img @if($client->avatar) src="{{asset('/storage/avatars/'.$client->avatar)}}" @else src="{{asset('custom/img/avatar/avatar-1.png')}}" @endif alt="user-image" class="img-fluid rounded-circle" style="height100px;width:100px;">
                        </a> --}}
                        <a href="{{(!empty($client->avatar))?  \App\Models\Utility::get_file($client->avatar): $logo."avatar.png"}}" target="_blank">
                            <img src="{{(!empty($client->avatar))?  \App\Models\Utility::get_file($client->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" style="height100px;width:100px;">
                        </a>

                        <h4 class="mt-2"><a href="{{route('clients.show',$client->id)}}">{{$client->name}}</a></h4>

                        <h6 class="office-time mb-0 mb-4">{{$client->email}}</h6>

                        <div class="col-12">
                            <hr class="my-3">
                        </div>

                            <div class="row g-2">
                                <div class="col-6 col-sm-4">
                                    <div class="d-grid">
                                        <span class="d-block  font-weight-bold mb-0 text-dark">{{$client->clientDeals->count()}}</span>
                                        <span class="d-block text-muted">{{__('Deals')}}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <div class="d-grid">
                                        <span class="d-block font-weight-bold mb-0 text-dark">{{$client->getInvoiceCount($client->id)}}</span>
                                        <span class="d-block text-muted">{{__('Invoices')}}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="d-grid">
                                        <span class="d-block font-weight-bold mb-0 text-dark">{{ $client->clientEstimations->count() }}</span>
                                        <span class="d-block text-muted">{{__('Estimations')}}</span>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        @endforeach

        @can('Create Client')
            <div class="col-md-3">

                <a href="#" class="btn-addnew-project" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Client')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Client')}}" data-url="{{route('clients.create')}}">
                    <div class="bg-primary proj-add-icon">
                        <i class="ti ti-plus"></i>
                    </div>
                    <h6 class="mt-4 mb-2">New Client</h6>
                    <p class="text-muted text-center">Click here to add New Client</p>
                </a>
            </div>
        @endcan


    </div>


@endsection

