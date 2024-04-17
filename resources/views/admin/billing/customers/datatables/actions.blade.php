<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.billing.customers.show', [$row->id, 'month' => $row->month, 'year' => $row->year, 'period' => $period]) }}" class="btn btn-sm btn-default">
            Detalhe
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.printer.billing.customers.shipments.summary', [$row->id, 'month' => $row->month, 'year' => $row->year, 'period' => $period, 't' => time()]) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Resumo Total
                </a>
            </li>
            <li>
                <a href="{{ route('admin.export.billing.customers.shipments', [$row->id, 'month' => $row->month, 'year' => $row->year, 'period' => $period]) }}">
                    <i class="fas fa-fw fa-file-excel"></i> Exportar Resumo Total
                </a>
            </li>
            <li>
                <a href="{{ route('admin.billing.customers.email.edit', [$row->id, 'month' => $row->month, 'year' => $row->year, 'period' => $period]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-fw fa-envelope"></i> Enviar Resumo por E-mail
                </a>
            </li>
            @if($total > 0.00)
            <li role="separator" class="divider"></li>
            <li>
                <a href="{{ route('admin.billing.customers.shipments.update-prices', [$row->id, $row->month, $row->year]) }}" class="text-blue"
                   data-method="post"
                   data-confirm="Confirma a atualização de preços?<br/><br/><small class='text-red m-t-10px'><i class='fas fa-exclamation-triangle'></i> Envios com preços superiores ao calculado, não serão alterados.<br/><i class='fas fa-exclamation-triangle'></i> Não serão calculados preços para envios com pagamento no destino.</small>"
                   data-confirm-title="Confirmar atualização de preços"
                   data-confirm-label="Atualizar"
                   data-confirm-class="btn-success">
                    <i class="fas fa-fw fa-sync-alt"></i> Atualizar Preço Envios
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>