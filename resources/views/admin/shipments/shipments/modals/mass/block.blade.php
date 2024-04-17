<div class="modal" id="modal-mass-block">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => 'admin.shipments.selected.block', 'method' => 'POST')) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Bloquear/Desbloquear Envios Selecionados</h4>
            </div>
            <div class="modal-body">
                <div class="form-group is-required">
                    {{ Form::label('selected_block_status', 'Que ação pretende aplicar?', ['class' => 'control-label']) }}
                    {{ Form::select('selected_block_status',  ['1' => 'Bloquear selecionados', '0' => 'Desbloquear selecionados'], null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>x
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" data-loading-text="Aguarde...">Bloquear/Desbloquear</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>