<div class="modal fade" id="modal-update-balance-status">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.billing.balance.update.payment-status']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Atualizar Estados de Pagamento</h4>
            </div>
            <div class="modal-body">
                <h4>
                    Confirma a atualização de estados de pagamento para todos os documentos em aberto?<br/>
                    <small class="text-blue"><i class="fas fa-info-circle"></i> Esta operação poderá demorar vários minutos até estar concluída.</small>
                </h4>
                <h4 class="text-center loading-status" style="display: none;">
                    <i class='fas fa-spin fa-circle-notch'></i>
                    A sincronizar informação com o programa de faturação.
                </h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Atualizar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>