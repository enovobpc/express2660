<div class="modal" id="modal-send-balance-email">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.billing.balance.email.balance', $customer->id]]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">Enviar Conta Corrente</h4>
            </div>
            <div class="modal-body">
                <div class="form-group w-100">
                    {{ Form::label('email', __('Enviar conta corrente para o e-mail')) }}<br/>
                    {{ Form::text('email', $customer->billing_email, ['class' => 'form-control w-100']) }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">@trans('Enviar E-mail')</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>