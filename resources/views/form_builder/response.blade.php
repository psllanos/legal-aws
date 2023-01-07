@extends('layouts.admin')

@section('title')
    {{ $form->name.__("'s Response") }}
@endsection

@section('action-button')

    <div class="row align-items-center m-1">
        
    
        <div class="col-auto pe-0">
            <a href="{{ route('form_builder.index') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Back')}}" ><i class="ti ti-arrow-left text-white"></i></a>
        </div>
    </div>

@endsection

@section('breadcrumb')
<li class="breadcrumb-item" aria-current="page"><a href="{{route('form_builder.index')}}">{{__('Form Builder')}}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{__('Form Builder Response')}}</li>
@endsection


@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                
                    <div class="card-header card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table">
                                @if($form->response->count() > 0)
                                    <tbody>
                                    @php
                                        $first = null;
                                        $second = null;
                                        $third = null;
                                        $i = 0;
                                    @endphp
                                    @foreach ($form->response as $response)
                                        @php
                                            $i++;
                                                $resp = json_decode($response->response,true);
                                                if(count($resp) == 1)
                                                {
                                                    $resp[''] = '';
                                                    $resp[' '] = '';
                                                }
                                                elseif(count($resp) == 2)
                                                {
                                                    $resp[''] = '';
                                                }
                                                $firstThreeElements = array_slice($resp, 0, 3);

                                                $thead= array_keys($firstThreeElements);
                                                $head1 = ($first != $thead[0]) ? $thead[0] : '';
                                                $head2 = (!empty($thead[1]) && $second != $thead[1]) ? $thead[1] : '';
                                                $head3 = (!empty($thead[2]) && $third != $thead[2]) ? $thead[2] : '';
                                        @endphp
                                        @if(!empty($head1) || !empty($head2) || !empty($head3) && $head3 != ' ')
                                            <tr>
                                                <th>{{ $head1 }}</th>
                                                <th>{{ $head2 }}</th>
                                                <th>{{ $head3 }}</th>
                                                <th></th>
                                            </tr>
                                        @endif
                                        @php
                                            $first =  $thead[0];
                                            $second =  $thead[1];
                                            $third =  $thead[2];
                                        @endphp
                                        <tr>
                                            @foreach(array_values($firstThreeElements) as $ans)
                                                <td>{{$ans}}</td>
                                            @endforeach
                                            <td class="Action">
                                                <span>
                                                    <div class="action-btn bg-warning ms-2">
                                                        <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Response Detail')}}"  data-url="{{ route('response.detail',$response->id) }}" data-ajax-popup="true" data-title="{{__('Response Detail')}}" class="btn btn-icon btn-sm" ><i class="ti ti-eye text-white"></i></a>
                                                    </div>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                @else
                                    <tbody>
                                    <tr>
                                        <td class="text-center">{{__('No data available in table')}}</td>
                                    </tr>
                                    </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection
