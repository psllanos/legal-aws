@extends('layouts.admin')

@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('title')
    {{$deal->name}}
@endsection

@push('head')
    <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('custom/libs/dropzonejs/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('custom/libs/bootstrap-timepicker/css/bootstrap-timepicker.css')}}">
    <style>
        .nav-tabs .nav-link-tabs.active {
            background: none;
        }
    </style>

    @if($calenderTasks)
        <!-- <link rel="stylesheet" href="{{asset('custom/libs/fullcalendar/dist/fullcalendar.min.css')}}"> -->
    @endif
@endpush

@push('script')
    <!-- <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script> -->
    <!-- <script src="{{asset('custom/libs/dropzonejs/min/dropzone.min.js')}}"></script> -->
    <!-- <script src="{{asset('custom/libs/bootstrap-timepicker/js/bootstrap-timepicker.js')}}"></script> -->
    <script>
        $(document).on("change", "#change-deal-status select[name=deal_status]", function () {
            $('#change-deal-status').submit();
        });

        @if(Auth::user()->type != 'Client' || in_array('Client View Files',$permission))
            Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {

            url: "{{route('deals.file.upload',$deal->id)}}",
            success: function (file, response) {
                if (response.is_success) {
                    dropzoneBtn(file, response);
                    show_toastr('{{__("Success")}}', 'Attachment Create Successfully!', 'success');
                } else {
                    myDropzone.removeFile(file);
                    show_toastr('{{__("Error")}}', 'File type must be match with Storage setting.', 'error');
                }
            },
            error: function (file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('Error', response.error, 'error');
                } else {
                    show_toastr('Error', response, 'error');
                }
            }
        });
        myDropzone.on("sending", function (file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("deal_id", {{$deal->id}});
        });

        myDropzone2 = new Dropzone("#dropzonewidget2", {
            
            url: "{{route('deals.file.upload',$deal->id)}}",
            success: function (file, response) {
                if (response.is_success) {
                    dropzoneBtn(file, response);
                    show_toastr('{{__("Success")}}', 'Attachment Create Successfully!', 'success');
                } else {
                    myDropzone2.removeFile(file);
                    show_toastr('{{__("Error")}}', 'File type must be match with Storage setting.', 'error');
                }
            },
            error: function (file, response) {
                myDropzone2.removeFile(file);
                if (response.error) {
                    show_toastr('Error', response.error, 'error');
                } else {
                    show_toastr('Error', response, 'error');
                }
            }
        });
        myDropzone2.on("sending", function (file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("deal_id", {{$deal->id}});
        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "btn btn-sm btn-primary m-1");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{__('Download')}}");
            download.innerHTML = "<i class='ti ti-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "btn btn-sm btn-danger mx-1");
            del.setAttribute('data-toggle', "tooltip");
            del.setAttribute('data-original-title', "{{__('Delete')}}");
            del.innerHTML = "<i class='ti ti-trash'></i>";

            del.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm("Are you sure ?")) {
                    var btn = $(this);
                    $.ajax({
                        url: btn.attr('href'),
                        data: {_token: $('meta[name="csrf-token"]').attr('content')},
                        type: 'DELETE',
                        success: function (response) {
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                show_toastr('Error', response.error, 'error');
                            }
                        },
                        error: function (response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('Error', response.error, 'error');
                            } else {
                                show_toastr('Error', response, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.appendChild(download);
            @if(Auth::user()->type != 'Client')
            @can('Edit Deal')
            html.appendChild(del);
            @endcan
            @endif

            file.previewTemplate.appendChild(html);

            // if ($(".top-5-scroll").length) {
            //     $(".top-5-scroll").css({
            //         height: 315
            //     }).niceScroll();
            // }
        }
        @foreach($deal->files as $file)
        // Create the mock file:
        var mockFile = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('deal_files/'.$file->file_path))}} };
        // Call the default addedfile event handler
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, "{{asset(Storage::url('deal_files/'.$file->file_path))}}");
        myDropzone.emit("complete", mockFile);

        dropzoneBtn(mockFile, {download: "{{route('deals.file.download',[$deal->id,$file->id])}}", delete: "{{route('deals.file.delete',[$deal->id,$file->id])}}"});

        // Create the mock file:
        var mockFile2 = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('deal_files/'.$file->file_path))}} };
        // Call the default addedfile event handler
        myDropzone2.emit("addedfile", mockFile2);
        // And optionally show the thumbnail of the file:
        myDropzone2.emit("thumbnail", mockFile2, "{{asset(Storage::url('deal_files/'.$file->file_path))}}");
        myDropzone2.emit("complete", mockFile2);

        dropzoneBtn(mockFile2, {download: "{{route('deals.file.download',[$deal->id,$file->id])}}", delete: "{{route('deals.file.delete',[$deal->id,$file->id])}}"});

        @endforeach
        @endif
        @can('Edit Deal')
        $('.summernote-simple').on('summernote.blur', function () {
            $.ajax({
                url: "{{route('deals.note.store',$deal->id)}}",
                data: {_token: $('meta[name="csrf-token"]').attr('content'), notes: $(this).val()},
                type: 'POST',
                success: function (response) {
                    if (response.is_success) {
                        // show_toastr('Success', response.success,'success');
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('Error', response.error, 'error');
                    } else {
                        show_toastr('Error', response, 'error');
                    }
                }
            })
        });
        @else
        $('.summernote-simple').summernote('disable');
        @endcan

        @can('Edit Task')
        $(document).on("click", ".task-checkbox", function () {
            var chbox = $(this);
            var lbl = chbox.parent().parent().find('label');

            $.ajax({
                url: chbox.attr('data-url'),
                data: {_token: $('meta[name="csrf-token"]').attr('content'), status: chbox.val()},
                type: 'PUT',
                success: function (response) {
                    if (response.is_success) {
                        chbox.val(response.status);
                        if (response.status) {
                            lbl.addClass('strike');
                            lbl.find('.badge').removeClass('bg-warning').addClass('bg-success');
                        } else {
                            lbl.removeClass('strike');
                            lbl.find('.badge').removeClass('bg-success').addClass('bg-warning');
                        }
                        lbl.find('.badge').html(response.status_label);

                        show_toastr('Success', response.success, 'success');
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('Error', response.error, 'error');
                    } else {
                        show_toastr('Error', response, 'error');
                    }
                }
            })
        });
        @endcan

        $(document).ready(function () {
            var tab = 'general';
                @if ($tab = Session::get('status'))
            var tab = '{{ $tab }}';
            @endif
            $("#myTab2 .nav-link-tabs[href='#" + tab + "']").trigger("click");
        });
    </script>

    @if($calenderTasks)
        <!-- <script src="{{ asset('custom/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script> -->
        <script>

            $(document).ready(function () {
                var etitle;
                var etype;
                var etypeclass;
                var calendar = new FullCalendar.Calendar(document.getElementById('event_calendar'), {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'timeGridDay,timeGridWeek,dayGridMonth'
                    },
                    buttonText: {
                    timeGridDay: "{{__('Day')}}",
                    timeGridWeek: "{{__('Week')}}",
                    dayGridMonth: "{{__('Month')}}"
                    },
                    themeSystem: 'bootstrap',
                    initialDate: '{{ $transdate }}',
                    slotDuration: '00:10:00',
                    navLinks: true,
                    droppable: true,
                    selectable: true,
                    selectMirror: true,
                    editable: true,
                    dayMaxEvents: true,
                    handleWindowResize: true,
                    events: {!! json_encode($calenderTasks) !!},
                });
                calendar.render();


                $(document).on('click', '.fc-daygrid-event', function (e) {
                    if (!$(this).hasClass('deal')) {
                        e.preventDefault();
                        var event = $(this);
                        var title = $(this).find('.fc-event-title-container .fc-event-title').html();
                        var size = 'md';
                        var url = $(this).attr('href');
                        $("#commonModal .modal-title").html(title);
                        $("#commonModal .modal-dialog").addClass('modal-' + size);

                        $.ajax({
                            url: url,
                            success: function (data) {
                                $('#commonModal .modal-body').html(data);
                                $("#commonModal").modal('show');
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('Error', data.error, 'error')
                            }
                        });
                    }
                });
            });
        </script>
    @endif
