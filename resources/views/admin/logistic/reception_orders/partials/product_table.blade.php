<table class="table table-condensed m-b-0 m-t-10" style="background: #fff">
    <tr class="bg-gray">
        <th class="w-140px">@trans('SKU')</th>
        <th>@trans('Artigo')</th>
        <th class="w-180px">@trans('Lote/Nº Série')</th>
        {{--<th class="w-110px">Localização</th>
        <th class="w-1">Max</th>--}}
        <th class="w-90px">@trans('Qtd Receber')</th>
        <th class="w-80px">@trans('Preço')</th>
        <th class="w-1"></th>
    </tr>
    <tbody>
        @if($receptionOrder->exists)
            @foreach($receptionOrder->lines as $line)
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
                    {{--<td class="vertical-align-middle">{{ @$line->location->code }}</td>--}}
                    {{--<td class="vertical-align-middle text-center">{{ @$line->product_location->stock }}</td>--}}
                    <td class="vertical-align-middle">
                        {{ Form::text('product_qty[]', $line->qty, [
                        'class' => 'form-control input-sm text-center number',
                        'maxlength' => 6,
                        'data-qty' => @$line->qty,
                        'data-url' => route('admin.logistic.reception-orders.product.update', [$line->reception_order_id, $line->id])
                        ]) }}
                    </td>
                    <td class="vertical-align-middle">
                        {{ Form::text('product_price[]', $line->price, ['class' => 'form-control input-sm text-center decimal']) }}
                    </td>
                    <td class="vertical-align-middle">
                        <a href="{{ route('admin.logistic.reception-orders.product.remove', [$line->reception_order_id, $line->id]) }}" class="text-red btn-delete-product">
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