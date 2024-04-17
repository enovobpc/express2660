@if ($row->has_stock)
    @if ($row->stock_total > 0)
        <span>
            <i class="fas fa-circle fs-12 text-green"></i> {{ $row->stock_total }} {{ $row->unity }}
        </span>
    @else
        <span>
            <i class="fas fa-circle fs-12 text-red"></i> {{ $row->stock_total }} {{ $row->unity }}
        </span>
    @endif
@endif
