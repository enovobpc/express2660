<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.budgets.courier.edit', $row->id) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-lg">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.budgets.courier.email.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
                <i class="fas fa-envelope"></i> Enviar por E-mail
            </a>
        </li>
        <li>
            <a href="{{ route('admin.budgets.courier.print', $row->id) }}" target="_blank">
                <i class="fas fa-print"></i> Imprimir Orçamento
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.budgets.courier.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-trash-alt"></i> Eliminar
            </a>
        </li>
    </ul>
</div>
