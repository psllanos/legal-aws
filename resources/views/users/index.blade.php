@extends('layouts.admin')

@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('title')
    {{ __('Manage Users') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('User')}}</li>
@endsection

@section('action-button')

    <div class="row m-1">

         <div class="col-auto pe-0">
            <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create User')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create User')}}" data-url="{{route('users.create')}}"><i class="ti ti-plus text-white"></i></a>
        </div>
    </div>

@endsection


@section('content')
    <div class="row">
        @foreach($users as $user)
            <div class="col-md-3 col-sm-6 col-md-3">
                <div class="card text-white text-center">
                    @if(Gate::check('Edit User') || Gate::check('Delete User'))
                        <div class="card-header border-0 pb-0">
                            @if(\Auth::user()->type == 'Super Admin')
                                <div class="d-flex align-items-center">

                                    <div class="d-grid">
                                        <div class="badge bg-primary p-2 px-3 rounded">{{!empty($user->getPlan()->first())?$user->getPlan()->name:''}}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex align-items-center">
                                    <div class="d-grid">
                                        <div class="badge bg-primary p-2 px-3 rounded">{{ ucfirst($user->type) }}
                                        </div>
                                    </div>
                                </div>

                            @endif
                            <div class="card-header-right">
                                @if($user->is_active == 1)
                                    <div class="btn-group card-option">
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if(\Auth::user()->type != 'Super Admin')
                                                <a href="{{route('users.show',$user->id)}}" class="dropdown-item ms-1">
                                                    <i class="ti ti-eye"></i>
                                                    <span>{{__('View')}}</span>
                                                </a>
                                            @endif
                                            @can('Edit User')
                                                 <a href="#" class="dropdown-item " data-url="{{ route('users.edit',$user->id) }}" data-ajax-popup="true" data-size="lg"  data-title="{{__('Edit User')}}"><i class="ti ti-pencil mx-1"></i>{{__('Edit')}}</a>
                                            @endcan
                                            @can('Delete User')
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user['id']],'id'=>'delete-form-'.$user['id']]) !!}
                                                    <a href="#!" class="dropdown-item show_confirm">
                                                       <i class="ti ti-archive"></i><span class="ms-1">@if($user->delete_status == 0){{__('Delete')}} @else {{__('Restore')}}@endif</span>
                                                    </a>
                                                {!! Form::close() !!}
                                            @endcan


                                             <a href="#" class="dropdown-item" data-url="{{route('user.reset',\Crypt::encrypt($user->id))}}" data-ajax-popup="true" data-size="md" data-title="{{__('Reset Password')}}" data-toggle="tooltip" data-original-title="{{__('Reset Password')}}"><i class="ti ti-key"></i>
                                                <span>{{__('Reset Password')}}</span>
                                            </a>

                                            @if(\Auth::user()->type == 'Super Admin')
                                                <a href="#" class="dropdown-item" data-size="lg" data-url="{{ route('plan.upgrade',$user->id) }}" data-ajax-popup="true" data-title="{{__('Upgrade Plan')}}" data-toggle="tooltip" data-original-title="{{__('Upgrade Plan')}}"><i class="ti ti-award"></i>
                                                    {{__('Upgrade Plan')}}
                                                </a>
                                            @endif


                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="card-body">
                        {{-- <img alt="user-image" class="img-fluid rounded-circle card-avatar" src="{{(!empty($user->avatar))? asset(Storage::url("avatars/".$user->avatar)): asset(Storage::url("avatars/avatar.png"))}}" style="height:100px;width:100px;"> --}}
                       <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" target="_blank">
                            <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="100">
                        </a>
                        @if(\Auth::user()->type != 'Super Admin')
                            <h4 class="mt-2"><a href="{{route('users.show',$user->id)}}">{{ $user->name }}</a></h4>
                        @else
                             <h4 class="mt-2"><a href="{{route('users.show',$user->id)}}">{{ $user->name }}</a></h4>
                        @endif
                        @if($user->delete_status == 1)
                            <h5 class="office-time mb-0">{{__('Deleted')}}</h5>
                        @endif


                        <h6 class="office-time mb-0 mb-4">{{ $user->email }}</h6>

                        @if(\Auth::user()->type == 'Super Admin')
                            <div class="col-12">
                                <hr class="my-3">
                            </div>

                            <div class="col-12 text-center pb-2">
                                <span class="text-dark text-xs">{{__('Plan Expired : ') }} {{!empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date): __('Unlimited')}}</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6 col-sm-4">
                                    <div class="d-grid">
                                        <span class="d-block  font-weight-bold mb-0 text-dark">{{ $user->totalUser() }}</span>
                                        <span class="d-block text-muted">{{__('Users')}}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <div class="d-grid">
                                        <span class="d-block font-weight-bold mb-0 text-dark">{{ $user->totalCilent() }}</span>
                                        <span class="d-block text-muted">{{__('Clients')}}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="d-grid">
                                        <span class="d-block font-weight-bold mb-0 text-dark">{{ $user->totalDeals() }}</span>
                                        <span class="d-block text-muted">{{__('Deals')}}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-md-3">
            <a href="#" class="btn-addnew-project" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create User')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create User')}}" data-url="{{route('users.create')}}">
                <div class="bg-primary proj-add-icon">
                    <i class="ti ti-plus"></i>
                </div>
                <h6 class="mt-4 mb-2">{{__('New User')}}</h6>
                <p class="text-muted text-center">{{__('Click here to add New User')}}</p>
            </a>
        </div>

    </div>
@endsection
