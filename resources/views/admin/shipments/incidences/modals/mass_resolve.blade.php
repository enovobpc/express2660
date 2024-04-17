<div class="modal" id="modal-resolve-all">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.incidences.selected.resolve'], 'method' => 'POST']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Marcar incidências como resolvidas</h4>
            </div>
            <div class="modal-body">
                <h4 class="bold">Confirma a resolução das incidências selecionadas?</h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Resolver</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>