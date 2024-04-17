<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.tracking.status.edit', $row->id) }}" class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote">
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
        @if(Auth::user()->isAdmin() || !$row->is_static)
        <li>
            <a href="{{ route('admin.tracking.status.destroy', $row->id) }}"
               data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?"
               class="text-red">
                <i class="fas fa-trash-alt"></i> Eliminar
            </a>
        </li>
        @endif
    </ul>
</div>