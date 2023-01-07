@extends('layouts.admin')
@section('title')
    {{ __('Email Templates') }}
@endsection

@push('head')
    <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}">
@endpush

@push('script')
    <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script>
@endpush

@section('breadcrumb') 
        <li class="breadcrumb-item active" aria-current="page">{{__('Email Templates')}}</li>
@endsection


@section('action-button')
        <div class="text-end mb-3">
            <div class="d-flex justify-content-end drp-languages">
                <ul class="list-unstyled mb-0 m-2">
                    <li class="dropdown dash-h-item drp-language" style="list-style-type: none;">
                        <a
                        class="dash-head-link dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false"
                        >
                        <span class="drp-text hide-mob text-primary">{{Str::upper($currEmailTempLang->lang )}}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @foreach(Utility::languages() as $lang)
                      
                        <a href="{{route('manageemail.lang',[$emailTemplate->id,$lang])}}" class="dropdown-item {{($currEmailTempLang->lang  == $lang) ? 'text-primary' : '' }}">
                            <span>{{Str::upper($lang)}}</span>
                        </a>
                        @endforeach
                    
                        </div>
                    </li>
                </ul>    
                <ul class="list-unstyled mb-0 m-2">
                    <li class="dropdown dash-h-item drp-language" style="list-style-type: none;">
                        <a class="dash-head-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="drp-text hide-mob text-primary">{{ __('Template: ') }} {{ $emailTemplate->name }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                            @foreach ($EmailTemplates as $EmailTemplate)
                        <a href="{{ route('manageemail.lang',[$EmailTemplate->id,(Request::segment(3)?Request::segment(3):\Cookie::get('LANGUAGE'))]) }}" class="dropdown-item">
                            <span>{{ $EmailTemplate->name }}</span>
                        </a>
                        @endforeach
                    
                        </div>
                    </li>
                </ul>
            </div>
        </div>
@endsection


