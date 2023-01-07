@php
    // $logo=asset(Storage::url('logo/'));
    $logo=\App\Models\Utility::get_file('logo/');

    $favicon=Utility::getValByName('company_favicon');
    $SITE_RTL = env('SITE_RTL');
    $color = 'theme-3';
    if (!empty($setting['color'])) {
        $color = $setting['color'];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{$SITE_RTL == 'on'?'rtl':''}}">

<head>
    <title>{{(Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'LeadGo')}} &dash; @yield('title')</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"
    />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Rajodiya Infotech" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" href="{{$logo.'/'.(isset($favicon) && !empty($favicon)?$favicon:'favicon.png')}}" type="image/x-icon" />
     @stack('head')
     <link rel="stylesheet" href="{{asset('assets/css/plugins/bootstrap-switch-button.min.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/flatpickr.min.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/dragula.min.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/style.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/main.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/datepicker-bs5.min.css')}}">
    <!-- font css -->
    <link rel="stylesheet" href="{{asset('assets/fonts/tabler-icons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/feather.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome.css')}}">
    <link rel="stylesheet" href="{{ asset('custom/libs/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/fonts/material.css')}}">
        <link rel="stylesheet" href="{{asset('assets/css/plugins/animate.min.css')}}">

    <!-- vendor css -->

    <link rel="stylesheet" href="{{asset('assets/css/customizer.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/plugins/dropzone.min.css')}}">
    <link rel="stylesheet" href="{{ asset('custom/css/custom.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/landing.css')}}" />

    @if($SITE_RTL=='on')

        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
        @else
        <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" id="main-style-link">
    @endif

</head>
<body class="{{$color}}">
  <!-- [ Pre-loader ] start -->
  <!-- [ Mobile header ] End -->



<!-- [ Main Content ] start -->
<div class="container">
    <div class="dash-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
              <div class="page-block">
                  <div class="row align-items-center">
                      <div class="col-md-12 mt-5 mb-4">
                          <div class="d-block d-sm-flex align-items-center justify-content-between">
                              <div>
                                  <div class="page-header-title">
                                      <h4 class="m-b-10">@yield('title')</h4>
                                  </div>
                                  <ul class="breadcrumb">
                                      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                                          @yield('breadcrumb')
                                  </ul>

                                 <!--  <ul class="breadcrumb">
                                      <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                      <li class="breadcrumb-item">General Statistics</li>
                                  </ul> -->
                              </div>
                              <div>
                                @yield('action-button')
                              </div>

                          </div>
                      </div>
                  </div>
              </div>
          </div>

        <!-- <div class="row"> -->
               @yield('content')

        <!-- </div> -->

    </div>
</div>



<script src="{{asset('custom/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/popper.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/feather.min.js')}}"></script>
<script src="{{asset('assets/js/dash.js')}}"></script>
<script src="{{asset('assets/js/plugins/apexcharts.min.js')}}"></script>
<script src="{{ asset('assets/js/plugins/simple-datatables.js')}}"></script>
<script src="{{asset('assets/js/plugins/main.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>
<script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{asset('assets/js/plugins/dropzone-amd-module.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootstrap-switch-button.min.js')}}"></script>
<!-- <script src="{{ asset('assets/js/pages/ac-alert.js') }}"></script> -->
<!-- Time picker -->
<script src="{{asset('assets/js/plugins/flatpickr.min.js')}}"></script>
<!-- datepicker -->
<script src="{{asset('assets/js/plugins/datepicker-full.min.js')}}"></script>

<!-- <script src="{{asset('assets/js/pages/ac-datepicker.js')}}"></script> -->


<script src="{{asset('custom/js/jquery.form.js')}}"></script>

@stack('script')

<!-- <script>
  if ($(".pc-dt-simple").length) {
    const dataTable = new simpleDatatables.DataTable(".pc-dt-simple");
  }
</script> -->



</body>

</html>
