<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.customers.edit', $row->id) }}" class="btn btn-sm btn-default">
        @trans('Editar')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">@trans('Opções Extra')</span>
    </button>
    <ul class="dropdown-menu pull-right">
        @if($row->password)
            <li>
                <a href="{{ route('admin.customers.remote-login', $row->id) }}" class="text-yellow"
                   data-method="post" data-confirm-title="Iniciar Sessão Remota"
                   data-confirm-class="btn-success"
                   data-confirm-label="Iniciar Sessão"
                   data-confirm="Pretende iniciar sessão como {{ $row->display_name }}?"
                   target="_blank">
                    <i class="fas fa-fw fa-sign-in-alt"></i> @trans('Iniciar Sessão')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.login-log.show', ['customers', $row->id]) }}" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-history"></i> @trans('Histórico de Acessos')
                </a>
            </li>
            <li class="divider"></li>
        @endif
        <li>
            @if($row->is_active)
            <a href="{{ route('admin.customers.inactivate', $row->id) }}"
               data-method="post"
               data-confirm-title="Inativar Cliente"
               data-confirm-class="btn-success"
               data-confirm-label="Inativar"
               data-confirm="Pretende inativar o cliente e esconde-lo desta lista? Pode voltar a ativar o cliente a qualquer momento.">
                <i class="fas fa-fw fa-user-times"></i> @trans('Inativar Cliente')
            </a>
            @else
            <a href="{{ route('admin.customers.inactivate', $row->id) }}"
               data-method="post"
               data-confirm-title="Ativar Cliente"
               data-confirm-class="btn-success"
               data-confirm-label="Ativar"
               data-confirm="Pretende voltar a ativar o cliente e envia-lo de novo para a lista de clientes?">
                <i class="fas fa-fw fa-user-check"></i> @trans('Ativar Cliente')
            </a>
            @endif
        </li>
        <li>
            <a href="{{ route('admin.customers.convert.prospect', $row->id) }}"
               data-method="post"
               data-confirm-title="Converter em potencial cliente"
               data-confirm-class="btn-success"
               data-confirm-label="Converter"
               data-confirm="Pretende conveter o cliente em potencial cliente?">
                <i class="fas fa-fw fa-user-tie"></i> @trans('Converter em Prospect')
            </a>
        </li>
        <li>
            <a href="{{ route('admin.change-log.show', ['Customer', $row->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-fw fa-history"></i> @trans('Histórico de Edições')
            </a>
        </li>
        <div class="divider"></div>
        <li>
            <a href="{{ route('admin.printer.customers.sepa', $row->id) }}" target="_blank">
                <i class="fas fa-print"></i> @trans('Autorização Débito Direto')
            </a>
        </li>
        @if(hasModule('account_wallet') && hasPermission('gateway_payments'))
            <div class="divider"></div>
            <li>
                <a href="{{ route('admin.gateway.payments.wallet.edit', ['customer' => $row->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xs">
                    <i class="fas fa-wallet"></i> @trans('Gerir Saldo Conta')
                </a>
            </li>
        @endif
        @if(!$row->final_consumer)
            <div class="divider"></div>
            <li>
                <a href="{{ route('admin.customers.destroy', $row->id) }}"
                   data-method="delete"
                   data-confirm="Confirma a remoção do registo selecionado?"
                   class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
                </a>
            </li>
        @endif
    </ul>
</div>