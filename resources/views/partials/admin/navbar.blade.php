@php
    $logo=asset(Storage::url('logo/'));
    $company_logo=Utility::get_superadmin_logo();
    $setting = App\Models\Utility::settings();
@endphp


{{-- <nav class="dash-sidebar light-sidebar transprent-bg"> --}}
    <nav class="dash-sidebar light-sidebar {{ isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on' ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
      <div class="m-header main-logo">
        <a href="{{route('home')}}" class="b-brand">
          <!-- ========   change your logo hear   ============ -->
          <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo-dark.png')}}" alt="{{ config('app.name', 'LeadGo') }}"  class="logo logo-lg">
          
        </a>
      </div>
      <div class="navbar-content">
        <ul class="dash-navbar">
            <li class="dash-item  {{ (Request::route()->getName() == 'home') ? 'active' : '' }}">
                <a href="{{route('home')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-home"></i></span><span class="dash-mtext">{{__('Dashboard')}}</span></a>
            </li>

            @if(Gate::check('Manage Users') || Gate::check('Manage Clients') || Gate::check('Manage Roles') || Gate::check('Manage Permissions'))
                @can('Manage Users')
                    <li class="dash-item  {{ request()->is('users*') ? 'active' : '' }}">
                        <a href="{{route('users')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-users"></i></span><span class="dash-mtext">{{__('Users')}}</span></a>
                    </li>
                @endcan
                @can('Manage Clients')
                    <li class="dash-item {{ request()->is('clients*') ? 'active' : '' }}">
                        <a href="{{route('clients.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-user"></i></span><span class="dash-mtext">{{__('Clients')}}</span></a>
                    </li>
                @endcan
                @can('Manage Roles')
                    <li class="dash-item  {{ (Request::route()->getName() == 'roles.index') ? 'active' : '' }}">
                        <a href="{{route('roles.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-user-x"></i></span><span class="dash-mtext">{{__('Roles')}}</span></a>
                    </li>
                @endcan
            @endif

            @can('Manage Leads')
                <li class="dash-item  {{ request()->is('leads*') ? 'active' : '' }}">
                    <a href="{{route('leads.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-3d-cube-sphere"></i></span><span class="dash-mtext">{{__('Leads')}}</span></a>
                </li>
            @endcan

            @can('Manage Deals')
                <li class="dash-item  {{ request()->is('deals*') ? 'active' : '' }}">
                    <a href="{{route('deals.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-rocket"></i></span><span class="dash-mtext">{{__('Deals')}}</span></a>
                </li>
            @endcan

            @if(Gate::check('Manage Products'))
                @can('Manage Products')
                    <li class="dash-item {{ (Request::route()->getName() == 'products.index') ? 'active' : '' }}">
                        <a href="{{route('products.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-plane-departure"></i></span><span class="dash-mtext">{{__('Products & Services')}}</span></a>
                    </li>
                @endcan
            @endif

            @can('Manage Estimations')
                <li class="dash-item {{ (Request::route()->getName() == 'estimations.index' || Request::route()->getName() == 'estimations.show') ? 'active' : '' }}">
                    <a href="{{route('estimations.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-shopping-cart-plus"></i></span><span class="dash-mtext">{{__('Estimations')}}</span></a>
                </li>
            @endcan


            @if(Gate::check('Manage Expenses') || Gate::check('Manage Invoices'))
                 @can('Manage Invoices')
                    <li class="dash-item {{ (Request::route()->getName() == 'invoices.index' || Request::route()->getName() == 'invoices.show') ? 'active' : '' }}">
                        <a href="{{route('invoices.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-file-invoice"></i></span><span class="dash-mtext">{{__('Invoices')}}</span></a>
                    </li>
                 @endcan
                 @can('Manage Expenses')
                    <li class="dash-item {{ (Request::route()->getName() == 'expenses.index' || Request::route()->getName() == 'expenses.show') ? 'active' : '' }}">
                        <a href="{{route('expenses.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-cash"></i></span><span class="dash-mtext">{{__('Expenses')}}</span></a>
                    </li>
                @endcan
            @endif

            @can('Manage Invoice Payments')
                <li class="dash-item {{ (Request::route()->getName() == 'invoices.payments') ? 'active' : '' }}">
                    <a href="{{route('invoices.payments')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-report-money"></i></span><span class="dash-mtext">{{__('Payments')}}</span></a>
                </li>
            @endcan

            @if(Auth::user()->type == 'Owner')
                <li class="dash-item {{ request()->is('form_builder*') || request()->is('form_response*') ? 'active' : '' }}">
                    <a href="{{route('form_builder.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-notebook"></i></span><span class="dash-mtext">{{__('Form Builder')}}</span></a>
                </li>
            @endif

            @if(Gate::check('Manage MDFs') && \App\Models\Utility::checkPermissionExist('Manage MDFs') != 0)
                <li class="dash-item {{ (Request::route()->getName() == 'mdf.index' || Request::route()->getName() == 'mdf.show') ? 'active' : '' }}">
                    <a href="{{route('mdf.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-cash-banknote"></i></span><span class="dash-mtext">{{__('MDF')}}</span></a>
                </li>
            @endif

            {{-- @if(Auth::user()->type != 'Super Admin' && Auth::user()->type != 'Client' && env('CHAT_MODULE') == 'yes') --}}
            @if(Auth::user()->type != 'Super Admin' && Auth::user()->type != 'Client')
                <li class="dash-item {{ (Request::route()->getName() == 'chats') ? 'active' : '' }}">
                    <a href="{{url('chats')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-messages"></i></span><span class="dash-mtext">{{__('Messenger')}}</span></a>
                </li>
            @endif

            @if(\Auth::user()->type=='Owner' || \Auth::user()->type=='Client')
            {{-- @can('Manage Contracts') --}}
            <li class="dash-item {{ (Request::route()->getName() == 'contract.index' || Request::route()->getName() == 'contract.show') ? 'active' : '' }}">
                <a href="{{route('contract.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-device-floppy"></i></span><span class="dash-mtext">{{__('Contracts')}}</span></a>
            </li>
            {{-- @endcan --}}
            @endif  


            @if(Auth::user()->type != 'Super Admin')
                <li class="dash-item {{ request()->is('zoommeeting*') ? 'active' : '' }}">
                    <a href="{{route('zoommeeting.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-video"></i></span><span class="dash-mtext">{{__('Zoom Meeting')}}</span></a>
                </li>
            @endif

            @if(Gate::check('Manage Email Templates'))
                <li class="dash-item {{ (Request::route()->getName() == 'email_template.index' || Request::segment(1) == 'email_template_lang'  || Request::route()->getName() == 'manageemail.lang') ? 'active' : '' }}">
                    <a href="{{route('email_template.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-mail"></i></span><span class="dash-mtext">{{__('Email Templates')}}</span></a>
                </li>
            @endif
                
            @if(Gate::check('Manage Coupons'))
                <li class="dash-item {{ (Request::route()->getName() == 'coupons.index' || Request::route()->getName() == 'coupons.show') ? 'active' : '' }}">
                    <a href="{{route('coupons.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-gift"></i></span><span class="dash-mtext">{{__('Coupons')}}</span></a>
                </li>
            @endif

            @if(Gate::check('System Settings') || Gate::check('Manage Pipelines') || Gate::check('Manage Sources') || Gate::check('Manage Payments') || Gate::check('Manage Expense Categories') || Gate::check('Manage Stages') || Gate::check('Manage Labels') || Gate::check('Manage Custom Fields') || Gate::check('Manage Contract Types') || Gate::check('Manage Email Templates'))
                @if(Gate::check('Manage Pipelines'))
                    <li class="dash-item dash-hasmenu {{ (Request::route()->getName() == 'pipelines.index' || Request::route()->getName() == 'sources.index' || Request::route()->getName() == 'payments.index' || Request::route()->getName() == 'expense_categories.index' || Request::route()->getName() == 'stages.index' || Request::route()->getName() == 'labels.index' || Request::route()->getName() == 'custom_fields.index'  || Request::route()->getName() == 'contract_type.index'  || Request::route()->getName() == 'lead_stages.index' || Request::route()->getName()
                    == 'email_template.index' || Request::route()->getName() == 'mdf_status.index' || Request::route()->getName() == 'mdf_type.index' || Request::route()->getName() == 'mdf_sub_type.index') ? '' : '' }} ">
                    <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-layout-2"></i></span><span
                            class="dash-mtext">{{ __('Setup')}}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu {{ (Request::route()->getName() == 'pipelines.index' || Request::route()->getName() == 'sources.index' || Request::route()->getName() == 'payments.index' || Request::route()->getName() == 'expense_categories.index' || Request::route()->getName() == 'stages.index' || Request::route()->getName() == 'labels.index' || Request::route()->getName() == 'custom_fields.index'  || Request::route()->getName() == 'contract_type.index'  || Request::route()->getName() == 'lead_stages.index' || Request::route()->getName()
                    == 'email_template.index' || Request::route()->getName() == 'mdf_status.index' || Request::route()->getName() == 'mdf_type.index' || Request::route()->getName() == 'mdf_sub_type.index') ? 'show' : '' }}">
                        @can('Manage Pipelines')
                            <li class="dash-item {{ (Request::route()->getName() == 'pipelines.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('pipelines.index')}}">{{__('Pipelines')}}</a>
                            </li>
                        @endcan
                        @can('Manage Stages')
                            <li class="dash-item {{ (Request::route()->getName() == 'stages.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('stages.index')}}">{{__('Deal Stages')}}</a>
                            </li>
                        @endcan
                        @can('Manage Lead Stages')
                            <li class="dash-item {{ (Request::route()->getName() == 'lead_stages.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('lead_stages.index')}}">{{__('Lead Stages')}}</a>
                            </li>
                        @endcan
                        @can('Manage Labels')
                            <li class="dash-item {{ (Request::route()->getName() == 'labels.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('labels.index')}}">{{__('Labels')}}</a>
                            </li>
                        @endcan
                        @can('Manage Sources')
                            <li class="dash-item {{ (Request::route()->getName() == 'sources.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('sources.index')}}">{{__('Sources')}}</a>
                            </li>
                        @endcan
                        @can('Manage Payments')
                            <li class="dash-item {{ (Request::route()->getName() == 'payments.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('payments.index')}}">{{__('Payment Methods')}}</a>
                            </li>
                        @endcan
                        @can('Manage Expense Categories')
                            <li class="dash-item {{ (Request::route()->getName() == 'expense_categories.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('expense_categories.index')}}">{{__('Expense Categories')}}</a>
                            </li>
                        @endcan
                        @can('Manage Contract Types')
                            <li class="dash-item {{ (Request::route()->getName() == 'contract_type.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('contract_type.index')}}">{{__('Contract Type')}}</a>
                            </li>
                        @endcan
                        @can('Manage Taxes')
                            <li class="dash-item {{ (Request::route()->getName() == 'taxes.index') ? 'active' : '' }}">
                                <a href="{{route('taxes.index')}}" class="dash-link">{{__('Tax Rates')}}</a>
                            </li>
                        @endcan
                        @can('Manage Custom Fields')
                            <li class="dash-item {{ (Request::route()->getName() == 'custom_fields.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('custom_fields.index')}}">{{__('Custom Fields')}}</a>
                            </li>
                        @endcan
                        @can('Manage MDF Status')
                            <li class="dash-item {{ (Request::route()->getName() == 'mdf_status.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('mdf_status.index')}}">{{__('MDF Status')}}</a>
                            </li>
                        @endcan
                        @can('Manage MDF Types')
                            <li class="dash-item {{ (Request::route()->getName() == 'mdf_type.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('mdf_type.index')}}">{{__('MDF Type')}}</a>
                            </li>
                        @endcan
                        @can('Manage MDF Sub Types')
                            <li class="dash-item {{ (Request::route()->getName() == 'mdf_sub_type.index') ? 'active' : '' }}">
                                <a class="dash-link" href="{{route('mdf_sub_type.index')}}">{{__('MDF Sub Type')}}</a>
                            </li>
                        @endcan
                    </ul>
                  </li>
                @endif
            @endif
                  
            @can('System Settings')
                <li class="dash-item {{ (Request::route()->getName() == 'settings') ? 'active' : '' }}">
                    <a href="{{route('settings')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-settings"></i></span><span class="dash-mtext">{{__('System Settings')}}</span></a>
                </li>
            @endcan
                
        </ul>
    </div>

          
        </ul>
        <div class="card bg-primary">
          <div class="card-body">
            <h2 class="text-white">Need help?</h2>
            <p class="text-white"><i>Please check our docs.</i></p>
            <div class="d-grid">
              <button class="btn btn-light">Documentation</button>
            </div>
            <img
              src="{{asset('assets/images/sidebar-card.svg')}}"
              alt=""
              class="img-sidebar-card"
            />
          </div>
        </div>
      </div>
    </div>
  </nav>
