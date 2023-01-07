@extends('layouts.admin')

@section('title')
    {{$contract->name}}
@endsection


@php
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

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
        Dropzone.autoDiscover = true;
        myDropzone = new Dropzone("#my-dropzone", {
            url: "{{route('contracts.file.upload',[$contract->id])}}",
            success: function (file, response) {
                location.reload();
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
                    show_toastr('{{__("Error")}}', response.error, 'error');
                } else {
                    show_toastr('{{__("Error")}}', response.error, 'error');
                }
            }
        });
        myDropzone.on("sending", function (file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("contract_id", {{$contract->id}});

        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{__('Download')}}");
            download.innerHTML = "<i class='fas fa-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "action-btn btn-danger mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
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
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            }
                        },
                        error: function (response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            } else {
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.setAttribute('class', "text-center mt-10");
            html.appendChild(download);
            html.appendChild(del);

            file.previewTemplate.appendChild(html);
        }

        @foreach($contract->files as $file)

        @endforeach

    </script>

    <script>
        // $('.summernote').on('summernote.blur', function () {
        //     alert();
        //     $.ajax({
        //         url: "{{route('contracts.note.store',$contract->id)}}",
        //         data: {_token: $('meta[name="csrf-token"]').attr('content'), notes: $(this).val()},
        //         type: 'POST',
        //         success: function (response) {
        //             if (response.is_success) {
        //                 // show_toastr('Success', response.success,'success');
        //             } else {
        //                 show_toastr('Error', response.error, 'error');
        //             }
        //         },
        //         error: function (response) {
        //             response = response.responseJSON;
        //             if (response.is_success) {
        //                 show_toastr('Error', response.error, 'error');
        //             } else {
        //                 show_toastr('Error', response, 'error');
        //             }
        //         }
        //     })
        // });



        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200,
            });


        });
    </script>


<script>
    $(document).on('click', '#comment_submit', function (e) {
                var curr = $(this);

                var comment = $.trim($("#form-comment textarea[name='comment']").val());

                if (comment != '') {
                    $.ajax({
                        url: $("#form-comment").data('action'),
                        data: {comment: comment, "_token": "{{ csrf_token() }}",},
                        type: 'POST',
                        success: function (data) {

                            show_toastr('{{__("Success")}}', 'Comment Create Successfully!', 'success');


                            setTimeout(function () {
                                location.reload();
                            }, 500)
                            data = JSON.parse(data);
                            console.log(data);
                            var html = "<div class='list-group-item px-0'>" +
                                "                    <div class='row align-items-center'>" +
                                "                        <div class='col-auto'>" +
                                "                            <a href='#' class='avatar avatar-sm rounded-circle ms-2'>" +
                                "                                <img src="+data.default_img+" alt='' class='avatar-sm rounded-circle'>" +
                                "                            </a>" +
                                "                        </div>" +
                                "                        <div class='col ml-n2'>" +
                                "                            <p class='d-block h6 text-sm font-weight-light mb-0 text-break'>" + data.comment + "</p>" +
                                "                            <small class='d-block'>"+data.current_time+"</small>" +
                                "                        </div>" +
                                "                        <div class='action-btn bg-danger me-4'><div class='col-auto'><a href='#' class='mx-3 btn btn-sm  align-items-center delete-comment' data-url='" + data.deleteUrl + "'><i class='ti ti-trash text-white'></i></a></div></div>" +
                                "                    </div>" +
                                "                </div>";

                            $("#comments").prepend(html);
                            $("#form-comment textarea[name='comment']").val('');
                            load_task(curr.closest('.task-id').attr('id'));
                            show_toastr('is_success', 'Comment Added Successfully!');
                        },
                        error: function (data) {
                            show_toastr('error', 'Some Thing Is Wrong!');
                        }
                    });
                } else {
                    show_toastr('error', 'Please write comment!');
                }
            });

            $(document).on("click", ".delete-comment", function () {
                var btn = $(this);

                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    data: {comment: comment, "_token": "{{ csrf_token() }}",},
                    success: function (data) {
                        load_task(btn.closest('.task-id').attr('id'));
                        show_toastr('success', 'Comment Deleted Successfully!');
                        btn.closest('.list-group-item').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('error', data.message);
                        } else {
                            show_toastr('error', 'Some Thing Is Wrong!');
                        }
                    }
                });
            });
</script>

<script>
    $(document).on("click", ".status", function() {

           var status = $(this).attr('data-id');
           var url = $(this).attr('data-url');
           $.ajax({
               url: url,
               type: 'POST',
               data: {

                   "status": status,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data) {
                   show_toastr('{{__("Success")}}', 'Status Update Successfully!', 'success');
                   location.reload();
               }

           });
       });
