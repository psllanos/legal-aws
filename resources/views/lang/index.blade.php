@extends('layouts.admin')

@section('title')
    {{__('Manage Languages')}}
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
       @can('Create Language')
            <div class="col-auto pe-0">
               <a href="#" class="btn btn-sm btn-primary btn-icon m-1 " data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Language')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Language')}}" data-url="{{ route('lang.create') }}"><i class="ti ti-tag text-white"></i></a>
            </div>
        @if($currantLang != \App\Models\Utility::settings()['default_language'])
            <div class="col-auto pe-0">
                {!! Form::open(['method' => 'DELETE', 'route' => ['lang.destroy', $currantLang],'id'=>'delete-form-'.$currantLang]) !!}
                    <a href="#!" class="btn btn-sm btn-danger btn-icon m-1 show_confirm">
                       <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                {!! Form::close() !!}

            </div>
        @endif
        @endcan
    </div>   
   
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        @foreach(Utility::languages() as $lang)
                            <a href="{{route('lang',$lang)}}" class="nav-link text-sm font-weight-bold @if($currantLang == $lang) active @endif">
                                <i class="d-lg-none d-block mr-1"></i>
                                <span class="d-none d-lg-block">{{Str::upper($lang)}}</span>
                            </a>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-labels-tab" data-bs-toggle="pill" href="#labels" role="tab" aria-controls="pills-labels" aria-selected="true">{{__('Labels')}}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="pills-messages-tab" data-bs-toggle="pill" href="#messages" role="tab" aria-controls="messages" aria-selected="false">{{__('Messages')}}</a>
                        </li>

                    </ul>
                    @can('Edit Language')
                        <form method="post" action="{{route('lang.store.data',$currantLang)}}">
                            @csrf
                            @endcan
                            <div class="tab-content">
                                <div class="tab-pane active" id="labels">
                                    <div class="row">
                                        @foreach($arrLabel as $label => $value)
                                            <div class="col-lg-6">
                                                <div class="form-group mb-3">
                                                    <label class="col-form-label text-dark">{{$label}}</label>
                                                    <input type="text" class="form-control" name="label[{{$label}}]" value="{{$value}}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="tab-pane show" id="messages">
                                    @foreach($arrMessage as $fileName => $fileValue)
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h4>{{ucfirst($fileName)}}</h4>
                                            </div>
                                            @foreach($fileValue as $label => $value)
                                                @if(is_array($value))
                                                    @foreach($value as $label2 => $value2)
                                                        @if(is_array($value2))
                                                            @foreach($value2 as $label3 => $value3)
                                                                @if(is_array($value3))
                                                                    @foreach($value3 as $label4 => $value4)
                                                                        @if(is_array($value4))
                                                                            @foreach($value4 as $label5 => $value5)
                                                                                <div class="col-lg-6">
                                                                                    <div class="form-group mb-3">
                                                                                        <label class="col-form-label text-dark">{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}.{{$label4}}.{{$label5}}</label>
                                                                                        <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}][{{$label4}}][{{$label5}}]" value="{{$value5}}">
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        @else
                                                                            <div class="col-lg-6">
                                                                                <div class="form-group mb-3">
                                                                                    <label class="col-form-label text-dark">{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}.{{$label4}}</label>
                                                                                    <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}][{{$label4}}]" value="{{$value4}}">
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group mb-3">
                                                                            <label class="col-form-label text-dark">{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}</label>
                                                                            <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}]" value="{{$value3}}">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <div class="col-lg-6">
                                                                <div class="form-group mb-3">
                                                                    <label class="col-form-label text-dark">{{$fileName}}.{{$label}}.{{$label2}}</label>
                                                                    <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}]" value="{{$value2}}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <div class="col-lg-6">
                                                        <div class="form-group mb-3">
                                                            <label class="col-form-label text-dark">{{$fileName}}.{{$label}}</label>
                                                            <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}]" value="{{$value}}">
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @can('Edit Language')
                                <div class="form-group col-12 text-end">
                                    <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                                </div>
                            @endcan
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection
