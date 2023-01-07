{{ Form::model($mdf, array('route' => array('mdf.products.store', $mdf->id), 'method' => 'POST')) }}

<div class="modal-body">

<div class="row">
    <div class="col-12 py-3">
        <div class="d-flex radio-check">
            <div class="form-check m-1">
                <input type="radio" name="product_type" value="product_service" id="product_service_radio" class="form-check-input" checked>
                <label class="form-check-label" for="product_service_radio">{{__('Product & Serivce')}}</label>
            </div>
            <div class="form-check m-1">
                <input type="radio" name="product_type" value="others" id="others_radio" class="form-check-input">
                <label class="form-check-label" for="others_radio">{{__('Other')}}</label>
            </div>
        </div>
    </div>

    <div id="product_service" class="col-12">
        <div class="form-group">
            {{ Form::label('product_id', __('Product'),['class'=>'form-control-label']) }}
            {{ Form::select('product_id', $products,null, array('class' => 'form-control select2')) }}
        </div>
        <div class="form-group">
            {{ Form::label('quantity', __('Quantity'),['class'=>'form-control-label']) }}
            {{ Form::number('quantity', isset($product)?null:1, array('class' => 'form-control','min'=>'1')) }}
        </div>
    </div>
    <div id="others" class="d-none col-12">
        <div class="form-group">
            {{ Form::label('title', __('Title'),['class'=>'form-control-label']) }}
            {{ Form::text('title', null, array('class' => 'form-control')) }}
        </div>
        <div class="form-group">
            {{ Form::label('price', __('Price'),['class'=>'form-control-label']) }}
            {{ Form::number('price', null, array('class' => 'form-control','min'=>'1')) }}
        </div>
    </div>
    <div class="col-12 form-group">
        {{ Form::label('description', __('Description'),['class'=>'form-control-label']) }}
        {{ Form::textarea('description', isset($product)?null:'', array('class' => 'form-control')) }}
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>

</div>



{{ Form::close() }}


<script>
$(document).ready(function () {
    changeProduct($("input[type=radio][name=product_type]:checked").val());
});
</script>