</script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page"><a href="{{route('contract.index')}}">{{__('Contract')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{$contract->name}}</li>
@endsection


@section('action-button')

<div class="col-md-6 text-end d-flex">
        @if(\Auth::user()->type == 'Owner' && $contract->status == 'accept')
            <a href="{{route('send.mail.contract',$contract->id)}}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-original-title="{{__('Send Email')}}"  >
               <i class="ti ti-mail text-white"></i>
           </a>
       @endif

       @if(\Auth::user()->type == 'Owner' && $contract->status == 'accept')
            <a href="#" data-size="lg" data-url="{{route('contracts.copy',$contract->id)}}"data-ajax-popup="true" data-title="{{__('Duplicate')}}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Duplicate')}}" ><i class="ti ti-copy text-white"></i></a>
        @endif

       @if(\Auth::user()->type == 'Owner' || \Auth::user()->type == 'Client')
            <a href="{{route('contract.download.pdf',\Crypt::encrypt($contract->id))}}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Download')}}" target="_blanks"><i class="ti ti-download text-white"></i></a>
        @endif

        @if(\Auth::user()->type == 'Owner' || \Auth::user()->type == 'Client')
            <a href="{{route('get.contract',$contract->id)}}" target="_blank" class="btn btn-sm btn-primary btn-icon m-1" title="{{__('Preview')}}" data-bs-toggle="tooltip" data-bs-placement="top">
                <i class="ti ti-eye"></i>
            </a>
        @endif

        @if(((\Auth::user()->type =='Owner') && ($contract->owner_signature == '')||(\Auth::user()->type =='Client') && ($contract->client_signature == '') )&& ($contract->status == 'accept'))
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-url="{{ route('signature',$contract->id) }}" data-ajax-popup="true" data-title="{{__('Signature')}}" data-size="md" title="{{__('Signature')}}" data-bs-toggle="tooltip" data-bs-placement="top">
                    <i class="ti ti-pencil"></i>
                </a>
        @endif



        @php
        $status = App\Models\Contract::status();
   @endphp
   <ul class="list-unstyled mb-0 ms-1">
       <li class="dropdown dash-h-item drp-language">
           <a class="dash-head-link dropdown-toggle arrow-none me-0 ms-0 p-2 rounded-1" data-bs-toggle="dropdown" href="#"
               role="button" aria-haspopup="false" aria-expanded="false">
               <span class="drp-text hide-mob">
                   <i class=" drp-arrow nocolor hide-mob">{{ ucfirst($contract->status) }}<span class="ti ti-chevron-down"></span></i>
           </a>
           <div class="dropdown-menu dash-h-dropdown">
               @foreach ($status as $k => $status)
                   <a class="dropdown-item status" data-id="{{ $k }}"
                       data-url="{{ route('contract.status', $contract->id) }}"
                       href="#">{{ ucfirst($status) }}</a>
                @endforeach
           </div>
       </li>
   </ul>



</div>
@endsection


