<div class="modal" id="modal-customer-blocked">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cliente bloqueado por pagamentos em atraso.</h4>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle text-red fs-50"></i>
                <h4 class="text-red blocked-days">
                    Não é possível criar envios para o cliente selecionado por se encontrar
                    com pagamentos em atraso à mais de <span class="limitdays"></span> dias.
                </h4>
                <h4 class="text-red blocked-credit" style="display:none;">
                    Não é possível criar envios para o cliente selecionado
                    por este ter ultrapassado o limite de crédito.
                </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-confirm-no">Compreendi</button>
            </div>
        </div>
    </div>
</div>