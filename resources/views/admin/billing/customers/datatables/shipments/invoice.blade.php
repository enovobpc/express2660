@if($row->invoice_type == 'nodoc')
    <span data-toggle="tooltip" title="Marcado com faturado sem emissão de documento.">N/A</span><br/>
    @if(@$billedShipments[$row->id]['billing_type'] == 'partial')
        <span class="label" style="background: #24cdff">Fat. Parcial</span>
    @elseif(@$billedShipments[$row->id]['billing_type'] == 'month')
        <span class="label bg-blue">Fat. Mensal</span>
    @elseif(@$billedShipments[$row->id]['billing_type'] == 'single')
        <span class="label" style="background: #34495e">Fat. Individual</span>
    @endif
@elseif($row->invoice_doc_id && $row->invoice_draft)
    <i class="fas fa-file-alt text-yellow"></i>
    {{ trans('admin/billing.types_code.' . $row->invoice_type) }} {{ $row->invoice_doc_id }}
    <span class="label label-warning" data-target="tooltip" title="Rascunho Gerado">
        Rascunho
    </span>
@else
    @if(@$billedShipments[$row->id])
        <a href="{{ route('admin.invoices.download', [$row->customer_id, @$billedShipments[$row->id]['invoice_doc_id'], 'id' => @$billedShipments[$row->id]['invoice_id'], 'type' => @$billedShipments[$row->id]['invoice_type'], 'key' => @$billedShipments[$row->id]['api_key']]) }}" target="_blank" data-toggle="tooltip" title="Este envio encontra-se faturado na Fatura Mensal">
            <span data-target="tooltip" title="Fatura Gerada">
                <i class="text-blue fas fa-file-alt"></i>
                {{ trans('admin/billing.types_code.' . @$billedShipments[$row->id]['invoice_type']) }} {{ @$billedShipments[$row->id]['invoice_doc_id'] }}
            </span>
        </a>
        <br/>
        @if(@$billedShipments[$row->id]['billing_type'] == 'partial')
        <span class="label" style="background: #24cdff">Fat. Parcial</span>
        @elseif(@$billedShipments[$row->id]['billing_type'] == 'month')
        <span class="label bg-blue">Fat. Mensal</span>
        @elseif(@$billedShipments[$row->id]['billing_type'] == 'single')
        <span class="label" style="background: #34495e">Fat. Individual</span>
        @endif

    @elseif($row->invoice_doc_id && !$row->invoice_draft)
        <a href="{{ route('admin.invoices.download', [$row->customer_id, $row->invoice_doc_id, 'id' => $row->invoice_id, 'key' => $row->invoice_key]) }}" target="_blank" data-toggle="tooltip" title="Este envio foi faturado individualmente">
            <span data-target="tooltip" title="Fatura Gerada">
                <i class="text-blue fas fa-file-alt"></i> {{ trans('admin/billing.types_code.' . $row->invoice_type) }} {{ $row->invoice_doc_id }}
            </span>
        </a>
        <br/>
        <span class="label" style="background: #34495e">Fat. Individual</span>
    @endif
@endif