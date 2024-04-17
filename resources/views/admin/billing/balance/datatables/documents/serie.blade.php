@if($row->sense == 'credit' || $row->doc_serie == 'SIND')
    @if($row->doc_type == 'credit-note')
        <a href="{{ route('admin.billing.balance.invoice', [$row->id]) }}" target="_blank">
            {{ $row->doc_serie }} {{ $row->doc_id }}
        </a>
    @else
        <a href="{{ route('admin.billing.balance.invoice', [$row->id]) }}" target="_blank">
            {{ $row->doc_serie }} {{ $row->doc_id }}{{ $row->receipt_part ? '-' . $row->receipt_part : '' }}
        </a>
    @endif
@else
    <a href="{{ route('admin.billing.balance.invoice', [$row->id]) }}" target="_blank">
        {{ $row->doc_serie }} {{ $row->doc_id }}
    </a>
@endif