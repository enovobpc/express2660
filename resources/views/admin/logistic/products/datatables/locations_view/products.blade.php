@foreach($row->products as $product)
    <div class="m-b-3">
        <span class="label label-default" data-toggle="tooltip" title="Stock: {{ $product->stock_total }} {{ $product->unity ? trans('admin/global.measure-units.' . $product->unity) : '' }}">{{ $product->sku }}</span> {{ $product->name }}
    </div>
@endforeach