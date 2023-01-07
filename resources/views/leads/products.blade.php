
{{ Form::model($lead, array('route' => array('leads.products.update', $lead->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('products', __('Products'),['class'=>'col-form-label']) }}
            {{ Form::select('products[]', $products,null, array('class' => 'form-control multi-select','id'=>'choices-multiple','multiple'=>'','required'=>'required')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Save')}}</button>
</div>

{{ Form::close() }}
