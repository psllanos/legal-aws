

{{ Form::open(array('url' => 'zoommeeting')) }}
<div class="modal-body">
    <div class="tab-content tab-bordered">
        <div class="tab-pane fade show active" id="tab-1" role="tabpanel">
            <div class="row">
                <div class="col-6 form-group">
                    {{ Form::label('title', __('Title'),['class'=>'col-form-label']) }}
                    {{ Form::text('title', null, array('class' => 'form-control','required'=>'required')) }}
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('lead_id', __('Lead'),['class'=>'col-form-label']) }}
                    {{ Form::select('lead_id', $Leads,null, array('class' => 'form-control select2')) }}
                    @if(count($Leads) == 1)
                        <div class="text-muted text-xs">
                            {{__('Please create new Leads')}} <a href="{{route('users')}}">{{__('here')}}</a>.
                        </div>
                    @endif
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('client_id', __('Client'),['class'=>'col-form-label']) }}
                    {{ Form::select('client_id', $clients,null, array('class' => 'form-control select2')) }}
                    @if(count($clients) == 1)
                        <div class="text-muted text-xs">
                            {{__('Please create new Clients')}} <a href="{{route('users')}}">{{__('here')}}</a>.
                        </div>
                    @endif
                </div>
                <div class="col-6 form-group">
                    {{ Form::label('user_id', __('Users'),['class'=>'col-form-label']) }}
                    {{ Form::select('user_id', $users,null, array('class' => 'form-control select2')) }}
                    @if(count($users) == 1)
                        <div class="text-muted text-xs">
                            {{__('Please create new users')}} <a href="{{route('users')}}">{{__('here')}}</a>.
                        </div>
                    @endif
                </div>
                 <div class="col-6 form-group">
                    {{ Form::label('password', __('Password'),['class'=>'col-form-label']) }}
                    {{ Form::text('password', null, array('class' => 'form-control')) }}
                </div>
                
                 <div class="form-group col-md-6">
                    {{ Form::label('datetime', __('Start Date / Time'),['class'=>'col-form-label']) }}
                    <input type="datetime-local" class="form-control" id="birthdaytime" name="start_date">
                   
                </div> 
                <div class="col-6 form-group">
                    {{ Form::label('duration', __('Duration'),['class'=>'col-form-label']) }}
                    {{ Form::text('duration', null, array('class' => 'form-control','required'=>'required')) }}
                </div>
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
     const d_week = new Datepicker(document.querySelector('.d_week'), {
                    buttonClass: 'btn',
                    timePicker: true,
                    singleDatePicker: true,
                    timePicker24Hour: true,
                    format: 'yyyy-mm-dd H-i-s',
                    locale: {
                            format: 'MM/DD/YYYY H:mm'
                        },
                });

</script>