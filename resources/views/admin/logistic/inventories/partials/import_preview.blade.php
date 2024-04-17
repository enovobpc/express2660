<table class="table table-condensed">
    <tr>
        <th class="bg-gray w-1">@trans('SKU')</th>
        <th class="bg-gray">@trans('Artigo')</th>
        <th class="bg-gray w-1">@trans('Localização')</th>
        <th class="bg-gray w-1">@trans('Qtd')</th>
    </tr>
    @foreach($products as $product)
    <tr>
        <td>
            {{ $product['sku'] }}
            <input type="hidden" name="import_ids[]" value="{{ $product['id'] }}"/>
        </td>
        <td>{{ $product['name'] }}</td>
        <td>{{ $product['location'] }}</td>
        <td>{{ $product['stock'] }}</td>
    </tr>
    @endforeach
</table>