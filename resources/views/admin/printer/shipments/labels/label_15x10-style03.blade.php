<?php
$shippingDate = new Date($shipment->shipping_date);
$deliveryDate = new Date($shipment->delivery_date)
?>
{{--<div style="position:absolute; bottom: 2mm; right: 24mm">
    <div class="adhesive-block" style="height: 5mm;">
        <h1 style="margin: 0px; padding: 0;font-weight: bold;">
            {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
        </h1>
    </div>
</div>
<div style="position:absolute; bottom: 2mm;">
    <div class="adhesive-block" style="height: 5mm;">
        <h3 style="margin: 0px; padding: 0;font-weight: bold;">
            {{ $shipment->weight }}kg
        </h3>
    </div>
</div>--}}
<div style="position:absolute; top: 2mm; right: 3mm">
    <img src="{{ @$qrCode }}" height="65"/>
</div>
<div style="position:absolute; font-size: 6.5pt; rotate: -90; right: 2mm; margin-top: 18mm; font-weight: bold">
    @if(config('app.source') == 'moovelogistica')
        @if($shipment->reference)
            <barcode code="{{ $shipment->reference }}" type="C128A" size="0.83" height="2.2" style="margin-left: -4mm; margin-bottom: 3px"/>
        @endif
    @else
        <barcode code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="0.83" height="2.2" style="margin-left: -4mm; margin-bottom: 3px"/>
    @endif
</div>
<div class="adhesive-label" style="width: 120mm; height: 120mm;">
    <div class="adhesive-row">
        <div class="adhesive-block" style="width: 56mm; font-size: 20px; margin-bottom: 4px; margin-top: 10px">
            @if($shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
                <img src="{{ asset($shipment->agency->filepath_black) }}" style="height: 35px; max-width: 56mm;" class="margin-left"/>
            @elseif($shipment->agency->filepath && File::exists($shipment->agency->filepath))
                <img src="{{ asset($shipment->agency->filepath) }}" style="height: 35px; max-width: 56mm;" class="margin-left"/>
            @else
                <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
            @endif
        </div>
        <div class="adhesive-block" style="width: 50mm; text-align: right; float: right">
            <h3 style="margin: 0; font-weight: bold; width: 400px; float: left;">{{ $shipment->tracking_code }}</h3>
            <p style="margin: 0">{{ $shippingDate->format('Y-m-d') }}</p>
        </div>
    </div>

    <div class="adhesive-row" style="height: 9mm; padding-top: 3px; border-top: 2px solid #000; margin-top: 5px">
        <div class="adhesive-block" style="width: 15mm; height: 12.5mm; border: 1px solid #000; background: #000; text-align: center">
            <div style="margin: 0; font-size: 18px; font-weight: bold; padding-top: 4px; color: #fff;">
                {{--<div style="font-weight: normal; font-size: 12px; color: #fff; margin-bottom: 4px;">Zona</div>--}}
                <div style="font-weight: normal; font-size: 12px; color: #fff; margin-bottom: 4px;">
                    {{--@if(@$shipment->route->code)--}}
                    @if(($shipment->recipient_country == 'pt' || $shipment->recipient_country == 'es') && @$shipment->route->code)
                        {{ @$shipment->route->code }}
                    @else
                        @if($shipment->recipient_country == 'pt')
                            NAC
                        @elseif($shipment->recipient_country == 'es')
                            ESP
                        @else
                            INT
                        @endif
                    @endif
                </div>
                {{ zipcodeCP4($shipment->recipient_zip_code) }}
            </div>
        </div>
        <div class="adhesive-block" style="width: 99mm; height: 12.5mm; text-align: left; border: 0px solid #000;">
            {{--<div style="margin: 0; font-size: 27px; font-weight: bold; color:#000;  padding-top: 17px; padding-left: 5px; float: left">{{ $shipment->recipientAgency->print_name }}</div>--}}
            <div style="margin: 0; font-size: 27px; text-transform: uppercase; font-weight: bold; color:#000;  padding-top: 17px; padding-left: 5px; float: left">
                @if(($shipment->recipient_country == 'pt' || $shipment->recipient_country == 'es') && @$shipment->route->code)
                {{--@if(@$shipment->route->code)--}}
                    {{ @$shipment->route->name }}
                @else
                    @if($shipment->recipient_country == 'pt')
                        @if($shipment->sender_agency_id != $shipment->recipient_agency_id)
                            {{ $shipment->recipientAgency->print_name }}
                        @else
                            Expedição Nacional
                        @endif
                    @elseif($shipment->recipient_country == 'es')
                        Expedição Espanha
                    @else
                        Internacional
                    @endif
                @endif
            </div>
        </div>
    </div>
    <div class="adhesive-row" style="border-top: 2px solid #000; margin-top: 3px; padding-top: 5px">
        <div class="adhesive-block" style="width: 75mm;">
            <div class="adhesive-row">
                <div class="adhesive-block" style="height: 10mm; width: 15mm; border: 1px solid #000;; margin-left: -3px">
                    <h4 style="margin: 0px; padding: 0;font-weight: bold; padding-top: 4px; text-align: center">
                        {{ @$shipment->service->display_code }}<br/>
                        <small style="font-weight: normal; font-size: 9px; color: #000">SERVIÇO</small>
                    </h4>
                </div>
                @if ($shipment->cod == 'D' || $shipment->cod == 'S')
                <div class="adhesive-block" style="height: 10mm; width: 15mm; border: 1px solid #000; margin-left: 3px">
                    <h4 style="margin: 0px; padding: 0;font-weight: bold; padding-top: 4px; text-align: center">
                        COD<br/>
                        <small style="font-weight: normal; font-size: 9px; color: #000">{{ $shipment->cod == 'D' ? 'PORTES DT' : 'PORTES RC' }}</small>
                    </h4>
                </div>
                @endif
                @if($shipment->charge_price != 0.00)
                <div class="adhesive-block" style="height: 10mm; width: 15mm; border: 1px solid #000; margin-left: 3px">
                     <h4 style="margin: 0px; padding: 0;font-weight: bold; padding-top: 4px; text-align: center">
                         REMB<br/>
                         <small style="font-weight: normal; font-size: 9px; color: #000">{{ money($shipment->charge_price, 'EUR') }}</small>
                     </h4>
                 </div>
                @endif
                @if(!empty($shipment->has_return))
                    @if(in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return))
                    <div class="adhesive-block" style="height: 10mm; width: 15mm; border: 1px solid #000; margin-left: 3px">
                        <h4 style="margin: 0px; padding: 0;font-weight: bold; padding-top: 4px; text-align: center">
                            RET<br/>
                            <small style="font-weight: normal; font-size: 9px; color: #000">RETORNO</small>
                        </h4>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        <div class="adhesive-block" style="width: 40mm;">
            <div class="adhesive-row">
                <div class="adhesive-block">
                    <h1 style="margin: 0px; padding: 0;font-weight: bold; text-align: right">
                        {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <div class="adhesive-row" style="border-top: 0px solid #000; margin-top: 3px">
        <div class="adhesive-block" style="text-align: left; width: 65%; font-size: 6pt;">
            EXPEDIDOR: <br/>
            <div style="font-weight: bold; font-size: 11px">
                {{ $shipment->sender_name }}
            </div>
            <div style="font-weight: bold; font-size: 11px">{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</div>
        </div>
        <div class="adhesive-block" style="text-align: left; line-height: 20px; width: 30%; text-align: right; margin-top: -10px; font-size: 6pt;">
            <div style="font-weight: bold; font-size: 16px">{{ $shipment->weight }}kg</div>
            <div style="font-size: 12px">
                @if($shipment->reference)
                    Ref: {{ $shipment->reference }}<br/>
                @endif
            </div>
        </div>
    </div>
    <div class="adhesive-row" style="border-bottom: 0px solid #000; height: 26mm">
        <div class="adhesive-block" style="text-align: left; width: 20%; font-size: 6pt; margin-bottom: -5px">
            DESTINATÁRIO
        </div>
        <div class="adhesive-block" style="text-align: left; width: 75.5%; font-size: 9pt; text-align: right; margin-top: -2px">
            @if($shipment->recipient_phone)
            <span>Tlf: <span style="font-weight: bold">{{ $shipment->recipient_phone }}</span></span>
            @endif
        </div>
        <div class="adhesive-block" style="margin-top: -5px;">
            <p style="font-size: 16px; line-height: 18px; width: 100%; margin: 0; height: 20mm;">
                @if($shipment->recipient_attn)
                    <span style="font-size: 11px">A/C: {{ $shipment->recipient_attn }}</span><br/>
                @endif
                <b style="font-weight: bold">{{ substr($shipment->recipient_name, 0, 50) }}<br/>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ substr($shipment->recipient_city, 0, 28) }}
                </b>
            </p>
        </div>
    </div>

    @if($shipment->obs)
    <div class="adhesive-row" style="height:12mm; border-top: 2px solid #000;">
        <div class="adhesive-block" style="width: 140mm; font-size: 12px">
            <div style="margin-top: 5px">
                {{ $shipment->obs }}
            </div>
        </div>
    </div>
    @endif
</div>