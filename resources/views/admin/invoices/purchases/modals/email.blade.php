{{ Form::open(['route' => ['admin.invoices.purchase.payment-notes.email.submit', $paymentNote->id], 'class' => 'send-email']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Enviar Nota de Pagamento</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('email', 'Enviar nota de pagamento para') }}
        {{ Form::text('email', @$paymentNote->provider->billing_email, ['class' => 'form-control email nospace lowercase']) }}
    </div>
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