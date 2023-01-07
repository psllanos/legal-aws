
{{ Form::model($product, array('route' => array('products.update', $product->id), 'method' => 'PUT','enctype'=>'multipart/form-data')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('name', __('Product Name'),['class'=>'col-form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('price', __('Price'),['class'=>'col-form-label']) }}
            {{ Form::number('price', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('type', __('Type'),['class'=>'col-form-label']) }}
            {{ Form::select('type', ['Product'=>'Product','Service'=>'Service'], null, array('class'=>'form-control select2')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('image', __('Image'),['class'=>'col-form-label']) }}
            <div class="choose-files ">
                <label for="image">
                    <div class=" bg-primary image_update"> <i class="ti ti-upload px-1"></i>{{__('Choose file here')}}</div>
                    <input type="file" class="form-control file" name="image" id="image" data-filename="image_update" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">

                    <img src="{{(!empty($product->image))?  \App\Models\Utility::get_file($product->image): asset(url("custom/img/news/img01.jpg"))}}" class="img-fluid">
                </label>
            </div>
        </div>
        <div class="col-12 form-group">
            {{ Form::label('description', __('Description'),['class'=>'col-form-label']) }}
            {{ Form::textarea('description', null, array('class' => 'form-control')) }}
        </div>


        @include('custom_fields.formBuilder')
    </div>
</div>
 <div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary" data-bs-dismiss="modal">{{__('Update')}}</button>
</div>
{{ Form::close() }}

