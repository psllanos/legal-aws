@extends('layouts.admin')

@section('title')
    {{$company->name}}
@endsection

@push('head')
    <link rel="stylesheet" href="{{asset('assets/modules/bootstrap-social/bootstrap-social.css')}}">
@endpush

@section('action-button')
    @can('Create Client')
        <a href="#" class="btn btn-primary btn-sm mr-1" data-ajax-popup="true" data-title="{{__('Create Client')}}" data-url="{{route('clients.create').'?company_id='.$company->id}}"><i class="fas fa-plus-circle"></i> {{__('Add Client')}} </a>
        <a href="#" class="btn btn-primary btn-sm" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit')}}" data-url="{{route('companies.edit',$company->id)}}"><i class="fas fa-pencil-alt"></i> {{__('Edit')}} </a>
    @endcan
@endsection

@section('content')

    <div class="row">

        @php
            $clients =$company->clients();
            $invoices =$company->invoices;
            $deals =$company->deals;
            $contacts =$company->contacts();
        @endphp

        <div class="col-12 col-sm-12 col-lg-12">
            <div class="card profile-widget">
                <div class="profile-widget-header">
                    <div class="profile-widget-items no-image">
                        <div class="profile-widget-item">
                            <div class="profile-widget-item-label">{{__('Clients')}}</div>
                            <div class="profile-widget-item-value">{{count($clients)}}</div>
                        </div>
                        <div class="profile-widget-item">
                            <div class="profile-widget-item-label">{{__('Invoices')}}</div>
                            <div class="profile-widget-item-value">{{count($invoices)}}</div>
                        </div>
                        <div class="profile-widget-item">
                            <div class="profile-widget-item-label">{{__('Deals')}}</div>
                            <div class="profile-widget-item-value">{{count($deals)}}</div>
                        </div>
                        <div class="profile-widget-item">
                            <div class="profile-widget-item-label">{{__('Contacts')}}</div>
                            <div class="profile-widget-item-value">{{count($contacts)}}</div>
                        </div>
                    </div>
                </div>
                <div class="profile-widget-description pb-0">
                    <div class="profile-widget-name">{{$company->name}}</div>
                    <div class="row">
                        <div class="col-lg-4">
                            <dl class="dl-horizontal">
                                <dt>{{__('Email')}}:</dt> <dd>{{$company->email}}</dd>
                                <dt>{{__('Address')}}:</dt> <dd> {{($company->address)?$company->address:"-"}}</dd>
                                <dt>{{__('City')}}:</dt> <dd> {{($company->city)?$company->city:"-"}} </dd>
                            </dl>
                        </div>
                        <div class="col-lg-4">
                            <dl class="dl-horizontal">
                                <dt>{{__('State')}}:</dt> <dd> {{($company->state)?$company->state:"-"}} </dd>
                                <dt>{{__('Zip Code')}}:</dt> <dd>{{($company->zip_code)?$company->zip_code:"-"}} </dd>
                                <dt>{{__('Country')}}:</dt> <dd> {{($company->country)?$company->country:"-"}} </dd>
                            </dl>
                        </div>
                        <div class="col-lg-4">
                            <dl class="dl-horizontal">
                                <dt>{{__('Phone')}}:</dt> <dd>{{($company->phone)?$company->phone:"-"}}</dd>
                                <dt>{{__('Website')}}:</dt> <dd> {{($company->website)?$company->website:"-"}}</dd>
                                <dt>{{__('Groups')}}:</dt>
                                <dd>
                                    @foreach($company->groups() as $group)
                                        <span class="badge badge-secondary">{{$group->name}}</span>
                                    @endforeach
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center pt-0">

                    @if($company->facebook)
                        <a href="{{$company->facebook}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Facebook')}}" class="btn btn-social-icon mr-1 btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if($company->skype)
                        <a href="{{$company->skype}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Skype')}}" class="btn btn-social-icon mr-1 btn-dropbox">
                            <i class="fab fa-skype"></i>
                        </a>
                    @endif
                    @if($company->linkedin)
                        <a href="{{$company->linkedin}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Linkedin')}}" class="btn btn-social-icon mr-1 btn-linkedin">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    @endif
                    @if($company->twitter)
                        <a href="{{$company->twitter}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Twitter')}}" class="btn btn-social-icon mr-1 btn-twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    @endif
                    @if($company->youtube)
                        <a href="{{$company->youtube}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Youtube')}}" class="btn btn-social-icon mr-1 btn-google">
                            <i class="fab fa-youtube"></i>
                        </a>
                    @endif
                    @if($company->pinterest)
                        <a href="{{$company->pinterest}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Pinterest')}}" class="btn btn-social-icon mr-1 btn-pinterest">
                            <i class="fab fa-pinterest"></i>
                        </a>
                    @endif
                    @if($company->tumblr)
                        <a href="{{$company->tumblr}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Tumblr')}}" class="btn btn-social-icon mr-1 btn-tumblr">
                            <i class="fab fa-tumblr"></i>
                        </a>
                    @endif
                    @if($company->instagram)
                        <a href="{{$company->instagram}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Instagram')}}" class="btn btn-social-icon mr-1 btn-instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    @endif
                    @if($company->github)
                        <a href="{{$company->github}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Github')}}" class="btn btn-social-icon mr-1 btn-github">
                            <i class="fab fa-github"></i>
                        </a>
                    @endif
                    @if($company->digg)
                        <a href="{{$company->digg}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Digg')}}" class="btn btn-social-icon mr-1 btn-bitbucket">
                            <i class="fab fa-digg"></i>
                        </a>
                    @endif

                </div>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                @can('Manage Clients')
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab2" data-toggle="tab" href="#home2" role="tab" aria-controls="home" aria-selected="true">{{__('Clients')}}</a>
                </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab2" data-toggle="tab" href="#profile2" role="tab" aria-controls="profile" aria-selected="false">Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab2" data-toggle="tab" href="#contact2" role="tab" aria-controls="contact" aria-selected="false">Deals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab2" data-toggle="tab" href="#contact3" role="tab" aria-controls="contact" aria-selected="false">Contact</a>
                </li>
            </ul>
            <div class="tab-content tab-bordered" id="myTab3Content">
                <div class="tab-pane fade show active" id="home2" role="tabpanel" aria-labelledby="home-tab2">
                    <div class="table-responsive">

                        <table class="table table-striped mb-0" id="dataTable">
                            <thead>
                            <tr>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Email')}}</th>
                                <th class="text-right" width="250px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{ $client->name }}</td>
                                    <td>{{ $client->email }}</td>
                                    <td class="text-right">
                                        @can('Edit Client')
                                            <a href="#" data-url="{{ URL::to('clients/'.$client->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Client')}}" class="btn btn-outline-primary btn-sm mr-1"><i class="fas fa-pencil-alt"></i> <span>{{__('Edit')}}</span></a>
                                        @endcan
                                        @can('Delete Client')
                                            <a href="#" class="btn btn-outline-danger btn-sm"  data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-{{$client->id}}').submit();"><i class="fas fa-trash"></i> <span>{{__('Delete')}}</span></a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['clients.destroy', $client->id],'id'=>'delete-form-'.$client->id]) !!}
                                            {!! Form::close() !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile-tab2">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th width="60px">{{__('Invoice')}}</th>
                                <th>{{__('Deal')}}</th>
                                <th>{{__('Issue Date')}}</th>
                                <th>{{__('Due Date')}}</th>
                                <th>{{__('Value')}}</th>
                                <th>{{__('Status')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>
                                    @can('View Invoice')
                                        <a href="{{route('invoices.show',$invoice->id)}}" class="btn btn-outline-primary btn-sm btn-round"> <i class="fas fa-file-invoice"></i> {{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                    @else
                                        {{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                    @endcan
                                </td>
                                <td>{{ $invoice->deal->name }}</td>
                                <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                <td>{{ Auth::user()->dateFormat($invoice->due_date) }}</td>
                                <td>{{ Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                <td>
                                    @if($invoice->status == 0)
                                        <span class="badge badge-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 1)
                                        <span class="badge badge-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 2)
                                        <span class="badge badge-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 3)
                                        <span class="badge badge-success">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 4)
                                        <span class="badge badge-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="contact2" role="tabpanel" aria-labelledby="contact-tab2">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Pipeline')}}</th>
                            <th>{{__('Stage')}}</th>
                            <th>{{__('Status')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($deals as $deal)
                        <tr>
                            <td>
                                {{$deal->name}}
                            </td>
                            <td>
                                {{$deal->pipeline->name}}
                            </td>
                            <td>
                                {{$deal->stage->name}}
                            </td>
                            <td>
                                @if($deal->status == 'Won')
                                    <div class="badge badge-success">{{__($deal->status)}}</div>
                                @elseif($deal->status == 'Loss')
                                    <div class="badge badge-danger">{{__($deal->status)}}</div>
                                @else
                                    <div class="badge badge-info">{{__($deal->status)}}</div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="contact3" role="tabpanel" aria-labelledby="contact-tab2">
                    <table class="table table-striped mb-0" id="dataTable">
                        <thead>
                        <tr>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Email')}}</th>
                            <th>{{__('Contact')}}</th>
                            <th>{{__('Social')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($contacts as $contact)
                        <tr>
                            <td>
                                {{$contact->name}}
                            </td>
                            <td>
                                {{$contact->email}}
                            </td>
                            <td>
                                {{$contact->phone}}
                            </td>
                            <td>
                                @if($contact->facebook)
                                <a href="{{$contact->facebook}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Facebook')}}" class="btn btn-social-icon mr-1 btn-facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                @endif
                                @if($contact->skype)
                                <a href="{{$contact->skype}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Skype')}}" class="btn btn-social-icon mr-1 btn-dropbox">
                                    <i class="fab fa-skype"></i>
                                </a>
                                @endif
                                @if($contact->linkedin)
                                <a href="{{$contact->linkedin}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Linkedin')}}" class="btn btn-social-icon mr-1 btn-linkedin">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                                @endif
                                @if($contact->twitter)
                                <a href="{{$contact->twitter}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Twitter')}}" class="btn btn-social-icon mr-1 btn-twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                @endif
                                @if($contact->youtube)
                                <a href="{{$contact->youtube}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Youtube')}}" class="btn btn-social-icon mr-1 btn-google">
                                    <i class="fab fa-youtube"></i>
                                </a>
                                @endif
                                @if($contact->pinterest)
                                <a href="{{$contact->pinterest}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Pinterest')}}" class="btn btn-social-icon mr-1 btn-pinterest">
                                    <i class="fab fa-pinterest"></i>
                                </a>
                                @endif
                                @if($contact->tumblr)
                                <a href="{{$contact->tumblr}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Tumblr')}}" class="btn btn-social-icon mr-1 btn-tumblr">
                                    <i class="fab fa-tumblr"></i>
                                </a>
                                @endif
                                @if($contact->instagram)
                                <a href="{{$contact->instagram}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Instagram')}}" class="btn btn-social-icon mr-1 btn-instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                @endif
                                @if($contact->github)
                                <a href="{{$contact->github}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Github')}}" class="btn btn-social-icon mr-1 btn-github">
                                    <i class="fab fa-github"></i>
                                </a>
                                @endif
                                @if($contact->digg)
                                <a href="{{$contact->digg}}" target="_blank" data-toggle="tooltip" data-original-title="{{__('Digg')}}" class="btn btn-social-icon mr-1 btn-bitbucket">
                                    <i class="fab fa-digg"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
