
    {{ Form::open(array('url' => 'estimations')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('client_id', __('Client'),['class'=>'col-form-label']) }}
            {{ Form::select('client_id', $client,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('issue_date', __('Issue Date'),['class'=>'col-form-label']) }}
            {{ Form::date('issue_date',null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('tax_id', __('Tax %'),['class'=>'col-form-label']) }}
            {{ Form::select('tax_id', $taxes,null, array('class' => 'form-control select2','required'=>'required')) }}
            @if(count($taxes) <= 0)
                <div class="text-muted text-xs">
                    {{__('Please create new Tax')}} <a href="{{route('taxes.index')}}">{{__('here')}}</a>.
                </div>
            @endif
        </div>

        <div class="col-12 form-group">
            {{ Form::label('terms', __('Terms'),['class'=>'col-form-label']) }}
            {{ Form::textarea('terms',null, array('class' => 'form-control')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>

</div>

{{ Form::close() }}
