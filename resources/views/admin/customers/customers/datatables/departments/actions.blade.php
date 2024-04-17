<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.customers.departments.edit', [$row->customer_id, $row->id]) }}"
       class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-lg">
       @trans('Editar')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        @if($row->password)
        <li>
            <a href="{{ route('admin.customers.remote-login', $row->id) }}"
               class="text-yellow"
               data-method="post"
               data-confirm-title="Iniciar Sessão Remota"
               data-confirm-class="btn-success"
               data-confirm-label="Iniciar Sessão"
               data-confirm="Pretende iniciar sessão como {{ $row->display_name }}?"
               target="_blank">
                <i class="fas fa-fw fa-sign-in-alt"></i> @trans('Iniciar Sessão')
            </a>
        </li>
        <div class="divider"></div>
        @endif
        <li>
            <a href="{{ route('admin.customers.departments.destroy', [$row->customer_id, $row->id]) }}"
               data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
    </ul>
</div>