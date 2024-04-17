<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.event-manager.edit', $row->id) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-xl">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções Extra</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.event-manager.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> Eliminar
            </a>
        </li>
        @if ($row->is_draft)
            <li>
                <a href=""
                    data-href="{{ route('admin.event-manager.status.update', $row->id) }}" 
                    data-confirm-label="Confirmação"
                    data-confirm-class="btn-success btn-finish"
                    data-title="Finalizar evento"
                    data-body="Confirma a finalização do evento?" 
                    class="text-green confirmationBootBox">
                    <i class="fas fa-fw fa-check confirmationBootBox"></i> Finalizar
                </a>
            </li>
        @endif
    </ul>
</div>