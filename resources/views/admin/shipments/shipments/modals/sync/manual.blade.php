{{ Form::model($shipment, ['route' => ['admin.shipments.sync.manual.store', $shipment->id], 'method' => 'POST', 'class' => 'ajax-manual-sync']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title"><i class="fas fa-plug"></i> Editar dados de conexão</h4>
</div>
<div class="modal-body">
    <table class="table table-condensed m-0">
        <tr>
            <th>
                <div class="p-t-5">Webservice</div>
            </th>
            <td class="input-sm">{{ Form::select('webservice_method', ['' => 'Nenhum webservice'] + $webserviceMethods, null, ['class' => 'form-control input-sm select2']) }}</td>
        </tr>
        <tr>
            <th>
                <div class="p-t-5">Código do Envio {!! tip('Se existir mais que um código, separe-os por vírgula.') !!}</div>
            </th>
            <td>{{ Form::text('provider_tracking_code', null, ['class' => 'form-control input-sm']) }}</td>
        </tr>
        <tr>
            <th>
                <div class="p-t-5">Agência Responsável</div>
            </th>
            <td>{{ Form::text('provider_cargo_agency', null, ['class' => 'form-control input-sm']) }}</td>
        </tr>
        <tr>
            <th>
                <div class="p-t-5">Agência de Origem</div>
            </th>
            <td>{{ Form::text('provider_sender_agency', null, ['class' => 'form-control input-sm']) }}</td>
        </tr>
        <tr>
            <th>
                <div class="p-t-5">Agência de Destino</div>
            </th>
            <td>{{ Form::text('provider_recipient_agency', null, ['class' => 'form-control input-sm']) }}</td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.ajax-manual-sync').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit],.btn-submit');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function (data) {
            if (data.result) {
                oTable.draw(false); //update datatable without change pagination
                Growl.success(data.feedback);
                $('#modal-remote-xs').modal('hide');
            } else {
                Growl.error(data.feedback)
            }

        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $button.button('reset');
        })
    })
</script>