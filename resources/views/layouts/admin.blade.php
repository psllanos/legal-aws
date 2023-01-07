
@php
// $logo=asset(Storage::url('logo/'));
$logo=\App\Models\Utility::get_file('logo/');
$favicon=Utility::getValByName('company_favicon');
$color = Utility::getValByName('color');
$SITE_RTL = Utility::getValByName('SITE_RTL');
$setting = App\Models\Utility::colorset();
$color = 'theme-3';
if (!empty($setting['color'])) {
    $color = $setting['color'];
}

if(\Auth::user()->type=="Super Admin")
{
    $company_logo=Utility::get_superadmin_logo();
}
else
{
    $company_logo=Utility::get_company_logo();
}
$settings = App\Models\Utility::settings();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{$SITE_RTL == 'on'?'rtl':''}}">

<head>
<title>{{(Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'LeadGo')}} &dash; @yield('title')</title>

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
<link rel="stylesheet" href="{{asset('custom/css/custom.css') }}">



@if ($SITE_RTL == 'on')
    <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
@endif
 @if (isset($settings['cust_darklayout']) && $settings['cust_darklayout'] == 'on')
    <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
@else
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"id="main-style-link">
@endif

<style>
    [dir="rtl"] .dash-sidebar {
        left: auto !important;
    }
    [dir="rtl"] .dash-header {
        left: 0;
        right: 280px;
    }
    [dir="rtl"] .dash-header:not(.transprent-bg) .header-wrapper {
        padding: 0 0 0 30px;
    }
    [dir="rtl"] .dash-header:not(.transprent-bg):not(.dash-mob-header) ~ .dash-container {
        margin-left: 0px;
    }
    [dir="rtl"] .me-auto.dash-mob-drp {
        margin-right: 10px !important;
    }
    [dir="rtl"] .me-auto {
        margin-left: 10px !important;
    }
</style>

<meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">
<script src="{{ asset('js/chatify/autosize.js') }}"></script>
<script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>
<link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>

@stack('css-page')
</head>


<body class="{{!empty($color) ? $color : 'theme-3'}}">
<!-- [ Pre-loader ] start -->
<!-- [ Mobile header ] End -->

<!-- [ navigation menu ] start -->
@include('partials.admin.navbar')
<!-- [ navigation menu ] end -->
<!-- [ Header ] start -->
@include('partials.admin.topbar')

<!-- Modal -->
<div
class="modal notification-modal fade"
id="notification-modal"
tabindex="-1"
role="dialog"
aria-hidden="true"
>
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-body">
      <button
        type="button"
        class="btn-close float-end"
        data-bs-dismiss="modal"
        aria-label="Close"
      ></button>
      <h6 class="mt-2">
        <i data-feather="monitor" class="me-2"></i>Desktop settings
      </h6>
      <hr />
      <div class="form-check form-switch">
        <input
          type="checkbox"
          class="form-check-input"
          id="pcsetting1"
          checked
        />
        <label class="form-check-label f-w-600 pl-1" for="pcsetting1"
          >Allow desktop notification</label
        >
      </div>
      <p class="text-muted ms-5">
        you get lettest content at a time when data will updated
      </p>
      <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input" id="pcsetting2" />
        <label class="form-check-label f-w-600 pl-1" for="pcsetting2"
          >Store Cookie</label
        >
      </div>
      <h6 class="mb-0 mt-5">
        <i data-feather="save" class="me-2"></i>Application settings
      </h6>
      <hr />
      <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input" id="pcsetting3" />
        <label class="form-check-label f-w-600 pl-1" for="pcsetting3"
          >Backup Storage</label
        >
      </div>
      <p class="text-muted mb-4 ms-5">
        Automaticaly take backup as par schedule
      </p>
      <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input" id="pcsetting4" />
        <label class="form-check-label f-w-600 pl-1" for="pcsetting4"
          >Allow guest to print file</label
        >
      </div>
      <h6 class="mb-0 mt-5">
        <i data-feather="cpu" class="me-2"></i>System settings
      </h6>
      <hr />
      <div class="form-check form-switch">
        <input
          type="checkbox"
          class="form-check-input"
          id="pcsetting5"
          checked
        />
        <label class="form-check-label f-w-600 pl-1" for="pcsetting5"
          >View other user chat</label
        >
      </div>
      <p class="text-muted ms-5">Allow to show public user message</p>
    </div>
    <div class="modal-footer">
      <button
        type="button"
        class="btn btn-light-danger btn-sm"
        data-bs-dismiss="modal"
      >
        Close
      </button>
      <button type="button" class="btn btn-light-primary btn-sm">
        Save changes
      </button>
    </div>
  </div>
</div>
</div>
<!-- [ Header ] end -->
</body>

