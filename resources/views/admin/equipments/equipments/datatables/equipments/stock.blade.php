@if($row->stock_status == 'blocked')
    <span class="text-red">
    <i class="fas fa-fw fa-ban text-red"></i> {{ $row->stock_total }}
    </span>
    <span class="label bg-red">Bloqueado</span>
@else
    @if($row->stock_total <= 0)
        <i class="fas fa-fw fa-circle text-red"></i> {{ $row->stock_total ? $row->stock_total : '0.00' }}
        <i class="fas fa-exclamation-triangle hide"></i>
    @elseif($row->stock_total <= $row->stock_min)
        <span data-toggle="tooltip" title="Stock Minímo: {{ @$row->stock_min }}">
            <i class="fas fa-fw fa-circle text-yellow"></i> {{ $row->stock_total }}
        </span>
    @else
        <i class="fas fa-fw fa-circle text-green"></i> {{ $row->stock_total }}
    @endif
@endif