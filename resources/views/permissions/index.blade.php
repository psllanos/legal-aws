@extends('layouts.admin')

@section('title')
    {{__('Manage Permissions')}}
@endsection





@section('action-button')
    @can('Create Permission')
        <div class="row m-1">
             <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Permission')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Permission')}}" data-url="{{ route('permissions.create') }}"><i class="ti ti-plus text-white"></i></a>
            </div>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card"> 
                <div class="card-header card-body table-border-style">  
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{__('Permissions')}}</th>
                                    <th class="text-right" width="200px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td class="text-right">
                                            @can('Edit Permission')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="lg" data-url="{{ URL::to('permissions/'.$permission->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Permission')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Permission')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('Delete Permission')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['permissions.destroy', $permission->id]]) !!}
                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm">
                                                           <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endif
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
