
{{ Form::open(array('url' => 'contract')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6 form-group">
            {{ Form::label('name', __('Contract Name'),['class'=>'col-form-label']) }}
            {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('client_name', __('Client Name'),['class'=>'col-form-label']) }}
            {{ Form::select('client_name', $client,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('subject', __('Subject'),['class'=>'col-form-label']) }}
            {{ Form::text('subject', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('value', __('Value'),['class'=>'col-form-label']) }}
            {{ Form::number('value', '', array('class' => 'form-control','required'=>'required','min' => '1')) }}
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('type', __('Type'),['class'=>'col-form-label']) }}
                {{ Form::select('type', $contractType,null, array('class' => 'form-control select2','required'=>'required')) }}
                @if(count($contractType) <= 0)
                    <div class="text-muted text-xs">
                        {{__('Please create new contract type')}} <a href="{{route('contract_type.index')}}">{{__('here')}}</a>.
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('date', __('Start Date / End Date'),['class'=>'col-form-label']) }}
                <div class='input-group'>
                    <input type='text' name="date" id='pc-daterangepicker-2'
                        class="form-control" placeholder="Select date range" />
                    <span class="input-group-text"><i
                            class="feather icon-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('notes', __('Descripation'),['class'=>'col-form-label']) }}
                {{ Form::textarea('notes', '', array('class' => 'form-control')) }}
            </div>
        </div>
        {{-- <div class="col-md-12 form-group">
            <label class="col-form-label">{{__('Status')}}</label>
            <div class="d-flex radio-check">
                <div class="custom-control custom-radio custom-control-inline m-1">
                    <input type="radio" id="start" name="status" value="Start" class="form-check-input" checked>
                    <label class="form-check-labe" for="start">{{__('Start')}}</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline m-1">
                    <input type="radio" id="close" name="status" value="Close" class="form-check-input">
                    <label class="form-check-labe" for="close">{{__('Close')}}</label>
                </div>
            </div>
        </div> --}}
    </div>
</div>


<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>

</div>
{{ Form::close() }}

