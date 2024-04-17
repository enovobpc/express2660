{{ Form::open(['route' => ['admin.shipments.replicate', $shipment->id], 'class' => 'form-replicate']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" style="float: right">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Duplicar envio</h4>
</div>
<div class="modal-body">
    <h4 class="bold">Confirma a duplicação do envio {{ $shipment->tracking_code }}?</h4>
    <p class="text-info">
        <i class="fas fa-info-circle"></i> Ao duplicar, o envio não será sincronizado com nenhum webservice.<br/>
        <i class="fas fa-info-circle"></i> Será atribuido um novo número ao novo envio.
    </p>
    <hr style="margin: 5px 0"/>
    <div class="checkbox">
        <label style="padding-left: 0">
            {{ Form::checkbox('linked', 1, false) }}
            Manter o novo envio ligado ao envio original <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao manter ambos os envios vinculados, quando os estados do envio duplicado forem alterados, os estados do envio original vão ser também alterados."></i>
        </label>
    </div>
    <div class="checkbox">
        <label style="padding-left: 0">
            {{ Form::checkbox('expenses', 1, true) }}
            Duplicar também taxas e encargos adicionais
        </label>
    </div>
    <div class="checkbox" style="margin-bottom: 0">
        <label style="padding-left: 0">
            {{ Form::checkbox('edit', 1, true) }}
            Depois de duplicar, abrir janela para edição do novo envio.
        </label>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A criar cópia..">Duplicar</button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('[data-toggle="tooltip"]').tooltip();

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

                // if(data.html) {
                //     $('#modal-remote').find('.modal-dialog').addClass('modal-xl').find('.modal-content').html(data.html);
                // } else {
                //     $('#modal-remote').modal('hide')
                // }

                $('#modal-remote').modal('hide')

                if (data.button) {
                    var $button = $(data.button);
                    $('body').append($button);
                    $button.click();
                    $button.remove();
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