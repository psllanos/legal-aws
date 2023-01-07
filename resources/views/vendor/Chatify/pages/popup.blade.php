@php(\App::setLocale( basename(App::getLocale())))
@foreach($messages as $message)
    @if($message->from_data)
        <a href="{{route('chats')}}" class="list-group-item list-group-item-action">
            <div class="d-flex align-items-center">
                <div>
                    <img @if($message->from_data->avatar) src="{{asset('/storage/avatar/'.$message->from_data->avatar)}}" @else src="{{asset('storage/avatar/avatar.png')}}" @endif class="avatar rounded-circle"/>
                </div>
                <div class="flex-fill ml-3">
                    <div class="h6 text-sm mb-0">{{$message->from_data->name}} <small class="float-right text-muted">{{$message->created_at->diffForHumans()}}</small></div>
                    <p class="text-xs text-muted lh-140 mb-0">{!! $message->body !!}</p>
                </div>
            </div>
        </a>
    @endif
@endforeach
