<?php
$currency = Setting::get('app_currency');
?>
<style>
    .tblabel {
        width: 53px;
        font-weight: bold;
        padding: 3px;
        text-align: right;
        font-size: 10px;
    }

    .tbinput {
        border: 0.5px solid #555;
        padding: 5px 2px;
        font-size: 10px;
        border-radius: 4px;
    }
</style>
<div style="padding: 5px 2px; margin-bottom: 5px;  font-size: 11px; margin-top: -20px; margin-left: -10px">
    <div style="width: 22%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Origem</td>
                <td class="tbinput">{{ strtoupper($manifest->start_location) }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Destino</td>
                <td class="tbinput">{{ strtoupper($manifest->end_location) }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 23%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Motorista</td>
                <td class="tbinput">{{ @$manifest->operator->name }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Acomp.</td>
                <td class="tbinput">&nbsp;{{ @$manifest->assistants_names }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 15%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Viatura</td>
                <td class="tbinput">{{ $manifest->vehicle }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Reboque</td>
                <td class="tbinput">{{ $manifest->trailer ? $manifest->trailer : '&nbsp;' }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 19%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Dt Início</td>
                <td class="tbinput">{{ $manifest->pickup_date ? $manifest->pickup_date->format('Y-m-d') . ' ' . $manifest->start_hour : '&nbsp;'  }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Km Início</td>
                <td class="tbinput">{{ $manifest->start_km ? (int) $manifest->start_km : '&nbsp;' }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 20%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Data Fim</td>
                <td class="tbinput">
                    {{ $manifest->delivery_date ? $manifest->delivery_date->format('Y-m-d') . ' ' . $manifest->end_hour : '&nbsp;'  }}
                </td>
            </tr>
        </table>
            <table style="width: 100%">
                <tr>
                    <td class="tblabel">Km Fim</td>
                    <td class="tbinput"> {{ $manifest->end_km ? (int) $manifest->end_km : '&nbsp;' }}</td>
                </tr>
            </table>
    </div>
</div>
<table class="table table-bordered table-pdf font-size-7pt" style="margin-bottom: 10px">
    <tr>
        <th style="width: 40px"></th>
        <th style="width: 50px">TRK</th>
        <th style="width: 80px">Referência</th>
        <th>Origem</th>
        <th>Destino</th>
        <th style="width: 80px">Mercadoria</th>
        <th style="width: 60px">Entrega</th>
    </tr>
    <?php $documentTotal = 0; $countTotal = 0; $billingSubtotal = 0; $billingVat = 0; $billingTotal = 0; ?>
    @foreach($manifest->shipments as $shipment)
        <?php
        $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code);
        $qrCode->disableBorder();
        $output = new \Mpdf\QrCode\Output\Png();
        $qrCode = 'data:image/png;base64,'.base64_encode($output->output($qrCode, 30));

        $billingSubtotal+= $shipment->billing_subtotal;
        $billingVat+= $shipment->billing_vat;
        $billingTotal+= $shipment->billing_total;
        ?>
        <tr>
            <td style="padding: 0">
                <img src="{{ $qrCode }}"/>
            </td>
            <td>
                <strong style="font-weight: bold; font-size: 12px">{{ $shipment->tracking_code }}</strong><br/>
                {{ $shipment->date }}
            </td>
            <td>
                @if($shipment->reference)
                    {{ $shipment->reference }}<br/>
                @endif
                @if($shipment->reference2)
                {{ $ref2name }} {{ $shipment->reference2 }}
                @endif
            </td>
            <td>
                <strong style="font-weight: bold">{{ $shipment->sender_name }}</strong><br/>
                <small>
                    {{ $shipment->sender_address }}<br/>
                    {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                </small>
            </td>
            <td>
                <strong style="font-weight: bold">{{ $shipment->recipient_name }}</strong><br/>
                <small>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                </small>
            </td>
            <td>
                @if(is_array($shipment->packaging_type) && !empty($shipment->packaging_type))
                    @foreach($shipment->packaging_type as $type => $qty)
                        <div>{{ $qty }} {{ @$packTypes[$type] }}</div>
                    @endforeach
                @elseif($shipment->volumes)
                    @if($shipment->conferred_volumes)
                        <div class="bold" data-toggle="tooltip" title="Conferido pelo operador: {{ $shipment->conferred_volumes }}">{{ $shipment->volumes }} Vol.</div>
                    @else
                        <div>{{ $shipment->volumes }} Vol.</div>
                    @endif
                @else
                    <div class="text-muted">--- Vol.</div>
                @endif
                <div>{{ $shipment->weight }} KG</div>
            </td>
            <td>
                {{ $shipment->delivery_date->format('d-m-Y') }}
                @if($shipment->end_hour)
                <br/>
                <small>Até {{ $shipment->end_hour }}</small>
                @endif
                @if($shipment->charge_price)
                COD: {{ money($shipment->charge_price, Setting::get('app_currency')) }}
                @endif
            </td>
        </tr>
    @endforeach
</table>

<div style="width: 300px; height: 9mm;  float: right; border: 1px solid #ddd; padding: 2px 5px; border-radius: 3px">
    <table style="text-align: left; float: right; display: block">
        <tr>
            <td>
                <h5 style="width: 100px; padding-right: 15px">
                    <small>Guias</small><br/>
                    <span style="font-weight: bold">{{ @$manifest->shipments->count() }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding-right: 15px">
                    <small>Volumes</small><br/>
                    <span style="font-weight: bold">{{ @$manifest->shipments->sum('volumes') }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding-right: 15px">
                    <small>Cobrança</small><br/>
                    <span style="font-weight: bold">{{ money(@$manifest->shipments->sum('charge_price'), Setting::get('app_currency')) }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding-right: 15px">
                    <small>LDM</small><br/>
                    <span style="font-weight: bold">{{ money(@$manifest->shipments->sum('ldm'), 'm') }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding: 0">
                    <small>Peso</small><br/>
                    <span style="font-weight: bold">{{ money(@$manifest->shipments->sum('weight'), 'kg', 0) }}</span>
                </h5>
            </td>
        </tr>
    </table>
</div>
