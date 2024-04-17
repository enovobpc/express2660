@if($row->sense == 'debit')
    @if($row->doc_type == 'payment-note')
        <b data-total="-{{ $row->total }}" class="text-muted">
            {{ money($row->total * -1, $row->currency) }}
        </b>
    @else
        <b data-total="-{{ $row->total }}" class="text-muted">
            {{ money($row->total * -1, $row->currency) }}
        </b>
    @endif
@endif