@section('content')
<div class="row">

    <div class="col-12">
        <div class="row">
        </div>
        <div class="card">
            <div class="card-body">
                <div class="language-wrap">
                    <div class="row"> 
                        <h6>{{ __('Place Holders') }}</h6>
                        <div class="col-lg-12 col-md-9 col-sm-12 language-form-wrap">

                            <div class="card">
                                <div class="card-header card-body">
                                    <div class="row text-xs">
                                        @if($emailTemplate->name == 'Assign Deal' || $emailTemplate->name == 'Move Deal')
                                        <div class="row">
                                            {{-- <h6 class="font-weight-bold">{{__('Deal')}}</h6> --}}
                                            <p class="col-4">{{__('Deal Name')}} : <span class="pull-right text-primary">{deal_name}</span></p>
                                            <p class="col-4">{{__('Deal Pipeline')}} : <span class="pull-right text-primary">{deal_pipeline}</span></p>
                                            <p class="col-4">{{__('Deal Stage')}} : <span class="pull-right text-primary">{deal_stage}</span></p>
                                            <p class="col-4">{{__('Deal Status')}} : <span class="pull-right text-primary">{deal_status}</span></p>
                                            <p class="col-4">{{__('Deal Price')}} : <span class="pull-right text-primary">{deal_price}</span></p>
                                            <p class="col-4">{{__('Deal Old Stage')}} : <span class="pull-right text-primary">{deal_old_stage}</span></p>
                                            <p class="col-4">{{__('Deal New Stage')}} : <span class="pull-right text-primary">{deal_new_stage}</span></p>
                                        </div>
                                        @endif

                                        @if($emailTemplate->name == 'Create Task')
                                        <div class="row">
                                            {{-- <h6 class="font-weight-bold">{{__('Task')}}</h6> --}}
                                            <p class="col-4">{{__('Task Name')}} : <span class="pull-right text-primary">{task_name}</span></p>
                                            <p class="col-4">{{__('Task Priority')}} : <span class="pull-right text-primary">{task_priority}</span></p>
                                            <p class="col-4">{{__('Task Status')}} : <span class="pull-right text-primary">{task_status}</span></p>
                                        </div>
                                        @endif
                                        @if($emailTemplate->name == 'Assign Lead' || $emailTemplate->name == 'Move Lead')
                                            <div class="row">
                                                {{-- <h6 class="font-weight-bold">{{__('Lead')}}</h6> --}}
                                                <p class="col-4">{{__('Lead Name')}} : <span class="pull-right text-primary">{lead_name}</span></p>
                                                <p class="col-4">{{__('Lead Email')}} : <span class="pull-right text-primary">{lead_email}</span></p>
                                                <p class="col-4">{{__('Lead Pipeline')}} : <span class="pull-right text-primary">{lead_pipeline}</span></p>
                                                <p class="col-4">{{__('Lead Stage')}} : <span class="pull-right text-primary">{lead_stage}</span></p>
                                                <p class="col-4">{{__('Lead Old Stage')}} : <span class="pull-right text-primary">{lead_old_stage}</span></p>
                                                <p class="col-4">{{__('Lead New Stage')}} : <span class="pull-right text-primary">{lead_new_stage}</span></p>
                                            </div>
                                        @endif
                                        @if($emailTemplate->name == 'Assign Estimation')
                                            <div class="row">
                                                {{-- <h6 class="font-weight-bold">{{__('Estimation')}}</h6> --}}
                                                <p class="col-4">{{__('Estimation Id')}} : <span class="pull-right text-primary">{estimation_name}</span></p>
                                                <p class="col-4">{{__('Estimation Client')}} : <span class="pull-right text-primary">{estimation_client}</span></p>
                                                <p class="col-4">{{__('Estimation Status')}} : <span class="pull-right text-primary">{estimation_status}</span></p>
                                            </div>
                                        @endif
                                        @if($emailTemplate->name=='Contract')
                                            <div class="row">
                                                {{-- <h6 class="font-weight-bold">{{__('Contract')}}</h6> --}}
                                                <p class="col-4">{{__('Contract Subject')}} : <span class="pull-right text-primary">{contract_subject}</span></p>
                                                <p class="col-4">{{__('Contract Client')}} : <span class="pull-right text-primary">{contract_client}</span></p>
                                                <p class="col-4">{{__('Contract Start Date')}} : <span class="pull-right text-primary">{contract_start_date}</span></p>
                                                <p class="col-4">{{__('Contract End Date')}} : <span class="pull-right text-primary">{contract_end_date}</span></p>
                                            </div>
                                        @endif
                                        <div class="row">
                                            {{-- <h6 class="font-weight-bold">{{__('Other')}}</h6> --}}
                                            <p class="col-4">{{__('App Name')}} : <span class="pull-right text-primary">{app_name}</span></p>
                                            <p class="col-4">{{__('Company Name')}} : <span class="pull-right text-primary">{company_name}</span></p>
                                            <p class="col-4">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                            <p class="col-4">{{__('Email')}} : <span class="pull-right text-primary">{email}</span></p>
                                            <p class="col-4">{{__('Password')}} : <span class="pull-right text-primary">{password}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-9 col-sm-12 language-form-wrap">
                            {{Form::model($currEmailTempLang, array('route' => array('email_template.update', $currEmailTempLang->parent_id), 'method' => 'PUT')) }}
                            <div class="row">
                                <div class="form-group col-12">
                                    {{Form::label('subject',__('Subject'),['class'=>'form-control-label text-dark'])}}
                                    {{Form::text('subject',null,array('class'=>'form-control font-style','required'=>'required'))}}
                                </div>
                                
                                <div class="form-group col-md-6">
                                    {{Form::label('name',__('Name'),['class'=>'form-control-label text-dark'])}}
                                    {{Form::text('name',$emailTemplate->name,['class'=>'form-control font-style','disabled'=>'disabled'])}}
                                </div>
                                <div class="form-group col-md-6">
                                    {{Form::label('from',__('From'),['class'=>'form-control-label text-dark'])}}
                                    {{ Form::text('from', $emailTemplate->from, ['class' => 'form-control font-style', 'required' => 'required']) }}
                                </div>
                                <div class="form-group col-12">
                                    {{Form::label('content',__('Email Message'),['class'=>'form-control-label text-dark'])}}
                                    {{Form::textarea('content',$currEmailTempLang->content,array('class'=>'pc-tinymce-2','required'=>'required'))}}

                                </div>
                               
                                @can('Edit Email Template Lang')
                                <div class="col-md-12 text-end">
                                    {{Form::hidden('lang',null)}}
                                    <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary">
                                </div>
                                @endcan
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   
@endsection

