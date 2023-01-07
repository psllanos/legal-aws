@extends('layouts.admin')

@section('title')
    {{ __('Company') }}
@endsection

@push('head')
    <link rel="stylesheet" href="{{asset('assets/modules/bootstrap-social/bootstrap-social.css')}}">
    <link rel="stylesheet" href="{{asset('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endpush

@push('script')
    <script src="{{asset('assets/modules/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
@endpush

@section('action-button')
    @can('Create Company')
        <a href="#" class="btn btn-primary btn-sm" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Company')}}" data-url="{{route('companies.create')}}"><i class="fas fa-plus-circle"></i> {{__('Add')}} </a>
    @endcan
@endsection

@section('content')

    <div class="row">

        @foreach($companies as $company)

            <div class="col-md-6">

                <div class="card author-box card-primary">
                    <div class="card-body">

                        <div class="author-box-name">
                            <a href="{{route('companies.show',$company->id)}}">{{$company->name}}</a>
                            <div class="float-right">
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle btn btn-outline-primary btn-sm" data-toggle="dropdown"> &nbsp;<i class="fas fa-cog"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('Edit Company')
                                            <a href="#" data-url="{{route('companies.edit',$company->id)}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Company')}}" class="dropdown-item has-icon"><i class="fas fa-pencil-alt"></i> {{__('Edit')}}</a>
                                        @endcan
                                        @can('Delete Company')
                                            <a href="#" class="dropdown-item has-icon text-danger"  data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-{{$company->id}}').submit();"><i class="fas fa-trash"></i> {{__('Delete')}}</a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['companies.destroy', $company->id],'id'=>'delete-form-'.$company->id]) !!}
                                            {!! Form::close() !!}
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="author-box-job mt-3">
                            <i class="fas fa-envelope"></i> {{$company->email}}
                            @if($company->phone)<br><i class="fas fa-phone"></i> {{$company->phone}}@endif
                            @if($company->address)
                                <div><i class="fas fa-home"></i> {{$company->address}} {{$company->city}} {{$company->state}} {{$company->zip_code}} {{$company->country}}</div>@endif
                        </div>
                        <div class="author-box-description">
                            @foreach ($company->clients() as $client)
                                <img data-toggle="tooltip" data-original-title="{{$client->name}}" alt="image" @if($client->avatar) src="{{asset('/storage/avatars/'.$client->avatar)}}" @else src="{{asset('assets/img/avatar/avatar-1.png')}}" @endif class="rounded-circle profile-widget-picture" width="30">
                            @endforeach
                        </div>
                        @if($company->facebook)
                            <a href="{{$company->facebook}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Facebook')}}" class="btn btn-social-icon mr-1 btn-facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if($company->skype)
                            <a href="{{$company->skype}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Skype')}}" class="btn btn-social-icon mr-1 btn-dropbox">
                                <i class="fab fa-skype"></i>
                            </a>
                        @endif
                        @if($company->linkedin)
                            <a href="{{$company->linkedin}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Linkedin')}}" class="btn btn-social-icon mr-1 btn-linkedin">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        @endif
                        @if($company->twitter)
                            <a href="{{$company->twitter}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Twitter')}}" class="btn btn-social-icon mr-1 btn-twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        @if($company->youtube)
                            <a href="{{$company->youtube}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Youtube')}}" class="btn btn-social-icon mr-1 btn-google">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                        @if($company->pinterest)
                            <a href="{{$company->pinterest}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Pinterest')}}" class="btn btn-social-icon mr-1 btn-pinterest">
                                <i class="fab fa-pinterest"></i>
                            </a>
                        @endif
                        @if($company->tumblr)
                            <a href="{{$company->tumblr}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Tumblr')}}" class="btn btn-social-icon mr-1 btn-tumblr">
                                <i class="fab fa-tumblr"></i>
                            </a>
                        @endif
                        @if($company->instagram)
                            <a href="{{$company->instagram}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Instagram')}}" class="btn btn-social-icon mr-1 btn-instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if($company->github)
                            <a href="{{$company->github}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Github')}}" class="btn btn-social-icon mr-1 btn-github">
                                <i class="fab fa-github"></i>
                            </a>
                        @endif
                        @if($company->digg)
                            <a href="{{$company->digg}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Digg')}}" class="btn btn-social-icon mr-1 btn-bitbucket">
                                <i class="fab fa-digg"></i>
                            </a>
                        @endif
                        <div class="w-100 d-sm-none"></div>

                    </div>
                    <div class="card-footer border-top">

                        <div class="row">
                            <div class="col-md-3 col-xs-3 text-center border-right">
                                <div class="font-bold">{{__('Clients')}} </div>
                                {{count($company->clients())}}
                            </div>
                            <div class="col-md-3 col-xs-3 text-center border-right">
                                <div class="font-bold">{{__('Invoices')}} </div>
                                {{count($company->invoices)}}
                            </div>
                            <div class="col-md-3 col-xs-3 text-center border-right">
                                <div class="font-bold">{{__('Deals')}} </div>
                                {{count($company->deals)}}
                            </div>
                            <div class="col-md-3 col-xs-3 text-center">
                                <div class="font-bold">{{__('Contacts')}} </div>
                                {{count($company->contacts())}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection
