@php
    $logo=asset(Storage::url('logo/'));
    $company_favicon=Utility::getValByName('company_favicon');
@endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{(Utility::getValByName('title_text')) ? Utility::getValByName('title_text') : config('app.name', 'HRMGo')}}</title>
    <link rel="icon" href="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')}}" type="image" sizes="16x16">
    <!-- Landing External CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('landing/css/font-awesome.min.css') }}">
    <link href="{{ asset('landing/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('landing/css/style.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('landing/css/responsive.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('landing/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css" media="all">
    <script src="{{ asset('landing/js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('landing/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('landing/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('landing/js/script.js') }}"></script>
</head>
<body>
<div class="content">
    <div class="top-header-part bg-gredient">
        <div class="container">
            <div class="row top-bar">
                <div class="col-lg-6 col-md-6 left-part">
                    <ul>
                        <li>
                            <a href="#">
                                <img src="{{$logo.'/logo-light.png'}}" alt="logo" height="35px" width="auto">
                            </a>
                        </li>
                        <li>
                            <a href="#">Features</a>
                        </li>
                        <li>
                            <a href="#">Pricing</a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-6 right-part">
                    <ul>
                        <li>
                            <a href="{{ route('login') }}">Login</a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="button">Signup</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 inner-text">
                    <h2>LeadGo SaaS</h2>
                    <span class="sub-heading">Lead Management Tool</span>
                    <div class="body-text">
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                    </div>
                    <a href="#" class="button">get started - it's free</a>
                    <span>no creadit card reqired</span>
                </div>
                <div class="col-lg-12 top-banner-img">
                    <img src="{{ asset('landing/images/top-banner.png') }}" alt="dashboard">
                </div>
            </div>
        </div>
    </div>
    <div class="logo-part-main back-part">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 logo-img">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-3 ">
                                    <img src="{{ asset('landing/images/nexo.png') }}" alt="">
                                </div>
                                <div class="col-3 ">
                                    <img src="{{ asset('landing/images/edge.png') }}" alt="">
                                </div>
                                <div class="col-3">
                                    <img src="{{ asset('landing/images/atomic.png') }}" alt="">
                                </div>
                                <div class="col-3">
                                    <img src="{{ asset('landing/images/brd.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-3">
                                    <img src="{{ asset('landing/images/trust.png') }}" alt="">
                                </div>
                                <div class="col-3">
                                    <img src="{{ asset('landing/images/keep-key.png') }}" alt="">
                                </div>
                                <div class="col-3">
                                    <img src="{{ asset('landing/images/atomic.png') }}" alt="">
                                </div>
                                <div class="col-3">
                                    <img src="{{ asset('landing/images/edge.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="simple-sec odd main-image">
            <div class="responsive-image">
                <img src="{{ asset('landing/images/cal-sec.png') }}" alt="calander">
            </div>
            <div class="container">
                <div class="row com-padding">
                    <div class="col-lg-6 inner-text">
                        <div class="main-inner-text">
                            <span class="heading-btn">Features</span>
                            <h3>Lorem Ipsum is simply dummy</h3>
                            <span class="sub-heading">text of the printing</span>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting</p>
                            <a href="#" class="button">try our system</a>
                        </div>
                    </div>
                    <div class="col-lg-6 inner-image">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="simple-sec even bg-gredient1">
        <div class="responsive-image">
            <img src="{{ asset('landing/images/sec-2.png') }}" alt="calander">
        </div>
        <div class="container">
            <div class="row com-padding">
                <div class="col-lg-6 inner-text">
                    <div class="main-inner-text">
                        <span class="heading-btn">Features</span>
                        <h3>Lorem Ipsum is simply dummy</h3>
                        <span class="sub-heading">text of the printing</span>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting</p>
                        <a href="#" class="button">try our system</a>
                    </div>
                </div>
                <div class="col-lg-6 inner-image">
                </div>
            </div>
        </div>
    </div>
    <div class="simple-sec odd bg-gredient1">
        <div class="responsive-image">
            <img src="{{ asset('landing/images/sec-3.png') }}" alt="calander">
        </div>
        <div class="container">
            <div class="row com-padding">
                <div class="col-lg-6 inner-text">
                    <div class="main-inner-text">
                        <span class="heading-btn">Features</span>
                        <h3>Lorem Ipsum is simply dummy</h3>
                        <span class="sub-heading">text of the printing</span>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting</p>
                        <a href="#" class="button">try our system</a>
                    </div>
                </div>
                <div class="col-lg-6 inner-image">
                </div>
            </div>
        </div>
    </div>
    <div class="simple-sec even bg-gredient1">
        <div class="responsive-image">
            <img src="{{ asset('landing/images/sec-4.png') }}" alt="calander">
        </div>
        <div class="container">
            <div class="row com-padding">
                <div class="col-lg-6 inner-text">
                    <div class="main-inner-text">
                        <span class="heading-btn">Features</span>
                        <h3>Lorem Ipsum is simply dummy</h3>
                        <span class="sub-heading">text of the printing</span>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting</p>
                        <a href="#" class="button">try our system</a>
                    </div>
                </div>
                <div class="col-lg-6 inner-image">
                </div>
            </div>
        </div>
    </div>
    <div class="simple-sec odd bg-gredient1">
        <div class="responsive-image">
            <img src="{{ asset('landing/images/sec-5.png') }}" alt="calander">
        </div>
        <div class="container">
            <div class="row com-padding">
                <div class="col-lg-6 inner-text">
                    <div class="main-inner-text">
                        <span class="heading-btn">Features</span>
                        <h3>Lorem Ipsum is simply dummy</h3>
                        <span class="sub-heading">text of the printing</span>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting</p>
                        <a href="#" class="button">try our system</a>
                    </div>
                </div>
                <div class="col-lg-6 inner-image">
                </div>
            </div>
        </div>
    </div>
    <div class="simple-sec even bg-gredient1">
        <div class="responsive-image">
            <img src="{{ asset('landing/images/sec-6.png') }}" alt="calander">
        </div>
        <div class="container">
            <div class="row com-padding">
                <div class="col-lg-6 inner-text">
                    <div class="main-inner-text">
                        <span class="heading-btn">Features</span>
                        <h3>Lorem Ipsum is simply dummy</h3>
                        <span class="sub-heading">text of the printing</span>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting</p>
                        <a href="#" class="button">try our system</a>
                    </div>
                </div>
                <div class="col-lg-6 inner-image">
                </div>
            </div>
        </div>
    </div>
    <div class="features-inner-part">
        <div class="features-part">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="see-more">
                            <span>See more features</span>
                        </div>
                    </div>
                    <div class="col-lg-12 inner-main-text">
                        <h3>All Features <span>in one place</span></h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 features-card">
                        <div class="inner-text">
                            <h5>Attractive Dashboard
                                Customer & Vendor Login
                                Multi Languages
                            </h5>
                            <p>
                                Invoice, Billing & Transaction
                                Multi User & Permission
                                Paypal & Stripe for Invoice
                                User Friendly Invoice Theme
                                Make your own setting
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 features-card">
                        <div class="inner-text">
                            <p>Multi User & Permission
                                Paypal & Stripe for Invoice
                                User Friendly Invoice Theme
                                Make your own setting
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 features-card">
                        <div class="inner-text">
                            <p>Multi User & Permission
                                Paypal & Stripe for Invoice
                                User Friendly Invoice Theme
                                Make your own setting
                                User Friendly Invoice Theme
                                Make your own setting
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 features-card">
                        <div class="inner-text">
                            <p>Multi User & Permission
                                Paypal & Stripe for Invoice
                            </p>
                        </div>
                    </div>
                    <div class="features-button col-lg-12"><a href="#">TRY OUR SYSTEM</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-gredient2 our-system">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3>See our system <span> on images</span></h3>
                    <ul class="nav nav-tabs">
                        <li><a data-toggle="tab" href="#Dashboard" class="active">Dashboard</a></li>
                        <li><a data-toggle="tab" href="#Functions">Functions</a></li>
                        <li><a data-toggle="tab" href="#Reports"> Reports</a></li>
                        <li><a data-toggle="tab" href="#Tables"> Tables</a></li>
                        <li><a data-toggle="tab" href="#Settings"> Settings</a></li>
                        <li><a data-toggle="tab" href="#Contact"> Contact</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="Dashboard" class="tab-pane in active">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-1.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-2.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-3.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-1.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-2.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="Functions" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-1.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-2.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-3.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="Reports" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-1.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-2.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="Tables" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-1.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-2.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-3.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-1.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="Settings" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-1.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-2.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="Contact" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-md-6">
                                <div class="panal-1">
                                    <figure>
                                        <img alt="data-1" src="{{ asset('landing/images/tab-1.png') }}">
                                        <figcaption>
                                            <div class="contant-tab">
                                                <h5>Dashboard</h5>
                                                <p>Main Page</p>
                                            </div>
                                            <a href="#" class="button">LIVE DEMO</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-gredient2">
        <div class="container">
            <!-- TESTIMONIALS -->
            <section class="testimonials">
                <div class="container">
                    <h3>Testimonials</h3>
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="customers-testimonials" class="owl-carousel">
                                <div class="item">
                                    <div class="shadow-effect">
                                        <p>"We have been building AI projects for a long time and we decided it was time to build a
                                            platform that can streamline the broken process that we had to put up with. Here are some of the key things we wish we had when we were building projects before.”
                                        </p>
                                        <div class="img-testimonial">
                                            <img class="img-circle" src="{{ asset('landing/images/testimonials-img.png') }}" alt="">
                                            <div class="testimonial-name">
                                                <h4>Lorem Ipsum</h4>
                                                <h5>Founder and CEO at Rajodiya Infotech</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="shadow-effect">
                                        <p>"We have been building AI projects for a long time and we decided it was time to build a
                                            platform that can streamline the broken process that we had to put up with. Here are some of the key things we wish we had when we were building projects before.”
                                        </p>
                                        <div class="img-testimonial">
                                            <img class="img-circle" src="{{ asset('landing/images/testimonials-img.png') }}" alt="">
                                            <div class="testimonial-name">
                                                <h4>Lorem Ipsum</h4>
                                                <h5>Founder and CEO at Rajodiya Infotech</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="shadow-effect">
                                        <p>"We have been building AI projects for a long time and we decided it was time to build a
                                            platform that can streamline the broken process that we had to put up with. Here are some of the key things we wish we had when we were building projects before.”
                                        </p>
                                        <div class="img-testimonial">
                                            <img class="img-circle" src="{{ asset('landing/images/testimonials-img.png') }}" alt="">
                                            <div class="testimonial-name">
                                                <h4>Lorem Ipsum</h4>
                                                <h5>Founder and CEO at Rajodiya Infotech</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="shadow-effect">
                                        <p>"We have been building AI projects for a long time and we decided it was time to build a
                                            platform that can streamline the broken process that we had to put up with. Here are some of the key things we wish we had when we were building projects before.”
                                        </p>
                                        <div class="img-testimonial">
                                            <img class="img-circle" src="{{ asset('landing/images/testimonials-img.png') }}" alt="">
                                            <div class="testimonial-name">
                                                <h4>Lorem Ipsum</h4>
                                                <h5>Founder and CEO at Rajodiya Infotech</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="shadow-effect">
                                        <p>"We have been building AI projects for a long time and we decided it was time to build a
                                            platform that can streamline the broken process that we had to put up with. Here are some of the key things we wish we had when we were building projects before.”
                                        </p>
                                        <div class="img-testimonial">
                                            <img class="img-circle" src="{{ asset('landing/images/testimonials-img.png') }}" alt="">
                                            <div class="testimonial-name">
                                                <h4>Lorem Ipsum</h4>
                                                <h5>Founder and CEO at Rajodiya Infotech</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END OF TESTIMONIALS -->
        </div>
    </div>
    <section class="pricing-plan bg-gredient3">
        <div class="container our-system">
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-tabs">
                        <li>
                            <a data-toggle="tab" href="#monthly" class="active">{{__('Monthly')}}</a>
                        </li>
                        <li class="annual-billing">
                            <a data-toggle="tab" href="#annually">{{__('Annually')}}</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="monthly" class="tab-pane in active">
                        <div class="row">
                            @foreach ($plans as $key => $plan)
                                <div class="col-lg-3 col-md-4 col-sm-6 py-2">
                                    <div class="plan-2">
                                        <h6 class="text-center">{{ $plan->name }}</h6>
                                        <p class="price">
                                            <sup>{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}</sup>
                                            {{ number_format($plan->monthly_price) }}
                                            <sub>/ {{__('Month')}}</sub>
                                        </p>
                                        <p class="text-sm font-weight-bold">{{__('Trial : ').$plan->trial_days.__(' Days')}}</p>
                                        <ul class="plan-detail">
                                            <li>{{ ($plan->max_users < 0) ? __('Unlimited'):$plan->max_users }} {{__('Users')}}</li>
                                            <li>{{ ($plan->max_clients < 0) ? __('Unlimited'):$plan->max_clients }} {{__('Clients')}}</li>
                                            <li>{{ ($plan->max_deals < 0) ? __('Unlimited'):$plan->max_deals }} {{__('Deals')}}</li>
                                        </ul>
                                        <a href="{{ route('register') }}" class="button">Active</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="annually" class="tab-pane in">
                        <div class="row">
                            @foreach ($plans as $key => $plan)
                                <div class="col-lg-3 col-md-4 col-sm-6 py-2">
                                    <div class="plan-2">
                                        <h6 class="text-center">{{ $plan->name }}</h6>
                                        <p class="price">
                                            <sup>{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}</sup>
                                            {{ number_format($plan->annual_price) }}
                                            <sub>/ {{__('Annual')}}</sub>
                                        </p>
                                        <p class="text-sm font-weight-bold">{{__('Trial : ').$plan->trial_days.__(' Days')}}</p>
                                        <ul class="plan-detail">
                                            <li>{{ ($plan->max_users < 0) ? __('Unlimited'):$plan->max_users }} {{__('Users')}}</li>
                                            <li>{{ ($plan->max_clients < 0) ? __('Unlimited'):$plan->max_clients }} {{__('Clients')}}</li>
                                            <li>{{ ($plan->max_deals < 0) ? __('Unlimited'):$plan->max_deals }} {{__('Deals')}}</li>
                                        </ul>
                                        <a href="{{ route('register') }}" class="button">Active</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="subscribe-part">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <span class="top-heading">Try for free</span>
                <h3>Lorem Ipsum is simply dummy text</h3>
                <span class="sub-heading">of the printing and typesetting industry</span>
                <p>Type your email address and click the button</p>
                <form action="#">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Type your email address.." id="demo" name="email">
                        <button type="submit" class="btn btn-default">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="social-links">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-sm-6 inner-text">
                <div class="links">
                    <a href="#">Facebook</a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 inner-text">
                <div class="links">
                    <a href="#">LinkedIn</a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 inner-text">
                <div class="links">
                    <a href="#">Twitter</a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 inner-text">
                <div class="links">
                    <a href="#">Discord</a>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="bg-gredient4">
    <div class="container top-part-main">
        <div class="row">
            <div class="col-lg-3 top-part">
                <div class="first-sec">
                    <a href="#">
                        <img src="{{$logo.'/logo-light.png'}}" alt="logo" height="35px" width="auto">
                    </a>
                    <div class="copy-right">
                        ©
                        <script>document.write(new Date().getFullYear());</script>
                        All rights reserved.
                    </div>
                </div>
            </div>
            <div class="col-lg-3 top-part">
                <h3>FIO Protocol</h3>
                <ul>
                    <li><a href="#">Feature</a></li>
                    <li><a href="#">Download</a></li>
                    <li><a href="#">Integration</a></li>
                    <li><a href="#">Extras</a></li>
                </ul>
            </div>
            <div class="col-lg-3 top-part">
                <h3>Learn</h3>
                <ul>
                    <li><a href="#">Feature</a></li>
                    <li><a href="#">Download</a></li>
                    <li><a href="#">Integration</a></li>
                    <li><a href="#">Extras</a></li>
                </ul>
            </div>
            <div class="col-lg-3 top-part">
                <h3>Foundation</h3>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Customers</a></li>
                    <li><a href="#">Resources</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
            <div class="col-lg-3 top-part">
                <h3>Contact</h3>
                <ul>
                    <li><a href="#"><img src="{{ asset('landing/images/app-store.png') }}" alt="logo"></a></li>
                    <li><a href="#"><img src="{{ asset('landing/images/google-pay.png') }}" alt="logo"></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container bottom-part">
        <div class="row">
            <div class="col-lg-6 col-md-6 inner-text">
                <div class="copy-right">
                    ©
                    <script>document.write(new Date().getFullYear());</script>
                    All rights reserved.
                </div>
            </div>
            <div class="col-lg-6 col-md-6 inner-text">
                <ul>
                    <li>
                        <a href="#">Privacy Policy</a>
                    </li>
                    <li>
                        <a href="#">Github</a>
                    </li>
                    <li>
                        <a href="#">Press Kit</a>
                    </li>
                    <li>
                        <a href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
