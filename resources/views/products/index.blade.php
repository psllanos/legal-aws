@extends('layouts.admin')

@section('title')
    {{__('Manage Products')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{__('Products')}}</li>
@endsection

@section('action-button')

             <a href="{{route('product.export')}}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-original-title="{{__('Export')}}"  >
                <i class="ti ti-file-export text-white"></i>
            </a>
               <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Import')}}" data-size="md" data-ajax-popup="true" data-title="{{__('Import Product CSV File')}}" data-url="{{route('product.file.import')}}">
                <i class="ti ti-file-import text-white"></i>
            </a>
        @can('Create Product')
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Product')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Product')}}" data-url="{{route('products.create')}}"><i class="ti ti-plus text-white"></i></a>
        @endcan
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
                                    <th width="70">{{__('Image')}}</th>
                                    <th>{{__('Product')}}</th>
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Description')}}</th>
                                    @if(\Auth::user()->can('Edit Product') || \Auth::user()->can('Delete Product'))
                                        <th width="250px">{{__('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <a href="@if($product->image) {{\App\Models\Utility::get_file($product->image)}} @else {{\App\Models\Utility::get_file('product/img01.jpg')}} @endif" target="_blank">
                                            <img src="@if($product->image) {{\App\Models\Utility::get_file($product->image)}} @else {{\App\Models\Utility::get_file('product/img01.jpg')}} @endif" width="70"/></td>
                                        </a>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ Auth::user()->priceFormat($product->price) }}</td>
                                    <td>{{ $product->type }}</td>
                                    <td>{{ $product->description }}</td>
                                    @if(\Auth::user()->can('Edit Product') || \Auth::user()->can('Delete Product'))
                                        <td>
                                            @can('Edit Product')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="lg" data-url="{{ URL::to('products/'.$product->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Product')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Product')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('Delete Product')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['products.destroy', $product->id]]) !!}
                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Product')}}">
                                                           <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endif
                                        </td>
                                    @endif
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
