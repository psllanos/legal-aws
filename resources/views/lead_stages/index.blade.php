@extends('layouts.admin')

@section('title')
    {{__('Manage Lead Stages')}}
@endsection

@push('script')
    <script src="{{ asset('custom/libs/jquery-ui/jquery-ui.js') }}"></script>
    <script>
        $(function () {
            $(".sortable").sortable();
            $(".sortable").disableSelection();
            $(".sortable").sortable({
                stop: function () {
                    var order = [];
                    $(this).find('li').each(function (index, data) {
                        order[index] = $(data).attr('data-id');
                    });

                    $.ajax({
                        url: "{{route('lead_stages.order')}}",
                        data: {order: order, _token: $('meta[name="csrf-token"]').attr('content')},
                        type: 'POST',
                        success: function (data) {
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('Error', data.error, 'error')
                        }
                    })
                }
            });
        });
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Setup')}}</li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Lead Stages')}}</li>
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        @can('Create Lead Stage')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Lead Stage')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Lead Stage')}}" data-url="{{route('lead_stages.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
    
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                @php($i=0)
                @foreach($pipelines as $key => $pipeline)
                    <li class="nav-item">
                        <a class="nav-link @if($i==0) active @endif" id="pills-home-tab" data-bs-toggle="pill" href="#tab{{$key}}" role="tab" aria-controls="pills-home" aria-selected="true">{{$pipeline['name']}}</a>
                    </li>
                    @php($i++)
                @endforeach
            </ul>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content tab-bordered">
                        @php($i=0)
                        @foreach($pipelines as $key => $pipeline)
                            <div class="tab-pane fade show @if($i==0) active @endif" id="tab{{$key}}" role="tabpanel">
                                <ul class="list-group sortable">
                                    @foreach ($pipeline['lead_stages'] as $lead_stages)
                                        <li class="list-group-item" data-id="{{$lead_stages->id}}">
                                            <span class="text-m text-dark">{{$lead_stages->name}}</span>
                                            <span class="float-end">
                                                @can('Edit Lead Stage')
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-size="md" data-url="{{ URL::to('lead_stages/'.$lead_stages->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Lead Stages')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Lead Stages')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                    </div>
                                                @endcan
                                                @if(count($pipeline['lead_stages']))
                                                    @can('Delete Lead Stage')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['lead_stages.destroy', $lead_stages->id]]) !!}
                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
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
        </div>
    </div>

    <div class="alert alert-dark" role="alert">
        {{__('Note : You can easily change order of Lead stage using drag & drop.')}}
    </div>

@endsection
