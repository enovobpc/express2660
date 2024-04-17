@foreach($row->products as $product)
    <div class="m-b-3">
        <i class="fas fa-fw fa-circle text-{{ $product->getStockLabel() }}"></i> {{ $product->stock_total }}
        <small class="text-uppercase">{{ $product->unity ? trans('admin/global.measure-units-abbrv.'.$product->unity) : 'UN'  }}</small>
    </div>
@endforeach