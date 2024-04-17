<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.cpanel.emails.login', $row->id) }}" class="btn btn-sm btn-default" target="_blank">
        <i class="fas fa-external-link-alt"></i> @trans('Iniciar Sessão')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <i class="fas fa-cog"></i> @trans('Opções') <span class="caret"></span>
        <span class="sr-only">@trans('Opções Extra')</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.cpanel.emails.forwarders.edit', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-xs">
                <i class="fas fa-share-square"></i> @trans('Redirecionamentos')
            </a>
        </li>
        <li>
            <a href="{{ route('admin.cpanel.emails.autoresponders.edit', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-reply"></i> @trans('Resposta Automática')
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.cpanel.emails.edit', $row->id) }}"
            data-toggle="modal"
            data-target="#modal-remote-xs">
                <i class="fas fa-pencil-alt"></i> @trans('Editar Conta ou Password')
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.cpanel.emails.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção da conta e-mail selecionada? <br/><small>Esta operação é irreversível e irá eliminar todos os e-mails.</small>"

               class="text-red">
                <i class="fas fa-trash-alt"></i> @trans('Eliminar Conta')
            </a>
        </li>
    </ul>
</div>