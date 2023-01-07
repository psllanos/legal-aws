@extends('layouts.admin')

@php
    $logo = \App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('title')
    {{ __('Manage Leads') }} @if ($pipeline)
        - {{ $pipeline->name }}
    @endif
@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('custom/libs/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Leads') }}</li>
@endsection


@push('script')
    <script src="{{ asset('custom/libs/summernote/summernote-bs4.js') }}"></script>
    @can('Move Lead')
        @if ($pipeline)
            <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
            <script>
                ! function(a) {
                    "use strict";
                    var t = function() {
                        this.$body = a("body")
                    };
                    t.prototype.init = function() {
                        a('[data-plugin="dragula"]').each(function() {
                            var t = a(this).data("containers"),
                                n = [];
                            if (t)
                                for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]);
                            else n = [a(this)[0]];
                            var r = a(this).data("handleclass");
                            r ? dragula(n, {
                                moves: function(a, t, n) {
                                    return n.classList.contains(r)
                                }
                            }) : dragula(n).on('drop', function(el, target, source, sibling) {

                                var order = [];
                                $("#" + target.id + " > div").each(function() {
                                    order[$(this).index()] = $(this).attr('data-id');
                                });

                                var id = $(el).attr('data-id');

                                var old_status = $("#" + source.id).data('status');
                                var new_status = $("#" + target.id).data('status');
                                var stage_id = $(target).attr('data-id');
                                var pipeline_id = '{{ $pipeline->id }}';

                                $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div")
                                    .length);
                                $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div")
                                    .length);
                                show_toastr('{{__("Success")}}', 'card move Successfully!', 'success')
                                $.ajax({
                                    url: '{{ route('leads.order') }}',
                                    type: 'POST',
                                    data: {
                                        lead_id: id,
                                        stage_id: stage_id,
                                        order: order,
                                        new_status: new_status,
                                        old_status: old_status,
                                        pipeline_id: pipeline_id,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(data) {
                                        // console.log(data);
                                    },
                                    error: function(data) {
                                        data = data.responseJSON;
                                        show_toastr('Error', data.error, 'error')
                                    }
                                });
                            });
                        })
                    }, a.Dragula = new t, a.Dragula.Constructor = t
                }(window.jQuery),
                function(a) {
                    "use strict";

                    a.Dragula.init()

                }(window.jQuery);
            </script>
        @endif
    @endcan
    <script>
        $(document).on("change", "#change-pipeline select[name=default_pipeline_id]", function() {
            $('#change-pipeline').submit();
        });
    </script>
@endpush


@section('action-button')
    <div class="row align-items-center">
        @if ($pipeline)
            <div class="col-auto pe-0">
                {{ Form::open(['route' => 'deals.change.pipeline', 'id' => 'change-pipeline']) }}
                {{ Form::select('default_pipeline_id', $pipelines, $pipeline->id, ['class' => 'form-select custom-form-select', 'id' => 'default_pipeline_id']) }}
                {{ Form::close() }}
            </div>
        @endif

        <div class="col-auto pe-0">
            <a href="{{ route('leads.list') }}" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('List View') }}" class="btn btn-sm btn-primary btn-icon"><i class="ti ti-list"></i> </a>
        </div>
        @can('Create Lead')
            <div class="col-auto ps-1">
                <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Create Lead') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create Lead') }}"
                    data-url="{{ route('leads.create') }}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    @if ($pipeline)
        <div class="row">
            @php
                $lead_stages = $pipeline->leadStages;
                $json = [];
                foreach ($lead_stages as $lead_stage) {
                    $json[] = 'task-list-' . $lead_stage->id;
                }
            @endphp

            <div class="col-12">
                <div class="row kanban-wrapper horizontal-scroll-cards" data-plugin="dragula"
                    data-containers='{!! json_encode($json) !!}'>
                    @foreach ($lead_stages as $lead_stage)
                        @php($leads = $lead_stage->lead())
                        <div class="col" id="progress">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        <button class="btn btn-sm btn-primary btn-icon count">
                                            {{ count($leads) }}
                                        </button>
                                    </div>
                                    <h4 class="mb-0">{{ $lead_stage->name }}</h4>
                                </div>
                                <div id="task-list-{{ $lead_stage->id }}" data-id="{{ $lead_stage->id }}"
                                    class="card-body kanban-box">
                                    @foreach ($leads as $lead)
                                        <div class="card" data-id="{{ $lead->id }}">
                                            @php($labels = $lead->labels())
                                            <div class="pt-3 ps-3">
                                                @if ($labels)
                                                    @foreach ($labels as $label)
                                                        <div class="badge bg-{{ $label->color }} p-2 px-3 rounded">
                                                            {{ $label->name }}</div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="card-header border-0 pb-0 position-relative">
                                                <h5><a href="@can('View Lead')@if ($lead->is_active){{ route('leads.show', $lead->id) }}@else#@endif @else#@endcan"
                                                        class="text-body">{{ $lead->name }}</a></h5>

                                                <div class="card-header-right">
                                                    <div class="btn-group card-option">
                                                        @if (!$lead->is_active)
                                                            <div class="btn dropdown-toggle">
                                                                <a href="#" class="action-item" data-toggle="dropdown"
                                                                    aria-expanded="false"><i class="fas fa-lock"></i></a>
                                                            </div>
                                                        @else
                                                            <button type="button" class="btn dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="ti ti-dots-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                @can('Edit Lead')
                                                                    <a href="#"
                                                                        data-url="{{ URL::to('leads/' . $lead->id . '/labels') }}"
                                                                        data-ajax-popup="true" data-title="{{ __('Labels') }}"
                                                                        class="dropdown-item">{{ __('Labels') }}</a>
                                                                    <a href="#"
                                                                        data-url="{{ URL::to('leads/' . $lead->id . '/edit') }}"
                                                                        data-size="lg" data-ajax-popup="true"
                                                                        data-title="{{ __('Edit Lead') }}"
                                                                        class="dropdown-item">{{ __('Edit') }}</a>
                                                                @endcan
                                                                @can('Delete Lead')
                                                                    {!! Form::open([
                                                                        'method' => 'DELETE',
                                                                        'route' => ['leads.destroy', $lead->id],
                                                                        'id' => 'delete-form-' . $lead->id,
                                                                    ]) !!}
                                                                    <a class="dropdown-item show_confirm">
                                                                        {{ __('Delete') }}
                                                                    </a>
                                                                    {!! Form::close() !!}
                                                                @endcan
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted text-sm"></p>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <ul class="list-inline mb-0">

                                                    </ul>
                                                    <div class="user-group">
                                                        @foreach ($lead->users as $user)
                                                            <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                                {{-- <img alt="image" data-bs-toggle="tooltip" data-bs-placement="top" title="{{$user->name}}" @if ($user->avatar) src="{{asset('/storage/avatars/'.$user->avatar)}}" @else src="{{asset('custom/img/avatar/avatar-1.png')}}" @endif class="rounded-circle " width="25" height="25"> --}}

                                                                <img src="{{ !empty($user->avatar) ? \App\Models\Utility::get_file($user->avatar) : $logo . 'avatar.png' }}"
                                                                    class="img-fluid rounded-circle">
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
