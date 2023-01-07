@php
    $logo=asset(Storage::url('logo/'));
    $dark_logo=Utility::getValByName('dark_logo');
    $favicon=Utility::getValByName('company_favicon');
    $color = 'theme-3';
    if (!empty($setting['color'])) {
        $color = $setting['color'];
    }
@endphp


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

    <!-- font css -->
    <link rel="stylesheet" href="{{asset('assets/fonts/tabler-icons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/feather.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/material.css')}}">

    <!-- vendor css -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" id="main-style-link">
    <link rel="stylesheet" href="{{asset('assets/css/customizer.css')}}">
  </head>

  <body class="{{$color}}">
    <!-- [ auth-signup ] start -->
    <div class="auth-wrapper auth-v1">
      <div class="auth-content">
        
        <div class="row align-items-center justify-content-center text-start">
          <div class="col-xl-6 text-center">
            <div class="mx-3 mx-md-5">
              <h2 class="mb-3 text-white f-w-600"><img src="{{$logo.'/'.(isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png')}}" alt="{{ config('app.name', 'LeadGo') }}" class="navbar-brand-img"></h2>
            </div>
            <div class="card">
              <div class="card-body mx-auto">
                 @if($form->is_active == 1)
                    <div class="page-title"><h5>{{$form->name}}</h5></div>
                    <form method="POST" action="{{ route('form.view.store') }}">
                        @csrf
                        @if($objFields && $objFields->count() > 0)
                            @foreach($objFields as $objField)
                                @if($objField->type == 'text')
                                    <div class="form-group">
                                        {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'col-form-label']) }}
                                        {{ Form::text('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                    </div>
                                @elseif($objField->type == 'email')
                                    <div class="form-group">
                                        {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'col-form-label']) }}
                                        {{ Form::email('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                    </div>
                                @elseif($objField->type == 'number')
                                    <div class="form-group">
                                        {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'col-form-label']) }}
                                        {{ Form::number('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                    </div>
                                @elseif($objField->type == 'date')
                                    <div class="form-group">
                                        {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'col-form-label']) }}
                                        {{ Form::date('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                    </div>
                                @elseif($objField->type == 'textarea')
                                    <div class="form-group">
                                        {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'col-form-label']) }}
                                        {{ Form::textarea('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                    </div>
                                @endif
                            @endforeach
                            <input type="hidden" value="{{$code}}" name="code">
                            <div class="d-grid">
                                <button class="btn btn-primary btn-block mt-2">{{__('Submit')}}</button>
                              </div>
                        @endif
                    </form>
                @else
                    <div class="page-title"><h5>{{__('Form is not active.')}}</h5></div>
                @endif
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ auth-signup ] end -->

    <!-- Required Js -->
    <script src="{{asset('assets/js/vendor-all.js')}}"></script>
    <script src="{{asset('assets/js/plugins/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/feather.min.js')}}"></script>
    <script>
      feather.replace();
    </script>
  </body>
</html>



