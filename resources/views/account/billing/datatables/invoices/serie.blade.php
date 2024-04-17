{{-- @if($row->sense == 'credit' || $row->doc_serie == "SIND")
    {{ $row->doc_serie }} {{ $row->doc_id }}
@else
    <a href="{{ route('account.billing.invoices.download.invoice', [$row->id, 'key' => $row->api_key]) }}" target="_blank">
        {{ $row->doc_serie }} {{ $row->doc_id }}
    </a>
@endif --}}

<a href="{{ route('account.billing.invoices.download.invoice', [$row->id]) }}" target="_blank">
    {{ $row->name }}
</a>
