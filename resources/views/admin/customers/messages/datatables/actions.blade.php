<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.customers.messages.edit', $row->id) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-lg">
        @trans('Editar')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.customers.messages.show', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-fw fa-users"></i> @trans('Ver Destinatários')
            </a>
        </li>
        @if($row->send_email)
        <li>
            <a href="{{ route('admin.customers.messages.show', [$row->id, 'list' => 'emails']) }}"
               data-toggle="modal" d
               data-target="#modal-remote">
                <i class="fas fa-fw fa-envelope"></i> @trans('Ver E-mails Enviados')
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('admin.customers.messages.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
    </ul>
</div>
