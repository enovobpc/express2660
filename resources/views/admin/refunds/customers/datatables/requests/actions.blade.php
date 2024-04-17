<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.refunds.requests.edit', $row->id) }}" class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-lg">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções Extra</span>
    </button>
    <ul class="dropdown-menu pull-right">
{{--        <li>
            <a href="{{ route('admin.change-log.show', ['RefundControl', $row->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-fw fa-history"></i> Histórico de Edições
            </a>
        </li>
        <li class="divider"></li>--}}
        <li>
            <a href="{{ route('admin.printer.refunds.customers.summary', ["id[]" => $row->shipments]) }}" target="_blank">
                <i class="fas fa-fw fa-print"></i> Imprimir comprovativo
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.refunds.requests.destroy', $row->id) }}"
               data-method="delete"
               data-confirm="Pretende cancelar este pedido de reembolso?">
                <i class="fas fa-fw fa-times"></i> Anular Pedido Reembolso
            </a>
        </li>
    </ul>
</div>