
    {{ Form::open(array('url' => 'invoices')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('deal_id', __('Deal'),['class'=>'col-form-label']) }}
            {{ Form::select('deal_id', $deals,null, array('class' => 'form-control select2','required'=>'required')) }}
            @if(count($deals) <= 0)
                <div class="text-muted text-xs">
                    {{__('Please create new Deal')}} <a href="{{route('deals.index')}}">{{__('here')}}</a>.
                </div>
            @endif
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
        <div class="col-6 form-group">
            {{ Form::label('issue_date', __('Issue Date'),['class'=>'col-form-label']) }}
            {{ Form::date('issue_date',null, array('class' => 'form-control','id'=>'data_picker1','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('due_date', __('Due Date'),['class'=>'col-form-label']) }}
            {{ Form::date('due_date',null, array('class' => 'form-control','id'=>'data_picker2','required'=>'required')) }}
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
