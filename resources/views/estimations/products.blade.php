
@if(isset($product))
    {{ Form::model($product, array('route' => array('estimations.products.update', $estimation->id,$product->id), 'method' => 'PUT')) }}
@else
    {{ Form::model($estimation, array('route' => array('estimations.products.store', $estimation->id), 'method' => 'POST')) }}
@endif
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('product_id', __('Product'),['class'=>'col-form-label']) }}
            {{ Form::select('product_id', $products,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('quantity', __('Quantity'),['class'=>'col-form-label']) }}
            {{ Form::number('quantity', isset($product)?null:1, array('class' => 'form-control','required'=>'required','min'=>'1')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('description', __('Description'),['class'=>'col-form-label']) }}
            {{ Form::textarea('description', null, array('class' => 'form-control')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
     @if(isset($product))
         <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
    @else
         <button type="submit" class="btn  btn-primary">{{__('Add')}}</button>
    @endif
</div>

{{ Form::close() }}

