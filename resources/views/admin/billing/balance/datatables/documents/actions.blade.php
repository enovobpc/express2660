@if($row->sense == 'debit' && !in_array($row->doc_serie, ['SIND']) && !in_array($row->doc_type, ['regularization']))
<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.billing.balance.invoice', [$row->id]) }}" class="btn btn-sm btn-default" target="_blank">
        <i class="fas fa-print"></i> Imprimir
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções Extra</span>
    </button>
    <ul class="dropdown-menu pull-right">
        @if(!$row->is_paid)
            @if(Route::currentRouteName() == 'admin.billing.balance.datatable.balance')
                <li>
                    <a href="{{ route('admin.invoices.index', ['customer' => $row->customer_id, 'doc_type' => 'receipt', 'tab' => 'receipts']) }}" target="_blank">
                        <i class="fas fa-fw fa-file"></i> Ir para página Recibos
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('admin.invoices.receipt.create', ['customer' => $row->customer_id, 'doc'=>$row->doc_id,'serie' => $row->doc_serie_id]) }}"
                       class="text-green"
                       data-toggle="modal"
                       data-target="#modal-remote-xl">
                        <i class="fas fa-fw fa-file"></i> Emitir Recibo
                    </a>
                </li>
            @endif
        @endif
        {{-- <li>
            <a href="{{ route('admin.billing.balance.email.invoice.edit', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote">
                <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
            </a>
        </li> --}}

        {{-- @if($row->is_hidden)
            <li>
                <a href="{{ route('admin.billing.balance.hide', [$row->customer_id, $row->id]) }}"
                   class="text-green"
                   data-method="post"
                   data-confirm="Pretende reativar este registo das contas correntes?"
                   data-confirm-title="Reativar registo da conta corrente"
                   data-confirm-label="Reativar"
                   data-confirm-class="btn-success">
                    <i class="fas fa-fw fa-eye"></i> Reativar Registo
                </a>
            </li>
        @else
            <li>
                <a href="{{ route('admin.billing.balance.hide', [$row->customer_id, $row->id]) }}"
                   class="text-red"
                   data-method="post"
                   data-confirm="Pretende ocultar este registo das contas correntes?"
                   data-confirm-title="Ocultar registo da conta corrente"
                   data-confirm-label="Ocultar"
                   data-confirm-class="btn-danger">
                    <i class="fas fa-fw fa-eye-slash"></i> Ocultar Registo
                </a>
            </li>
        @endif --}}
    </ul>
</div>
@elseif($row->doc_type == 'receipt' || $row->doc_type == 'credit-note')
    <div class="btn-group btn-table-actions">
        <a href="{{ route('admin.billing.balance.invoice', [$row->id]) }}" class="btn btn-sm btn-default" target="_blank">
            <i class="fas fa-print"></i> Imprimir
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            {{-- <li>
                <a href="{{ route('admin.billing.balance.email.invoice.edit', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                </a>
            </li> --}}
        </ul>
    </div>
@endif