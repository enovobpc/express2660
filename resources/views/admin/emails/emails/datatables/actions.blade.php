<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.emails.edit', $row->id) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-lg">
        @if($row->is_draft)
        <i class="fas fa-pencil-alt"></i> Editar
        @else
        Visualizar
        @endif
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.emails.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> Eliminar
            </a>
        </li>
    </ul>
</div>
