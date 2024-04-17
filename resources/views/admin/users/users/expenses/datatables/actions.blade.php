<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.users.expenses.edit', [$row->user_id, $row->id]) }}"
       class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote">
       @trans('Editar')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">@trans('Opções Extra')</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.users.expenses.destroy', [$row->user_id, $row->id]) }}"
               data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?"
               class="text-red">
                <i class="fas fa-trash-alt"></i>@trans(' Eliminar')
            </a>
        </li>
    </ul>
</div>