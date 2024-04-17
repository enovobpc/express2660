<?php
$shippingDate = new Date($shipment->shipping_date);
$deliveryDate = new Date($shipment->delivery_date)
?>
<div class="adhesive-label" style="width: 100mm; height: 103mm;">
    <div class="adhesive-row" style="">
        <div class="adhesive-block" style="width: 75.5mm; height: 12mm; text-align: left;">
            <barcode code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="0.84" height="1.2" style="margin-left: -4mm; margin-bottom: 3px"/>
            <h3 style="margin: 0; font-weight: bold; width: 170px; float: left;">{{ $shipment->tracking_code }}</h3>
            <h3 style="margin: 0; font-weight: bold; width: 112px; text-align: right">{{ zipcodeCP4($shipment->recipient_zip_code) }}</h3>
        </div>
        <div class="adhesive-block" style="width: 16.2mm; text-align: left;">
            <img src="{{ @$qrCode }}" height="65"/>
        </div>
    </div>
    <div class="adhesive-row" style="height: 9mm; margin-top: 5px">
        <div class="adhesive-block" style="width: 100mm; margin: 0px 0; text-align: center; border: 2px solid #000; color: #fff">
            <h3 style="margin: 0; font-size: 20px; font-weight: bold; color: #000; padding: 4px 0">
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
            </h3>
        </div>
    </div>
    <div class="adhesive-row" style="border-bottom: 1px solid #000">
        <div class="adhesive-block" style="text-align: left; width: 20%; font-size: 6pt;">
            EXPEDIDOR
        </div>
        <div class="adhesive-block" style="text-align: left; width: 75.5%; font-size: 8pt; text-align: right">
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
                Local carga: {{ $shipment->sender_city }}
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
        <div class="adhesive-block" style="text-align: left; width: 75.5%; font-size: 8pt; text-align: right; margin: 0">
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
                Local Descarga: {{ $shipment->recipient_city }}
            </p>
            <p style="font-size: 7pt; width: 30%; float: right;  margin: 0">
                Data: {{ $deliveryDate-> format('Y-m-d H:i') }}
            </p>
        </div>
    </div>

    <div class="adhesive-row" style="height: 31mm;">
        <div class="adhesive-block" style="text-align: left; width: 48%; font-size: 6pt; margin: 0">
            DECLARAÇÃO/INSTRUÇÕES DESTINATÁRIO
        </div>
        <div class="adhesive-block" style="text-align: left; width: 47%;font-size: 10pt; margin-top: 15px; margin-bottom: -20px; text-align: right; font-weight: bold">
            {{ $shipment->weight }}KG | {{ @$shipment->service->display_code }}
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

            @if ($shipment->cod == 'D' || $shipment->cod == 'S')
                <div style="float: left; width: 110px; border: 1px solid #000;  padding: 2px; margin-left: 5px">
                    <span class="guide-payment" style="width: 21mm; font-size: 12px; background: #fff; color: #000; font-weight: bold ">
                        {{ $shipment->cod == 'D' ? 'PORTES DESTINO' : 'PORTES REMETENTE' }}
                    </span>
                    {{--&nbsp;
                    {{ $shipment->charge_price }}EUR--}}
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
        <div class="adhesive-block" style="width: 56mm; font-size: 20px; margin-bottom: 4px; margin-top: 10px">
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
            <br/>
            Preço ref. combustível: {{ money(Setting::get('guides_fuel_price'), Setting::get('app_currency')) }}/Litro (n.º4 art. 4-A, DL 239/2003 de 4/10, alterado pelo DL 145/2008 de 28/7).
            Material embalado não conferido.
        </div>
    </div>
</div>