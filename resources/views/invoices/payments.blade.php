
{{ Form::model($invoice, array('route' => array('invoices.payments.store', $invoice->id), 'method' => 'POST')) }}
<div class="modal-body">
     <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('amount', __('Amount'),['class'=>'col-form-label']) }}
            {{ Form::number('amount', $invoice->getDue(), array('class' => 'form-control','required'=>'required','min'=>'0',"step"=>"0.01")) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('date', __('Payment Date'),['class'=>'col-form-label']) }}
            {{ Form::date('date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('payment_id', __('Payment Method'),['class'=>'col-form-label']) }}
            {{ Form::select('payment_id', $payment_methods,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('notes', __('Notes'),['class'=>'col-form-label']) }}
            {{ Form::textarea('notes', null, array('class' => 'form-control')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Add')}}</button>

</div>

{{ Form::close() }}

