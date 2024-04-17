@if($row->sense == 'credit')
    @if($row->is_settle)
        <b data-total="{{ $row->total }}" class="text-green">
            {{ money($row->total, $row->currency) }}
        </b>
    @else
        <b data-total="{{ $row->total }}" class="text-red">
            {{ money($row->total, $row->currency) }}
        </b>
    @endif
@endif