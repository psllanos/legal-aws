<div class="card-body">
	<div class="row">
        @foreach($response as $que => $ans)
            <div class="col-12 text-xs">
                <b>{{$que}}</b> <br>
                <p>{{$ans}}</p>
            </div>
        @endforeach
    </div>
</div>
    

