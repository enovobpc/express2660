<div class="modal" id="modal-confirm-change-provider">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alterar fornecedor</h4>
            </div>
            <div class="modal-body">
                <h4 class="text-center text-red m-b-15"><i class="fas fa-exclamation-triangle"></i> Atenção, envio sincronizado</h4>
                <p>
                    Este envio está sincronizado via {{ $shipment->webservice_method }}.
                    Só é possível mudar de fornecedor se eliminar a ligação ao webservice atual.
                </p>
                <p>
                    <b>Pretende eliminar eliminar a sincronização atual e mudar de fornecedor?</b>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-confirm-no">Não</button>
                <button type="button" class="btn btn-default btn-confirm-yes" data-loading-text="Aguarde...">Sim, eliminar ligação</button>
            </div>
        </div>
    </div>
</div>