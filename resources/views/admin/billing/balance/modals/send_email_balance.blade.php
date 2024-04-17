{{ Form::open(['route' => ['admin.billing.balance.email.balance', $customer->id], 'class' => 'ajax-form']) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Enviar Extrato Conta Corrente</h4>
</div>
<div class="modal-body">
    <div class="form-group w-100">
        {{ Form::label('email', 'Enviar conta corrente para o e-mail') }}<br/>
        {{ Form::text('email', $customer->billing_email, ['class' => 'form-control w-100']) }}
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Enviar E-mail</button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.modal .ajax-form button[type="submit"]').on('click', function(e) {
        e.preventDefault();

        var $modal = $(this).closest('.modal');
        var $form  = $(this).closest('form');
        var $btn   = $(this);

        $btn.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback);
                $modal.modal('hide');
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function(){
            Growl.error500()
        }).always(function(){
            $btn.button('reset');
        })
    })
</script>