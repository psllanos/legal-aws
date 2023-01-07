
{{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}
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
      
        <div class="col-6 form-group">
            <label class="col-form-label" for="job_title">{{ __('Job Title') }}</label>
            <input type="text" class="form-control" id="job_title" name="job_title" value="{{$user->job_title}}"/>
        </div>
        <div class="col-6 form-group">
            <label class="col-form-label" for="exampleSelect1">{{ __('Role') }}</label>
            <select name="role" class="form-select select2" required>
                <option value="">{{__('Select Role')}}</option>
                @foreach($roles as $role)
                    <option value="{{$role->id}}" @if($role->id == $userRole) selected @endif>{{$role->name}}</option>
                @endforeach
            </select>
        </div>

        @include('custom_fields.formBuilder')
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary" data-bs-dismiss="modal">{{__('Update')}}</button>
   
</div>
{{ Form::close() }}

