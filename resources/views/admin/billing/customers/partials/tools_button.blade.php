<div class="btn-group btn-group-sm" role="group">
    <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-wrench"></i> Ferramentas <i class="fas fa-angle-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="text-green" href="#" data-toggle="modal" data-target="#modal-update-prices">
                    <i class="fas fa-sync-alt"></i> Atualizar todos os preços
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="#" data-toggle="modal" data-target="#modal-mass-print">
                    <i class="fas fa-print"></i> Imprimir Resumos em Massa
                </a>
            </li>
            <li>
                <a href="{{ route('admin.printer.billing.customers.shipments.month-values', ['month' => $month, 'year' => $year, 'period' => $period]) }}" id="print-billing-summary" target="_blank">
                    <i class="fas fa-print"></i> Imprimir Valores a Faturar
                </a>
            </li>
            <li>
                <a href="{{ route('admin.export.billing', ['month' => $month, 'year' => $year, 'period' => $period] + Request::all()) }}"
                   target="_blank"
                   data-toggle="export-url">
                    <i class="fas fa-file-excel"></i> Exportar para Excel
                </a>
            </li>
            @if(config('app.source') == 'papiro')
            <li>
                <a href="#" data-toggle="modal" data-target="#modal-export-sap">
                    <i class="fas fa-file-alt"></i> Exportar para SAP
                </a>
            </li>
            @endif
        </ul>
    </div>

    <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
        <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
    </button>
</div>