{{ Form::open(['route' => ['admin.shipments.invoices.email.store', $shipment->id], 'class' => 'send-email']) }}
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
        {{ Form::text('email', null, ['class' => 'form-control', 'required']) }}
    </div>
    <label>Serão enviados em anexo os seguintes documentos</label>
    <div class="clearfix"></div>
    <div class="checkbox-inline" style="margin: 5px 0 0 0; padding-left: 0">
        <label style="padding-left: 0; font-weight: normal">
            {{ Form::checkbox('summary', 1, true, ['disabled' => true]) }}
            Resumo do Envio
        </label>
    </div>
    <div class="checkbox-inline" style="margin: 5px 0 0 15px;">
        @if(hasModule('invoices'))
            <label style="padding-left: 0; font-weight: normal">
                {{ Form::checkbox('invoice', 1, true, ['disabled' => true]) }}
                Fatura
            </label>
        @endif
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A enviar e-mail...">Enviar E-mail
        </button>
    </div>
</div>
{{ Form::close() }}

<script>

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('form.send-email').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-remote').modal('hide');
            } else {
                $.bootstrapGrowl(data.feedback, {type: 'error', align: 'center', width: 'auto', delay: 8000});
            }
        }).error(function () {
            $.bootstrapGrowl('Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.', {type: 'error', align: 'center', width: 'auto', delay: 8000});
        }).always(function(){
            $button.button('reset');
        })
    });
</script>