<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.payments-at-recipient.edit', $row->id) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-pencil-alt"></i> Editar
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.shipments.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
                    <i class="fas fa-truck"></i> Editar Envio
                </a>
            </li>
        </ul>
    </div>
</div>