
{{ Form::open(array('url' => 'companies')) }}

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-1" role="tab" aria-selected="true">{{__('Basic Info')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-2" role="tab" aria-selected="true">{{__('Social')}}</a>
        </li>

        @if(!$customFields->isEmpty())
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-3" role="tab" aria-selected="true">{{__('Custom Fields')}}</a>
        </li>
        @endif
    </ul>
    <div class="tab-content tab-bordered">
        <div class="tab-pane fade show active" id="tab-1" role="tabpanel">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('name', __('Name')) }}
                        {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('email', __('E-Mail Address')) }}
                        {{ Form::email('email', null, array('class' => 'form-control','required'=>'required')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('phone', __('Phone')) }}
                        {{ Form::number('phone', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('address', __('Address')) }}
                        {{ Form::text('address', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('city', __('City')) }}
                        {{ Form::text('city', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('state', __('State')) }}
                        {{ Form::text('state', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('zip_code', __('Zip Code')) }}
                        {{ Form::text('zip_code', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('country', __('Country')) }}
                        {{ Form::text('country', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('description', __('Description')) }}
                        {{ Form::textarea('description', null, array('class' => 'form-control')) }}
                    </div>
                    </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="group">{{ __('Group') }}</label>
                        <div class="row gutters-xs">
                            @foreach($groups as $group)
                                <div class="col-6 custom-control custom-checkbox">
                                    {{ Form::checkbox('groups[]',$group->id,null,['class' => 'custom-control-input','id'=>'group_'.$group->id]) }}
                                    {{ Form::label('group_'.$group->id, ucfirst($group->name),['class'=>'custom-control-label ml-4']) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="tab-pane fade show" id="tab-2" role="tabpanel">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('website', __('Website')) }}
                        {{ Form::url('website', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('facebook', __('Facebook')) }}
                        {{ Form::url('facebook', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('skype', __('Skype')) }}
                        {{ Form::url('skype', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('linkedin', __('LinkedIn')) }}
                        {{ Form::url('linkedin', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('twitter', __('Twitter')) }}
                        {{ Form::url('twitter', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('youtube', __('Youtube')) }}
                        {{ Form::url('youtube', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('pinterest', __('Pinterest')) }}
                        {{ Form::url('pinterest', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('tumblr', __('Tumblr')) }}
                        {{ Form::url('tumblr', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('instagram', __('Instagram')) }}
                        {{ Form::url('instagram', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('github', __('Github')) }}
                        {{ Form::url('github', null, array('class' => 'form-control')) }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {{ Form::label('digg', __('Digg')) }}
                        {{ Form::url('digg', null, array('class' => 'form-control')) }}
                    </div>
                </div>
            </div>
        </div>
        @if(!$customFields->isEmpty())
        <div class="tab-pane fade show" id="tab-3" role="tabpanel">
            @include('custom_fields.formBuilder')
        </div>
        @endif
    </div>
    <div class="form-group mt-4 mb-0">
        {{ Form::button('<i class="fas fa-plus-circle"></i> '.__('Create'), ['type' => 'submit','class' => 'btn btn-primary']) }}
    </div>
{{ Form::close() }}