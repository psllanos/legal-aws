@extends('layouts.admin')

@section('title')
    {{ $formBuilder->name.__("'s Form Field") }}
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        
    
        <div class="col-auto pe-0">
            <a href="{{ route('form_builder.index') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Back')}}" ><i class="ti ti-arrow-left text-white"></i></a>
        </div>

        <div class="col-auto pe-0">
            <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Invoice')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Form')}}" data-url="{{ route('form.field.create',$formBuilder->id) }}"><i class="ti ti-plus text-white"></i></a>
        </div>
    </div>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item" aria-current="page"><a href="{{route('form_builder.index')}}">{{__('Form Builder')}}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{__('Form Builder Detail')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card"> 
                <div class="card-header card-body table-border-style">  
                    <div class="table-responsive">
                        <table class="table pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th width="250px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($formBuilder->form_field->count())
                                    @foreach ($formBuilder->form_field as $field)
                                        <tr>
                                            <td>{{ $field->name }}</td>
                                            <td>{{ ucfirst($field->type) }}</td>
                                            <td class="Action">
                                                <span>
                                                    <div class="action-btn btn-info ms-2">
                                                        <a href="#" data-size="lg" data-url="{{ route('form.field.edit',[$formBuilder->id,$field->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Field')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Field')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                    </div>

                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['form.field.destroy', [$formBuilder->id,$field->id]]]) !!}
                                                            <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Field')}}">
                                                               <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">{{__('No data available in table')}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