@endpush

@section('breadcrumb')
<li class="breadcrumb-item" aria-current="page"><a href="{{route('deals.index')}}">{{__('Deals')}}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{__($deal->name)}}</li>
@endsection

@section('action-button')

        @can('Edit Deal')
               <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Labels')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Label')}}" data-url="{{ URL::to('deals/'.$deal->id.'/labels') }}"><i class="ti ti-tag text-white"></i></a>

            <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit Deal')}}" data-url="{{ URL::to('deals/'.$deal->id.'/edit') }}"><i class="ti ti-pencil text-white"></i></a>
        @endcan
        @if($deal->status == 'Won')
            <a href="#" class="btn btn-sm btn-success btn-icon ">{{__($deal->status)}}</a>
        @elseif($deal->status == 'Loss')
            <a href="#" class="btn btn-sm btn-danger btn-icon">{{__($deal->status)}}</a>
        @else
            <a href="#" class="btn btn-sm btn-info btn-icon">{{__($deal->status)}}</a>
        @endif

@endsection

@section('content')
    @php($labels = $deal->labels())
    @if($labels)
        <div class="row">
            <div class="col-12 mb-2">
                <div class="text-end">
                    @foreach($labels as $label)
                        <span class="badge bg-{{$label->color}} p-2 px-3 rounded">{{$label->name}}</span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12 mb-3">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#general" role="tab" aria-controls="pills-home" aria-selected="true">{{__('General')}}</a>
                </li>
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Tasks',$permission))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-tasks-tab" data-bs-toggle="pill" href="#tasks" role="tab" aria-controls="tasks" aria-selected="false">{{__('Tasks')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Products',$permission))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-products-tab" data-bs-toggle="pill" href="#products" role="tab" aria-controls="products" aria-selected="false">{{__('Products')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Sources',$permission))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-sources-tab" data-bs-toggle="pill" href="#sources" role="tab" aria-controls="sources" aria-selected="false">{{__('Sources')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Files',$permission))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-files-tab" data-bs-toggle="pill" href="#files" role="tab" aria-controls="files" aria-selected="false">{{__('Files')}}</a>
                    </li>
                @endif
                    <li class="nav-item">
                        <a class="nav-link" id="pills-discussion-tab" data-bs-toggle="pill" href="#discussion" role="tab" aria-controls="discussion" aria-selected="false">{{__('Discussion')}}</a>
                    </li>

                 @can('Edit Deal')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-notes-tab" data-bs-toggle="pill" href="#notes" role="tab" aria-controls="notes" aria-selected="false">{{__('Notes')}}</a>
                    </li>
                @endcan
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Invoices',$permission))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-invoices-tab" data-bs-toggle="pill" href="#invoices" role="tab" aria-controls="invoices" aria-selected="false">{{__('Invoices')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Custom fields',$permission))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-custom_fields-tab" data-bs-toggle="pill" href="#custom_fields" role="tab" aria-controls="custom_fields" aria-selected="false">{{__('Custom Fields')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Super Admin')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-clients-tab" data-bs-toggle="pill" href="#clients" role="tab" aria-controls="clients" aria-selected="false">{{__('Clients')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Super Admin')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-calls-tab" data-bs-toggle="pill" href="#calls" role="tab" aria-controls="calls" aria-selected="false">{{__('Calls')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Super Admin')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-emails-tab" data-bs-toggle="pill" href="#emails" role="tab" aria-controls="emails" aria-selected="false">{{__('Emails')}}</a>
                    </li>
                @endif

            </ul>
        </div>

        <div class="col-12">
            <div class="tab-content tab-bordered">
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="card">
                        <div class="card-header card-body">
                            <ul class="nav nav-pills p-1">
                                <li class="nav-item p-1">
                                    <a  href="#">{{__('Price')}} <span class="badge rounded p-2 px-3 bg-primary">{{\Auth::user()->priceFormat($deal->price)}}</span></a>
                                </li>
                                <li class="nav-item p-1">
                                    <a  href="#">{{__('Pipeline')}} <span class="badge rounded p-2 px-3 bg-success">{{$deal->pipeline->name}}</span></a>
                                </li>
                                <li class="nav-item p-1">
                                    <a href="#">{{__('Stage')}} <span class="badge rounded p-2 px-3 bg-warning">{{$deal->stage->name}}</span></a>
                                </li>
                                <li class="nav-item p-1">
                                    <a href="#">{{__('Created')}} <span class="badge rounded p-2 px-3 bg-secondary">{{\Auth::user()->dateFormat($deal->created_at)}}</span></a>
                                </li>
                                @can('Edit Deal')
                                    <li class="col-sm-1 nav-item deal_status" data-toggle="tooltip" data-original-title="{{__('Change Deal Status')}}">
                                        <span class="py-0">
                                        {{ Form::open(array('route' => array('deals.change.status',$deal->id),'id'=>'change-deal-status','style'=>'margin-right: 10px;')) }}
                                            {{ Form::select('deal_status', \App\Models\Deal::$statues,$deal->status, array('class' => 'form-control select2','id'=>'deal_status')) }}
                                            {{ Form::close() }}
                                        </span>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </div>

                    <?php
                    $tasks = $deal->tasks;
                    $products = $deal->products();
                    $sources = $deal->sources();
                    $invoices = $deal->invoices;
                    $calls = $deal->calls;
                    $emails = $deal->emails;
                    ?>

                    <div class="row">
                        <div class="col">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-b-20">{{__('Task')}}</h6>
                                            <h3 class="text-primary">{{count($tasks)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tasks bg-success text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-b-20">{{__('Product')}}</h6>
                                            <h3 class="text-info">{{count($products)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dolly bg-info text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-b-20">{{__('Source')}}</h6>
                                            <h3 class="text-warning">{{count($sources)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-eye bg-warning text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-b-20">{{__('Files')}}</h6>
                                            <h3 class="text-danger">{{count($deal->files)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-alt bg-danger text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-b-20">{{__('Invoices')}}</h6>
                                            <h3 class="text-dark">{{count($invoices)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-invoice bg-dark text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if(Auth::user()->type != 'Super Admin' || in_array('Client View Members',$permission))
                            <div class="col-xl-4 col-lg-4 col-sm-6 col-md-6">
                                <div class="card table-card deal-card">
                                    <div class="card-header">
                                        <h5>{{__('Users')}}
                                            @can('Edit Deal')
                                                <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add User')}}" data-url="{{ route('deals.users.edit',$deal->id) }}" data-ajax-popup="true" data-title="{{__('Add User')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan
                                        </h5>

                                    </div>
                                    <div class="card-body pt-0 table-border-style bg-none height-450 ">
                                        <div class="">
                                            <table class="table align-items-center mb-0">
                                                <tbody class="list">
                                                    @foreach($deal->users as $user)
                                                        <tr>
                                                            <td>
                                                                <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar):$logo."avatar.png"}}" target="_blank">
                                                                    <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar):$logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <span class="number-id">{{$user->name}}</span>
                                                            </td>
                                                            @can('Edit Deal')
                                                                <td class="text-end">
                                                                    @if($deal->created_by == \Auth::user()->id)
                                                                        <div class="action-btn bg-danger ">
                                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['deals.users.destroy',$deal->id,$user->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete User')}}">
                                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                            {!! Form::close() !!}
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            @endcan
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(Auth::user()->type != 'Super Admin' || in_array('Client View Products',$permission))
                            <div class="col-xl-4 col-lg-4 col-sm-6 col-md-6">
                                <div class="card table-card deal-card">
                                    <div class="card-header">
                                        <h5>{{__('Products')}}
                                            @can('Edit Deal')
                                                <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Products')}}" data-url="{{ route('deals.products.edit',$deal->id) }}" data-ajax-popup="true" data-title="{{__('Add Products')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                                @endcan
                                        </h5>

                                    </div>
                                    <div class="card-body pt-0 table-border-style bg-none height-450 " style ="overflow: auto;">
                                        <div class="">
                                            <table class="table align-items-center mb-0">
                                                <tbody class="list">
                                                    @php($products=$deal->products())
                                                    @if($products)
                                                        @foreach($products as $product)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{(!empty($product->image))?  \App\Models\Utility::get_file($product->image): \App\Models\Utility::get_file("product/img01.jpg")}}" target="_blank">
                                                                        <img src="{{(!empty($product->image))?  \App\Models\Utility::get_file($product->image): \App\Models\Utility::get_file("product/img01.jpg")}}" class="img-fluid" width="50">
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <span class="number-id">{{$product->name}} </span> (<span class="text-muted">{{\Auth::user()->priceFormat($product->price)}}</span>)
                                                                </td>
                                                                @can('Edit Deal')
                                                                    <td class="text-end">
                                                                        <div class="action-btn bg-danger ">
                                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['deals.products.destroy',$deal->id,$product->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Product')}}">
                                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                            {!! Form::close() !!}
                                                                        </div>
                                                                    </td>
                                                                @endcan
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td>{{__('No Product Found.!')}}</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(Auth::user()->type != 'Super Admin' || in_array('Client View Files',$permission))
                            <div class="col-lg-4">
                                <div class="card deal-card">
                                    <div class="card-header" style="min-height: 81px;">
                                        <h5>{{__('Files')}}</h5>
                                    </div>
                                    <div class="card-body" style ="overflow: auto;">
                                        <div>
                                            <div class="card-body bg-none" >
                                                <div class="col-md-12 dropzone browse-file " id="dropzonewidget"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">

                        @if($calenderTasks)
                            <div class="col-6">
                                 <div class="card">
                                    <div class="card-header">
                                        <h5>{{__('Calendar')}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div  id='event_calendar' class='calendar' data-toggle="event_calendar"></div>
                                    </div>
                                </div>


                            </div>
                        @endif

                        @if(Auth::user()->type != 'Super Admin' || in_array('Client Deal Activity',$permission))
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{__('Activity')}}</h5>
                                    </div>
                                    <div class="card-body" >

                                        <div class="row" style="height:504px !important;overflow-y: scroll;">
                                            <ul class="event-cards list-group list-group-flush mt-3 w-100">
                                                @foreach($deal->activities as $activity)
                                                    <li class="list-group-item card mb-3">
                                                        <div class="row align-items-center justify-content-between">
                                                            <div class="col-auto mb-3 mb-sm-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="theme-avtar bg-primary">
                                                                        <i class="fas {{ $activity->logIcon() }}"></i>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <h6 class="m-0">{!! $activity->getRemark() !!}</h6>
                                                                        <small class="text-muted">{{$activity->created_at->diffForHumans()}}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">

                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    </div>
                                 </div>
                            </div>
                        @endif
                    </div>
                    @can('Edit Deal')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{__('Notes')}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class=" height-450">
                                            <div class="card-body bg-none">
                                                <textarea class="tox-target pc-tinymce-2" id="pc_demo1">{!! $deal->notes !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan
                </div>
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Tasks',$permission))
                    <div class="tab-pane fade show" id="tasks" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">

                            <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Tasks')}}
                                            @can('Edit Deal')
                                                <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Task')}}" data-url="{{ route('deals.tasks.create',$deal->id) }}" data-ajax-popup="true" data-title="{{__('Add Task')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan
                                        </h5>
                                    </div>
                                    <div class="card-body pt-0 table-border-style bg-none height-450 ">
                                        <div class="">
                                            <table class="table align-items-center mb-0">
                                                <tbody class="list">
                                                    @foreach($tasks as $task)
                                                        <tr>
                                                            <td>
                                                                <div class="custom-control custom-switch form-check form-switch mb-2">
                                                                    @can('Edit Task')
                                                                        <input type="checkbox"  class="form-check-input task-checkbox" role="switch" id="task_{{$task->id}}" @if($task->status) checked="checked" @endcan type="checkbox" value="{{$task->status}}" data-url="{{route('deals.tasks.update_status',[$deal->id,$task->id])}}"/>

                                                                    @endcan
                                                                    <label for="task_{{$task->id}}" class="custom-control-label ml-4 @if($task->status) strike @endif">
                                                                        <h6 class="media-title text-sm form-check-label">
                                                                            {{$task->name}}
                                                                            @if($task->status)
                                                                                <div class="badge rounded p-2 px-3 bg-success mb-1">{{__(\App\Models\DealTask::$status[$task->status])}}</div>
                                                                            @else
                                                                                <div class="badge rounded p-2 px-3 bg-warning mb-1">{{__(\App\Models\DealTask::$status[$task->status])}}</div>
                                                                            @endif
                                                                        </h6>
                                                                        <div class="text-xs text-muted">{{__(\App\Models\DealTask::$priorities[$task->priority])}} -
                                                                            <span class="text-primary">{{Auth::user()->dateFormat($task->date)}} {{Auth::user()->timeFormat($task->time)}}</span></div>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td class="Action text-end">
                                                                <span>
                                                                    @can('Edit Task')
                                                                        <div class="action-btn btn-info ms-2">
                                                                            <a href="#" data-size="md" data-url="{{route('deals.tasks.edit',[$deal->id,$task->id])}}" data-ajax-popup="true" data-title="{{__('Edit Task')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Task')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                                        </div>
                                                                    @endcan
                                                                    @can('Delete Task')
                                                                        <div class="action-btn bg-danger ms-2">
                                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['deals.tasks.destroy',$deal->id,$task->id],'id'=>'delete-form-'.$task->id]) !!}
                                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Task')}}">
                                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                            {!! Form::close() !!}
                                                                        </div>
                                                                    @endcan
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
                    </div>
                @endif
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Products',$permission))
                    <div class="tab-pane fade show" id="products" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Products')}}
                                            @can('Edit Deal')
                                                <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Products')}}" data-url="{{ route('deals.products.edit',$deal->id) }}" data-ajax-popup="true" data-title="{{__('Add Products')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan
                                        </h5>

                                    </div>
                                    <div class="card-body pt-0 table-border-style bg-none height-450 ">
                                        <div class="">
                                            <table class="table align-items-center mb-0">
                                                <tbody class="list">
                                                    @php($products=$deal->products())
                                                    @if($products)
                                                        @foreach($products as $product)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{(!empty($product->image))?  \App\Models\Utility::get_file($product->image): \App\Models\Utility::get_file("product/img01.jpg")}}" target="_blank">
                                                                        <img src="{{(!empty($product->image))?  \App\Models\Utility::get_file($product->image): \App\Models\Utility::get_file("product/img01.jpg")}}" class="img-fluid" width="50">
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <span class="number-id">{{$product->name}} </span> (<span class="text-muted">{{\Auth::user()->priceFormat($product->price)}}</span>)
                                                                </td>
                                                                @can('Edit Deal')
                                                                    <td class="text-end">
                                                                        <div class="action-btn bg-danger ">
                                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['deals.products.destroy',$deal->id,$product->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Product')}}">
                                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                            {!! Form::close() !!}
                                                                        </div>
                                                                    </td>
                                                                @endcan
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td>{{__('No Product Found.!')}}</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(Auth::user()->type != 'Super Admin' ||in_array('Client View Sources',$permission))
                    <div class="tab-pane fade show" id="sources" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Sources')}}
                                            @can('Edit Deal')
                                                <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Sources')}}" data-url="{{ route('deals.sources.edit',$deal->id) }}" data-ajax-popup="true" data-title="{{__('Add Sources')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan
                                        </h5>

                                    </div>
                                    <div class="card-body pt-0 table-border-style bg-none height-450 ">
                                        <div class="">
                                            <table class="table align-items-center mb-0">
                                                <tbody class="list">
                                                    @if($sources)
                                                        @foreach($sources as $source)
                                                            <tr>
                                                                <td>
                                                                    <span class="text-dark">{{$source->name}}</span>
                                                                </td>
                                                                <td class="text-end">
                                                                    @can('Edit Deal')
                                                                        <div class="action-btn bg-danger ">
                                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['deals.sources.destroy',$deal->id,$source->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Source')}}">
                                                                                   <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                            {!! Form::close() !!}
                                                                        </div>
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Files',$permission))
                    <div class="tab-pane fade show" id="files" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card deal-card">
                                    <div class="card-header">
                                        <h5>{{__('Files')}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class=" height-450">
                                            <div class="card-body bg-none ">
                                                <div class="col-md-12 dropzone browse-file" id="dropzonewidget2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="tab-pane fade show" id="discussion" role="tabpanel">
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{__('Discussion')}}
                                        <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Message')}}" data-url="{{ route('deals.discussions.create',$deal->id) }}" data-ajax-popup="true" data-title="{{__('Add Message')}}">
                                            <i class="ti ti-plus text-white"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled list-unstyled-border">
                                        @foreach($deal->discussions as $discussion)
                                            <li class="media">
                                                <div style="margin-right: 10px;">
                                                    <a href="{{(!empty($discussion->user->avatar))?  \App\Models\Utility::get_file($discussion->user->avatar): $logo."avatar.png"}}" target="_blank">
                                                        <img src="{{(!empty($discussion->user->avatar))?  \App\Models\Utility::get_file($discussion->user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                    </a>
                                                </div>

                                                <div class="media-body">
                                                    <div class="mt-0 mb-1 font-weight-bold text-sm">{{$discussion->user->name}}
                                                        <small>{{$discussion->user->type}}</small>
                                                        <small class="float-end">{{$discussion->created_at->diffForHumans()}}</small>
                                                    </div>
                                                    <div class="text-xs"> {{$discussion->comment}}</div>

                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade show" id="notes" role="tabpanel">
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{__('Notes')}}</h5>
                                </div>
                                <div class="card-body">
                                    <div class=" height-450">
                                        <div class="card-body bg-none ">
                                            <textarea class="tox-target pc-tinymce-2" id="pc_demo1">{!! $deal->notes !!}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Invoices',$permission))
                    <div class="tab-pane fade show" id="invoices" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Invoices')}}</h5>
                                    </div>
                                    <div class=" card-body table-border-style">

                                        <div class="">
                                            <table class="table mb-0 pc-dt-simple">
                                                <thead>
                                                    <tr>
                                                        <th>{{__('Invoice')}}</th>
                                                        <th>{{__('Issue Date')}}</th>
                                                        <th>{{__('Due Date')}}</th>
                                                        <th>{{__('Value')}}</th>
                                                        <th>{{__('Status')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($invoices as $invoice)
                                                        <tr>
                                                            <td class="Id">
                                                                @can('View Invoice')
                                                                    <a class="btn  btn-outline-primary" href="{{route('invoices.show',$invoice->id)}}"> <i class="fas fa-file-invoice"></i> {{ Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                                                @else
                                                                    {{ Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                                                @endcan
                                                            </td>
                                                            <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                                            <td>{{ Auth::user()->dateFormat($invoice->due_date) }}</td>
                                                            <td>{{ Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                                            <td>
                                                                @if($invoice->status == 0)
                                                                    <span class="badge rounded p-2 px-3 bg-primary">{{ __(\App\Models\Invoice::$statues[0]) }}</span>
                                                                @elseif($invoice->status == 1)
                                                                    <span class="badge rounded p-2 px-3 bg-danger">{{ __(\App\Models\Invoice::$statues[1]) }}</span>
                                                                @elseif($invoice->status == 2)
                                                                    <span class="badge rounded p-2 px-3 bg-warning">{{ __(\App\Models\Invoice::$statues[2]) }}</span>
                                                                @elseif($invoice->status == 3)
                                                                    <span class="badge rounded p-2 px-3 bg-success">{{ __(\App\Models\Invoice::$statues[3]) }}</span>
                                                                @elseif($invoice->status == 4)
                                                                    <span class="badge rounded p-2 px-3 bg-info">{{ __(\App\Models\Invoice::$statues[4]) }}</span>
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
                    </div>
                @endif
                @if(Auth::user()->type != 'Super Admin' || in_array('Client View Custom fields',$permission))
                    <div class="tab-pane fade show" id="custom_fields" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Custom Fields')}}</h5>

                                    </div>
                                    <div class="card-body table-border-style bg-none height-450 ">
                                        <table class="table mb-0">
                                            <tbody>
                                                @foreach($customFields as $field)
                                                    <tr>
                                                        <td class="text-dark">{{$field->name}}</td>
                                                        @if(!empty($deal->customField))
                                                            <td>{{(isset($deal->customField[$field->id]) ? $deal->customField[$field->id] : '-')}}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(Auth::user()->type != 'Super Admin')
                    <div class="tab-pane fade show" id="clients" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Clients')}}
                                            @can('Create Lead Call')
                                                <a href="#" data-size="md" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Client')}}" data-url="{{route('deals.clients.edit',$deal->id)}}" data-ajax-popup="true" data-title="{{__('Add Client')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan
                                        </h5>
                                    </div>
                                    <div class="card-body table-border-style">

                                        <div class="">
                                            <table class="table mb-0 pc-dt-simple">
                                                <thead>
                                                    <tr>
                                                        <th>{{__('Avatar')}}</th>
                                                        <th>{{__('Name')}}</th>
                                                        <th>{{__('Email')}}</th>
                                                        @can('Edit Deal')
                                                        <th>{{__('Action')}}</th>
                                                        @endcan
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($deal->clients as $client)
                                                        <tr>
                                                            <td>
                                                                <a href="{{(!empty($client->avatar))?  \App\Models\Utility::get_file($client->avatar): $logo."avatar.png"}}" target="_blank">
                                                                    <img src="{{(!empty($client->avatar))?  \App\Models\Utility::get_file($client->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                                </a>
                                                            <td>{{ $client->name }}</td>
                                                            <td>{{ $client->email }}</td>
                                                            <td class="text-end">
                                                                @can('Edit Deal')
                                                                <div class="action-btn btn-info ms-2">
                                                                    <a href="#" data-size="lg" data-url="{{route('deals.client.permission',[$deal->id,$client->id])}}" data-ajax-popup="true" data-title="{{__('Permission')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Permission')}}" ><i class="ti ti-lock text-white"></i></a>
                                                                </div>
                                                                @endcan
                                                                @can('Edit Deal')
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['deals.clients.destroy',$deal->id,$client->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Client')}}">
                                                                           <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                                @endcan
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
                    </div>
                @endif
                @if(Auth::user()->type != 'Super Admin')
                    <div class="tab-pane fade show" id="calls" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Calls')}}
                                            @can('Create Deal Call')
                                                <a href="#" data-size="lg" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Call')}}" data-url="{{ route('deals.calls.create',$deal->id) }}" data-ajax-popup="true" data-title="{{__('Add Call')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan
                                        </h5>
                                    </div>
                                    <div class=" card-body table-border-style">

                                        <div class="">
                                            <table class="table mb-0 pc-dt-simple">
                                                <thead>
                                                    <tr>
                                                        <th>{{__('Subject')}}</th>
                                                        <th>{{__('Call Type')}}</th>
                                                        <th>{{__('Duration')}}</th>
                                                        <th>{{__('User')}}</th>
                                                        <th width="14%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($calls as $call)
                                                        <tr>
                                                            <td>{{ $call->subject }}</td>
                                                            <td>{{ ucfirst($call->call_type) }}</td>
                                                            <td>{{ $call->duration }}</td>
                                                            <td>{{ $call->getDealCallUser->name }}</td>
                                                            <td class="text-end">
                                                                @can('Edit Deal Call')
                                                                    <div class="action-btn btn-info ms-2">
                                                                        <a href="#" data-size="lg" data-url="{{ URL::to('deals/'.$deal->id.'/call/'.$call->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Deal Call')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Deal Call')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                                    </div>
                                                                @endcan
                                                                @can('Delete Lead Call')
                                                                    <div class="action-btn bg-danger ms-2">
                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['deals.calls.destroy',$deal->id ,$call->id],'id'=>'delete-form-'.$deal->id]) !!}
                                                                            <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Deal')}}">
                                                                               <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                        {!! Form::close() !!}
                                                                    </div>
                                                                @endcan
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
                    </div>
                @endif
                @if(Auth::user()->type != 'Super Admin')
                    <div class="tab-pane fade show" id="emails" role="tabpanel">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Email')}}
                                            @can('Create Deal Email')
                                                <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Email')}}" data-url="{{ route('deals.emails.create',$deal->id) }}" data-ajax-popup="true" data-title="{{__('Add Email')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan

                                        </h5>

                                    </div>
                                    <div class="card-body table-border-style bg-none height-450 ">
                                        <ul class="list-unstyled list-unstyled-border">
                                            @foreach($emails as $email)
                                                <li class="media mb-3">
                                                    <div style="margin-right: 10px;">
                                                            <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="mt-0 mb-1 font-weight-bold text-sm">{{$email->subject}}
                                                            <small class="float-right">{{$email->created_at->diffForHumans()}}</small>
                                                        </div>
                                                        <div class="text-xs">{{$email->to}}</div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
