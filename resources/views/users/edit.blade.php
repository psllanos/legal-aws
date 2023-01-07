
<form method="post" action="{{ route('users.update',$user->id) }}">
    @csrf
    @method('PUT')
<div class="modal-body">
    <div class="row">
            <div class="col-6 form-group">
                <label class="col-form-label" for="name">{{ __('Name') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}" required/>
            </div>
            <div class="col-6 form-group">
                <label class="col-form-label" for="email">{{ __('E-Mail Address') }}</label>
                <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}" required/>
            </div>
        </div>
</div>        
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
   
</div>
</form>

