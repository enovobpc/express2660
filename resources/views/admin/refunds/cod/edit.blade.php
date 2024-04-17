{{ Form::model($refund, ['route' => ['admin.refunds.cod.update', $refund->shipment_id], 'method' => 'put', 'class' => 'form-refunds']) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar informação de recebimento</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('payment_method', 'Forma de pagamento:') }}
                {{ Form::select('payment_method', trans('admin/refunds.payment-methods-list'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('payment_date', 'Pago em:') }}
                <div class="input-group">
                    {{ Form::text('payment_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('obs', 'Observações:') }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required m-b-0">
                {{ Form::label('price', 'Total pago') }}
                <div class="input-group">
                    {{ Form::text('price', $shipment->total_price_for_recipient, ['class' => 'form-control decimal', 'required']) }}
                    <span class="input-group-addon">
                        €
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group m-b-0">
                <div class="checkbox m-b-0 m-t-25">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('paid', 1, false) }}
                        Marcar envio como Pago
                    </label>
                </div>
            </div>
        </div>
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
    $('.modal .select2').select2(Init.select2());

    $('.form-refunds').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');
        
        $button.button('loading');
        $.ajax({
            type: 'PUT', 
            url: $form.attr('action'), 
            data: $form.serialize(),
            success: function(data) {
                if (data.result) {
                    oTable.draw();
                    Growl.success(data.feedback)
                    $('#modal-remote-xs').modal('hide');

                    if (data.printProof) {
                        if (window.open(data.printProof, '_blank')) {
                            $('#modal-remote-lg').modal('hide');
                        } else {
                            $('#modal-remote-lg').find('.modal-lg').removeClass('modal-lg').find('.modal-content').html(data.html);
                        }
                    }
                } else {
                    Growl.error(data.feedback)
                }
            }
        }).fail(function (data) {
            Growl.error500();
        }).always(function (data) {
            $button.button('reset');
        })
    });
</script>