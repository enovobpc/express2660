{{ Form::open(['route' => ['admin.billing.customers.email.submit', $customer->id, 'month' => $month, 'year' => $year, 'period' => $period], 'class' => 'send-email']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Enviar resumo por e-mail</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('email', 'E-mail') }}
        {{ Form::text('email', $customer->billing_email, ['class' => 'form-control email nospace lowercase']) }}
    </div>
    <label>Que documentos pretende anexar ao e-mail?</label>
    <div class="clearfix"></div>
    <div class="checkbox-inline" style="margin: 5px 0 0 0; padding-left: 0">
        <label style="padding-left: 0; font-weight: normal">
            {{ Form::checkbox('attachments[]', 'summary', true) }}
            Resumo de Envios (PDF)
        </label>
    </div>
    <div class="checkbox-inline" style="margin: 5px 0 0 15px;">
        <label style="padding-left: 0; font-weight: normal">
            {{ Form::checkbox('attachments[]', 'excel', Setting::get('billing_attach_excel')) }}
            Resumo de Envios (Excel)
        </label>
    </div>
    @if(hasModule('invoices'))
        <div class="clearfix"></div>
        <label class="m-t-15">Que faturas pretende anexar ao e-mail?</label>
        <div class="clearfix"></div>
        @foreach($invoices as $invoice)
            @if($invoice->invoice_type != 'nodoc' && !$invoice->invoice_draft)
                <div class="checkbox-inline" style="margin: 5px 15px 0 0;">
                    <label style="padding-left: 0; font-weight: normal">
                        {{ Form::checkbox('invoices[]', $invoice->invoice_doc_id, true) }}
                        Fatura {{ trans('admin/billing.types_code.' . $invoice->invoice_type) }} {{ $invoice->invoice_doc_id }}
                    </label>
                </div>
            @endif
        @endforeach
    @else
    <div class="checkbox-inline" style="margin: 5px 0 0 15px;">
        <label style="padding-left: 0; font-weight: normal" data-toggle="tooltip" data-placement="right" title="Não possui ligação a nenhum software de faturação.">
            {{ Form::checkbox('invoices[]', '', true, ['disabled' => true]) }}
            Fatura
        </label>
    </div>
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