<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.billing.api-keys.edit', $row->id) }}"
           class="btn btn-sm btn-default"
            data-toggle="modal"
            data-target="#modal-remote">
            Editar
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.billing.api-keys.destroy', $row->id) }}" data-method="delete"
                   data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </a>
            </li>
        </ul>
    </div>
</div>