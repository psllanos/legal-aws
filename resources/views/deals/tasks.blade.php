
@if(isset($task))
    {{ Form::model($task, array('route' => array('deals.tasks.update', $deal->id, $task->id), 'method' => 'PUT')) }}
@else
    {{ Form::open(array('route' => ['deals.tasks.store',$deal->id])) }}
@endif
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('name', __('Name'),['class'=>'col-form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('date', __('Date'),['class'=>'col-form-label']) }}
            {{ Form::date('date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('time', __('Time'),['class'=>'col-form-label']) }}
            <div class="input-group timepicker">
                <input class="form-control pc-timepicker-2" placeholder="Select time" type="text" name="time"/>
                <span class="input-group-text">
                    <i class="feather icon-clock"></i>
                </span>
            </div>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('priority', __('Priority'),['class'=>'col-form-label']) }}
            <select class="form-control select2" name="priority" required>
                @foreach($priorities as $key => $priority)
                    <option value="{{$key}}" @if(isset($task) && $task->priority == $key) selected @endif>{{__($priority)}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('status', __('Status'),['class'=>'col-form-label']) }}
            <select class="form-control select2" name="status" required>
                @foreach($status as $key => $st)
                    <option value="{{$key}}" @if(isset($task) && $task->status == $key) selected @endif>{{__($st)}}</option>
                @endforeach
            </select>
        </div>
     </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    @if(isset($task))
        <button type="submit" class="btn  btn-primary">{{__('Edit')}}</button>
    @else
        <button type="submit" class="btn  btn-primary">{{__('Save')}}</button>
    @endif


</div>

{{ Form::close() }}


