<div class="modal" id="modal-sync-balance">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.billing.balance.sync', $customer->id]]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Sincronizar Conta Corrente')</h4>
            </div>
            <div class="modal-body">
                <h4 class="fw-400">@trans('Pretende sincronizar e atualizar a conta corrente?')</h4>
                <h4 class="fw-400 text-center loading-status" style="display: none;">
                    <i class='fas fa-spin fa-circle-notch'></i>
                    @trans('A sincronizar informação com o programa de faturação.')'
                </h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="button" class="btn btn-success .btn-sync-balance-documents" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">@trans('Atualizar')</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>