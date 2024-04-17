{{ Form::model($refund, ['route' => ['admin.payments-at-recipient.update', $refund->shipment_id], 'method' => 'put', 'class' => 'form-refunds']) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar informação de recebimento</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('method', 'Forma de pagamento:') }}
                {{ Form::select('method', trans('admin/shipments.charge_payment_methods'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('paid_at', 'Recebido em:') }}
                {{ Form::text('paid_at', empty($refund->paid_at) ? date('Y-m-d') : $refund->paid_at, ['class' => 'form-control datepicker', 'required']) }}
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('obs', 'Observações:') }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 4]) }}
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    $('.form-refunds').on('submit', function(e){
        e.preventDefault();
        

        var $form = $(this);
        var $button = $('button[type=submit]');
        
        $button.button('loading');
        $.ajax({
            type: 'PUT', 
            url: $form.attr('action'), 
            data: $form.serialize(),
            success: function(data){
                if(data.result) {
                    oTable.draw(); //update datatable
                    $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                    $('#modal-remote').modal('hide');
                } else {
                    $.bootstrapGrowl(data.feedback, {type: 'error', align: 'center', width: 'auto', delay: 8000});
                }
            },
            error: function (data) {
                    $.bootstrapGrowl('Ocorreu um erro ao gravar o seu pedido.', {type: 'error', align: 'center', width: 'auto', delay: 8000});
            },
            always: function() {
                $button.button('reset');
            }
        });
    });
</script>