<!-- [ Main Content ] start -->
<div class="dash-container">
<div class="dash-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
          <div class="page-block">
              <div class="row align-items-center">
                  <div class="col-md-12">
                      <div class="d-block d-sm-flex align-items-center justify-content-between">
                          <div>
                              <div class="page-header-title">
                                  <h4 class="m-b-10">@yield('title')</h4>
                              </div>
                              @if(Request::route()->getname() != 'home')
                              <ul class="breadcrumb">
                                  <li class="breadcrumb-item" ><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                                      @yield('breadcrumb')
                              </ul>
                              @endif
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

<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="body ">
            </div>
        </div>
    </div>
</div>

<input type="checkbox" class="d-none" id="cust-theme-bg"  {{ Utility::getValByName('cust_theme_bg') == 'on' ? 'checked' : '' }} />
<input type="checkbox" class="d-none" id="cust-darklayout" {{ Utility::getValByName('cust_darklayout') == 'on' ? 'checked' : '' }} />

<!-- [ Main Content ] end -->
@include('partials.admin.footer')

<script src="{{asset('custom/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/popper.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/feather.min.js')}}"></script>
<script src="{{asset('assets/js/dash.js')}}"></script>
<script src="{{asset('assets/js/plugins/simple-datatables.js')}}"></script>
<script src="{{asset('assets/js/plugins/main.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>
<script src="{{asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{asset('assets/js/plugins/dropzone-amd-module.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootstrap-switch-button.min.js')}}"></script>


{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> --}}
{{-- <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/25686/jSignature.min.js"></script> --}}

<!-- Time picker -->
<script src="{{asset('assets/js/plugins/flatpickr.min.js')}}"></script>
<!-- datepicker -->
<script src="{{asset('assets/js/plugins/datepicker-full.min.js')}}"></script>


<script src="{{asset('custom/js/jquery.form.js')}}"></script>


<script>

$(document).ready(function() {
        cust_theme_bg();
        cust_darklayout();

        @if(Auth::user()->type != 'Super Admin')
          $(document).on('keyup', '.search_keyword', function () {

              search_data($(this).val());
          });

          if ($(".top-5-scroll").length) {
              $(".top-5-scroll").css({
                  height: 315
              }).niceScroll();
          }
          @endif

          @if(Auth::user()->type != 'Super Admin')
            // Common main search
            var currentRequest = null;

            function search_data(keyword = '') {

                currentRequest = $.ajax({
                    url: '{{ route('search.json') }}',
                    data: {keyword: keyword},
                    beforeSend: function () {
                        if (currentRequest != null) {
                            currentRequest.abort();
                        }
                    },
                    success: function (data) {
                        $('.search-output').html(data);
                    }
                });
            }
            @endif



    });



    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function() {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }

    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];

        c.addEventListener("click", function(event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }

    function cust_theme_bg() {
        var custthemebg = document.querySelector("#cust-theme-bg");
        // custthemebg.addEventListener("click", function() {

        if (custthemebg.checked) {
            document.querySelector(".dash-sidebar").classList.add("transprent-bg");
            document
                .querySelector(".dash-header:not(.dash-mob-header)")
                .classList.add("transprent-bg");
        } else {
            document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
            document
                .querySelector(".dash-header:not(.dash-mob-header)")
                .classList.remove("transprent-bg");
        }
        // });
    }
    var custthemebg = document.querySelector("#cust-theme-bg");
    custthemebg.addEventListener("click", function() {
        if (custthemebg.checked) {
            document.querySelector(".dash-sidebar").classList.add("transprent-bg");
            document
                .querySelector(".dash-header:not(.dash-mob-header)")
                .classList.add("transprent-bg");
        } else {
            document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
            document
                .querySelector(".dash-header:not(.dash-mob-header)")
                .classList.remove("transprent-bg");
        }
    });


    function cust_darklayout() {
        var custdarklayout = document.querySelector("#cust-darklayout");
        // custdarklayout.addEventListener("click", function() {
      @if(\Auth::user()->type=="Super Admin")
        if (custdarklayout.checked) {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "{{$logo.'/'.'logo-light.png'}}");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
        } else {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "{{$logo.'/'.'logo-dark.png'}}");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "{{ asset('assets/css/style.css') }}");
        }

      @else
        if (custdarklayout.checked) {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "{{$logo.'/'.($company_logo)}}");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
        } else {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "{{$logo.'/'.($company_logo)}}");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "{{ asset('assets/css/style.css') }}");
        }

      @endif
    }

    var custdarklayout = document.querySelector("#cust-darklayout");
    custdarklayout.addEventListener("click", function() {
        @if(\Auth::user()->type=="Super Admin")
        if (custdarklayout.checked) {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "{{$logo.'/'.'logo-light.png'}}");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
        } else {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "{{$logo.'/'.'logo-dark.png'}}");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "{{ asset('assets/css/style.css') }}");
        }

      @else
        if (custdarklayout.checked) {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "{{$logo.'/'.($company_logo)}}");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
        } else {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "{{$logo.'/'.($company_logo)}}");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "{{ asset('assets/css/style.css') }}");
        }

      @endif
    });

    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }

