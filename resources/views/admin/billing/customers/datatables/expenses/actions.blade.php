<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.shipments.edit', [$row->shipment_id]) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-xl">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.shipments.expenses.destroy', [$row->shipment_id, $row->id]) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-trash-alt"></i> Eliminar
            </a>
        </li>
    </ul>
</div>