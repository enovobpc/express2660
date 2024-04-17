@if(@$billedCovenants[$row->id])
    <a href="{{ route('admin.invoices.download', [$row->customer_id, @$billedCovenants[$row->id]['invoice_id'], 'key' => @$billedCovenants[$row->id]['api_key']]) }}" target="_blank" data-toggle="tooltip" title="Esta avença encontra-se faturada na Fatura Mensal">
        <span data-target="tooltip" title="Fatura Gerada">
            <i class="text-blue fas fa-file-alt"></i>
            {{ trans('admin/billing.types_code.' . @$billedCovenants[$row->id]['invoice_type']) }} {{ @$billedCovenants[$row->id]['invoice_id'] }}
        </span>
    </a>
    <br/>
    @if(@$billedCovenants[$row->id]['billing_type'] == 'partial')
    <span class="label" style="background: #24cdff">Fat. Parcial</span>
    @elseif(@$billedCovenants[$row->id]['billing_type'] == 'month')
    <span class="label bg-blue">Fat. Mensal</span>
    @elseif(@$billedCovenants[$row->id]['billing_type'] == 'single')
    <span class="label" style="background: #34495e">Fat. Individual</span>
    @endif

@elseif($row->invoice_id && !$row->invoice_draft)
    <a href="{{ route('admin.invoices.download', [$row->customer_id, $row->invoice_id, 'key' => $row->api_key]) }}" target="_blank" data-toggle="tooltip" title="Esta avença foi faturada individualmente">
        <span data-target="tooltip" title="Fatura Gerada">
            <i class="text-blue fas fa-file-alt"></i> {{ trans('admin/billing.types_code.' . $row->invoice_type) }} {{ $row->invoice_id }}
        </span>
    </a>
    <br/>
    <span class="label" style="background: #34495e">Fat. Individual</span>
@endif