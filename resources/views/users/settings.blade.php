@php
    $SITE_RTL = App\Models\Utility::getValByName('SITE_RTL');
    if($SITE_RTL == ''){
        $SITE_RTL == 'off';
    }
    $color = isset($settings['color']) ? $settings['color'] : 'theme-4';
    // dd($color);
    // $setting = App\Models\Utility::colorset();
    //  $color = 'theme-3';
    // if (!empty($setting['color'])) {
    //     $color = $setting['color'];
    // }
    $logo=\App\Models\Utility::get_file('logo/');

 $file_type = config('files_types');
$setting = App\Models\Utility::settings();

$local_storage_validation    = $setting['local_storage_validation'];
$local_storage_validations   = explode(',', $local_storage_validation);

$s3_storage_validation    = $setting['s3_storage_validation'];
$s3_storage_validations   = explode(',', $s3_storage_validation);

$wasabi_storage_validation    = $setting['wasabi_storage_validation'];
$wasabi_storage_validations   = explode(',', $wasabi_storage_validation);


@endphp
@if($color == 'theme-1')
 <style>
 .btn-check:checked + .btn-outline-success, .btn-check:active + .btn-outline-success, .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show {
            color: #ffffff;
            background: linear-gradient(141.55deg, rgba(81, 69, 157, 0) 3.46%, rgba(255, 58, 110, 0.6) 99.86%), #51459d !important;
            border-color: #51459d !important;

        }

        .btn-outline-success:hover
        {
            color: #ffffff;
            background: linear-gradient(141.55deg, rgba(81, 69, 157, 0) 3.46%, rgba(255, 58, 110, 0.6) 99.86%), #51459d !important;
            border-color: #51459d !important;
        }
        .btn.btn-outline-success{
            color: #51459d;
            border-color: #51459d !important;
        }
</style>
@endif
@if($color == 'theme-2')
<style>
    .btn-check:checked + .btn-outline-success, .btn-check:active + .btn-outline-success, .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show {
            color: #ffffff;
            background: linear-gradient(141.55deg, rgba(240, 244, 243, 0) 3.46%, #4ebbd3 99.86%)#1f3996 !important;
            border-color: #1F3996 !important;

        }

        .btn-outline-success:hover
        {
            color: #ffffff;
            background: linear-gradient(141.55deg, rgba(240, 244, 243, 0) 3.46%, #4ebbd3 99.86%)#1f3996 !important;
            border-color: #1F3996 !important;
        }
        .btn.btn-outline-success{
            color: #1F3996;
            border-color: #1F3996 !important;
        }
</style>
@endif

@if($color == 'theme-4')
<style>
    .btn-check:checked + .btn-outline-success, .btn-check:active + .btn-outline-success, .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show {
        color: #ffffff;
        background-color: #584ed2 !important;
        border-color: #584ed2 !important;

    }

    .btn-outline-success:hover
    {
        color: #ffffff;
        background-color: #584ed2 !important;
        border-color: #584ed2 !important;
    }
    .btn.btn-outline-success{
        color: #584ed2;
        border-color: #584ed2 !important;
    }
</style>
@endif

@if($color == 'theme-3')
    <style>
    .btn-check:checked + .btn-outline-success, .btn-check:active + .btn-outline-success, .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show {
            color: #ffffff;
            background-color: #6fd943 !important;
            border-color: #6fd943 !important;

        }

        .btn-outline-success:hover
        {
            color: #ffffff;
            background-color: #6fd943 !important;
            border-color: #6fd943 !important;
        }
        .btn.btn-outline-success{
            color: #6fd943;
            border-color: #6fd943 !important;
        }
    </style>

@endif

@push('script')
    <script>
        $(document).ready(function () {
            if ($('.gdpr_fulltime').is(':checked') ) {

                $('.fulltime').show();
            } else {

                $('.fulltime').hide();
            }

            $('#gdpr_cookie').on('change', function() {
                if ($('.gdpr_fulltime').is(':checked') ) {

                    $('.fulltime').show();
                } else {

                    $('.fulltime').hide();
                }
            });
        });

    </script>
     <script>
       var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: '#useradd-sidenav',
        offset: 300,

    })
   $(".list-group-item").click(function(){
          $('.list-group-item').filter(function(){
                return this.href == id;
        }).parent().removeClass('text-primary');
    });

    function check_theme(color_val) {
            $('.theme-color').prop('checked', false);
            $('input[value="'+color_val+'"]').prop('checked', true);
            $('#color_value').val(color_val);
        }

</script>
<script>
    $(document).on("click", '.send_email', function(e) {
        e.preventDefault();
        var title = $(this).attr('data-title');

        var size = 'md';
        var url = $(this).attr('data-url');
        if (typeof url != 'undefined') {
            $("#commonModal .modal-title").html(title);
            $("#commonModal .modal-dialog").addClass('modal-' + size);
            $("#commonModal").modal('show');

            $.post(url, {
                _token:'{{csrf_token()}}',
                mail_driver: $("#mail_driver").val(),
                mail_host: $("#mail_host").val(),
                mail_port: $("#mail_port").val(),
                mail_username: $("#mail_username").val(),
                mail_password: $("#mail_password").val(),
                mail_encryption: $("#mail_encryption").val(),
                mail_from_address: $("#mail_from_address").val(),
                mail_from_name: $("#mail_from_name").val(),

            }, function(data) {
                    $('#commonModal .body').html(data);
                });
            }
        });


        $(document).on('submit', '#test_email', function(e) {
            e.preventDefault();
            $("#email_sending").show();
            var post = $(this).serialize();
            var url = $(this).attr('action');
            $.ajax({
                type: "post",
                url: url,
                data: post,
                cache: false,
                beforeSend: function() {
                    $('#test_email .btn-create').attr('disabled', 'disabled');
                },
                success: function(data) {
                    if (data.is_success) {
                        show_toastr('Success', data.message, 'success');
                    } else {
                        show_toastr('Error', data.message, 'error');
                    }
                    $("#email_sending").hide();
                    $('#commonModal').modal('hide');
                },
                complete: function() {
                    $('#test_email .btn-create').removeAttr('disabled');
                },
            });
        });
</script>

