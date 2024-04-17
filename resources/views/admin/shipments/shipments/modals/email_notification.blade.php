{{ Form::open(['route' => ['admin.shipments.selected.notify.send']]) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" style="float: right">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Notificar em massa</h4>
</div>
<div class="modal-body">
    <h4 class="bold">Confirma que pretende enviar uma notificação de envio?</h4>
    <p>As notificações serão enviadas por E-mail e/ou SMS conforme esteja configurado na ficha de cada cliente.</p>
</div>
<div class="modal-footer">
    {{ Form::hidden('ids', $ids) }}
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A enviar notificação...">Enviar</button>
    </div>
</div>
{{ Form::close() }}
