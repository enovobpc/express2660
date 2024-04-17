<div class="modal" id="modal-confirm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Eliminar ficheiro</h4>
            </div>
            <div class="modal-body">
                <h4>Confirma a remoção do ficheiro selecionado?</h4>
            </div>
            <div class="modal-footer">
                {{ Form::hidden('file') }}
                <button type="button" class="btn btn-default" data-confirm-btn="0">Cancelar</button>
                <button type="button" class="btn btn-danger" data-confirm-btn="1">Eliminar</button>
            </div>
        </div>
    </div>
</div>