@php
    $unseenCounter=App\Models\ChMessage::where('to_id', Auth::user()->id)->where('seen', 0)->count();
    $setting = App\Models\Utility::settings();
    $logo=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

{{-- <header class="dash-header transprent-bg"> --}}
  <header class="dash-header  {{ isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on' ? 'transprent-bg' : '' }}">
    <div class="header-wrapper"><div class="me-auto dash-mob-drp">
      <ul class="list-unstyled">
        <li class="dash-h-item mob-hamburger">
          <a href="#!" class="dash-head-link" id="mobile-collapse">
            <div class="hamburger hamburger--arrowturn">
              <div class="hamburger-box">
                <div class="hamburger-inner"></div>
              </div>
            </div>
          </a>
        </li>
        @if(\Auth::user()->type != 'Super Admin')

        <li class="dropdown dash-h-item">
          <a
            class="dash-head-link dropdown-toggle arrow-none ms-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            aria-expanded="false"
            data-action="omnisearch-open" data-target="#omnisearch"
          >
            <i class="ti ti-search"></i>
          </a>
          <div id="omnisearch" class="dropdown-menu dash-h-dropdown drp-search omnisearch drp-search-custom">
            <form class="px-3">
              <div class="form-group mb-0 d-flex align-items-center">
                <i data-feather="search"></i>
                <input
                  type="text"
                  class="form-control border-0 shadow-none search_keyword"
                  placeholder="Search here. . ."
                />
              </div>
              <div class="search-output">
              </div>
            </form>
          </div>
        </li>
         @endif
        <li class="dropdown dash-h-item drp-company">
          <a
            class="dash-head-link dropdown-toggle arrow-none me-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            aria-expanded="false"
          >
            <span class="theme-avtar">
                {{-- <img src="@if(Auth::user()->avatar) {{asset('/storage/avatars/'.Auth::user()->avatar)}} @else {{asset('custom/img/avatar/avatar-1.png')}} @endif" alt="user-image" class="user-avtar ms-2"/> --}}
                <img src="{{(!empty(\Auth::user()->avatar))?  \App\Models\Utility::get_file(\Auth::user()->avatar): $logo."avatar.png"}}" class="img-fluid rounded-circle">

            </span>
            <span class="hide-mob ms-2">{{__('Hi,')}}{{Auth::user()->name}}!</span>
            <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
          </a>
          <div class="dropdown-menu dash-h-dropdown">
            <a href="{{route('profile')}}" class="dropdown-item">
              <i class="ti ti-user"></i>
              <span>{{__('My Profile')}}</span>
            </a>
            <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
              <i class="ti ti-power"></i>
              <span>{{__('Logout')}}</span>
            </a>
             <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>
          </div>
        </li>
      </ul>
    </div>
    <div class="ms-auto">
      <ul class="list-unstyled">
        @if(Auth::user()->type != 'Super Admin' && Auth::user()->type != 'Client')
          <li class="dash-h-item">
            <a
              class="dash-head-link me-0"
              href="{{ url('chats') }}"
            >
              <i class="ti ti-message-2"></i>
              <span class="bg-danger dash-h-badge message-toggle-msg message-counter custom_messanger_counter "
                >{{$unseenCounter}}<span class="sr-only"></span
              ></span>
            </a>
          </li>
        @endif
        @if(\Auth::user()->type != 'Super Admin')
          <li class="dropdown dash-h-item drp-notification">
            <a
              class="dash-head-link dropdown-toggle arrow-none me-0"
              data-bs-toggle="dropdown"
              href="#"
              role="button"
              aria-haspopup="false"
              aria-expanded="false"
            >
             @php
                $notifications = \Auth::user()->notifications();
            @endphp
              <i class="ti ti-bell"></i>
              <span class="bg-danger dash-h-badge @if(count($notifications))dots @endif "
                ><span class="sr-only"></span
              ></span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
              <div class="noti-header">
                <h5 class="m-0">{{__('Notifications')}}</h5>
                <a href="#" class="dash-head-link mark_all_as_read">Clear All</a>
              </div>
              <div class="noti-body">


                @foreach($notifications as $notification)
                          {!! $notification->toHtml() !!}
                      @endforeach
              </div>
            </div>
          </li>
        @endif

        @php
          $currantLang = basename(\App::getLocale())
        @endphp
        <li class="dropdown dash-h-item drp-language">
          <a
            class="dash-head-link dropdown-toggle arrow-none me-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            aria-expanded="false"
          >
            <i class="ti ti-world nocolor"></i>
            <span class="drp-text hide-mob">{{Str::upper($currantLang)}}</span>
            <i class="ti ti-chevron-down drp-arrow nocolor"></i>
          </a>
          <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
            @foreach(Utility::languages() as $lang)
             <a href="{{route('lang.change',$lang)}}" class="dropdown-item {{(basename(App::getLocale()) == $lang) ? 'text-primary' : '' }}">
              <span>{{Str::upper($lang)}}</span>
            </a>
            @endforeach
            @can('Manage Languages')
                <div class="dropdown-divider m-0"></div>
                <a href="{{route('lang',basename(App::getLocale()))}}" class="dropdown-item text-primary">{{__('Manage Language')}}</a>
            @endcan
          </div>
        </li>
      </ul>
    </div>
    </div>
  </header>
