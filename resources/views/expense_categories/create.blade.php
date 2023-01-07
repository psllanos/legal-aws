
{{ Form::open(array('url' => 'expense_categories')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-12">
            {{ Form::label('name', __('Expense Category Name'),['class'=>'col-form-label']) }}
            {{ Form::text('name', '', array('class' => 'form-control','required'=> 'required')) }}
        </div>
        <div class="form-group col-12">
            {{ Form::label('description', __('Description'),['class'=>'col-form-label']) }}
            {{ Form::textarea('description', '', array('class' => 'form-control')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>

{{ Form::close() }}

