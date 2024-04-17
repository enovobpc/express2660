<div class="text-center">
    <div class="btn-group">
        {{--<a href="{{ route('admin.billing.balance.sync.all', $row->id) }}" class="btn btn-sm btn-default btn-update-balance" data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> Aguarde">
            <i class="fas fa-sync-alt"></i> Atualizar
        </a>--}}
        <a href="{{ route('admin.billing.balance.show', $row->id) }}"
           class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-file-alt"></i> Detalhe
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            {{--<li>
                <a href="{{ route('admin.billing.balance.show', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-file"></i> Ver detalhe
                </a>
            </li>--}}
            <li>
                <a href="{{ route('admin.invoices.receipt.create', ['customer' => $row->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-receipt"></i> Emitir Recibo
                </a>
            </li>
            <li>
                <a href="{{ route('admin.billing.balance.email.balance.edit', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.printer.invoices.balance', $row->id) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Conta Corrente
                </a>
            </li>
            <li>
                <a href="{{ route('admin.printer.invoices.customers.maps', ['unpaid', 'customer' => $row->id]) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Pendentes
                </a>
            </li>
            <li>
                <a href="{{ route('admin.printer.invoices.customers.maps', ['unpaid', 'customer' => $row->id, 'expired' => 1]) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Vencidos
                </a>
            </li>

            <li>
                <a href="{{ route('admin.customers.balance.reset', $row->id) }}"
                   data-method="post"
                   data-confirm-title="Reset Conta Corrente"
                   data-confirm-class="btn-success"
                   data-confirm-label="Recarregar"
                   data-confirm="Confirma a eliminação da conta corrente e o seu carregamento de novo?">
                    <i class="fas fa-fw fa-eraser"></i> Reset Conta Corrente
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.customers.edit', [$row->id, 'tab' => 'balance']) }}" target="_blank">
                    <i class="fas fa-fw fa-info-circle"></i> Ir Ficha Cliente
                </a>
            </li>
        </ul>
    </div>
</div>
