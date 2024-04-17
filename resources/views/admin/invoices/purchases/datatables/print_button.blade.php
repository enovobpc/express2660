@if($row->doc_type == 'payment-note')
    <a href="{{ route('admin.invoices.purchase.payment-notes.download', [$row->doc_id, 'reference' => $row->reference, 'provider' => $row->provider_id]) }}"
       class="btn btn-sm btn-default"
       target="_blank">
        <i class="fas fa-print"></i> Imprimir
    </a>
@else
    <a href="{{ route('admin.invoices.purchase.download', [$row->id]) }}"
       class="btn btn-sm btn-default"
       target="_blank">
        <i class="fas fa-print"></i> Imprimir
    </a>
@endif