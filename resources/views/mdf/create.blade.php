    {{ Form::open(array('url' => 'mdf')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('submitter', __('Submitter'),['class'=>'form-control-label']) }}
            {{ Form::text('submitter', \Auth::user()->name, array('class' => 'form-control','disabled'=>'true')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('status', __('Status'),['class'=>'form-control-label']) }}
            {{ Form::select('status', $status,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('request_type', __('Request Type'),['class'=>'form-control-label']) }}
            {{ Form::select('request_type', $type,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('event_type', __('Event Type'),['class'=>'form-control-label']) }}
            {{ Form::select('event_type', [''=>__('Select Event Type')],null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('event_date', __('Event Date'),['class'=>'form-control-label']) }}
            {{ Form::date('event_date',null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('amount_requested', __('Amount Requested'),['class'=>'form-control-label']) }}
            {{ Form::number('amount_requested', 0, array('class' => 'form-control','min'=>1)) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('description', __('Description'),['class'=>'form-control-label']) }}
            {{ Form::textarea('description',null, array('class' => 'form-control')) }}
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
        $("select[name=request_type]").trigger('change');
    });
    $(document).on("change", "select[name=request_type]", function () {
        fillEventType($(this).val());
    });
</script>
