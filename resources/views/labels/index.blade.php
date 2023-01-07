@extends('layouts.admin')

@section('title')
    {{__('Manage Labels')}}
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        @can('Create Label')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Label')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Label')}}" data-url="{{route('labels.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
    
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Setup')}}</li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Labels')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if($pipelines)
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    @php($i=0)
                    @foreach($pipelines as $key => $pipeline)
                        <li class="nav-item">
                            <a class="nav-link @if($i==0) active @endif" id="pills-home-tab" data-bs-toggle="pill" href="#tab{{$key}}" role="tab" aria-controls="pills-home" aria-selected="true">{{$pipeline['name']}}</a>
                        </li>
                        @php($i++)
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="col-md-12">
            @if($pipelines)
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content tab-bordered">
                            @php($i=0)
                            @foreach($pipelines as $key => $pipeline)
                                <div class="tab-pane fade show @if($i==0) active @endif" id="tab{{$key}}" role="tabpanel">
                                    <ul class="list-group sortable">
                                        @foreach ($pipeline['labels'] as $label)
                                            <li class="list-group-item" data-id="{{$label->id}}">
                                                <div class="badge rounded p-2 px-3 bg-{{$label->color}}">{{$label->name}}</div>
                                                <span class="float-end">
                                                    @can('Edit Label')
                                                        <div class="action-btn btn-info ms-2">
                                                            <a href="#" data-size="md" data-url="{{ URL::to('labels/'.$label->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Labels')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Labels')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete Label')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['labels.destroy', $label->id]]) !!}
                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endif
                                        </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @php($i++)
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
