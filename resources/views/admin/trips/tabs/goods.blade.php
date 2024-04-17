@if($dimensions->isEmpty())
    <div class="m-t-200 m-b-200 text-center">
        <h3 class="text-muted">
            <i class="fas fa-pallet fs-40"></i><br/>
            @trans('Sem mercadoria registada')
        </h3>
        <p class="text-muted">
            @trans('Nenhum dos serviços deste mapa tem informação sobre dimensões ou mercadoria transportada.')
        </p>
    </div>
@else
    <a href="{{ route('admin.trips.print', [$trip->id, 'goods']) }}"
       class="btn btn-sm btn-default pull-left"
       target="_blank">
        <i class="fas fa-print"></i>  @trans('Manifesto Mercadoria')
    </a>
    <div class="clearfix"></div>
    <table class="table table-shipments table-condensed table-hover table-dashed m-t-10" style="border: 0">
        <thead>
        <tr>
            <th class="bg-gray w-90px">TRK</th>
            <th class="bg-gray"> @trans('Mercadoria')</th>
            <th class="bg-gray w-100px"> @trans('Tipo')</th>
            <th class="bg-gray w-1"> @trans('Qtd')</th>
            <th class="bg-gray w-70px"> @trans('Comprim.')</th>
            <th class="bg-gray w-70px"> @trans('Largura')</th>
            <th class="bg-gray w-70px"> @trans('Altura')</th>
            <th class="bg-gray w-70px"> @trans('Peso') Kg</th>
            <th class="bg-gray w-70px"> @trans('M3')</th>
        </tr>
        </thead>
        <tbody class="sortable">
        <?php
        $lastShipmentId = null;
        $totalQty     = 0;
        $totalWeight  = 0;
        $totalWidth   = 0;
        $totalLength  = 0;
        $totalHeight  = 0;
        $totalM3      = 0;
        ?>
        @foreach($dimensions as $key => $dimension)
            <?php
            $totalQty+= $dimension->qty;
            $totalWeight+= $dimension->weight;
            $totalWidth+= $dimension->width;
            $totalLength+= $dimension->length;
            $totalHeight+= $dimension->height;
            $totalM3+= $dimension->volume;
            ?>
            <tr style="line-height: 15px;" class="{{ $dimension->shipment_id != $lastShipmentId ? 'border-divisor' : '' }}">
                <td style="line-height: 15px;">
                    <a href="{{ route('admin.shipments.show', $dimension->shipment_id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-xl"
                       class="bold">
                        {{ @$dimension->shipment->tracking_code }}
                    </a>
                </td>
                <td>{{ $dimension->description }}</td>

                <td>{{ @$dimension->packType->name }}</td>
                <td class="text-center">{{ $dimension->qty }}</td>
                <td class="text-right">{{ money($dimension->width) }}</td>
                <td class="text-right">{{ money($dimension->length) }}</td>
                <td class="text-right">{{ money($dimension->height) }}</td>
                <td class="text-right">{{ money($dimension->weight) }}</td>
                <td class="text-right">{{ money($dimension->volume) }}</td>
            </tr>
            <?php $lastShipmentId = $dimension->shipment_id ?>
        @endforeach
        </tbody>
        <tfooter>
            <tr>
                <td colspan="3" style="border: none" class="text-right bold">
                    Total
                </td>
                <td class="text-center bold">{{ $totalQty }}</td>
                <td class="text-right bold">{{ money($totalWidth) }}</td>
                <td class="text-right bold">{{ money($totalLength) }}</td>
                <td class="text-right bold">{{ money($totalHeight) }}</td>
                <td class="text-right bold">{{ money($totalWeight) }}</td>
                <td class="text-right bold">{{ money($totalM3) }}</td>
            </tr>
        </tfooter>
    </table>
@endif