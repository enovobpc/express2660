@if(@$grouped)
    <div class="btn-group btn-table-actions">
        <a href="{{ route('admin.refunds.customers.show', [$row->customer_id, 'type' => 'devolved']) }}" class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            Detalhe
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.refunds.customers.edit', [$row->customer_id, 'grouped' => '1']) }}"
                    data-toggle="modal"
                    data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-check-circle"></i> Marcar como Devolvido
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.printer.refunds.customers.proof', ['customer', 'customer' => $row->customer_id]) }}" target="_blank">
                    <i class="fas fa-fw fa-receipt"></i> Comprovativo Reembolso
                </a>
            </li>
            <li>
                <a href="{{ route('admin.printer.refunds.customers.summary', ['customer' => $row->customer_id]) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Resumo
                </a>
            </li>
            <li>
                <a href="{{ route('admin.export.refunds.customers', ['customer' => $row->customer_id]) }}" target="_blank">
                    <i class="fas fa-fw fa-file-excel"></i> Exportar Resumo
                </a>
            </li>
        </ul>
    </div>
@else
<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.refunds.customers.edit', $row->id) }}" class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-lg">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções Extra</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.change-log.show', ['RefundControl', $row->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-fw fa-history"></i> Histórico de Edições
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.printer.refunds.customers.proof', $row->id) }}" target="_blank">
                <i class="fas fa-fw fa-receipt"></i> Comprovativo Reembolso
            </a>
        </li>
        @if (config('app.source') === 'invictacargo')
        <li>
            <a href="{{ route('admin.printer.shipments.transport-guide', $row->id) }}" target="_blank">
                <i class="fas fa-fw fa-print"></i> Guia de Transporte
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('admin.refunds.customers.destroy', $row->id) }}"
               data-method="delete"
               data-confirm="Pretende cancelar este reembolso?">
                <i class="fas fa-fw fa-times"></i> Cancelar Reembolso
            </a>
        </li>
    </ul>
</div>
@endif