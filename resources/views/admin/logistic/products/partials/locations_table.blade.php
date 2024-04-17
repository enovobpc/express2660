<table class="table" id="locations-table table-dashed">
    <tr class="bg-gray-light">
        <th class="w-150px">@trans('Código Barras')</th>
        <th>@trans('Localização')</th>
        <th>@trans('Armazém')</th>
        <th class="w-70px text-center">@trans('Disponível')</th>
        <th class="w-70px text-center">@trans('Alocado')</th>
        <th class="w-70px text-center">@trans('Total')</th>
        <th class="w-110px">@trans('Inserido em')</th>
        <th class="w-110px">@trans('Ultimo Mov.')</th>
        <th class="w-1"></th>
    </tr>
    <?php
    $totalAvailable = $totalAllocated = $totalStock = 0;
    ?>
    @foreach($product->locations as $location)
        <?php
        $totalAvailable+= @$location->pivot->stock_available;
        $totalAllocated+= @$location->pivot->stock_allocated;
        $totalStock+= @$location->pivot->stock
        ?>
        <tr>
            <td>{{ @$location->pivot->barcode }}</td>
            <td class="bold"><i class="fas fa-square" style="color: {{ $location->color }}"></i> {{ $location->code }}</td>
            <td>{{ @$location->warehouse->name }}</td>
            <td class="text-center">
                <i class="fas fa-fw fa-circle text-{{ $product->getStockLabel(@$location->pivot->stock_available) }}"></i>
                <b>{{ @$location->pivot->stock_available }}</b>
            </td>
            <td class="text-center">{{ @$location->pivot->stock_allocated }}</td>
            <td class="text-center">{{ @$location->pivot->stock }}</td>
            <td>{{ $location->created_at->format('Y-m-d') }}</td>
            <td>{{ $location->updated_at->format('Y-m-d') }}</td>
            <td>
                <a href="{{ route('admin.logistic.products.stock.transfer', [$product->id, $location->id]) }}"
                   class="btn btn-xs bg-blue"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-fw fa-exchange-alt"></i> @trans('Transferir')
                </a>
            </td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td></td>
        <td class="text-right bold">@trans('Total')</td>
        <td class="text-center bold">{{ $totalAvailable }}</td>
        <td class="text-center bold">{{ $totalAllocated }}</td>
        <td class="text-center bold">{{ $totalStock }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>