<div class="text-center">
    <div class="btn-group">
        {{--@if($row->balance_total_unpaid > 0.00)
            <a href="{{ route('admin.invoices.purchase.payment-notes.create', ['provider' => $row->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg"
               class="btn btn-sm btn-default">
                <i class="fas fa-check"></i> Pagamento
            </a>
        @else--}}
            <a href="{{ route('admin.billing.balance.show', [$row->id, 'source' => 'providers']) }}"
               class="btn btn-sm btn-default"
               data-toggle="modal"
               data-target="#modal-remote-xl">
                Detalhe
            </a>
        {{--@endif--}}
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.invoices.purchase.payment-notes.create', ['provider' => $row->id]) }}"
                   data-toggle="modal"
                    data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-check"></i> Emitir Nota Pagamento
                </a>
            </li>
            <li>
                <a href="{{ route('admin.billing.balance.show', [$row->id, 'source' => 'providers']) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-list"></i> Ver Conta Corrente
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.printer.invoices.purchase.balance', ['providerId' => $row->id]) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Conta Corrente
                </a>
            </li>
            <li>
                <a href="{{ route('admin.printer.invoices.purchase.map', ['unpaid', 'provider' => $row->id]) }}"  target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Pendentes
                </a>
            </li>
            <li>
                <a href="{{ route('admin.printer.invoices.purchase.listing', [0, 'provider' => $row->id]) }}"  target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Todos Documentos
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.providers.edit', [$row->id, 'tab' => 'purchase-invoices']) }}" target="_blank">
                    <i class="fas fa-fw fa-info-circle"></i> Ir para Ficha Fornecedor
                </a>
            </li>
        </ul>
    </div>
</div>
