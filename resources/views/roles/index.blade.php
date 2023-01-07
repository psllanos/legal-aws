@extends('layouts.admin')

@section('title')
    {{__('Manage Roles')}}
@endsection

@section('action-button')

    <div class="row m-1">
         <div class="col-auto pe-0">
            <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Role')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Role')}}" data-url="{{route('roles.create')}}"><i class="ti ti-plus text-white"></i></a>
        </div>
    </div>


@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Role')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card"> 
                <div class="card-header card-body table-border-style">  
                    <div class="table-responsive">
                        <table class="table pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{__('Role')}}</th>
                                    <th >{{__('Permissions')}}</th>
                                    <th width="200px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr >
                                        <td class="Role">{{ $role->name }}</td>
                                        <td class="Permission"style="white-space: inherit !important;"  >
                                            @foreach($role->permissions()->pluck('name') as $permission)
                                            <span class="badge rounded p-2 m-1 px-3 bg-primary ">
                                                <a href="#" class="absent-btn text-white" >{{$permission}}</a>
                                            </span>
                                            @endforeach
                                        </td>
                                        <td class="Action">
                                            <span>
                                                @can('Edit Role')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="lg" data-url="{{ URL::to('roles/'.$role->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Role')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Role')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>

                                                @endcan
                                                @can('Delete Role')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id],'id'=>'delete-form-'.$role->id]) !!}
                                                            <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Role')}}">
                                                               <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endif
                                            </span>
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
