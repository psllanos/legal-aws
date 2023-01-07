{{ Form::open(array('url' => 'permissions')) }}
<div class="card-body">
    <div class="form-group">
        {{ Form::label('name', __('Permission Name')) }}
        {{ Form::text('name', '', array('class' => 'form-control')) }}
    </div>

    <div class="form-group">
        {{ Form::label('roles', __('Assign Roles',['class'=>'form-label'])) }}
        <div class="row gutters-xs">

            @foreach ($roles as $role)
                <div class="col-6 custom-control custom-checkbox radio-check">
                    {{ Form::checkbox('roles[]',$role->id,false,['class' => 'custom-control-input form-check-input','id'=>'permission_'.$role->id]) }}
                    {{ Form::label('permission_'.$role->id, ucfirst($role->name),['class'=>'custom-control-label ml-4 form-check-labe']) }}
                </div>
            @endforeach

        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>
{{ Form::close() }}
