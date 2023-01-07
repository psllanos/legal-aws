
<form method="post" action="{{ route('lang.store') }}">
    @csrf
    <div class="modal-body">
        <div class="row">
                <div class="form-group col-12">
                    <label for="code" class="col-form-label">{{ __('Language Code') }}</label>
                    <input class="form-control" type="text" id="code" name="code" required="" placeholder="{{ __('Language Code') }}">
                </div>
            </div>
    </div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>  
</div>


</form>

