<div class="modal" id="reset-password" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(array('route' => array('account.password.email'))) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-lock"></i> Recuperar palavra-passe</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {{ Form::label('email', 'E-mail') }}
                    {{ Form::email('email', null, array('class' => 'form-control nospace email lowercase', 'autocomplete' => 'off', 'required')) }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" data-loading-text="A enviar pedido...">Recuperar</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>