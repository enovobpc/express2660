<div class="btn-group btn-table-actions">
    @if(!$row->provider_id)
        <a href="{{ route('admin.billing.providers.show', [$row->provider_id, 'month' => $row->month, 'year' => $row->year]) }}" class="btn btn-sm btn-default w-100px">
            <i class="fas fa-search"></i> Detalhe
        </a>
    @else
        <a href="{{ route('admin.billing.providers.show', [$row->provider_id, 'month' => $row->month, 'year' => $row->year]) }}" class="btn btn-sm btn-default">
            <i class="fas fa-search"></i> Detalhe
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.printer.billing.providers.summary', [$row->provider_id, 'month' => $row->month, 'year' => $row->year]) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Resumo
                </a>
            </li>
            <li>
                <a href="{{ route('admin.export.billing.providers.shipments', [$row->provider_id, 'month' => $row->month, 'year' => $row->year]) }}">
                    <i class="fas fa-fw fa-file-excel"></i> Exportar Resumo
                </a>
            </li>
        </ul>
    @endif
</div>