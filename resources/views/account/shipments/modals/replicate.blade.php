{{ Form::open(['route' => ['account.shipments.replicate', $shipment->id], 'method' => 'POST', 'class' => 'form-replicate']) }}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
            <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
            <span class="sr-only">Fechar</span>
        </button>
        <h4 class="modal-title"><i class="fas fa-copy"></i> Duplicar pedido</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="bold">Confirma a duplicação do pedido {{ $shipment->tracking_code }}?</h4>
                <p>Ao duplicar, será criado um pedido com um novo número.<br/>Poderá editar a informação do pedido antes de gravar.</p>
                {{--<hr/>--}}
                <div class="checkbox m-b-0 m-t-15">
                    <label style="padding-left: 0">
                        <input name="replicate_packs" type="checkbox" value="1" checked>
                        Duplicar também informação e dimensões da mercadoria
                    </label>
                </div>
                @if(config('app.source') == 'corridadotempo' && $shipment->customer_id == '1443')
                <div class="form-group">
                    {{ Form::label('incidence_id', 'Motivo da duplicação', ['class' => 'control-label']) }}
                    {{ Form::select('incidence_id', ['' => ''] + $incidences, null, ['class' => 'form-control select2', 'required']) }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary m-l-5" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A criar cópia..">Duplicar</button>
    </div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-replicate').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(false); //update datatable without change pagination
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});

                if(data.html) {
                    $('#modal-remote').find('.modal-dialog').addClass('modal-xl').find('.modal-content').html(data.html);
                } else {
                    $('#modal-remote').modal('hide')
                }

            } else {
                $('.form-replicate .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

        }).fail(function () {
            Growl.error500()
        }).always(function(){
            $button.button('reset');
        })
    });
</script>