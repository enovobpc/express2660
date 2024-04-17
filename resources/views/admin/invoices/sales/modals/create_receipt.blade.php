{{ Form::open(['route' => ['admin.invoices.receipt.store', $invoice->customer_id, $invoice->doc_id], 'method' => 'POST']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Emitir recibo</h4>
</div>
<div class="modal-body">
    <h4 style="margin-top: 0">Confirma a emissão de recibo para a fatura indicada?</h4>
    <div class="form-group">
        {{ Form::label('email', 'Enviar fatura para o e-mail') }}
        {{ Form::text('email', @$invoice->customer->billing_email, ['class' => 'form-control']) }}
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-success"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A emitir...">Emitir Recibo
        </button>
    </div>
</div>
{{ Form::close() }}