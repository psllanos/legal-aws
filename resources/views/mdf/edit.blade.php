
{{ Form::model($mdf, array('route' => array('mdf.update', $mdf->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('from', __('Submitter'),['class'=>'col-form-label']) }}
            {{ Form::text('from', \Auth::user()->name, array('class' => 'form-control','required'=>'required','disabled'=>'true')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('status', __('Status'),['class'=>'col-form-label']) }}
            {{ Form::select('status', $status,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('request_type', __('Request Type'),['class'=>'col-form-label']) }}
            {{ Form::select('request_type', $type,$mdf->type, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('event_type', __('Event Type'),['class'=>'col-form-label']) }}
            {{ Form::select('event_type', [''=>__('Select Event Type')],$mdf->sub_type, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('event_date', __('Event Date'),['class'=>'col-form-label']) }}
            {{ Form::date('event_date',$mdf->date, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('amount_requested', __('Amount Requested'),['class'=>'col-form-label']) }}
            {{ Form::number('amount_requested', $mdf->amount, array('class' => 'form-control','min'=>1)) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('description', __('Description'),['class'=>'col-form-label']) }}
            {{ Form::textarea('description',null, array('class' => 'form-control')) }}
        </div>
    </div>
</div>
 <div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>

</div>
{{ Form::close() }}


<script>
    var sub_type = '{{$mdf->sub_type}}';
    $(document).ready(function () {
        $("select[name=request_type]").trigger('change');
    });
    $(document).on("change", "select[name=request_type]", function () {
        fillEventType($(this).val(), sub_type);
    });
</script>
