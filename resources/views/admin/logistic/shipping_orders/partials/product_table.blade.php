<table class="table table-condensed m-b-0 m-t-10" style="background: #fff">
    <tr class="bg-gray">
        <th class="w-140px">@trans('SKU')</th>
        <th>@trans('Artigo')</th>
        <th class="w-180px">@trans('Lote/Nº Série')</th>
        <th class="w-110px">@trans('Localização')</th>
        <th class="w-1">@trans('Max')</th>
        <th class="w-65px">@trans('Qtd')</th>
        <th class="w-65px">@trans('Preço')</th>
        <th class="w-1"></th>
    </tr>
    <tbody>
        @if($shippingOrder->exists)
            @foreach($shippingOrder->lines as $line)
                <tr>
                    <td class="vertical-align-middle">
                        <a href="{{ route('admin.logistic.products.show', $line->product_id) }}" target="_blank">
                            {{ @$line->product->sku }}
                        </a>
                        {{ Form::hidden('product_id', $line->product_id) }}
                        {{ Form::hidden('line_id', $line->line_id) }}
                    </td>
                    <td class="vertical-align-middle">{{ @$line->product->name }}</td>
                    <td class="vertical-align-middle">{{ @$line->product->serial_no ? @$line->product->serial_no : @$line->product->lote }}</td>
                    <td class="vertical-align-middle">{{ @$line->location->code }}</td>
                    <td class="vertical-align-middle text-center">{{ @$line->product_location->stock_available + $line->qty }}</td>
                    <td class="vertical-align-middle">
                        {{ Form::text('product_qty[]', $line->qty, [
                        'class' => 'form-control input-sm text-center number',
                        'maxlength' => 6,
                        'placeholder' => __('Max ') . (@$line->product_location->stock_available + $line->qty),
                        'data-qty' => @$line->qty,
                        'data-max' => (@$line->product_location->stock_available + $line->qty),
                        'data-url' => route('admin.logistic.shipping-orders.product.update', [$line->shipping_order_id, $line->id])
                        ]) }}
                        <small class="text-red qty-helper" style="display: none">Max. {{ (@$line->product_location->stock + $line->qty) }}</small>
                    </td>
                    <td class="vertical-align-middle">
                        {{ Form::text('product_price[]', $line->price, ['class' => 'form-control input-sm text-center decimal']) }}
                    </td>
                    <td class="vertical-align-middle">
                        <a href="{{ route('admin.logistic.shipping-orders.product.remove', [$line->shipping_order_id, $line->id]) }}" class="text-red btn-delete-product">
                            <i class="fas fa-times"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="tr-empty">
                <td colspan="6">
                    <p class="p-10 text-center">
                        <i class="fas fa-info-circle"></i> @trans('Não há artigos no pedido.')'
                    </p>
                </td>
            </tr>
        @endif
    </tbody>
</table>