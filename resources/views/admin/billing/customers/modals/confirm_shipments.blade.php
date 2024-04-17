<div class="modal" id="modal-confirm-selected-shipments">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => 'admin.billing.customers.shipments.confirm', 'method' => 'POST')) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Conferir/Desconferir Envios Selecionados</h4>
            </div>
            <div class="modal-body">
                <div class="form-group is-required">
                    {{ Form::label('confirm_status', 'Que ação pretende aplicar?', ['class' => 'control-label']) }}
                    {{ Form::select('confirm_status',  ['1' => 'Confirmar selecionados', '0' => 'Desconfirmar selecionados'], null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Aguarde...">Conferir/Desconferir</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>