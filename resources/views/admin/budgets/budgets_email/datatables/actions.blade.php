<div class="btn-group">
    <a href="{{ route('admin.budgets.show', $row->id) }}" class="btn btn-sm btn-default">
        Ver
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.budgets.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
                <i class="fas fa-pencil-alt"></i> Editar Dados Orçamento
            </a>
        </li>
        <li>
            <a href="{{ route('admin.budgets.merge', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
                <i class="fas fa-compress"></i> Juntar a outro orçamento
            </a>
        </li>
        @if(!$row->user_id)
        <li>
            <a href="{{ route('admin.budgets.adjudicate', $row->id) }}" data-method="post" data-confirm-title="Adjudicar Orçamento" data-confirm-class="btn-success" data-confirm-label="Adjudicar" data-confirm="Pretende ficar responsável por este orçamento?">
                <i class="fas fa-user-plus"></i> Adjudicar-me Orçamento
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('admin.budgets.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-trash-alt"></i> Eliminar
            </a>
        </li>
    </ul>
</div>
