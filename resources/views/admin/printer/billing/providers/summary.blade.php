<?php $documentTotal = $totalCost = 0; ?>
<div>
    @if($shipments)
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt;">
        <tr>
            <th>Envio</th>
            <th class="w-80px">TRK Fornecedor</th>
            <th style="width: 10px">Serv.</th>
            {{--<th>Referência</th>--}}
            <th>Remetente</th>
            <th>Destinatário</th>
            <th>Remessa</th>
            <th>Cobrança</th>
            <th class="w-80px">Obs.</th>
            <th class="w-45px">A pagar</th>
        </tr>
        <?php
            $countTotal = 0;
        ?>
        @foreach($shipments as $shipment)
            <?php
            $countTotal++;
            $totalCost+= $shipment->cost_price + $shipment->total_expenses_cost;
            ?>
            <tr>
                <td>
                    <b class="bold">{{ $shipment->tracking_code }}</b><br/>
                    <i>{{ $shipment->date }}</i>
                </td>
                <td>
                    {{ $shipment->provider_tracking_code }}
                </td>
                <td class="text-center">
                    {{ @$shipment->service->display_code }}<br/>
                    {{ strtoupper($shipment->recipient_country) }}
                </td>
                {{--<td>
                    @if($shipment->provider_id != 3)
                        {{ $shipment->reference }}
                    @endif
                </td>--}}
                <td>{{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>
                <td>{{ $shipment->recipient_name }}
                    <br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
                <td>
                    {{ $shipment->volumes }} vol.<br/>
                    @if($shipment->weight)
                    {{ $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight }} kg
                    @else
                        -- kg
                    @endif
                </td>
                <td>{{ $shipment->charge_price > 0.00 ? money($shipment->charge_price, Setting::get('app_currency')) : '' }}</td>
                <td>{{ $shipment->obs }}</td>
                <td class="text-right">
                    {{ money($shipment->cost_price + $shipment->total_expenses_cost, Setting::get('app_currency')) }}
                </td>
            </tr>
        @endforeach
    </table>
    <div style="width: 100%">
        <h4 class="pull-right text-right m-t-0" style="width: 85%">
            <small>N.º Env./Rec: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <small>Env./Rec.: <b class="bold" style="color: #000;">{{ money($totalCost, Setting::get('app_currency')) }}</b></small>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <small>Total:</small>
            <b class="bold">{{ money($totalCost, Setting::get('app_currency')) }}</b>
        </h4>
    </div>
    <div class="clearfix"></div>
    @endif

    <hr class="m-b-10 m-t-10"/>
    <h3 class="text-right m-t-0">
        <small>Total a Pagar:</small>
        <b class="bold">{{ money($totalCost, Setting::get('app_currency')) }}</b>
    </h3>
</div>