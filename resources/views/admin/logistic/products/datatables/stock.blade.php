@if($row->stock_status == 'blocked')
    <span class="text-red">
    <i class="fas fa-fw fa-ban text-red"></i> {{ $row->stock_total }}
        <small class="text-uppercase">{{ $row->unity ? trans('admin/global.measure-units-abbrv.'.$row->unity) : __('UN')  }}</small>
    </span>
    <span class="label bg-red">@trans('Bloqueado')</span>
@else
    @if(($row->stock_total - $row->stock_allocated) <= $row->stock_min)
        <span data-toggle="tooltip" title="@trans('Stock Minímo:') {{  $row->stock_min }}">
            <i class="fas fa-fw fa-circle text-{{ $row->getStockLabel() }}"></i> {{ $row->stock_total - $row->stock_allocated }}
            <small class="text-uppercase">{{ $row->unity ? trans('admin/global.measure-units-abbrv.'.$row->unity) : __('UN')  }}</small>
        </span>
    @else
    <i class="fas fa-fw fa-circle text-{{ $row->getStockLabel() }}"></i> {{ $row->stock_total - $row->stock_allocated }}
    <small class="text-uppercase">{{ $row->unity ? trans('admin/global.measure-units-abbrv.'.$row->unity) : __('UN')  }}</small>
    @endif
@endif
@if($row->stock_allocated)
    <div>
        <small class="text-muted">@trans('Aloc:')' {{ $row->stock_allocated }} {{ $row->unity ? trans('admin/global.measure-units-abbrv.'.$row->unity) : __('UN')  }}</small>
    </div>
@endif
{{--
@if($row->stock_min)
<div>
    <small class="text-muted">Min {{ $row->stock_min }} {{ $row->unity ? trans('admin/global.measure-units-abbrv.'.$row->unity) : 'UN'  }}</small>
</div>
@endif--}}
