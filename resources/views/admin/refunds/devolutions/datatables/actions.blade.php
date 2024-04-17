<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.refunds.cod.edit', $row->id) }}" class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-xs">
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
        <li>
            <a href="{{ route('admin.shipments.edit', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-xl">
                <i class="fas fa-fw fa-truck"></i> Editar Envio
            </a>
        </li>
    </ul>
</div>