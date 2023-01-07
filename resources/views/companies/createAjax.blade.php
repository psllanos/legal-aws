
{{ Form::open(array('url' => 'companies')) }}
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('name', __('Name')) }}
                {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('email', __('E-Mail Address')) }}
                {{ Form::email('email', null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
    </div>
    <div class="form-group mb-0">
        {{ Form::button('<i class="fas fa-plus-circle"></i> '.__('Create'), ['type' => 'submit','class' => 'btn btn-primary']) }}
    </div>
{{ Form::close() }}