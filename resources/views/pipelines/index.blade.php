@extends('layouts.admin')

@section('title')
    {{__('Manage Pipelines')}}
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        <div class="col-auto pe-0">
            @can('Create Pipeline')
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Pipeline')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Pipeline')}}" data-url="{{route('pipelines.create')}}"><i class="ti ti-plus text-white"></i></a>
            @endcan
        </div>
    </div>


@endsection


@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Setup')}}</li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Pipelines')}}</li>
@endsection


@section('content')

    <div class="row">
        <div class="col-xl-12">
            <div class="card table-card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{__('Pipeline')}}</th>
                                    <th width="250px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pipelines as $pipeline)
                                    <tr>
                                        <td>{{ $pipeline->name }}</td>
                                        <td class="Action">
                                            <span>
                                            @can('Edit Pipeline')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="lg" data-url="{{ URL::to('pipelines/'.$pipeline->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Pipeline')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Pipeline')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                                @endcan
                                                @if(count($pipelines) > 1)
                                                    @can('Delete Pipeline')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['pipelines.destroy', $pipeline->id]]) !!}
                                                            <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
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
