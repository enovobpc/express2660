<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.invoices.edit', [$row->id, 'schedule' => true]) }}" class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-xl">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções</span>
    </button>

    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->id, 'id' => $row->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote"

               class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> Eliminar
            </a>
        </li>
    </ul>
</div>