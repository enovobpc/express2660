<div class="modal fade" id="modal-password" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(array('route' => array('account.details.password.update'))) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span><span class="sr-only">Fechar</span></button>
                <h4 class="modal-title">{{ trans('account/global.settings.password.title') }}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {{ Form::label('current_password', trans('account/global.settings.password.current')) }}
                    {{ Form::password('current_password', ['class' => 'form-control', 'autocomplete' => 'off', 'required']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('password', trans('account/global.settings.password.new')) }}
                    {{ Form::password('password', ['class' => 'form-control', 'autocomplete' => 'off', 'required']) }}
                </div>
                <div class="form-group m-b-0">
                    {{ Form::label('password_confirmation', trans('account/global.settings.password.confirm')) }}
                    {{ Form::password('password_confirmation', ['class' => 'form-control', 'autocomplete' => 'off', 'required']) }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account.word.cancel') }}</button>
                <button type="submit" class="btn btn-black" data-loading-text="{{ trans('account.word.loading') }}...">{{ trans('account.word.save') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>