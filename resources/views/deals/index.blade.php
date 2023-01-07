@extends('layouts.admin')

@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('title')
    {{__('Manage Deals')}} @if($pipeline) - {{$pipeline->name}} @endif
@endsection

@push('head')
    <!-- <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}"> -->
@endpush

@push('script')
    <!-- <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script> -->
    @can("Move Deal")
        @if($pipeline)
            <script src="{{asset('assets/js/plugins/dragula.min.js')}}"></script>
            <script>
                !function (a) {
                    "use strict";
                    var t = function () {
                        this.$body = a("body")
                    };
                    t.prototype.init = function () {
                        a('[data-plugin="dragula"]').each(function () {
                            var t = a(this).data("containers"), n = [];
                            if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                            var r = a(this).data("handleclass");
                            r ? dragula(n, {
                                moves: function (a, t, n) {
                                    return n.classList.contains(r)
                                }
                            }) : dragula(n).on('drop', function (el, target, source, sibling) {

                                var order = [];
                                $("#" + target.id + " > div").each(function () {
                                    order[$(this).index()] = $(this).attr('data-id');
                                });

                                var id = $(el).attr('data-id');

                                var old_status = $("#" + source.id).data('status');
                                var new_status = $("#" + target.id).data('status');
                                var stage_id = $(target).attr('data-id');
                                var pipeline_id = '{{$pipeline->id}}';

                                $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                                $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);
                                show_toastr('{{__("Success")}}', 'Deal successfully updated', 'success')
                                $.ajax({
                                    url: '{{route('deals.order')}}',
                                    type: 'POST',
                                    data: {deal_id: id, stage_id: stage_id, order: order, new_status: new_status, old_status: old_status, pipeline_id: pipeline_id, "_token": $('meta[name="csrf-token"]').attr('content')},
                                    success: function (data) {
                                    },
                                    error: function (data) {
                                        data = data.responseJSON;
                                        show_toastr('Error', data.error, 'error')
                                    }
                                });
                            });
                        })
                    }, a.Dragula = new t, a.Dragula.Constructor = t
                }(window.jQuery), function (a) {
                    "use strict";

                    a.Dragula.init()

                }(window.jQuery);
            </script>
        @endif
    @endcan
    <script>
        $(document).on("change", "#change-pipeline select[name=default_pipeline_id]", function () {
            $('#change-pipeline').submit();
        })
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Deals')}}</li>
@endsection


@section('action-button')

    <div class="row align-items-center ">
        @if($pipeline)
         <div class="col-auto pe-0">
            {{ Form::open(array('route' => 'deals.change.pipeline','id'=>'change-pipeline','class'=>'mr-2')) }}
            {{ Form::select('default_pipeline_id', $pipelines,$pipeline->id, array('class' => 'form-control custom-form-select','id'=>'default_pipeline_id')) }}
            {{ Form::close() }}
        </div>

        @endif

         <div class="col-auto pe-0">
                 <a href="{{ route('deals.list') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('List View')}}" class="btn btn-sm btn-primary btn-icon m-1"><i class="ti ti-list"></i> </a>
        </div>
        @can('Create Deal')
         <div class="col-auto ps-1">
            <a href="#" class="btn btn-sm btn-primary btn-icon m-1 " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Deal')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Deal')}}" data-url="{{route('deals.create')}}"><i class="ti ti-plus text-white"></i></a>
        </div>
        @endcan
    </div>
@endsection

@section('content')
    @if($pipeline)
        <div class="row">
             <div class="col-xl-3 col-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-20">{{__('Total Deals')}}</h6>
                                <h3 class="text-primary">{{ $cnt_deal['total'] }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rocket bg-success text-white"></i>
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
                                <h6 class="m-b-20">{{__('This Month Total Deals')}}</h6>
                                <h3 class="text-info">{{ $cnt_deal['this_month'] }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rocket bg-info text-white"></i>
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
                                <h6 class="m-b-20">{{__('This Week Total Deals')}}</h6>
                                <h3 class="text-warning">{{ $cnt_deal['this_week'] }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rocket bg-warning text-white"></i>
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
                                <h6 class="m-b-20">{{__('Last 30 Days Total Deals')}}</h6>
                                <h3 class="text-danger">{{ $cnt_deal['last_30days'] }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rocket bg-danger text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @php
                    $stages = $pipeline->stages;

                    $json = [];
                    foreach ($stages as $stage){
                        $json[] = 'task-list-'.$stage->id;
                    }
                @endphp

                <div class="row kanban-wrapper horizontal-scroll-cards" data-plugin="dragula" data-containers='{!! json_encode($json) !!}' >
                    @foreach($stages as $stage)
                        @php($deals = $stage->deals())
                        <div class="col" id="progress">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        <button class="btn btn-sm btn-primary btn-icon count" >
                                            {{count($deals)}}
                                        </button>
                                    </div>
                                    <h4 class="mb-0">{{$stage->name}}</h4>
                                </div>
                                <div id="task-list-{{$stage->id}}" data-id="{{$stage->id}}" class="card-body kanban-box">
                                    @foreach($deals as $deal)
                                        <div class="card" data-id="{{$deal->id}}">
                                                @php($labels = $deal->labels())
                                                <div class="pt-3 ps-3">
                                                    @if($labels)
                                                        @foreach($labels as $label)
                                                        <div class="badge bg-{{$label->color}} p-2 px-3 rounded"> {{$label->name}}</div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="card-header border-0 pb-0 position-relative">
                                                    <h5><a href="@can('View Deal') @if($deal->is_active) {{route('deals.show',$deal->id)}} @else # @endif @else # @endcan" class="text-body">{{$deal->name}} </a></h5>
                                                    @if(Auth::user()->type != 'Client')
                                                        <div class="card-header-right">
                                                            <div class="btn-group card-option">
                                                                @if(!$deal->is_active)
                                                                    <div class="btn dropdown-toggle">
                                                                        <a href="#" class="action-item" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-lock"></i></a>
                                                                    </div>

                                                                @else
                                                                    <button type="button" class="btn dropdown-toggle"
                                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    <i class="ti ti-dots-vertical"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                         @can('Edit Deal')
                                                                                <a href="#" data-url="{{ URL::to('deals/'.$deal->id.'/labels') }}" data-ajax-popup="true" data-title="{{__('Labels')}}" class="dropdown-item">{{__('Labels')}}</a>
                                                                                <a href="#" data-url="{{ URL::to('deals/'.$deal->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Deal')}}" class="dropdown-item">{{__('Edit')}}</a>
                                                                            @endcan
                                                                            @can('Delete Deal')
                                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['deals.destroy', $deal->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                                    <a class="dropdown-item show_confirm" >
                                                                                    {{__('Delete')}}
                                                                                       </a>
                                                                                {!! Form::close() !!}
                                                                            @endcan
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                                <div class="card-body">
                                                    <p class="text-muted text-sm">
                                                        {{\Auth::user()->priceFormat($deal->price)}}</p>

                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <ul class="list-inline mb-0">
                                                            <li>
                                                                {{count($deal->tasks)}}/{{count($deal->complete_tasks)}}
                                                            </li>
                                                            <li>
                                                                {{__('Tasks')}}
                                                            </li>
                                                        </ul>
                                                        <div class="user-group">
                                                            @foreach($deal->users as $user)
                                                                <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" target="_blank">
                                                                    <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
