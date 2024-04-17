<div class="btn-group">
    <a href="{{ route('admin.budgets.proposes.create', [$row->budget_id, 'to' => $row->email]) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-lg">
        <i class="fas fa-reply"></i>
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.budgets.proposes.show', [$row->budget_id, $row->email]) }}" data-toggle="modal" data-target="#modal-remote-xl">
                <i class="fas fa-search"></i> Ver Conversa
            </a>
        </li>
        <li>
            <a href="{{ route('admin.budgets.proposes.destroy', [$row->budget_id, $row->email]) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-trash-alt"></i> Eliminar
            </a>
        </li>
    </ul>
</div>
