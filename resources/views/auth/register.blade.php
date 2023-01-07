@extends('layouts.auth')

@section('title')
    {{ __('Register') }}
@endsection

@section('language-bar')
    <div class="btn-group align-items-center">
        <select name="language" id="language" class="btn  btn-primary dropdown-toggle" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
            @foreach(Utility::languages() as $language)
                <option class="login_lang" @if($lang == $language) selected @endif value="{{ route('register',$language) }}">{{Str::upper($language)}}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('content')
    <div class="col-xl-6">

        <div class="card-body">
            <div class="">
                <h2 class="mb-3 f-w-600">{{__('Register')}}</h2>
            </div>
            <form method="POST" action="{{ route('register') }}" class="needs-validation" validate="">
            @csrf
                <div class="">
                    <div class="form-group mb-3">
                        <label class="form-label d-flex">{{__('Username')}}</label>
                        <input id="name" type="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter Your Name">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label d-flex">{{__('Email address')}}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter Your Email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label d-flex">{{__('Password')}}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter Your Password">
                         @error('password')
                        <span class="invalid-feedback" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label d-flex">{{__('Confirm Password')}}</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Your Password">
                         @error('password')
                        <span class="invalid-feedback" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                        @enderror
                    </div>
                    @if(env('RECAPTCHA_MODULE') == 'yes')
                        <div class="form-group mb-3">
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                            <span class="small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    @endif

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block mt-2" id="login_button">{{__('Register')}}</button>
                    </div>


                    <div class="my-4 text-xs text-muted text-left">
                        <p>
                            {{__("Already have an account?")}} <a href="{{route('login',$lang)}}">{{__('Login')}}</a>
                        </p>

                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection
@push('custom-scripts')
@if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
@endif
@endpush
