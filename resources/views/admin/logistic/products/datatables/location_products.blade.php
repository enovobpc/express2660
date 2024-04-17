@foreach($row->products as $product)
    <div class="m-b-3">
    <span class="label label-default"
          data-toggle="tooltip"
          title="Stock: {{ $product->stock_total }} {{ $product->unity ? trans('admin/logistic.products.unities.' . $product->unity) : '' }}">
        {{ $product->barcode }}
    </span>&nbsp;&nbsp;{{ $product->name }}
    </div>
@endforeach