@section('content')


    <div class="row">
        <div class="col-12 mb-3">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#general" role="tab" aria-controls="pills-home" aria-selected="true">{{__('General')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-files-tab" data-bs-toggle="pill" href="#attachments" role="tab" aria-controls="files" aria-selected="false">{{__('Attachments')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-files-tab" data-bs-toggle="pill" href="#comments" role="tab" aria-controls="files" aria-selected="false">{{__('Comments')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-files-tab" data-bs-toggle="pill" href="#notes" role="tab" aria-controls="files" aria-selected="false">{{__('Notes')}}</a>
                </li>
            </ul>
        </div>


        <div class="col-12">
            <div class="tab-content tab-bordered">
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="pills-general-tab">
                    <?php
                    // $products = $contract->products();
                    // $sources = $contract->sources();
                    // $calls = $contract->calls;
                    // $emails = $contract->emails;
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
                                            <h1 class="text-dark text-lg  mt-4 mb-4">{{__('Attachments')}}</h1>
                                            <h3 class="mb-0">{{count($contract->files)}}</h3>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="theme-avtar bg-info">
                                                <i class="ti ti-eye  text-white"></i>
                                            </div>
                                            <h1 class="text-dark text-lg  mt-4 mb-4">{{__('Comments')}}</h1>
                                            <h3 class="mb-0">{{count($contract->comment)}}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="theme-avtar bg-warning">
                                                <i class="ti ti-file-invoice  text-white"></i>
                                            </div>
                                            <h1 class="text-dark text-lg  mt-4 mb-4">{{__('Notes')}}</h1>
                                            <h3 class="mb-0">{{count($contract->note)}}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-6">
                            <div class="card report_card total_amount_card">
                                <div class="card-body pt-0">

                                    <address class="mb-0 text-sm">
                                        <div class="row mt-3 align-items-center">
                                            <div class="col-sm-4 h6 text-m">{{ __('Name') }}</div>
                                            <div class="col-sm-8 text-m"> {{ $contract->name }}</div>
                                            <div class="col-sm-4 h6 text-m">{{ __('Client Name') }}</div>
                                            <div class="col-sm-8 text-m"> {{ $contract->client->name }}</div>
                                            <div class="col-sm-4 h6 text-m">{{__('Type')}}</div>
                                            <div class="col-sm-8 text-m">{{$contract->contract_type->name }}</div>
                                            <div class="col-sm-4 h6 text-m">{{__('Value')}}</div>
                                            <div class="col-sm-8 text-m"> {{Auth::user()->priceFormat($contract->value) }}</div>
                                            <div class="col-sm-4 h6 text-m">{{__('Start Date')}}</div>
                                            <div class="col-sm-8 text-m">{{ Auth::user()->dateFormat($contract->start_date) }}</div>
                                            <div class="col-sm-4 h6 text-m">{{__('End Date')}}</div>
                                            <div class="col-sm-8 text-m">{{ Auth::user()->dateFormat($contract->end_date) }}</div>
                                        </div>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{__('Contact Description')}}</h5>
                                </div>
                                <div class="card-body">
                                    @if(\Auth::user()->type == 'Owner')

                                        {{ Form::open(['route' => ['contracts.description.store', $contract->id]]) }}
                                        <div class="form-group mt-3">
                                            <textarea class="tox-target pc-tinymce summernote" name="description"  id="summernote" rows="8">{!! $contract->description !!}</textarea>
                                        </div>
                                                @if( $contract->status == 'accept')
                                                <div class="col-md-12 text-end mb-0">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                                @endif
                                            {{ Form::close() }}
                                    @elseif(\Auth::user()->type == 'Client')
                                        {!!  $contract->description !!}
                                    @endif
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

                    <div class="tab-pane fade" id="attachments" role="tabpanel" aria-labelledby="pills-files-tab">
                        <div class="row ">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{__('Attachments')}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class=" ">
                                            @if( $contract->status == 'accept')
                                            {{-- <div class="card-body bg-none"> --}}
                                                <div class="col-md-12 dropzone browse-file" id="my-dropzone"></div>
                                            {{-- </div> --}}
                                            @endif
                                        </div>
                                    </div>

                                    @foreach($contract->files as $file)
                                    <div class="px-4 py-3">
                                        <div class="list-group-item ">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h6 class="text-sm mb-0">
                                                        <a href="#!">{{ $file->files }}</a>
                                                    </h6>
                                                    <p class="card-text small text-muted">
                                                        {{ number_format(\File::size(storage_path('contract_attechment/' . $file->files)) / 1048576, 2) . ' ' . __('MB') }}
                                                    </p>
                                                </div>

                                                     @php
                                                    $attachments=\App\Models\Utility::get_file('contract_attechment');

                                                    @endphp
                                                    <div class="action-btn bg-warning p-0 w-auto    ">
                                                        <a href="{{$attachments . '/' . $file->files }}"
                                                            class=" btn btn-sm d-inline-flex align-items-center"
                                                            download="" data-bs-toggle="tooltip" title="Download">
                                                        <span class="text-white"><i class="ti ti-download"></i></span>
                                                        </a>
                                                    </div>
                                                {{-- <div class="action-btn bg-warning p-0 w-auto">
                                                    <a href="{{ asset(Storage::url('contract_attechment')) . '/' . $file->files }}"
                                                        class=" btn btn-sm d-inline-flex align-items-center"
                                                        download="" data-bs-toggle="tooltip" title="Download">
                                                    <span class="text-white"><i class="ti ti-download"></i></span>
                                                    </a>
                                                </div> --}}
                                                <div class="col-auto p-0 ms-2 action-btn bg-danger">
                                                    @if(((\Auth::user()->id == $file->user_id) || (\Auth::user()->type == 'Owner')) &&($contract->status == 'accept'))
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['contracts.file.delete', $contract->id,$file->id]]) !!}
                                                            <a href="#!" class="btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                            <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                            </a>
                                                        {!! Form::close() !!}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="pills-comments-tab">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card">
                                        <div id="comment">
                                        <div class="card-header">
                                            <h5>{{__('Comments')}}</h5>
                                        </div>
                                        <div class="card-footer">
                                            @if( $contract->status == 'accept')
                                            {{-- {{ Form::open(['route' => ['comment.store', $contract->id]]) }} --}}
                                            <div class="col-12 d-flex">
                                                <div class="form-group mb-0 form-send w-100">
                                                    <form method="post" class="card-comment-box" id="form-comment" data-action="{{route('comment.store', [$contract->id, ])}}">
                                                        <textarea rows="1" class="form-control" name="comment" data-toggle="autosize" placeholder="Add a comment..." spellcheck="false"></textarea><grammarly-extension data-grammarly-shadow-root="true" style="position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;" class="cGcvT"></grammarly-extension><grammarly-extension data-grammarly-shadow-root="true" style="mix-blend-mode: darken; position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;" class="cGcvT"></grammarly-extension>
                                                    </form>
                                                </div>
                                                @can('Create Comment')
                                                <button id="comment_submit" class="btn btn-send"><i class="f-16 text-primary ti ti-brand-telegram">
                                                    </i>
                                                </button>
                                                @endcan
                                            </div>
                                            {{-- {{ Form::close() }} --}}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="list-group list-group-flush mb-0" id="comments">
                                        @foreach($contract->comment as $comment)
                                        @php
                                        $user = \App\Models\User::find($comment->user_id);
                                        @endphp
                                            <div class="list-group-item ">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" target="_blank">
                                                            <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                        </a>
                                                    </div>
                                                    <div class="col ml-n2">
                                                        <p class="d-block h6 text-sm font-weight-light mb-0 text-break">{{ $comment->comment }}</p>
                                                        <small class="d-block">{{$comment->created_at->diffForHumans()}}</small>
                                                    </div>
                                                    @if(((\Auth::user()->id == $comment->user_id) || (\Auth::user()->type == 'Owner')) &&($contract->status == 'accept'))
                                                    @can('Delete Comment')
                                                        <div class="col-auto p-0 mx-5 action-btn bg-danger">
                                                            {!! Form::open(['method' => 'GET', 'route' => ['comment.destroy', $comment->id]]) !!}
                                                                <a href="#!" class="btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="pills-notes-tab">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div id="notes">
                                    <div class="card">
                                    <div class="card-header">
                                        <h5>{{__('Notes')}}</h5>
                                    </div>
                                    <div class="card-body">
                                        @if( $contract->status == 'accept')
                                        {{ Form::open(['route' => ['contracts.note.store', $contract->id]]) }}
                                            <div class="form-group">
                                                <textarea class="tox-target pc-tinymce summernotes" style="width:100%" name="note"  id="summernote"></textarea>
                                            </div>
                                            @can('Create Note')
                                                <div class="col-md-12 text-end mb-0">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endcan
                                        {{ Form::close() }}
                                        @endif
                                            <div class="list-group list-group-flush mb-0" id="comments">
                                                @foreach($contract->note as $note)
                                                    @php
                                                    $user = \App\Models\User::find($note->user_id);
                                                    @endphp
                                                <div class="list-group-item ">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            {{-- <a href="{{ !empty($contract->client->avatar) ? asset(Storage::url('avatars')) . '/' . $contract->client->avatar : asset(Storage::url('avatars')) . '/avatar.png' }}" target="_blank">
                                                                <img class="rounded-circle"  width="25" height="25" src="{{ !empty($user->avatar) ? asset(Storage::url('avatars/')) . '/' . $user->avatar : asset(Storage::url('avatars/')) . '/avatar.png' }}">
                                                            </a> --}}

                                                            <a href="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" target="_blank">
                                                                <img src="{{(!empty($user->avatar))?  \App\Models\Utility::get_file($user->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle" width="30">
                                                            </a>
                                                        </div>
                                                        <div class="col ml-n2">
                                                            <p class="d-block h6 text-sm font-weight-light mb-0 text-break">{{ $note->note }}</p>
                                                            <small class="d-block">{{$note->created_at->diffForHumans()}}</small>
                                                        </div>
                                                        @if(((\Auth::user()->id == $note->user_id) || (\Auth::user()->type == 'Owner')) &&($contract->status == 'accept'))
                                                            @can('Delete Note')
                                                                <div class="col-auto col-auto p-0 mx-3 action-btn bg-danger">
                                                                    {!! Form::open(['method' => 'GET', 'route' => ['contracts.note.destroy', $note->id]]) !!}
                                                                        <a href="#!" class="btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                        <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                                        </a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </div>

@endsection
