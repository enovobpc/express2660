<div class="action-buttons text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Opções <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.departments.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-pencil-alt"></i> Editar
                </a>
            </li>
            <li>
                <a href="{{ route('account.departments.destroy', $row->id) }}" data-method="delete" data-confirm="Confirma a remoção do registo selecionado?">
                    <i class="fas fa-fw fa-trash-alt"></i> Eliminar
                </a>
            </li>
        </ul>
    </div>
</div>