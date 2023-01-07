@extends('layouts.admin')

@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('title')
    {{$lead->name}}
@endsection

@push('head')
    <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}">

    <style>
        .nav-tabs .nav-link-tabs.active {
            background: none;
        }
    </style>
@endpush

@push('script')
    <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script>

    <script>
        @if(Auth::user()->type != 'Client')
            Dropzone.autoDiscover = false;
            myDropzone = new Dropzone("#dropzonewidget", {

            url: "{{route('leads.file.upload',$lead->id)}}",
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
            formData.append("lead_id", {{$lead->id}});
        });

        myDropzone2 = new Dropzone("#dropzonewidget2", {
            
            url: "{{route('leads.file.upload',$lead->id)}}",
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
            formData.append("lead_id", {{$lead->id}});
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
            @can('Edit Lead')
            html.appendChild(del);
            @endcan
            @endif

            file.previewTemplate.appendChild(html);
        }

        @foreach($lead->files as $file)

        // Create the mock file:
        var mockFile = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('lead_files/'.$file->file_path))}} };
        // Call the default addedfile event handler
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, "{{asset(Storage::url('lead_files/'.$file->file_path))}}");
        myDropzone.emit("complete", mockFile);

        dropzoneBtn(mockFile, {download: "{{route('leads.file.download',[$lead->id,$file->id])}}", delete: "{{route('leads.file.delete',[$lead->id,$file->id])}}"});

        // Create the mock file:
        var mockFile2 = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('lead_files/'.$file->file_path))}} };
        // Call the default addedfile event handler
        myDropzone2.emit("addedfile", mockFile2);
        // And optionally show the thumbnail of the file:
        myDropzone2.emit("thumbnail", mockFile2, "{{asset(Storage::url('lead_files/'.$file->file_path))}}");
        myDropzone2.emit("complete", mockFile2);

        dropzoneBtn(mockFile2, {download: "{{route('leads.file.download',[$lead->id,$file->id])}}", delete: "{{route('leads.file.delete',[$lead->id,$file->id])}}"});

        @endforeach
        @endif
        @can('Edit Lead')
        $('.summernote-simple').on('summernote.blur', function () {
            $.ajax({
                url: "{{route('leads.note.store',$lead->id)}}",
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

        $(document).ready(function () {
            var tab = 'general';
                @if ($tab = Session::get('status'))
            var tab = '{{ $tab }}';
            @endif
            $("#myTab2 .nav-link-text[href='#" + tab + "']").trigger("click");
        });
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page"><a href="{{route('leads.index')}}">{{__('Leads')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{$lead->name}}</li>
@endsection


@section('action-button')

        @can('Edit Deal')
               <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Labels')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Label')}}" data-url="{{ URL::to('leads/'.$lead->id.'/labels') }}"><i class="ti ti-tag text-white"></i></a>

            <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit Lead')}}" data-url="{{ URL::to('leads/'.$lead->id.'/edit') }}"><i class="ti ti-pencil text-white"></i></a>
        @endcan

       @can('Convert Lead To Deal')
            @if(!empty($deal))
                    <a href="@can('View Deal') @if($deal->is_active) {{route('deals.show',$deal->id)}} @else # @endif @else # @endcan" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Already Converted To Deal')}}"><i class="ti ti-exchange text-white"></i></a>
            @else
                    <a href="#" class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Convert To Deal')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Convert [').$lead->subject.('] To Deal')}}" data-url="{{ URL::to('leads/'.$lead->id.'/show_convert') }}"><i class="ti ti-exchange text-white"></i></a>
            @endif
        @endcan

@endsection

