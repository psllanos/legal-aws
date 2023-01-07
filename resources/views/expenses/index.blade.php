@extends('layouts.admin')

@section('title')
    {{ __('Manage Expenses') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Expenses') }}</li>
@endsection

@section('action-button')

    <div class="row align-items-center m-1">
        @can('Create Expense')
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Create Expense') }}" data-ajax-popup="true" data-size="lg"
                    data-title="{{ __('Create Expense') }}" data-url="{{ route('expenses.create') }}"><i
                        class="ti ti-plus text-white"></i></a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('Total Expenses') }}</h6>
                            <h3 class="text-primary">{{ $cnt_expense['total'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill bg-success text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('This Month Total Expenses') }}</h6>
                            <h3 class="text-info">{{ $cnt_expense['this_month'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill bg-info text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('This Week Total Expenses') }}</h6>
                            <h3 class="text-warning">{{ $cnt_expense['this_week'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill bg-warning text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('Last 30 Days Total Expenses') }}</h6>
                            <h3 class="text-danger">{{ $cnt_expense['last_30days'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <div class="table-responsive">
                    <table class="table pc-dt-simple">
                        <thead>
                            <tr>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Deal') }}</th>
                                <th>{{ __('User') }}</th>
                                <th width="100px">{{ __('Attachment') }}</th>
                                <th width="150px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->category->name }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ Auth::user()->priceFormat($expense->amount) }}</td>
                                    <td>{{ Auth::user()->dateFormat($expense->date) }}</td>
                                    <td>
                                        @if (isset($expense->deal) && !empty($expense->deal))
                                            {{ $expense->deal->name }}
                                        @else
                                            - @endif
                                    </td>
                                    <td>{{ $expense->user->name }}</td>
                                    <td>
                                        @if ($expense->attachment)
                                            {{-- <a href="{{asset(Storage::url('attachment/'.$expense->attachment))}}" download="" class="btn btn-outline-primary btn-sm mr-1" data-toggle="tooltip" data-original-title="{{__('Download')}}"><i class="fas fa-download"></i> <span>{{__('Download')}}</span></a> --}}
                                            @php
                                                $attachments = \App\Models\Utility::get_file('');
                                                
                                            @endphp
                                            <a href="{{ $attachments . $expense->attachment }}" download=""
                                                class="btn btn-outline-primary btn-sm mr-1" data-toggle="tooltip"
                                                data-original-title="{{ __('Download') }}"><i class="fas fa-download"></i>
                                                <span>{{ __('Download') }}</span>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="Action">
                                        <span>
                                            @can('Edit Expense')
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="lg"
                                                        data-url="{{ URL::to('expenses/' . $expense->id . '/edit') }}"
                                                        data-ajax-popup="true" data-title="{{ __('Edit Expense') }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Edit Expense') }}"><i
                                                            class="ti ti-pencil text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('Delete Expense')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['expenses.destroy', $expense->id]]) !!}
                                                    <a href="#!"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Delete Expense') }}">
                                                        <span class="text-white"> <i class="ti ti-trash"></i></span>
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
