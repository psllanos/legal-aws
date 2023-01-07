{{ Form::model($permission, array('route' => array('permissions.update', $permission->id), 'method' => 'PUT')) }}

    <div class="form-group">
        {{ Form::label('name', __('Permission Name')) }}
        {{ Form::text('name', null, array('class' => 'form-control')) }}
    </div>

    <div class="form-actions pb-0">
        {{ Form::button('<i class="fas fa-pencil-alt"></i> '.__('Update'), ['type' => 'submit','class' => 'btn btn-primary mr-1']) }}
    </div>
{{ Form::close() }}