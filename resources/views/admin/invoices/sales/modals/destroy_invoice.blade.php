{{ Form::open(['route' => ['admin.invoices.destroy', $invoice->id], 'class' => 'destroy-invoice']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">@trans('Eliminar documento de venda')</h4>
</div>
<div class="modal-body">
    @if($invoice->is_scheduled)
        <h4 class="m-t-0">
            @trans('Confirma a remoção da fatura agendada?')
        </h4>
    @elseif($invoice->doc_type == 'nodoc')
        <h4 class="m-t-0">
            @trans('Confirma a anulação do estado "Faturado sem documento"?')
        </h4>
    @elseif(in_array($invoice->doc_type, [\App\Models\Invoice::DOC_TYPE_FP, \App\Models\Invoice::DOC_TYPE_INTERNAL_DOC]))
        <h4 class="m-t-0">
            @trans('Confirma a anulação deste documento?')
        </h4>
    @elseif($invoice->doc_type == 'receipt')
        <h4 class="m-0">
            @trans('Confirma a anulação do recibo emitido?')'
        </h4>
    @elseif(\App\Models\Invoice::getInvoiceSoftware() == \App\Models\Invoice::SOFTWARE_SAGEX3)
        <h4 class="m-t-0 bold">
            @trans('Confirma a anulação do documento selecionado?')
        </h4>
        <br/>
        <p>@trans('Este procedimento apenas disponibilizará o serviço para nova faturação.<br/>O documento não será eliminado no SAGE.')</p>
    @else
        @if(!$invoice->is_draft)
            @if($invoice->doc_type == \App\Models\Invoice::DOC_TYPE_NC)
                <h4 class="m-t-0 bold">@trans('Pretende estornar a Nota de Crédito?')</h4>
                <p class="text-blue">
                    @trans('A anulação de uma Nota de Crédito irá gerar uma Nota de Débito.')
                </p>
            @elseif($invoice->can_delete && $invoice->can_reverse)
                <h4 class="m-t-0 bold">@trans('Pretende anulação ou estornar este documento?')</h4>
                <p class="text-blue">
                    @trans('<b>Anular:</b> O documento é efetivamente anulado') {!! tip(__('Esta opção pode ser usada até 5 dias após a data de documento e caso não tenha ainda sido enviado ao cliente. A anulação irá constar no SAFT.')) !!}<br/>
                    @trans('<b>Estornar:</b> Cria uma nota de crédito da totalidade do documento.') {!! tip(__('Use esta opção quando a fatura já foi enviada para o cliente.')) !!}
                </p>
            @elseif($invoice->can_delete && !$invoice->can_reverse)
                <h4 class="m-t-0 bold">@trans('Confirma a anulação do documento selecionado?.')</h4>
            @else
                <h4 class="m-t-0 bold">@trans('Confirma o estorno do documento selecionado?.')</h4>
                <p class="text-blue">
                    @trans('Passaram mais de 5 dias desde a data do documento.<br/>O mesmo só pode ser estornado..')
                    @trans('Será criada uma nota de crédito da totalidade do documento.')
                </p>
            @endif
            <hr style="margin: 15px 0 15px;"/>
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group m-b-0 m-t-0 is-required">
                        {{ Form::label('credit_reason', $invoice->can_delete ? __('Motivo de anulação/estorno') : __('Motivo do estorno')) }}
                        {{ Form::text('credit_reason', null, ['class' => 'form-control', 'placeholder' => __('Justifique o motivo...'), 'required']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required m-b-0 m-t-0">
                        {{ Form::label('apiKey', __('Série')) }}
                        {{ Form::select('apiKey', $series, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required m-b-0 m-t-0">
                        {{ Form::label('credit_date', __('Data Documento')) }}
                        <div class="input-group">
                            {{ Form::text('credit_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
        <h4 class="m-t-0">
            @trans('Confirma a remoção do rascunho?')
        </h4>
        @endif
    @endif
</div>
<div class="modal-footer">
    @if(!$invoice->is_scheduled && !$invoice->is_draft)
    <div class="input-group input-group-email pull-left" style="width: 270px;">
        <div class="input-group-addon" data-toggle="tooltip" title="@trans('Ative esta opção para enviar e-mail ao cliente com documento anexado.')">
            <i class="fas fa-envelope m-t-3"></i>
            {{ Form::checkbox('send_email', 1, @$invoice->customer->billing_email ? true : false) }}
        </div>
        {{ Form::text('billing_email', @$invoice->customer->billing_email, ['class' => 'form-control pull-left nospace lowercase', 'placeholder' => __('E-mail do cliente')]) }}
    </div>
    @endif
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
        @if($invoice->is_scheduled || $invoice->is_draft)
            <button type="submit"
                class="btn btn-danger btn-delete"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> @trans('A eliminar...')">
                @trans('Eliminar')
            </button>
        @else
            @if($invoice->can_delete) 
            <button type="button"
                class="btn btn-primary btn-delete"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> @trans('A anular...')">
                    <i class="fas fa-trash-alt"></i> @trans('Anular')
            </button>
            @endif
            @if($invoice->can_reverse) 
            <button type="button"
                class="btn btn-danger btn-reverse"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> @trans('A estornar...')">
                    <i class="fas fa-copy"></i> @trans('Estornar')
            </button>
            @endif
        @endif
    </div>
</div>
{{ Form::hidden('delete_type', $invoice->is_scheduled || $invoice->is_draft ? 'delete' : 'reverse') }}
{{ Form::close() }}

<script>
    $('.destroy-invoice .datepicker').datepicker(Init.datepicker());
    $('.destroy-invoice .select2').select2(Init.select2());

    $('.btn-delete').on('click', function(e){
        e.preventDefault();
        $('form.destroy-invoice input[name="delete_type"]').val('delete');
        $('form.destroy-invoice').submit();
    });

    $('.btn-reverse').on('click', function(e){
        e.preventDefault();
        $('form.destroy-invoice input[name="delete_type"]').val('reverse');
        $('form.destroy-invoice').submit();
    });

    /**
     * Destroy invoice
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('form.destroy-invoice').on('submit', function(e){
        e.preventDefault();

        if($('form.destroy-invoice input[name="credit_reason"]').val() == '') {
            Growl.warning("@trans('Deve indicar o motivo do estorno ou anulação.')")
        } else {
            var $form = $(this);
            var $submitBtn = $('.btn-reverse');
            if($('form.destroy-invoice input[name="delete_type"]').val() == 'delete') {
                $submitBtn = $('.btn-delete');
            }
           

            $submitBtn.button('loading');

            $.ajax({
                url: $form.attr('action'),
                data: $form.serialize(),
                type: 'POST',
                success: function(data) {
                    if(data.result) {
                        Growl.success(data.feedback);
                        $('#modal-remote').modal('hide');
                        $('.billing-header').html(data.html_header)
                        $('.billing-sidebar').html(data.html_sidebar)

                        try {
                            oTable.draw(); //update datatable
                        } catch (e) {}

                        try {
                            oTableBalance.draw(); //update datatable on balance details
                        } catch (e) {}

                        if (data.printPdf) {
                            if (!window.open(data.printPdf, '_blank')) {
                                Growl.error("@trans('Não foi possivel abrir o separador para impressão. Verifique as definições de POP-UPS do browser.')")
                            }
                        }
                    } else {
                        Growl.error(data.feedback);
                    }
                }
            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $submitBtn.button('reset');
            });
        }
    });


</script>