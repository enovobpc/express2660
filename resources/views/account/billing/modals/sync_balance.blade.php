<div class="modal fade" id="modal-sync-balance">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['account.billing.invoices.sync']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Atualizar Conta Corrente</h4>
            </div>
            <div class="modal-body">
                <div>
                    <h4>Pretende sincronizar e atualizar a sua conta corrente?</h4>
                    <p>
                        A sua conta corrente é atualizada periódicamente.<br/>
                        Iremos comunicar com o nosso programa de faturação para obter a informação mais recente sobre a sua faturação.
                    </p>
                </div>
                <h4 class="text-center loading-status" style="display: none;">
                    <i class='fas fa-spin fa-circle-notch'></i>
                    A sincronizar informação com o programa de faturação.
                </h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success btn-sync-ballance" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Atualizar</button>
                </div>
            </div>
            {{ Form::hidden('auto') }}
            {{ Form::close() }}
        </div>
    </div>
</div>