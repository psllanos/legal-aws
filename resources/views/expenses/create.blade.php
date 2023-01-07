
{{ Form::open(array('url' => 'expenses','enctype'=>'multipart/form-data')) }}
<div class="card-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('category_id', __('Category'),['class'=>'col-form-label']) }}
            {{ Form::select('category_id', $category,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('amount', __('Amount'),['class'=>'col-form-label']) }}
            {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('date', __('Date'),['class'=>'col-form-label']) }}
            {{ Form::date('date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('deal_id', __('Deal'),['class'=>'col-form-label']) }}
            {{ Form::select('deal_id', $deals,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('user_id', __('User'),['class'=>'col-form-label']) }}
            {{ Form::select('user_id', [],null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('description', __('Description'),['class'=>'col-form-label']) }}
            {{ Form::textarea('description', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('attachment', __('Attachment'),['class'=>'col-form-label']) }}
            <div class="choose-files ">
                <label for="attachment">
                    <div class=" bg-primary attachment_update"> <i class="ti ti-upload px-1"></i>{{__('Choose file here')}}</div>
                    <input type="file" class="form-control file" name="attachment" id="attachment" data-filename="attachment_update" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">

                    <img id="blah"  alt="your image" width="60" height="60" />
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>
{{ Form::close() }}


<script>
    $(document).on("change", "select[name=deal_id]", function () {
        $.ajax({
            url: '{{route('deal.user.json')}}',
            data: {deal_id: $(this).val(), _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                $('#user_id').empty();
                $("#user_id").html('<option value="" selected="selected">{{__('Select User')}}</option>');
                $.each(data, function (key, data) {
                    $("#user_id").append('<option value="' + key + '">' + data + '</option>');
                });

                $('#user_id').select2({
                    placeholder: "{{__('Select User')}}"
                });
            }
        })
    });
</script>
