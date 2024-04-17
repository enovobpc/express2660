{{ Form::open(['route' => ['admin.invoices.autocreate.store', $invoice->id], 'class' => 'autocreate-invoice']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Gerar automático {{ trans('admin/billing.types.'.$invoice->doc_after_payment) }}</h4>
</div>
<div class="modal-body">
    <h4 class="m-t-0 bold">
        Confirma a geração automática de {{ trans('admin/billing.types.'.$invoice->doc_after_payment) }}?
    </h4>
    <p>Após ser gerada, a Fatura-Proforma será marcada como paga.</p>
    <hr style="margin: 15px 0 15px;"/>

    <div class="row row-5">
        <div class="col-sm-6">
            {{ Form::label('payment_date', 'Data e Forma Pagamento') }}
            <div class="row row-0">
                <div class="col-sm-6 col-md-5">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        {{ Form::text('payment_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 5px;']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::select('payment_method', ['' => ''] + $paymentMethods, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required m-b-0 m-t-0">
                {{ Form::label('apiKey', 'Série') }}
                {{ Form::select('apiKey', $series, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required m-b-0 m-t-0">
                {{ Form::label('doc_type', 'Documento') }}
                {{ Form::select('doc_type', ['' => ''] + trans('admin/billing.types_code'), $invoice->doc_after_payment, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required m-b-0 m-t-0">
                {{ Form::label('doc_date', 'Data Doc.') }}
                {{ Form::text('doc_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 8px;']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options">
        <div class="input-group input-group-email pull-left" style="width: 270px; margin-top: -3px">
            <div class="input-group-addon" data-toggle="tooltip" title="Ative esta opção para enviar e-mail ao cliente.">
                <i class="fas fa-envelope"></i>
                {{ Form::checkbox('send_email', 1, @$invoice->customer->billing_email ? true : false) }}
            </div>
            {{ Form::text('billing_email', @$invoice->customer->billing_email, ['class' => 'form-control pull-left nospace lowercase', 'placeholder' => 'E-mail do cliente']) }}
        </div>
    </div>
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit"
                class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gerar...">
            Gerar Documento
        </button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.autocreate-invoice .datepicker').datepicker(Init.datepicker());
    $('.autocreate-invoice .select2').select2(Init.select2());
</script>