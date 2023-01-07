
    {{ Form::model($deal, array('route' => array('deals.update', $deal->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#tab-1" role="tab" aria-controls="pills-home" aria-selected="true">{{__('Deal Detail')}}</a>
        </li>
        @if(!$customFields->isEmpty())
            <li class="nav-item">
                <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" href="#tab-2" role="tab" aria-controls="pills-profile" aria-selected="false">{{__('Custom Fields')}}</a>
            </li>
        @endif
        
    </ul>

   
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="tab-1" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="row">
                <div class="col-6 form-group">
                    {{ Form::label('name', __('Deal Name'),['class'=>'col-form-label']) }}
                    {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('price', __('Price'),['class'=>'col-form-label']) }}
                    {{ Form::number('price', null, array('class' => 'form-control')) }}
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('pipeline_id', __('Pipeline'),['class'=>'col-form-label']) }}
                    {{ Form::select('pipeline_id', $pipelines,null, array('class' => 'form-control','required'=>'required')) }}
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('stage_id', __('Stage'),['class'=>'col-form-label']) }}
                    {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control','required'=>'required')) }}
                </div>
                <div class="col-12 form-group">
                    {{ Form::label('sources', __('Sources'),['class'=>'col-form-label']) }}
                    {{ Form::select('sources[]', $sources,null, array('class' => 'form-control multi-select','id'=>'choices-multiple','multiple'=>'','required'=>'required')) }}
                </div>

                <div class="col-12 form-group">
                    {{ Form::label('products', __('Products'),['class'=>'col-form-label']) }}
                    {{ Form::select('products[]', $products,null, array('class' => 'form-control multi-select','id'=>'choices-multiple1','multiple'=>'','required'=>'required')) }}
                </div>
                <div class="col-12 form-group">
                     {{ Form::label('notes', __('Notes'),['class'=>'col-form-label']) }}
                    {{ Form::textarea('notes',null, array('class' => 'tox-target pc-tinymce-2','id'=>'pc_demo1')) }}
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('phone', __('Phone No'),['class'=>'col-form-label']) }}
                    {{ Form::text('phone', null, array('class' => 'form-control','required'=>'required')) }}
                </div>
            </div>
        </div>
        @if(!$customFields->isEmpty())
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                <div class="row">
                    @include('custom_fields.formBuilder')
                </div>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
       
    </div>

{{ Form::close() }}


<script>
    var stage_id = '{{$deal->stage_id}}';

    $(document).ready(function () {
        $("#commonModal select[name=pipeline_id]").trigger('change');
    });

    $(document).on("change", "#commonModal select[name=pipeline_id]", function () {
        $.ajax({
            url: '{{route('stages.json')}}',
            data: {pipeline_id: $(this).val(), _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                $('#stage_id').empty();
                $("#stage_id").append('<option value="" selected="selected">{{__('Select Stage')}}</option>');
                $.each(data, function (key, data) {
                    var select = '';
                    if (key == '{{ $deal->stage_id }}') {
                        select = 'selected';
                    }
                    $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data + '</option>');
                });
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{__('Select Stage')}}"
                });
            }
        })
    });


</script>

   



<!-- tagify -->
