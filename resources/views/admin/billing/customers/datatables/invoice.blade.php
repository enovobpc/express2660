<div class="text-center">
    @foreach($invoices as $invoiceId => $invoice)
        @if($invoiceId)
            <a href="{{ route('admin.invoices.download', [$row->customer_id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['type'],'key' => $invoice['key']]) }}" target="_blank">
                {{ $invoice['name'] }}
            </a>
            <br/>
        @endif
    @endforeach
</div>
