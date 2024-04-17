<?php
$shippingDate = new Date($shipment->shipping_date);
$deliveryDate = new Date($shipment->delivery_date)
?>
<div class="adhesive-label" style="width: 96mm; height: 103mm; padding: 1.5mm">
    <div class="adhesive-row" style="border-bottom: 2px solid #000">
        <div class="adhesive-block" style="width: 67.5mm; height: 12mm; text-align: left;">

        </div>
        <div class="adhesive-block" style="width: 22mm; height: 12mm; margin-bottom:8px; margin-right: -10px; text-align: left;">
            <img style="height: 22mm" src="data:image/png;base64,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"/>
            <div style="margin-top: -15px; font-size: 7px">&nbsp;&nbsp;&nbsp;Contrato {{ $shipment->provider_cargo_agency }}</div>
        </div>
    </div>
    <div class="adhesive-row" style="">
        <div class="adhesive-block" style="width: 72.8mm; height: 12mm; text-align: left;">
            <barcode code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="0.80" height="1.2" style="margin-left: -4mm; margin-bottom: 3px"/>
            <h3 style="margin: 0; font-weight: bold; width: 165px; font-size: 20px; float: left;">{{ $shipment->tracking_code }}</h3>
            <h3 style="margin: 0; font-weight: bold; width: 100px; font-size: 20px; text-align: right">{{ zipcodeCP4($shipment->recipient_zip_code) }}</h3>
        </div>
        <div class="adhesive-block" style="width: 12.7mm; text-align: left;">
            <img src="{{ @$qrCode }}" height="75"/>
        </div>
    </div>
    {{-- <div class="adhesive-row" style="height: 9mm; margin-top: 5px">
         <div class="adhesive-block" style="width: 100mm; margin: 0px 0; text-align: center; border: 2px solid #000; color: #fff">
             <h3 style="margin: 0; font-size: 20px; font-weight: bold; color: #000; padding: 4px 0">{{ $shipment->recipientAgency->print_name }}</h3>
         </div>
     </div>--}}
    <div class="adhesive-row" style="border-bottom: 1px solid #000; border-top: 1px solid #000">
        <div class="adhesive-block" style="text-align: left; width: 20%; font-size: 6pt;">
            EXPEDIDOR
        </div>
        <div class="adhesive-block" style="text-align: left; width: 73.5%; font-size: 8pt; text-align: right">
            <span style="font-weight: bold">
                NIF: {{ $shipment->sender_vat ? $shipment->sender_vat : '999999990' }}
            </span>
        </div>
        <div class="adhesive-block" style="margin-top: -4px">
            <p style="font-size: 12px; width: 100%; margin-bottom: 5px">
                @if($shipment->sender_attn)
                    <span>A/C: {{ $shipment->sender_attn }}</span><br/>
                @endif
                <b style="font-weight: bold">{{ $shipment->sender_name }}</b><br/>
                {{ $shipment->sender_address }}<br/>
                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
            </p>
            <p style="font-size: 7pt; width: 69%; float: left; margin: 0">
                Local carga: {{ substr($shipment->sender_city, 0, 30) }}
            </p>
            <p style="font-size: 7pt; width: 30%; float: right; margin: 0; text-align: right;">
                Data: <span style="font-weight: bold">{{ $shippingDate->format('Y-m-d H:i') }}</span>
            </p>
        </div>
    </div>
    <div class="adhesive-row" style="border-bottom: 1px solid #000">
        <div class="adhesive-block" style="text-align: left; width: 20%; font-size: 6pt; margin: 0">
            DESTINATÁRIO
        </div>
        <div class="adhesive-block" style="text-align: left; width: 73.5%; font-size: 8pt; text-align: right; margin: 0">
            @if($shipment->recipient_phone)
                <span>Tlf: {{ $shipment->recipient_phone }} | </span>
            @endif
            <span style="font-weight: bold">NIF: {{ $shipment->recipient_vat ? $shipment->recipient_vat : '999999990' }}</span>
        </div>
        <div class="adhesive-block" style="margin-top: -5px;">
            <p style="font-size: 16px; line-height: 18px; width: 100%; margin: 0; height: 24mm;">
                @if($shipment->recipient_attn)
                    <span style="font-size: 11px">A/C: {{ $shipment->recipient_attn }}</span><br/>
                @endif
                <b style="font-weight: bold">{{ substr($shipment->recipient_name, 0, 33) }}<br/>
                    {{ $shipment->recipient_address }}<br/>
                    {{ $shipment->recipient_zip_code }} {{ substr($shipment->recipient_city, 0, 28) }}
                </b>
            </p>
            <p style="font-size: 7pt; width: 69%; float: left; margin: 0">
                Local Descarga: {{ substr($shipment->recipient_city, 0, 29) }}
            </p>
            <p style="font-size: 7pt; width: 30%; float: right;  margin: 0">
                Data: {{ $deliveryDate-> format('Y-m-d H:i') }}
            </p>
        </div>
    </div>

    <div class="adhesive-row" style="height: 15mm;">
        <div class="adhesive-block" style="text-align: left; width: 48%; font-size: 6pt; margin: 0">
            DECLARAÇÃO/INSTRUÇÕES DESTINATÁRIO
        </div>
        <div class="adhesive-block" style="text-align: left; width: 47%;font-size: 10pt; margin-top: 15px; margin-bottom: -20px; text-align: right; font-weight: bold">
            @if(config('app.source') !== "entregaki")
            {{ $shipment->weight }}KG |
            @endif
            {{ @$shipment->service->display_code }}
        </div>

        <div class="adhesive-block" style="width: 100mm; font-size: 12px">
            @if($shipment->reference)
                Ref: {{ $shipment->reference }}<br/>
            @endif
            <div style="margin-top: 5px">
                {{ $shipment->obs }}
            </div>
        </div>
        <div class="adhesive-block" style="height: 5mm; font-size: 10px">
            @if($shipment->charge_price != 0.00)
                <div style="float: left; width: 90px; border: 1px solid #000;  padding: 2px">
                    <span class="guide-payment" style="width: 21mm; font-size: 12px; background: #fff; color: #000; font-weight: bold ">
                        € REEMBOLSO
                    </span>
                    {{--&nbsp;
                    {{ $shipment->charge_price }}EUR--}}
                </div>
            @endif

            @if($shipment->cod == 'D')
                <div style="float: left; width: 110px; border: 1px solid #000;  padding: 2px; margin-left: 5px">
                    <span class="guide-payment" style="width: 21mm; font-size: 12px; background: #fff; color: #000; font-weight: bold ">
                        PORTES DESTINO
                    </span>
                    {{--&nbsp;
                    {{ $shipment->billing_subtotal }}EUR--}}
                </div>
            @endif

            @if(!empty($shipment->has_return))
                @if(in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return))
                    <div style="float: left; width: 62px; border: 1px solid #000;  padding: 2px; margin-left: 5px">
                        <span class="guide-payment" style="width: 21mm; font-size: 12px; background: #fff; color: #000; font-weight: bold ">
                            RETORNO
                        </span>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="adhesive-row">
        <div class="adhesive-block" style="width: 50mm; font-size: 20px; margin-bottom: 4px">
            @if($shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
                <img src="{{ asset($shipment->agency->filepath_black) }}" style="height: 50px; max-width: 51mm;" class="margin-left"/>
            @elseif($shipment->agency->filepath && File::exists($shipment->agency->filepath))
                <img src="{{ asset($shipment->agency->filepath) }}" style="height: 50px; max-width: 51mm;" class="margin-left"/>
            @else
                <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
            @endif
        </div>
        <div class="adhesive-block" style="width: 35mm; text-align: right; float: right">
            <h1 style="margin: 0; font-weight: bold; ">
                {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
            </h1>
        </div>
        <div class="adhesive-block" style="width: 100mm; font-size: 7pt; line-height: 7pt">
            TRANSPORTADOR:
            {{ $shipment->agency->company }}. NIF:{{ $shipment->agency->vat }}
            @if($shipment->agency->charter)
            &bull; Alvará {{ $shipment->agency->charter }}
            @endif
            {{ $shipment->agency->address }}
            {{ $shipment->agency->zip_code }} {{ $shipment->agency->city }}
            Tlf: {{ $shipment->agency->phone }}
        </div>
    </div>
</div>