</script>

@push('script')
@include('Chatify::layouts.footerLinks')
@endpush
@if(Utility::getValByName('gdpr_cookie') == 'on')
<script type="text/javascript">
    var defaults = {
        'messageLocales': {
            /*'en': 'We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.'*/
            'en': "{{Utility::getValByName('cookie_text')}}"
        },
        'buttonLocales': {
            'en': 'Ok'
        },
        'cookieNoticePosition': 'bottom',
        'learnMoreLinkEnabled': false,
        'learnMoreLinkHref': '/cookie-banner-information.html',
        'learnMoreLinkText': {
            'it': 'Saperne di più',
            'en': 'Learn more',
            'de': 'Mehr erfahren',
            'fr': 'En savoir plus'
        },
        'buttonLocales': {
            'en': 'Ok'
        },
        'expiresIn': 30,
        'buttonBgColor': '#d35400',
        'buttonTextColor': '#fff',
        'noticeBgColor': '#000000',
        'noticeTextColor': '#fff',
        'linkColor': '#009fdd'
    };
</script>
<script src="{{ asset('custom/js/cookie.notice.js')}}"></script>
@endif
{{-- Pusher JS--}}
@if(\Auth::user()->type != 'Super Admin')
<script src="https://js.pusher.com/5.0/pusher.min.js"></script>
<script>
    $(document).ready(function () {
        pushNotification('{{ Auth::id() }}');
    });

    function pushNotification(id) {
        // ajax setup form csrf token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = false;

        var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
            cluster: '{{env('PUSHER_APP_CLUSTER')}}',
            forceTLS: true
        });

        // Pusher Notification
        var channel = pusher.subscribe('send_notification');
        channel.bind('notification', function (data) {
            if (id == data.user_id) {
                $(".notification-toggle").addClass('dots');
                $(".notification-dropdown #notification-list").prepend(data.html);
                $(".notification-dropdown #notification-list-mini").prepend(data.html);
            }
        });

        // Pusher Message
        var msgChannel = pusher.subscribe('my-channel');
        msgChannel.bind('my-chat', function (data) {
            if (id == data.to) {
                getChat();
            }
        });
    }

    // Mark As Read Notification
    $(document).on("click", ".mark_all_as_read", function () {
        $.ajax({
            url: '{{route('notification.seen',\Auth::user()->id)}}',
            type: "get",
            cache: false,
            success: function (data) {
                $('.notification-dropdown #notification-list').html('');
                $(".notification-toggle").removeClass('dots');
                $('.notification-dropdown #notification-list-mini').html('');
            }
        })
    });

    // Get chat for top ox
    function getChat() {
        $.ajax({
            url: '{{route('message.data')}}',
            type: "get",
            cache: false,
            success: function (data) {
                if (data.length != 0) {
                    $(".message-toggle-msg").addClass('beep');
                    $(".dropdown-list-message-msg").html(data);
                }
            }
        })
    }

    getChat();

    $(document).on("click", ".mark_all_as_read_message", function () {
        $.ajax({
            url: '{{route('message.seen')}}',
            type: "get",
            cache: false,
            success: function (data) {
                $('.dropdown-list-message-msg').html('');
                $(".message-toggle-msg").removeClass('beep');
            }
        })
    });
</script>


@endif

<script src="{{asset('custom/js/custom.js')}}"></script>
<script>
var toster_pos="{{$SITE_RTL =='on' ?'left' : 'right'}}";
</script>


@if ($message = Session::get('success'))
<script>show_toastr('Success', '{!! $message !!}', 'success')</script>
@endif

@if ($message = Session::get('error'))
<script>show_toastr('Error', '{!! $message !!}', 'error')</script>
@endif

@if ($message = Session::get('info'))
<script>show_toastr('Info', '{!! $message !!}', 'info')</script>
@endif



@stack('script')

<script>
a = document.getElementById('toastPlacement');
a && document.getElementById('selectToastPlacement').addEventListener('change', function () {
a.dataset.originalClass || (a.dataset.originalClass = a.className),
  a.className = a.dataset.originalClass + ' ' + this.value
});


d = document.getElementById('liveToastBtn'),
f = document.getElementById('liveToast'),
d && d.addEventListener('click', function () {
  var a = new bootstrap.Toast(f);
  a.show()
});
</script>
</body>

</html>
