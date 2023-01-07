
{{ Form::open(array('url' => 'custom_fields')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-12">
            {{ Form::label('name', __('Custom Field Name'),['class'=>'col-form-label']) }}
            {{ Form::text('name', '', array('class' => 'form-control')) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('type', __('Type'),['class'=>'col-form-label']) }}
            {{ Form::select('type', $types,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('module', __('Modules'),['class'=>'col-form-label']) }}
            {{ Form::select('module', $modules,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>
    
{{ Form::close() }}

