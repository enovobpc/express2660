<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.shipments.incidences.create', [$row->shipment_id, 'history' => $row->history_id]) }}" class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote">
        <i class="fas fa-reply"></i> Resp
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções Extra</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.shipments.show', [$row->shipment_id, 'tab' => 'incidences']) }}"
               data-toggle="modal"
               data-target="#modal-remote-xl">
                <i class="fas fa-search"></i> Consultar Envio
            </a>
        </li>
    </ul>
</div>

