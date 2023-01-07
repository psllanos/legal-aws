@extends('layouts.admin')

@section('title')
    {{__('Manage Expense Categories')}}
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        @can('Create Expense Category')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Expense Category')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Expense Category')}}" data-url="{{route('expense_categories.create')}}"><i class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
@endsection


@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Setup')}}</li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Expense Categories')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card table-card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple">
                            <thead>
                                <tr>
                                <th>{{__('Expense Category')}}</th>
                                <th>{{__('Description')}}</th>
                                <th width="250px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                             @foreach ($expense_categories as $expense_category)
                                <tr>
                                    <td>{{ $expense_category->name }}</td>
                                    <td>{{ $expense_category->description }}</td>
                                    <td class="Action">
                                        <span>
                                        @can('Edit Expense Category')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="md" data-url="{{ URL::to('expense_categories/'.$expense_category->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Expense Category')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Expense Category')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('Delete Expense Category')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['expense_categories.destroy', $expense_category->id]]) !!}
                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                           <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>            
            </div>
        </div>
    </div>
@endsection
