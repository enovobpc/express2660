<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.notices.edit', $row->id) }}" class="btn btn-sm btn-default">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.notices.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-trash-alt"></i> Eliminar
            </a>
        </li>
        <li>
            <a href="{{ route('admin.notices.views', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-eye"></i> Histórico de Visualizações
            </a>
        </li>
    </ul>
</div>
