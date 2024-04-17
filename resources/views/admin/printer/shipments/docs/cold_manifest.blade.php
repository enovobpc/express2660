<?php
$docCountTotal   = 0;
$docTotalWeight  = 0;
$docTotalVolumes = 0;
$docTotalCharges = 0;
$countPaymentRecipient = 0;
$totalPaymentRecipient = 0;
?>
<div>
    <div style="border: 1px solid #ccc; background: #ddd; width: 100%; padding: 2mm 3mm; margin-bottom: 30px; border-radius: 3px">
        <div style="float: left; width: 33%">
            <p style="margin: 0; font-size: 14px">Temperatura: <span style="font-weight: bold">{{ money($temperature) }}ºC</span></p>
        </div>
        <div style="float: left; width: 33%">
            <p style="margin: 0; font-size: 14px">Humidade: <span style="font-weight: bold">{{ money($humidity) }}%</span></p>
        </div>
        <div style="float: left; width: 33%; text-align: right">
            <p style="margin: 0; font-size: 14px">Data/Hora: <span style="font-weight: bold">{{ date('Y-m-d H:i') }}</span></p>
        </div>

    </div>
    @if($customers)
        @foreach($customers as $customeId => $shipments)
            @if($groupByCustomer)
                <h4>{{ @$shipments->first()->customer->code }} - {{ @$shipments->first()->customer->name }}</h4>
            @endif
            <table class="table table-bordered table-pdf m-b-3" style="font-size: 6.3pt;">
                <tr>
                    <th class="w-20px"></th>
                    <th>Envio</th>
                    <th style="width: 30px">Serv.</th>
                    {{--<th>Carga</th>--}}
                    <th>Descarga</th>
                    <th class="w-50px">Remessa</th>
                    <th class="w-50px">Cobrança</th>
                    <th style="width: 100px">Referência</th>
                    {{--<th class="w-50px">Portes</th>--}}
                    {{--<th class="w-100px">Obs.</th>--}}
                </tr>
                <?php
                $countTotal  = 0;
                $totalWeight = 0;
                $totalVolumes = 0;
                $totalCharges = 0;
                $countCharges = 0;
                ?>
                @foreach($shipments as $shipment)
                    <?php
                    $countTotal++;
                    $totalWeight+= $shipment->weight;
                    $totalVolumes+= $shipment->volumes;
                    $countCharges+= $shipment->charge_price > 0.00 ? 1 : 0;
                    $totalCharges+= $shipment->charge_price > 0.00 ? $shipment->charge_price : 0;
                    $countPaymentRecipient+= $shipment->total_price_for_recipient > 0.00 ? 1 : 0;
                    $totalPaymentRecipient+= $shipment->total_price_for_recipient > 0.00 ? $shipment->total_price_for_recipient : 0;

                    $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code);
                    $qrCode->disableBorder();
                    $output = new \Mpdf\QrCode\Output\Png();
                    $qrCode = 'data:image/png;base64,'.base64_encode($output->output($qrCode, 49));
                    ?>
                    <tr>
                        <td style="padding: 0">
                            <img src="{{ $qrCode }}" style="height: 28px; position: absolute"/>
                        </td>
                        <td style="width: 120px; font-size: 8pt" class="text-center">
                            <div style="display: inline-block">
                                <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.8" height="0.5"/>
                            </div>
                            <b style="font-weight: bold">{{ $shipment->tracking_code }}</b>
                            - {{ $shipment->delivery_date ? $shipment->delivery_date : $shipment->date }}
                        </td>
                        <td class="text-center">
                            {{ @$shipment->service->display_code }}<br/>
                            {{ strtoupper($shipment->recipient_country) }}
                        </td>
                        {{--<td>{{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>--}}
                        <td>{{ $shipment->recipient_name }}<br/>
                            {{ $shipment->recipient_address }}<br/>
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
                        <td class="text-center">
                            {{ $shipment->volumes }} Vol.<br/>
                            {{ $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight }}kg
                        </td>
                        <td>{{ $shipment->charge_price > 0.00 ? money($shipment->charge_price, Setting::get('app_currency')) : '' }}</td>
                        <td>
                            {{ $shipment->reference }}<br/>
                            @if(Setting::get('shipments_reference2_visible') && $shipment->reference2 && config('app.source') != 'intercourier')
                                <br/><b class="bold">{{ Setting::get('shipments_reference2_name') }}</b> {{ $shipment->reference2 }}
                            @endif
                        </td>
                        {{--<td>{{ $shipment->total_price_for_recipient > 0.00 ? money($shipment->total_price_for_recipient, Setting::get('app_currency')) : '' }}</td>--}}
                    </tr>
                @endforeach
            </table>
            <div style="width: 100%">
                <h4 class="pull-right text-right m-t-0" style="width: 85%">
                    <small>Expedições: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <small>Volumes: <b class="bold" style="color: #000;">{{ $totalVolumes }}</b></small>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <small>Portes: <b class="bold" style="color: #000;">{{ money($totalPaymentRecipient, Setting::get('app_currency')) }} ({{ $countPaymentRecipient }})</b></small>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <small>Cobranças: <b class="bold" style="color: #000;">{{ money($totalCharges, Setting::get('app_currency')) }} ({{ $countCharges }})</b></small>
                </h4>
            </div>
            <div class="clearfix"></div>
            <hr class="m-b-10 m-t-10"/>
            <?php
            $docCountTotal+=$countTotal;
            $docTotalWeight+=$totalWeight;
            $docTotalVolumes+=$totalVolumes;
            $docTotalCharges+=$totalCharges + $totalPaymentRecipient;
            ?>
        @endforeach
        <div style="width: 25%; float: left; padding: 3px; text-align: center">
            <div style="border: 1px solid #ddd;">
                Data e Hora de Carga<br/><br/>
                ______________________________
            </div>
        </div>
        <div style="width: 25%; float: left; padding: 3px; text-align: center">
            <div style="border: 1px solid #ddd;">
                Assinatura<br/><br/>
                ______________________________
            </div>
        </div>
        <div style="width: 48%; float: left; margin-top:5px">
            <h4 class="pull-right text-right m-t-0">
                <small>Total Expedições: <b class="bold" style="color: #000;">{{ $docCountTotal }}</b></small>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <small>Total Volumes: <b class="bold" style="color: #000;">{{ $docTotalVolumes }}</b></small>
                <br/>
                <small>Valor Total: <b class="bold" style="color: #000;">{{ money($docTotalCharges, Setting::get('app_currency')) }}</b></small>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <small>Peso Total: <b class="bold" style="color: #000;">{{ money($docTotalWeight, 'kg') }}</b></small>
            </h4>
        </div>
    @endif
</div>