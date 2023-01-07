
{{ Form::model($tax, array('route' => array('taxes.update', $tax->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('name', __('Tax Rate Name'),['class'=>'col-form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('rate', __('Tax Rate %'),['class'=>'col-form-label']) }}
            {{ Form::number('rate', null, array('class' => 'form-control','required'=>'required','step'=>"0.01")) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
</div>
{{ Form::close() }}

