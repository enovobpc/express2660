{{ Form::open(['route' => ['admin.invoices.email.submit', $invoice->id], 'class' => 'send-email']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Enviar Fatura por E-mail</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('email', 'E-mail') }}
        {{ Form::text('email', @$invoice->customer->billing_email, ['class' => 'form-control email nospace lowercase']) }}
    </div>
    <label>Que documentos pretende anexar ao e-mail?</label>
    <div class="clearfix"></div>
    @if(@$invoice->doc_type == 'receipt')
        <label style="padding-left: 0; font-weight: normal">
            {{ Form::checkbox('attachments[]', 'receipt', true) }}
            Recibo ({{ $invoice->doc_series ? $invoice->doc_series : trans('admin/billing.types_code.' . $invoice->doc_type) }} {{ $invoice->doc_id }})
        </label>
    @elseif(@$invoice->doc_type == 'credit-note')
        <label style="padding-left: 0; font-weight: normal">
            {{ Form::checkbox('attachments[]', 'invoice', true) }}
            Nota Crédito ({{ $invoice->doc_series }} {{ $invoice->doc_id }})
        </label>
    @else
        @if($invoice->target_id && $invoice->target == \App\Models\Invoice::TARGET_CUSTOMER_BILLING)
            @if(!empty(@$invoice->customer_billing->shipments))
                @if(@$invoice->customer_billing->billing_type == 'single')
                    <div class="checkbox-inline" style="margin: 5px 15px 0 0; padding-left: 0">
                        <label style="padding-left: 0; font-weight: normal">
                            {{ Form::checkbox('attachments[]', 'shipment', true) }}
                            Comprovativo de Envio
                        </label>
                    </div>
                @else
                <div class="checkbox-inline" style="margin: 5px 0 0 0; padding-left: 0">
                    <label style="padding-left: 0; font-weight: normal">
                        {{ Form::checkbox('attachments[]', 'summary', true) }}
                        Resumo de Envios (PDF)
                    </label>
                </div>
                <div class="checkbox-inline" style="margin: 5px 15px  0 15px;">
                    <label style="padding-left: 0; font-weight: normal">
                        {{ Form::checkbox('attachments[]', 'excel',  Setting::get('billing_attach_excel')) }}
                        Resumo de Envios (Excel)
                    </label>
                </div>
                @endif
            @endif
        @endif

        @if($invoice->exists && $invoice->doc_type != 'nodoc' && !$invoice->is_draft)
        <div class="checkbox-inline" style="margin: 5px 0 0 0">
            @if(hasModule('invoices'))
                <label style="padding-left: 0; font-weight: normal">
                    {{ Form::checkbox('attachments[]', 'invoice', true) }}
                    Fatura ({{ trans('admin/billing.types_code.' . $invoice->doc_type) }} {{ $invoice->doc_id }})
                </label>
                @if($invoice->assigned_receipt)
                    <label style="padding-left: 10px; font-weight: normal">
                        {{ Form::checkbox('attachments[]', 'receipt', true) }}
                        Recibo (RC {{ $invoice->assigned_receipt }})
                    </label>
                @endif
            @else
                <label style="padding-left: 0; font-weight: normal" data-toggle="tooltip" data-placement="right" title="Não possui ligação a nenhum software de faturação.">
                    {{ Form::checkbox('attachments[]', '', true, ['disabled' => true]) }}
                    Fatura
                </label>
            @endif
        </div>
        @endif
    @endif
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A enviar...">
            Enviar E-mail
        </button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('form.send-email').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback);
                $('#modal-remote').modal('hide');
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function(){
            $button.button('reset');
        })
    });
</script>