<script>
    var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: '#useradd-sidenav',
        offset: 300,
    })
    $(".list-group-item").click(function(){
        $('.list-group-item').filter(function(){
            return this.href == id;
        }).parent().removeClass('text-primary');
    });

    function check_theme(color_val) {
        $('#theme_color').prop('checked', false);
        $('input[value="' + color_val + '"]').prop('checked', true);
    }

    $(document).on('change','[name=storage_setting]',function(){
    if($(this).val() == 's3'){
        $('.s3-setting').removeClass('d-none');
        $('.wasabi-setting').addClass('d-none');
        $('.local-setting').addClass('d-none');
    }else if($(this).val() == 'wasabi'){
        $('.s3-setting').addClass('d-none');
        $('.wasabi-setting').removeClass('d-none');
        $('.local-setting').addClass('d-none');
    }else{
        $('.s3-setting').addClass('d-none');
        $('.wasabi-setting').addClass('d-none');
        $('.local-setting').removeClass('d-none');
    }
});
</script>
    @endpush
@extends('layouts.admin')

@section('title')
    {{ __('Brand Settings') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Settings')}}</li>
@endsection

@section('content')

<div class="row">
     <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="useradd-sidenav">
                        <a href="#brand-setting" class="list-group-item list-group-item-action border-0">{{__('Brand Settings')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                        <a href="#email-setting" class="list-group-item list-group-item-action border-0">{{__('Email Settings')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                        <a href="#pusher-setting" class="list-group-item list-group-item-action border-0">{{__('Pusher Settings')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                        <a href="#payment-setting" class="list-group-item list-group-item-action border-0">{{__('Payment Settings')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                        <a href="#recaptcha-setting" class="list-group-item list-group-item-action border-0">{{__('ReCaptcha Settings')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                        <a href="#storage-setting" class="list-group-item list-group-item-action border-0">{{__('Storage Settings')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="" id="brand-setting">
                    <div class="card ">
                        <div class="card-header">
                            <h5>{{__('Brand Settings')}}</h5>
                            <small class="text-dark font-weight-bold">{{__("Edit your Brand Settings")}}</small>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{route('settings.store')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card site-card">
                                            <div class="card-header">
                                                <h5>{{__('Dark Logo')}}</h5>
                                            </div>
                                            <div class="card-body pt-3">
                                                <div class=" setting-card">
                                                    <div class="logo-content mt-4 text-center">
                                                        <a href="{{$logo.'logo-dark.png'}}" target="_blank">
                                                            <img id="blah" alt="your image" src="{{$logo.'logo-dark.png'}}" width="150px" class="big-logo">
                                                        </a>
                                                        {{-- <a href="{{asset(Storage::url('logo/logo-dark.png'))}}" target="_blank">
                                                            <img id="blah" alt="your image"src="{{asset(Storage::url('logo/logo-dark.png'))}}" class="big-logo"/>
                                                        </a> --}}
                                                    </div>
                                                    <div class="choose-files mt-5 logo-btn">
                                                        <label for="dark_logo">
                                                            <div class=" bg-primary dark_logo_update"> <i class="ti ti-upload px-1"></i>{{__('Choose file here')}}</div>
                                                            <input type="file" class="form-control file" name="dark_logo" id="dark_logo" data-filename="dark_logo_update" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                        </label>
                                                    </div>
                                                    @error('company_logo')
                                                    <span class="invalid-dark_logo text-xs text-danger" role="alert">{{ $message }}</span>
                                                    @enderror
                                                    <p class="lh-160 mb-0 text-sm pt-0">{{__('These Logo will appear on Estimations and Invoice as well.')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card site-card">
                                            <div class="card-header">
                                                <h5>{{__('Light Logo')}}</h5>
                                            </div>
                                            <div class="card-body pt-3">
                                                <div class=" setting-card">
                                                    <div class="logo-content mt-4 text-center">
                                                        {{-- <a href="{{asset(Storage::url('logo/logo-light.png'))}}" target="_blank">
                                                            <img id="blah1" alt="your image" src="{{asset(Storage::url('logo/logo-light.png'))}}" class="big-logo img_setting" />
                                                        </a> --}}

                                                        <a href="{{$logo.'logo-light.png'}}" target="_blank">
                                                            <img id="blah1" alt="your image" src="{{$logo.'logo-light.png'}}" width="150px" class="big-logo img_setting">
                                                        </a>
                                                    </div>
                                                    <div class="choose-files mt-5 logo-btn">
                                                        <label for="light_logo">
                                                            <div class=" bg-primary light_logo_update"> <i class="ti ti-upload px-1"></i>{{__('Choose file here')}}</div>
                                                            <input type="file" class="form-control file" name="light_logo" id="light_logo" data-filename="light_logo_update" onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])">
                                                        </label>
                                                    </div>
                                                    @error('company_logo')
                                                    <span class="invalid-light_logo text-xs text-danger" role="alert">{{ $message }}</span>
                                                    @enderror
                                                    <p class="lh-160 mb-0 text-sm pt-0">{{__('These Logo will appear on Estimations and Invoice as well.')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card site-card">
                                            <div class="card-header">
                                                <h5>{{__('Favicon')}}</h5>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class=" setting-card">
                                                    <div class="logo-content mt-4 text-center">
                                                        <a href="{{$logo.'favicon.png'}}" target="_blank">
                                                            <img id="blah2" alt="your image" src="{{$logo.'favicon.png'}}" width="80px" class="big-logo img_setting">
                                                        </a>

                                                        {{-- <a href="{{asset(Storage::url('logo/favicon.png'))}}" target="_blank">
                                                            <img id="blah2" alt="your image" src="{{asset(Storage::url('logo/favicon.png'))}}" class="small-logo" style="width: 60px !important;"  alt="" />
                                                        </a> --}}
                                                    </div>
                                                    <div class="choose-files mt-5 logo-btn">
                                                        <label for="favicon">
                                                            <div class=" bg-primary favicon_update"> <i class="ti ti-upload px-1"></i>{{__('Choose file here')}}</div>
                                                            <input type="file" class="form-control file" name="favicon" id="favicon" data-filename="favicon_update" onchange="document.getElementById('blah2').src = window.URL.createObjectURL(this.files[0])">
                                                        </label>
                                                    </div>
                                                    @error('favicon')

                                                    <span class="invalid-favicon text-xs text-danger" role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{Form::label('header_text',__('Title Text'),['class'=>'col-form-label text-dark']) }}
                                            {{Form::text('header_text',Utility::getValByName('header_text'),array('class'=>'form-control','placeholder'=>__('Enter Header Title Text')))}}
                                            @error('header_text')
                                            <span class="invalid-header_text" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{Form::label('footer_text',__('Footer Text'),['class'=>'col-form-label text-dark']) }}
                                            {{Form::text('footer_text',Utility::getValByName('footer_text'),array('class'=>'form-control','placeholder'=>__('Enter Footer Text')))}}
                                            @error('footer_text')
                                            <span class="invalid-footer_text" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            {{Form::label('default_language',__('Default Language'),['class'=>'col-form-label text-dark']) }}
                                            <select name="default_language" id="default_language" class="form-control select2">
                                                @foreach(Utility::languages() as $language)
                                                    <option @if(Utility::getValByName('default_language') == $language) selected @endif value="{{$language}}">{{Str::upper($language)}}</option>
                                                @endforeach
                                            </select>
                                            @error('default_language')
                                            <span class="invalid-default_language" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3 my-auto">
                                        <div class="form-group">
                                             <label class="text-dark mb-1 mt-3" for="SITE_RTL">{{ __('Enable RTL') }}</label>
                                            <div class="">
                                                <input type="checkbox" name="SITE_RTL" id="SITE_RTL" data-toggle="switchbutton" value="on" {{$SITE_RTL == 'on' ? 'checked="checked"' : '' }} data-onstyle="primary">
                                                <label class="form-check-labe" for="SITE_RTL"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3 my-auto">

                                        <div class="form-group">
                                            <label class=" text-dark mb-1 mt-3" for="enable_landing">{{ __('Enable Landing Page') }}</label>
                                            <div class="">
                                                <input type="checkbox" value="yes" name="enable_landing" class="form-check-input" id="enable_landing" data-toggle="switchbutton" {{ (Utility::getValByName('enable_landing') == 'yes') ? 'checked' : '' }} data-onstyle="primary">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3 my-auto">

                                            <div class="form-group">
                                                 <label class="text-dark mb-1 mt-3" for="signup_button">{{ __('Enable Sign-Up Page') }}</label>
                                                <div class="">
                                                    <input type="checkbox" name="signup_button" id="signup_button" data-toggle="switchbutton" {{ Utility::getValByName('signup_button') == 'on' ? 'checked="checked"' : '' }} data-onstyle="primary">
                                                    <label class="form-check-labe" for="signup_button"></label>
                                                </div>
                                            </div>

                                    </div>
                                    <div class="col-3 my-auto">
                                        <div class="form-group">
                                             <label class="text-dark mb-1 mt-3" for="gdpr_cookie">{{ __('GDPR Cookie') }}</label>
                                            <div class="">
                                                <input type="checkbox" class="gdpr_fulltime gdpr_type" name="gdpr_cookie" id="gdpr_cookie" data-toggle="switchbutton" {{ isset($settings['gdpr_cookie']) && $settings['gdpr_cookie'] == 'on' ? 'checked="checked"' : '' }} data-onstyle="primary">
                                                <label class="form-check-labe" for="gdpr_cookie"></label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">

                                    <div class="form-group col-12">
                                        {{Form::label('cookie_text',__('GDPR Cookie Text'),array('class'=>'fulltime') )}}
                                        {!! Form::textarea('cookie_text',isset($settings['cookie_text']) && $settings['cookie_text'] ? $settings['cookie_text'] : '', ['class'=>'form-control fulltime','style'=>'display: hidden;resize: none;','rows'=>'4']) !!}
                                    </div>

                                </div>
                                <h4 class="small-title">{{__('Theme Customizer')}}</h4>
                                <div class="setting-card setting-logo-box p-3">
                                    <div class="row">
                                        <div class="col-4 my-auto">
                                            <h6 class="mt-3">
                                                <i data-feather="credit-card" class="me-2"></i>{{ __('Primary color settings') }}
                                              </h6>
                                              <hr class="my-2" />
                                              <div class="theme-color themes-color">
                                                <input type="hidden" name="color" id="color_value" value="{{ $settings['color'] }}">
                                                <a href="#!" class="{{($color =='theme-1') ? 'active_color' : ''}}" data-value="theme-1" onclick="check_theme('theme-1')"></a>
                                                <input type="radio" class="color" name="color" value="theme-1" style="display: none;">
                                                <a href="#!" class="{{($color =='theme-2') ? 'active_color' : ''}}" data-value="theme-2" onclick="check_theme('theme-2')"></a>
                                                <input type="radio" class="color" name="color" value="theme-2" style="display: none;">
                                                <a href="#!" class="{{($color =='theme-3') ? 'active_color' : ''}}" data-value="theme-3" onclick="check_theme('theme-3')"></a>
                                                <input type="radio" class="color" name="color" value="theme-3" style="display: none;">
                                                <a href="#!" class="{{($color =='theme-4') ? 'active_color' : ''}}" data-value="theme-4" onclick="check_theme('theme-4')"></a>
                                                <input type="radio" class="color" name="color" value="theme-4" style="display: none;">
                                            </div>
                                        </div>
                                        <div class="col-4 my-auto">
                                            <h6>
                                                <i data-feather="layout" class="me-2"></i>{{__('Sidebar settings')}}
                                              </h6>
                                              <hr class="my-2" />
                                              <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="cust-theme-bg" name="cust_theme_bg" {{ Utility::getValByName('cust_theme_bg') == 'on' ? 'checked' : '' }} />
                                                <label class="form-check-label f-w-600 pl-1" for="cust-theme-bg"
                                                  >{{__('Transparent layout')}}</label
                                                >
                                              </div>
                                        </div>
                                        <div class="col-4 my-auto">
                                            <h6 >
                                              <i data-feather="sun" class="me-2"></i>{{__('Layout settings')}}
                                            </h6>
                                            <hr class="my-2" />
                                            <div class="form-check form-switch mt-2">
                                                <input type="checkbox" class="form-check-input" id="cust-darklayout" name="cust_darklayout"{{ Utility::getValByName('cust_darklayout') == 'on' ? 'checked' : '' }} />
                                                <label class="form-check-label f-w-600 pl-1" for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12  text-end">
                                    <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-print-invoice  btn-primary m-r-10">
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="card" id="email-setting">
                    {{Form::open(['route'=>'email.settings.store','method'=>'post'])}}
                    @csrf

                        <div class="card-header">
                            <h5>{{__('Email Settings')}}</h5>
                            <small class="text-dark font-weight-bold">{{__("Edit your Email Settings")}}</small>
                        </div>
                        <div class="card-body">

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_driver" class="col-form-label text-dark">{{ __('Mail Driver') }}</label>
                                        <input type="text" name="mail_driver" id="mail_driver" class="form-control {{ ($errors->has('mail_driver')) ? 'is-invalid' : '' }}" value="{{env('MAIL_DRIVER')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_driver_placeholder') }}"/>
                                        @if ($errors->has('mail_driver'))
                                            <span class="invalid-feedback text-danger text-xs">
                                            {{ $errors->first('mail_driver') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_host" class="col-form-label text-dark">{{ __('Mail Host') }}</label>
                                        <input type="text" name="mail_host" id="mail_host" class="form-control {{ ($errors->has('mail_host')) ? 'is-invalid' : '' }}" value="{{env('MAIL_HOST')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_placeholder') }}"/>
                                        @if ($errors->has('mail_host'))
                                            <span class="invalid-feedback text-danger text-xs">
                                            {{ $errors->first('mail_host') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_port" class="col-form-label text-dark">{{ __('Mail Port') }}</label>
                                        <input type="number" name="mail_port" id="mail_port" class="form-control {{ ($errors->has('mail_port')) ? 'is-invalid' : '' }}" value="{{env('MAIL_PORT')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_placeholder') }}"/>
                                        @if ($errors->has('mail_port'))
                                            <span class="invalid-feedback text-danger text-xs">
                                            {{ $errors->first('mail_port') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_username" class="col-form-label text-dark">{{ __('Mail Username') }}</label>
                                        <input type="text" name="mail_username" id="mail_username" class="form-control {{ ($errors->has('mail_username')) ? 'is-invalid' : '' }}" value="{{env('MAIL_USERNAME')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_placeholder') }}"/>
                                        @if ($errors->has('mail_username'))
                                            <span class="invalid-feedback text-danger text-xs">
                                            {{ $errors->first('mail_username') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_password" class="col-form-label text-dark">{{ __('Mail Password') }}</label>
                                        <input type="text" name="mail_password" id="mail_password" class="form-control {{ ($errors->has('mail_password')) ? 'is-invalid' : '' }}" value="{{env('MAIL_PASSWORD')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_placeholder') }}"/>
                                        @if ($errors->has('mail_password'))
                                            <span class="invalid-feedback text-danger text-xs">
                                            {{ $errors->first('mail_password') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_encryption" class="col-form-label text-dark">{{ __('Mail Encryption') }}</label>
                                        <input type="text" name="mail_encryption" id="mail_encryption" class="form-control {{ ($errors->has('mail_encryption')) ? 'is-invalid' : '' }}" value="{{env('MAIL_ENCRYPTION')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_placeholder') }}"/>
                                        @if ($errors->has('mail_encryption'))
                                            <span class="invalid-feedback text-danger text-xs">
                                        {{ $errors->first('mail_encryption') }}
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_from_address" class="col-form-label text-dark">{{ __('Mail From Address') }}</label>
                                        <input type="text" name="mail_from_address" id="mail_from_address" class="form-control {{ ($errors->has('mail_from_address')) ? 'is-invalid' : '' }}" value="{{env('MAIL_FROM_ADDRESS')}}" placeholder="{{ __('Enter Mail From Address') }}"/>
                                        @if ($errors->has('mail_from_address'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_from_address') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_from_name" class="col-form-label text-dark">{{ __('Mail From Name') }}</label>
                                        <input type="text" name="mail_from_name" id="mail_from_name" class="form-control {{ ($errors->has('mail_from_name')) ? 'is-invalid' : '' }}" value="{{env('MAIL_FROM_NAME')}}" placeholder="{{ __('Enter Mail From Name') }}"/>
                                        @if ($errors->has('mail_from_name'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_from_name') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 text-right">
                                    <a href="#" class="btn btn-print-invoice  btn-primary m-r-10 send_email" data-ajax-popup="true" data-title="{{__('Send Test Mail')}}" data-url="{{route('test.email')}}">
                                        {{__('Send Test Mail')}}
                                    </a>

                                    {{-- <a href="#" data-url="{{ route('test.email') }}"
                                    data-title="{{ __('Send Test Mail') }}"
                                    class="btn btn-primary btn-submit text-white send_email">
                                    {{ __('Send Test Mail') }} --}}
                                </a>
                                </div>
                                <div class="col-lg-6  text-end">
                                    <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-print-invoice  btn-primary m-r-10">
                                </div>
                            </div>



                        </div>
                    {{Form::close()}}
                </div>

                <div class="card" id="pusher-setting">
                    <form method="POST" action="{{ route('pusher.settings.store') }}" accept-charset="UTF-8">
                        @csrf
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h5>{{__('Pusher Settings')}}</h5>
                                    <small>{{__('Pusher Settings')}}</small>
                                </div>
                                <div class="col-6">
                                    <div class="text-end">
                                        <input type="checkbox" name="enable_chat" id="enable_chat" data-toggle="switchbutton" @if(env('CHAT_MODULE') =='yes') checked @endif value="yes" data-onstyle="primary">
                                        <label class="form-check-labe" for="enable_chat"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pusher_app_id" class="col-form-label text-dark">{{ __('Pusher App Id') }}</label>
                                        <input type="text" name="pusher_app_id" id="pusher_app_id" class="form-control {{ ($errors->has('pusher_app_id')) ? 'is-invalid' : '' }}" value="{{env('PUSHER_APP_ID')}}" placeholder="{{ __('Pusher App Id') }}"/>
                                        @if ($errors->has('pusher_app_id'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('pusher_app_id') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pusher_app_key" class="col-form-label text-dark">{{ __('Pusher App Key') }}</label>
                                        <input type="text" name="pusher_app_key" id="pusher_app_key" class="form-control {{ ($errors->has('pusher_app_key')) ? 'is-invalid' : '' }}" value="{{env('PUSHER_APP_KEY')}}" placeholder="{{ __('Pusher App Key') }}"/>
                                        @if ($errors->has('pusher_app_key'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('pusher_app_key') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pusher_app_secret" class="col-form-label text-dark">{{ __('Pusher App Secret') }}</label>
                                        <input type="text" name="pusher_app_secret" id="pusher_app_secret" class="form-control {{ ($errors->has('pusher_app_secret')) ? 'is-invalid' : '' }}" value="{{env('PUSHER_APP_SECRET')}}" placeholder="{{ __('Pusher App Secret') }}"/>
                                        @if ($errors->has('pusher_app_secret'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('pusher_app_secret') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pusher_app_cluster" class="col-form-label text-dark">{{ __('Pusher App Cluster') }}</label>
                                        <input type="text" name="pusher_app_cluster" id="pusher_app_cluster" class="form-control {{ ($errors->has('pusher_app_cluster')) ? 'is-invalid' : '' }}" value="{{env('PUSHER_APP_CLUSTER')}}" placeholder="{{ __('Pusher App Cluster') }}"/>
                                        @if ($errors->has('pusher_app_cluster'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('pusher_app_cluster') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12 text-xs">
                                    <a href="https://pusher.com/channels" target="_blank">{{__('You can Make Pusher channel Account from here and Get your App Id and Secret key')}}</a>
                                </div>
                            </div>
                            <div class="col-lg-12  text-end">
                                <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card" id="payment-setting">
                    <div class="card-header">
                        <h5>{{__('Payment Settings')}}</h5>
                        <small class="text-dark font-weight-bold">{{__("These details will be used to collect subscription plan payments.Each subscription plan will have a payment button based on the below configuration.")}}</small>
                    </div>
                    <div class="card-body">

                            <form id="setting-form" method="post" action="{{route('payment.settings')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                        <label class="col-form-label">{{__('Currency')}} *</label>
                                                        <input type="text" name="currency" class="form-control" id="currency" value="{{(!isset($payment['currency']) || is_null($payment['currency'])) ? '' : $payment['currency']}}" required>
                                                        <small class="text-xs">
                                                            {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                                            <a href="https://stripe.com/docs/currencies" target="_blank">{{ __(' You can find out how to do that here.') }}</a>
                                                        </small>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 form-group"> 
                                                        <label for="currency_symbol" class="col-form-label">{{__('Currency Symbol')}}</label>
                                                        <input type="text" name="currency_symbol" class="form-control" id="currency_symbol" value="{{(!isset($payment['currency_symbol']) || is_null($payment['currency_symbol'])) ? '' : $payment['currency_symbol']}}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="faq justify-content-center">
                                    <div class="col-sm-12 col-md-10 col-xxl-12">
                                        <div class="accordion accordion-flush" id="accordionExample">

                                            <!-- Strip -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-2">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Stripe') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse1" class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Stripe') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">

                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_stripe_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_stripe_enabled" id="is_stripe_enabled" {{(isset($payment['is_stripe_enabled']) && $payment['is_stripe_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_stripe_enabled">{{__('Enable Stripe')}}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="stripe_key" class="col-form-label">{{__('Stripe Key')}}</label>
                                                                    <input class="form-control" placeholder="{{__('Stripe Key')}}" name="stripe_key" type="text" value="{{(!isset($payment['stripe_key']) || is_null($payment['stripe_key'])) ? '' : $payment['stripe_key']}}" id="stripe_key">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="stripe_secret" class="col-form-label">{{__('Stripe Secret')}}</label>
                                                                    <input class="form-control " placeholder="{{ __('Stripe Secret') }}" name="stripe_secret" type="text" value="{{(!isset($payment['stripe_secret']) || is_null($payment['stripe_secret'])) ? '' : $payment['stripe_secret']}}" id="stripe_secret">
                                                                </div>
                                                            </div>
                                                            {{-- <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="stripe_secret" class="col-form-label">{{__('Stripe_Webhook_Secret')}}</label>
                                                                    <input class="form-control " placeholder="{{ __('Enter Stripe Webhook Secret') }}" name="stripe_webhook_secret" type="text" value="{{(!isset($payment['stripe_webhook_secret']) || is_null($payment['stripe_webhook_secret'])) ? '' : $payment['stripe_webhook_secret']}}" id="stripe_webhook_secret">
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paypal -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-3">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Paypal') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse2" class="accordion-collapse collapse"aria-labelledby="heading-2-3"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Paypal') }}</h5>
                                                            </div>



                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_paypal_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_paypal_enabled" id="is_paypal_enabled" {{(isset($payment['is_paypal_enabled']) && $payment['is_paypal_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_paypal_enabled">{{__('Enable Paypal')}}</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 pb-4">
                                                                <label class="paypal-label col-form-label" for="paypal_mode">{{__('Paypal Mode')}}</label> <br>
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    <input type="radio" name="paypal_mode" value="sandbox" class="form-check-input" {{ !isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>

                                                                                    {{__('Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    <input type="radio" name="paypal_mode" value="live" class="form-check-input" {{ isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>

                                                                                    {{__('Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paypal_client_id" class="col-form-label">{{ __('Client ID') }}</label>
                                                                    <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="{{(!isset($payment['paypal_client_id']) || is_null($payment['paypal_client_id'])) ? '' : $payment['paypal_client_id']}}" placeholder="{{ __('Client ID') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paypal_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control" value="{{(!isset($payment['paypal_secret_key']) || is_null($payment['paypal_secret_key'])) ? '' : $payment['paypal_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paystack -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-4">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Paystack') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse3" class="accordion-collapse collapse"aria-labelledby="heading-2-4"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Paystack') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_paystack_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_paystack_enabled" id="is_paystack_enabled" {{(isset($payment['is_paystack_enabled']) && $payment['is_paystack_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_paystack_enabled">{{__('Enable Paystack')}}</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paypal_client_id" class="col-form-label">{{ __('Public Key')}}</label>
                                                                    <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control" value="{{(!isset($payment['paystack_public_key']) || is_null($payment['paystack_public_key'])) ? '' : $payment['paystack_public_key']}}" placeholder="{{ __('Public Key')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paystack_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="paystack_secret_key" id="paystack_secret_key" class="form-control" value="{{(!isset($payment['paystack_secret_key']) || is_null($payment['paystack_secret_key'])) ? '' : $payment['paystack_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                             <!-- FLUTTERWAVE -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-5">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Flutterwave') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse4" class="accordion-collapse collapse"aria-labelledby="heading-2-5"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Flutterwave') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_flutterwave_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_flutterwave_enabled" id="is_flutterwave_enabled" {{(isset($payment['is_flutterwave_enabled']) && $payment['is_flutterwave_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_flutterwave_enabled">{{__('Enable Flutterwave')}}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paypal_client_id" class="col-form-label">{{ __('Public Key')}}</label>
                                                                    <input type="text" name="flutterwave_public_key" id="flutterwave_public_key" class="form-control" value="{{(!isset($payment['flutterwave_public_key']) || is_null($payment['flutterwave_public_key'])) ? '' : $payment['flutterwave_public_key']}}" placeholder="Public Key">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paystack_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="flutterwave_secret_key" id="flutterwave_secret_key" class="form-control" value="{{(!isset($payment['flutterwave_secret_key']) || is_null($payment['flutterwave_secret_key'])) ? '' : $payment['flutterwave_secret_key']}}" placeholder="Secret Key">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Razorpay -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-6">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Razorpay') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse5" class="accordion-collapse collapse"aria-labelledby="heading-2-6"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Razorpay') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_razorpay_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_razorpay_enabled" id="is_razorpay_enabled" {{(isset($payment['is_razorpay_enabled']) && $payment['is_razorpay_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_razorpay_enabled">{{__('Enable Razorpay')}}</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paypal_client_id" class="col-form-label">Public Key</label>

                                                                    <input type="text" name="razorpay_public_key" id="razorpay_public_key" class="form-control" value="{{(!isset($payment['razorpay_public_key']) || is_null($payment['razorpay_public_key'])) ? '' : $payment['razorpay_public_key']}}" placeholder="Public Key">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paystack_secret_key" class="col-form-label">Secret Key</label>
                                                                    <input type="text" name="razorpay_secret_key" id="razorpay_secret_key" class="form-control" value="{{(!isset($payment['razorpay_secret_key']) || is_null($payment['razorpay_secret_key'])) ? '' : $payment['razorpay_secret_key']}}" placeholder="Secret Key">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paytm -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-7">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="true" aria-controls="collapse6">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Paytm') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse6" class="accordion-collapse collapse"aria-labelledby="heading-2-7"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Paytm') }}</h5>
                                                            </div>

                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_paytm_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_paytm_enabled" id="is_paytm_enabled" {{(isset($payment['is_paytm_enabled']) && $payment['is_paytm_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_paytm_enabled">{{__('Enable Paytm')}}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 pb-4">
                                                                <label class="paypal-label col-form-label" for="paypal_mode">Paytm Environment</label> <br>
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">

                                                                                    <input type="radio" name="paytm_mode" value="local" class="form-check-input" {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>

                                                                                    {{__('Local')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    <input type="radio" name="paytm_mode" value="production" class="form-check-input" {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>

                                                                                    {{__('Production')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="paytm_public_key" class="col-form-label">Merchant ID</label>
                                                                    <input type="text" name="paytm_merchant_id" id="paytm_merchant_id" class="form-control" value="{{(!isset($payment['paytm_merchant_id']) || is_null($payment['paytm_merchant_id'])) ? '' : $payment['paytm_merchant_id']}}" placeholder="Merchant ID">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="paytm_secret_key" class="col-form-label">Merchant Key</label>
                                                                    <input type="text" name="paytm_merchant_key" id="paytm_merchant_key" class="form-control" value="{{(!isset($payment['paytm_merchant_key']) || is_null($payment['paytm_merchant_key'])) ? '' : $payment['paytm_merchant_key']}}" placeholder="Merchant Key">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="paytm_industry_type" class="col-form-label">Industry Type</label>
                                                                    <input type="text" name="paytm_industry_type" id="paytm_industry_type" class="form-control" value="{{(!isset($payment['paytm_industry_type']) || is_null($payment['paytm_industry_type'])) ? '' : $payment['paytm_industry_type']}}" placeholder="Industry Type">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Mercado Pago-->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-8">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="true" aria-controls="collapse7">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Mercado Pago') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse7" class="accordion-collapse collapse"aria-labelledby="heading-2-8"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Mercado Pago') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_mercado_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_mercado_enabled" id="is_mercado_enabled" {{(isset($payment['is_mercado_enabled']) && $payment['is_mercado_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_mercado_enabled">{{__('Enable Mercado Pago')}}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 pb-4">
                                                                <label class="coingate-label col-form-label" for="mercado_mode">{{__('Mercado Mode')}}</label> <br>
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    <input type="radio" name="mercado_mode" value="sandbox" class="form-check-input" {{ isset($payment['mercado_mode']) && $payment['mercado_mode'] == '' || isset($payment['mercado_mode']) && $payment['mercado_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                    {{__('Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    <input type="radio" name="mercado_mode" value="live" class="form-check-input" {{ isset($payment['mercado_mode']) && $payment['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                    {{__('Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="mercado_access_token" class="col-form-label">{{ __('Access Token') }}</label>
                                                                    <input type="text" name="mercado_access_token" id="mercado_access_token" class="form-control" value="{{isset($payment['mercado_access_token']) ? $payment['mercado_access_token']:''}}" placeholder="{{ __('Access Token') }}"/>
                                                                    @if ($errors->has('mercado_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('mercado_access_token') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Mollie -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-9">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="true" aria-controls="collapse8">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Mollie') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse8" class="accordion-collapse collapse"aria-labelledby="heading-2-9"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Mollie') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_mollie_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_mollie_enabled" id="is_mollie_enabled" {{(isset($payment['is_mollie_enabled']) && $payment['is_mollie_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_mollie_enabled">{{__('Enable Mollie')}}</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="mollie_api_key" class="col-form-label">Mollie Api Key</label>
                                                                    <input type="text" name="mollie_api_key" id="mollie_api_key" class="form-control" value="{{(!isset($payment['mollie_api_key']) || is_null($payment['mollie_api_key'])) ? '' : $payment['mollie_api_key']}}" placeholder="Mollie Api Key">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="mollie_profile_id" class="col-form-label">Mollie Profile Id</label>
                                                                    <input type="text" name="mollie_profile_id" id="mollie_profile_id" class="form-control" value="{{(!isset($payment['mollie_profile_id']) || is_null($payment['mollie_profile_id'])) ? '' : $payment['mollie_profile_id']}}" placeholder="Mollie Profile Id">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="mollie_partner_id" class="col-form-label">Mollie Partner Id</label>
                                                                    <input type="text" name="mollie_partner_id" id="mollie_partner_id" class="form-control" value="{{(!isset($payment['mollie_partner_id']) || is_null($payment['mollie_partner_id'])) ? '' : $payment['mollie_partner_id']}}" placeholder="Mollie Partner Id">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Skrill -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-10">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('Skrill') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse9" class="accordion-collapse collapse"aria-labelledby="heading-2-10"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('Skrill') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_skrill_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_skrill_enabled" id="is_skrill_enabled" {{(isset($payment['is_skrill_enabled']) && $payment['is_skrill_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_skrill_enabled">{{__('Enable Skrill')}}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="mollie_api_key" class="col-form-label">Skrill Email</label>
                                                                    <input type="text" name="skrill_email" id="skrill_email" class="form-control" value="{{(!isset($payment['skrill_email']) || is_null($payment['skrill_email'])) ? '' : $payment['skrill_email']}}" placeholder="Enter Skrill Email">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CoinGate -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-11">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('CoinGate') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse10" class="accordion-collapse collapse"aria-labelledby="heading-2-11"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('CoinGate') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_coingate_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_coingate_enabled" id="is_coingate_enabled" {{(isset($payment['is_coingate_enabled']) && $payment['is_coingate_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_coingate_enabled">{{__('Enable CoinGate')}}</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 pb-4">
                                                                <label class="col-form-label" for="coingate_mode">CoinGate Mode</label> <br>
                                                                <div class="d-flex">
                                                                    <div class="mr-2" style="margin-right: 15px;">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">

                                                                                    <input type="radio" name="coingate_mode" value="sandbox" class="form-check-input" {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>

                                                                                    {{__('Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-2">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    <input type="radio" name="coingate_mode" value="live" class="form-check-input" {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                    {{__('Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="coingate_auth_token" class="col-form-label">CoinGate Auth Token</label>
                                                                    <input type="text" name="coingate_auth_token" id="coingate_auth_token" class="form-control" value="{{(!isset($payment['coingate_auth_token']) || is_null($payment['coingate_auth_token'])) ? '' : $payment['coingate_auth_token']}}" placeholder="CoinGate Auth Token">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PaymentWall -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-12">
                                                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse11" aria-expanded="true" aria-controls="collapse11">
                                                        <span class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i> {{ __('PaymentWall') }}
                                                        </span>
                                                    </button>
                                                </h2>
                                                <div id="collapse11" class="accordion-collapse collapse"aria-labelledby="heading-2-12"data-bs-parent="#accordionExample" >
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-6 py-2">
                                                                <h5 class="h5">{{ __('PaymentWall') }}</h5>
                                                            </div>
                                                            <div class="col-6 py-2 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <input type="hidden" name="is_paymentwall_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input" name="is_paymentwall_enabled" id="is_paymentwall_enabled" {{(isset($payment['is_paymentwall_enabled']) && $payment['is_paymentwall_enabled'] == 'on') ? 'checked' : ''}}>
                                                                    <label class="custom-control-label form-control-label" for="is_paymentwall_enabled">{{__('Enable PaymentWall')}}</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paymentwall_public_key" class="col-form-label">{{ __('Public Key')}}</label>
                                                                    <input type="text" name="paymentwall_public_key" id="paymentwall_public_key" class="form-control" value="{{(!isset($payment['paymentwall_public_key']) || is_null($payment['paymentwall_public_key'])) ? '' : $payment['paymentwall_public_key']}}" placeholder="{{ __('Public Key')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paymentwall_private_key" class="col-form-label">{{ __('Private Key') }}</label>
                                                                    <input type="text" name="paymentwall_private_key" id="paymentwall_private_key" class="form-control" value="{{(!isset($payment['paymentwall_private_key']) || is_null($payment['paymentwall_private_key'])) ? '' : $payment['paymentwall_private_key']}}" placeholder="{{ __('Private Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12  text-end">
                                        <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit" value="{{__('Save Changes')}}">
                                </div>
                            </form>
                        </div>

                </div>

                <div class="card" id="recaptcha-setting">

                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h5>{{__('ReCaptcha Settings')}}</h5>

                                <label class="custom-control-label form-control-label" for="recaptcha_module">{{ __('Google Recaptcha') }}<a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/" target="_blank" class="text-blue">
                                    <small>({{__('How to Get Google reCaptcha Site and Secret key')}})</small>
                                </a></label>
                            </div>

                            <div class="col-6 text-end">
                                <input type="checkbox" name="recaptcha_module" id="recaptcha_module" data-toggle="switchbutton" {{ env('RECAPTCHA_MODULE') == 'yes' ? 'checked="checked"' : '' }} value="yes" data-onstyle="primary">
                                    <label class="form-check-labe" for="recaptcha_module"></label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('recaptcha.settings.store') }}" accept-charset="UTF-8">
                            @csrf
                            <div class="row">

                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                    <label for="google_recaptcha_key" class="col-form-label">{{ __('Google Recaptcha Key') }}</label>
                                    <input class="form-control" placeholder="{{ __('Enter Google Recaptcha Key') }}" name="google_recaptcha_key" type="text" value="{{env('NOCAPTCHA_SITEKEY')}}" id="google_recaptcha_key">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                    <label for="google_recaptcha_secret" class="col-form-label">{{ __('Google Recaptcha Secret') }}</label>
                                    <input class="form-control " placeholder="{{ __('Enter Google Recaptcha Secret') }}" name="google_recaptcha_secret" type="text" value="{{env('NOCAPTCHA_SECRET')}}" id="google_recaptcha_secret">
                                </div>
                            </div>
                            <div class="col-lg-12  text-end">
                                <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </form>
                    </div>
                </div>

                <!--storage Setting-->
                <div id="storage-setting" class="card mb-3">
                    {{ Form::open(array('route' => 'storage.setting.store', 'enctype' => "multipart/form-data")) }}
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-10 col-md-10 col-sm-10">
                                    <h5 class="">{{ __('Storage Settings') }}</h5>
                                    <small class="text-dark font-weight-bold">{{__("Edit your Storage Settings")}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="pe-2">
                                    <input type="radio" class="btn-check" name="storage_setting" id="local-outlined" autocomplete="off" {{  $setting['storage_setting'] == 'local'?'checked':'' }} value="local" checked>
                                    <label class="btn  btn-outline-success" for="local-outlined">{{ __('Local') }}</label>
                                </div>
                                <div  class="pe-2">
                                    <input type="radio" class="btn-check" name="storage_setting" id="s3-outlined" autocomplete="off" {{  $setting['storage_setting']=='s3'?'checked':'' }}  value="s3">
                                    <label class="btn btn-outline-success" for="s3-outlined"> {{ __('AWS S3') }}</label>
                                </div>

                                <div  class="pe-2">
                                    <input type="radio" class="btn-check" name="storage_setting" id="wasabi-outlined" autocomplete="off" {{  $setting['storage_setting']=='wasabi'?'checked':'' }} value="wasabi">
                                    <label class="btn btn-outline-success" for="wasabi-outlined">{{ __('Wasabi') }}</label>
                                </div>
                            </div>
                            <div  class="mt-2">
                                <div class="local-setting row {{  $setting['storage_setting']=='local'?' ':'d-none' }}">
                                    {{-- <h4 class="small-title">{{ __('Local Settings') }}</h4> --}}
                                    <div class="form-group col-8 switch-width">
                                        {{Form::label('local_storage_validation',__('Only Upload Files'),array('class'=>' form-label')) }}
                                            <select name="local_storage_validation[]" class="form-control multi-select"  id="local_storage_validation"  multiple>
                                                @foreach($file_type as $f)
                                                    <option @if (in_array($f, $local_storage_validations)) selected @endif>{{$f}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="local_storage_max_upload_size">{{ __('Max upload size ( In KB)')}}</label>
                                            <input type="number" name="local_storage_max_upload_size" class="form-control" value="{{(!isset($setting['local_storage_max_upload_size']) || is_null($setting['local_storage_max_upload_size'])) ? '' : $setting['local_storage_max_upload_size']}}" placeholder="{{ __('Max upload size') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="s3-setting row {{  $setting['storage_setting']=='s3'?' ':'d-none' }}">

                                    <div class=" row ">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_key">{{ __('S3 Key') }}</label>
                                                <input type="text" name="s3_key" class="form-control" value="{{(!isset($setting['s3_key']) || is_null($setting['s3_key'])) ? '' : $setting['s3_key']}}" placeholder="{{ __('S3 Key') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_secret">{{ __('S3 Secret') }}</label>
                                                <input type="text" name="s3_secret" class="form-control" value="{{(!isset($setting['s3_secret']) || is_null($setting['s3_secret'])) ? '' : $setting['s3_secret']}}" placeholder="{{ __('S3 Secret') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_region">{{ __('S3 Region') }}</label>
                                                <input type="text" name="s3_region" class="form-control" value="{{(!isset($setting['s3_region']) || is_null($setting['s3_region'])) ? '' : $setting['s3_region']}}" placeholder="{{ __('S3 Region') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_bucket">{{ __('S3 Bucket') }}</label>
                                                <input type="text" name="s3_bucket" class="form-control" value="{{(!isset($setting['s3_bucket']) || is_null($setting['s3_bucket'])) ? '' : $setting['s3_bucket']}}" placeholder="{{ __('S3 Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_url">{{ __('S3 URL')}}</label>
                                                <input type="text" name="s3_url" class="form-control" value="{{(!isset($setting['s3_url']) || is_null($setting['s3_url'])) ? '' : $setting['s3_url']}}" placeholder="{{ __('S3 URL')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_endpoint">{{ __('S3 Endpoint')}}</label>
                                                <input type="text" name="s3_endpoint" class="form-control" value="{{(!isset($setting['s3_endpoint']) || is_null($setting['s3_endpoint'])) ? '' : $setting['s3_endpoint']}}" placeholder="{{ __('S3 Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="form-group col-8 switch-width">
                                            {{Form::label('s3_storage_validation',__('Only Upload Files'),array('class'=>' form-label')) }}
                                                <select name="s3_storage_validation[]" class="form-control multi-select" id="s3_storage_validation" multiple>
                                                    @foreach($file_type as $f)
                                                        <option @if (in_array($f, $s3_storage_validations)) selected @endif>{{$f}}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_max_upload_size">{{ __('Max upload size ( In KB)')}}</label>
                                                <input type="number" name="s3_max_upload_size" class="form-control" value="{{(!isset($setting['s3_max_upload_size']) || is_null($setting['s3_max_upload_size'])) ? '' : $setting['s3_max_upload_size']}}" placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="wasabi-setting row {{  $setting['storage_setting']=='wasabi'?' ':'d-none' }}">
                                    <div class=" row ">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_key">{{ __('Wasabi Key') }}</label>
                                                <input type="text" name="wasabi_key" class="form-control" value="{{(!isset($setting['wasabi_key']) || is_null($setting['wasabi_key'])) ? '' : $setting['wasabi_key']}}" placeholder="{{ __('Wasabi Key') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_secret">{{ __('Wasabi Secret') }}</label>
                                                <input type="text" name="wasabi_secret" class="form-control" value="{{(!isset($setting['wasabi_secret']) || is_null($setting['wasabi_secret'])) ? '' : $setting['wasabi_secret']}}" placeholder="{{ __('Wasabi Secret') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_region">{{ __('Wasabi Region') }}</label>
                                                <input type="text" name="wasabi_region" class="form-control" value="{{(!isset($setting['wasabi_region']) || is_null($setting['wasabi_region'])) ? '' : $setting['wasabi_region']}}" placeholder="{{ __('Wasabi Region') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="wasabi_bucket">{{ __('Wasabi Bucket') }}</label>
                                                <input type="text" name="wasabi_bucket" class="form-control" value="{{(!isset($setting['wasabi_bucket']) || is_null($setting['wasabi_bucket'])) ? '' : $setting['wasabi_bucket']}}" placeholder="{{ __('Wasabi Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="wasabi_url">{{ __('Wasabi URL')}}</label>
                                                <input type="text" name="wasabi_url" class="form-control" value="{{(!isset($setting['wasabi_url']) || is_null($setting['wasabi_url'])) ? '' : $setting['wasabi_url']}}" placeholder="{{ __('Wasabi URL')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="wasabi_root">{{ __('Wasabi Root')}}</label>
                                                <input type="text" name="wasabi_root" class="form-control" value="{{(!isset($setting['wasabi_root']) || is_null($setting['wasabi_root'])) ? '' : $setting['wasabi_root']}}" placeholder="{{ __('Wasabi Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="form-group col-8 switch-width">
                                            {{Form::label('wasabi_storage_validation',__('Only Upload Files'),array('class'=>'form-label')) }}

                                            <select name="wasabi_storage_validation[]" class="form-control multi-select" id="wasabi_storage_validation" multiple>
                                                @foreach($file_type as $f)
                                                    <option @if (in_array($f, $wasabi_storage_validations)) selected @endif>{{$f}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="wasabi_root">{{ __('Max upload size ( In KB)')}}</label>
                                                <input type="number" name="wasabi_max_upload_size" class="form-control" value="{{(!isset($setting['wasabi_max_upload_size']) || is_null($setting['wasabi_max_upload_size'])) ? '' : $setting['wasabi_max_upload_size']}}" placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
                            </div>
                        </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
