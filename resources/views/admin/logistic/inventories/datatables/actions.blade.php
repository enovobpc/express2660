<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.logistic.inventories.edit', $row->id) }}"
       class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-xl">
       @trans('Editar')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.logistic.inventories.print.summary', $row->id) }}" target="_blank">
                <i class="fas fa-fw fa-print"></i> @trans('Imprimir')
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.logistic.inventories.destroy', $row->id) }}" data-method="delete"
               data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
    </ul>
</div>
