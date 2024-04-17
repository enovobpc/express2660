<div>
    <i class="fas fa-fw fa-circle text-{{ $row->getStockLabel() }}"></i> {{ $row->stock_total }}

    @if ($row->unities_by_pack)
        <br />
        <small class="italic">Total: {{ $row->stock_total * $row->unities_by_pack }}</small>
    @endif
</div>
