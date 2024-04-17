<table class="table table-condensed m-b-0 table-products">
    <tr class="bg-gray">
        <th style="width: 120px">SKU</th>
        <th>@trans('Produto')</th>
        <th>@trans('Lote/N.º Série')</th>
        <th class="w-100px">@trans('Localização')</th>
        <th class="w-60px">@trans('Pedido')</th>
        <th class="w-60px">@trans('Qtd Sat.')</th>
    </tr>
    <?php $count = $qty = 0; ?>
    @foreach($shippingOrder->lines as $line)
        <?php 
        $count++;
        $qty+= @$line->qty; 
        ?>

        <tr class="{{ $line->qty_satisfied < $line->qty ? 'rw-red' : 'rw-green' }}"
            data-product="{{ @$line->product_id }}"
            data-location="{{ @$line->location_id }}"
            data-location-code="{{ @$line->location->code }}"
            data-qty="{{ @$line->qty }}"
            data-sku="{{ @$line->product->sku }}"
            data-satisfied="{{ @$line->qty_satisfied }}">
            <td>{{ @$line->product->sku }}</td>
            <td>{{ @$line->product->name }}</td>
            <td>{{ @$line->product->lote ? @$line->product->lote : @$line->product->serial_no }}</td>
            <td>{{ @$line->location->code }}</td>
            <td class="text-center">{{ @$line->qty }}</td>
            <td class="bold text-center">
                {{ Form::text('qty['.$line->id.']', @$line->qty_satisfied ? $line->qty_satisfied : 0, ['class' => 'form-control qty-fld']) }}
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="3"></td>
        <td>{{ $count }} artigos</td>
        <td class="text-center">{{ $qty }} Un.</td>
        <td></td>
    </tr>
</table>