@section('content')
    @php($labels = $lead->labels())
    @if($labels)
        <div class="row">
            <div class="col-12 mb-3">
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
                @if(Auth::user()->type != 'Client')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-products-tab" data-bs-toggle="pill" href="#products" role="tab" aria-controls="products" aria-selected="false">{{__('Products')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Client')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-sources-tab" data-bs-toggle="pill" href="#sources" role="tab" aria-controls="sources" aria-selected="false">{{__('Sources')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Client')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-files-tab" data-bs-toggle="pill" href="#files" role="tab" aria-controls="files" aria-selected="false">{{__('Files')}}</a>
                    </li>
                @endif
                    <li class="nav-item">
                        <a class="nav-link" id="pills-discussion-tab" data-bs-toggle="pill" href="#discussion" role="tab" aria-controls="discussion" aria-selected="false">{{__('Discussion')}}</a>
                    </li>
                 @can('Edit Lead')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-notes-tab" data-bs-toggle="pill" href="#notes" role="tab" aria-controls="notes" aria-selected="false">{{__('Notes')}}</a>
                    </li>
                @endcan
                @if(Auth::user()->type != 'Client')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-calls-tab" data-bs-toggle="pill" href="#calls" role="tab" aria-controls="calls" aria-selected="false">{{__('Calls')}}</a>
                    </li>
                @endif
                @if(Auth::user()->type != 'Client')
                    <li class="nav-item">
                        <a class="nav-link" id="pills-emails-tab" data-bs-toggle="pill" href="#emails" role="tab" aria-controls="emails" aria-selected="false">{{__('Emails')}}</a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="col-12">
            <div class="tab-content tab-bordered">
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="pills-general-tab">
                    <?php
                    $products = $lead->products();
                    $sources = $lead->sources();
                    $calls = $lead->calls;
                    $emails = $lead->emails;
                    ?>
                    <div class="row">
                        <div class="col-xxl-6">
                            <div class="row">
                                <div class="col-lg-4 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="theme-avtar bg-success">
                                                <i class="ti ti-shopping-cart  text-white"></i>
                                            </div>

                                            <p class="text-muted text-sm mt-4 mb-2">{{__('Product')}}</p>
                                            <h3 class="mb-0">{{count($products)}}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="theme-avtar bg-info">
                                                <i class="ti ti-eye  text-white"></i>
                                            </div>
                                            <p class="text-muted text-sm mt-4 mb-2">{{__('Source')}}</p>
                                            <h3 class="mb-0">{{count($sources)}}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="theme-avtar bg-warning">
                                                <i class="ti ti-file-invoice  text-white"></i>
                                            </div>
                                            <p class="text-muted text-sm mt-4 mb-2">{{__('Files')}}</p>
                                            <h3 class="mb-0">{{count($lead->files)}}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-6">
                            <div class="card report_card total_amount_card">
                                <div class="card-body">
                                    <h4></h4>
                                    <div class="row mb-4 card_row mt-5">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="d-flex align-items-start">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="fa fa-dollar-sign"></i>
                                                </div>
                                                <div class="ms-2">
                                                    <p class="text-muted text-sm mb-0">{{__('Pipeline')}}</p>
                                                    <h3 class="mb-0 text-success">{{$lead->pipeline->name}}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 my-3 my-sm-0">
                                            <div class="d-flex align-items-start">
                                                <div class="theme-avtar bg-info">
                                                    <i class="fa fa-dollar-sign"></i>
                                                </div>
                                                <div class="ms-2">
                                                    <p class="text-muted text-sm mb-0">{{__('Stage')}}</p>
                                                    <h3 class="mb-0 text-info">{{$lead->stage->name}}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 ">
                                            <div class="d-flex align-items-start">
                                                <div class="theme-avtar bg-danger">
                                                    <i class="fa fa-dollar-sign"></i>
                                                </div>
                                                <div class="ms-2">
                                                    <p class="text-muted text-sm mb-0">{{__('Created')}} <span class="badge bg-secondary p-1 rounded">{{\Auth::user()->dateFormat($lead->created_at)}}</span></p>
                                                    <h4 class="mb-0 text-danger">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                                    <div class="d-flex align-items-center">
                                                                    </div>
                                                                    <span style="left: {{$precentage}}%;font-size:15px;">{{$precentage}}%</span>
                                                        </div>
                                                        <div class="progress mb-3">
                                                            <div class="progress-bar bg-primary" style="width: {{$precentage}}%;"></div>
                                                        </div>
                                                    </h4>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @if(Auth::user()->type != 'Client')
                            <div class="col-xl-4 col-lg-4 col-sm-6 col-md-6">
                                <div class="card table-card deal-card">
                                    <div class="card-header">
                                        <h5>{{__('Users')}}
                                            @can('Edit Lead')
                                                <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add User')}}" data-url="{{ route('leads.users.edit',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Add User')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan
                                        </h5>

                                    </div>
                                    <div class="card-body pt-0 table-border-style bg-none height-450">
                                        <div class="">
                                            <table class="table align-items-center mb-0">
                                                <tbody class="list">
                                                @foreach($lead->users as $user)
                                                    <tr>
                                                        <td>
                                                            <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar):$logo."avatar.png"}}" target="_blank">
                                                                <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar):$logo."avatar.png"}}" width="30" class="img-fluid rounded-circle">
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="number-id">{{$user->name}}</span>
                                                        </td>
                                                        @can('Edit Lead')
                                                            <td class="text-end">
                                                                @if($lead->created_by == \Auth::user()->id)
                                                                    <div class="action-btn bg-danger ">
                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['leads.users.destroy',$lead->id,$user->id],'id'=>'delete-form-'.$lead->id]) !!}
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
                        @if(Auth::user()->type != 'Client')
                            <div class="col-xl-4 col-lg-4 col-sm-6 col-md-6">
                                <div class="card table-card deal-card">
                                    <div class="card-header">
                                        <h5>{{__('Products')}}
                                            @can('Edit Lead')
                                                <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Products')}}" data-url="{{ route('leads.products.edit',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Add Products')}}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            @endcan
                                        </h5>

                                    </div>
                                    <div class="card-body pt-0 table-border-style bg-none height-450" style ="overflow: auto;">
                                        <div class="">
                                            <table class="table align-items-center mb-0">
                                                <tbody class="list">
                                                    @php($products = $lead->products())
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
                                                        @can('Edit Lead')
                                                            <td class="text-end">
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['leads.products.destroy',$lead->id,$product->id],'id'=>'delete-form-'.$lead->id]) !!}
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
                        @if(Auth::user()->type != 'Client')
                            <div class="col-lg-4">
                                <div class="card table-card deal-card">
                                    <div class="card-header" style="min-height: 81px;">
                                        <h5>{{__('Files')}}</h5>
                                    </div>
                                    <div class="card-body" style ="overflow: auto;">
                                        <div class=" height-450">
                                            <div class="card-body bg-none ">
                                                <div class="col-md-12 dropzone browse-file" id="dropzonewidget"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        @can('Edit Lead')
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{__('Notes')}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class=" height-450">
                                            <div class="card-body bg-none ">
                                                <textarea class="tox-target pc-tinymce-2" id="pc_demo1">{!! $lead->notes !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @if(Auth::user()->type != 'Client')

                            <div class="col-6">
                                 <div class="card">
                                    <div class="card-header">
                                        <h5>{{__('Activity')}}</h5>
                                    </div>
                                    <div class="card-body height-450">

                                        <div class="row" style="height:450px !important;overflow-y: scroll;">
                                            <ul class="event-cards list-group list-group-flush mt-3 w-100">
                                                @foreach($lead->activities as $activity)
                                                    <li class="list-group-item card mb-3">
                                                        <div class="row align-items-center justify-content-between">
                                                            <div class="col-auto mb-3 mb-sm-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="theme-avtar bg-primary">
                                                                        <i class="fas {{ $activity->logIcon() }}"></i>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <h6 class="m-0">{!! $activity->getLeadRemark() !!}</h6>
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
                </div>

                    @if(Auth::user()->type != 'Client')
                        <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="pills-products-tab">
                            <div class="row pt-2">
                                <div class="col-12">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <h5>{{__('Products')}}
                                                @can('Edit Lead')
                                                    <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Products')}}" data-url="{{ route('leads.products.edit',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Add Products')}}">
                                                        <i class="ti ti-plus text-white"></i>
                                                    </a>
                                                @endcan
                                            </h5>
                                        </div>
                                        <div class="card-body pt-0 table-border-style bg-none height-450">
                                            <div class="">
                                                <table class="table align-items-center mb-0">
                                                    <tbody class="list">
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
                                                                    <td class="text-end">
                                                                        @can('Edit Lead')
                                                                            <div class="action-btn bg-danger ms-2">
                                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leads.products.destroy',$lead->id,$product->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                                    <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Product')}}">
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
                    @if(Auth::user()->type != 'Client')
                        <div class="tab-pane fade" id="sources" role="tabpanel" aria-labelledby="pills-sources-tab">
                            <div class="row pt-2">
                                <div class="col-12">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <h5>{{__('Sources')}}
                                                @can('Edit Lead')
                                                    <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Sources')}}" data-url="{{ route('leads.sources.edit',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Edit Sources')}}">
                                                        <i class="ti ti-plus text-white"></i>
                                                    </a>
                                                @endcan
                                            </h5>

                                        </div>
                                        <div class="card-body pt-0 table-border-style bg-none height-450">
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
                                                                        @can('Edit Lead')
                                                                            <div class="action-btn bg-danger ms-2">
                                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leads.sources.destroy',$lead->id,$source->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                                    <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Sources')}}">
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
                    @if(Auth::user()->type != 'Client')
                        <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="pills-files-tab">
                            <div class="row pt-2">
                                <div class="col-12">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <h5>{{__('Files')}}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class=" height-450">
                                                <div class="card-body bg-none">
                                                    <div class="col-md-12 dropzone browse-file" id="dropzonewidget2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="tab-pane fade" id="discussion" role="tabpanel" aria-labelledby="pills-discussion-tab">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>{{__('Discussion')}}
                                            <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Message')}}" data-url="{{ route('leads.discussions.create',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Add Message')}}">
                                                <i class="ti ti-plus text-white"></i>
                                            </a>
                                        </h5>

                                    </div>
                                    <div class="card-body table-border-style bg-none height-450">
                                        <ul class="list-unstyled list-unstyled-border">
                                        @foreach($lead->discussions as $discussion)
                                            <li class="media">
                                               <div style="margin-right: 10px;">
                                                <a href="{{(!empty($discussion->user->avatar))?  \App\Models\Utility::get_file($discussion->user->avatar): $logo."avatar.png"}}" target="_blank">
                                                    <img src="{{(!empty($discussion->user->avatar))?  \App\Models\Utility::get_file($discussion->user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle " width="50" height="50">
                                                </a>
                                               </div>
                                                <div class="media-body">
                                                    <div class="mt-0 mb-1 font-weight-bold text-sm">{{$discussion->user->name}} <small>{{$discussion->user->type}}</small> <small class="float-end">{{$discussion->created_at->diffForHumans()}}</small></div>
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

                    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="pills-notes-tab">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{__('Notes')}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class=" height-450">
                                            <div class="card-body bg-none">
                                                <textarea class="tox-target pc-tinymce-2" id="pc_demo1">{!! $lead->notes !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(Auth::user()->type != 'Client')
                        <div class="tab-pane fade " id="calls" role="tabpanel" aria-labelledby="pills-calls-tab">
                            <div class="row pt-2">
                                <div class="col-12">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <h5>{{__('Calls')}}
                                                @can('Create Lead Call')
                                                    <a href="#" data-size="lg" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Call')}}" data-url="{{ route('leads.calls.create',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Add Call')}}">
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
                                                            <th width="">{{__('Subject')}}</th>
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
                                                                <td>{{ isset($call->getLeadCallUser) ? $call->getLeadCallUser->name : '-' }}</td>
                                                                <td class="text-end">
                                                                    @can('Edit Lead Call')
                                                                        <div class="action-btn btn-info ms-2">
                                                                                <a href="#" data-size="lg" data-url="{{ URL::to('leads/'.$lead->id.'/call/'.$call->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Lead')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Lead')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                                            </div>
                                                                    @endcan
                                                                    @can('Delete Lead Call')
                                                                        <div class="action-btn bg-danger ms-2">
                                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['leads.calls.destroy',$lead->id ,$call->id],'id'=>'delete-form-'.$call->id]) !!}
                                                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Call')}}">
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
                    @if(Auth::user()->type != 'Client')
                        <div class="tab-pane fade" id="emails" role="tabpanel" aria-labelledby="pills-emails-tab">
                            <div class="row pt-2">
                                <div class="col-12">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <h5>{{__('Email')}}
                                                @can('Create Lead Email')
                                                    <a href="#" class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Email')}}" data-url="{{ route('leads.emails.create',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Add Email')}}">
                                                        <i class="ti ti-plus text-white"></i>
                                                    </a>
                                                @endcan

                                            </h5>

                                        </div>
                                        <div class="card-body table-border-style bg-none height-450">
                                            <ul class="list-unstyled list-unstyled-border">
                                                @foreach($emails as $email)
                                                    <li class="media mb-3">
                                                        <div style="margin-right: 10px;">
                                                            {{-- <a href="{{asset('custom/img/avatar/avatar-1.png')}}" target="_blank">
                                                                <img alt="image" class="mr-3 rounded-circle" width="50" height="50" src="{{asset('custom/img/avatar/avatar-1.png')}}">
                                                            </a> --}}
                                                            <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" target="_blank">
                                                                <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle " width="50" height="50">
                                                            </a>
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="mt-0 mb-1 font-weight-bold text-sm">{{$email->subject}} <small class="float-right">{{$email->created_at->diffForHumans()}}</small></div>
                                                            <div class="text-xs"> {{$email->to}}</div>
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
