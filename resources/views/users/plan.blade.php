    <div class="card table-card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table mb-0">
                    @foreach($plans as $plan)
                        <tr>
                            <td>{{$plan->name}}</td>
                            <td>{{__('Users')}} : {{$plan->max_users}}</td>
                            <td>{{__('Clients')}} : {{$plan->max_clients}}</td>
                            <td>{{__('Deals')}} : {{$plan->max_deals}}</td>
                            <td>
                                @if($user->plan==$plan->id)
                                    <div class="badge rounded p-2 px-3 bg-success">{{__('Active')}}</div>
                                @else
                                    <a href="{{route('plan.active',[$user->id,$plan->id, 'duration' => 'monthly'])}}" class="badge rounded p-2 px-3 bg-primary text-white" title="{{__('Click to Upgrade Plan')}}">{{ __('One Month') }}</a>
                                    <a href="{{route('plan.active',[$user->id,$plan->id, 'duration' => 'annual'])}}" class="badge rounded p-2 px-3 bg-primary text-white" title="{{__('Click to Upgrade Plan')}}">{{ __('One Year') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

