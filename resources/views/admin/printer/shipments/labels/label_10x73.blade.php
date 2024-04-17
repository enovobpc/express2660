<div style="position: absolute; left: 0mm; width: 96mm; right: 4mm; bottom: 2.5mm; text-align: left;">
    <div class="adhesive-block" style="float: left; width: 69mm;">
        <barcode style="margin-top: 7mm" code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="0.75" height="1.57"/>
    </div>
    <div class="adhesive-block" style="float: left; width: 26mm;">
        <div style="font-weight: bold; border: 1px solid #000; padding: 1px; text-align: center; float: right; width: 15mm; margin-bottom: 10px">{{ @$shipment->service->display_code }}</div>
        <div style="float: left; width: 26mm; margin-top: 3px">
            <p style="margin: 0 0 6px; text-align: right">{{ $shipment->weight }}kg</p>
            <h4 style="margin: 0px; padding: 0; font-weight: bold; font-size: 25px; text-align: right; margin-top: -1.5mm">
                {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
            </h4>
        </div>
    </div>
</div>

<div style="width: 95.4mm; height: 69.7mm; float: left;">
    <div class="adhesive-row">
        <div class="adhesive-block" style="width: 54mm; height: 12mm; float: left; text-align: left;">
            @if($shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
                <img src="{{ asset($shipment->agency->filepath_black) }}" style="height: 33px; max-width: 50mm; margin-top: 1mm" class="margin-left"/>
            @elseif($shipment->agency->filepath && File::exists($shipment->agency->filepath))
                <img src="{{ asset($shipment->agency->filepath) }}" style="height: 33px; max-width: 50mm; margin-top: 1mm" class="margin-left"/>
            @else
                <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
            @endif
        </div>
        {{--<div class="adhesive-block" style="width: 56mm; height: 10mm; float: left; text-align: right; margin-right: -3mm">
            <barcode code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="0.65" height="1" style="margin-right: -4mm"/>
            <div style="font-size: 12px; font-weight: bold; margin-right: -5px">
                <div style="float: left; text-align: left; width: 145px; letter-spacing: 2px">{{ $shipment->tracking_code }}</div>
                <div style="float: left; width: 65px;">{{ $shipment->date }}</div>
            </div>
        </div>--}}
        <div class="adhesive-block" style="width:41mm; height: 9mm; float: left; text-align: right; margin-right: -3mm">
            <h3 style="margin: 0; font-weight: bold; font-size: 22px; letter-spacing: 0px">{{ $shipment->tracking_code }}</h3>
            <p style="margin: 0; font-size: 12px">{{ $shipment->date }}</p>
        </div>
    </div>
    <div class="adhesive-ro" style="background: #111; height: 1.5mm; color: #fff; text-align: center; padding: 0">
        <div class="adhesive-bloc">
            <h4 style="font-weight: bold; text-transform: uppercase; font-size: 15px; color: #fff; line-height: 10px; padding: 0">
                @if(@$shipment->route->code)
                    {{ @$shipment->route->code }} - {{ @$shipment->route->name }}
                @else
                    @if($shipment->recipient_country == 'pt')
                        RN - Expedição Nacional
                    @elseif($shipment->recipient_country == 'es')
                        RE - Expedição Espanha
                    @else
                        RI - Expedição Internacional
                    @endif
                @endif
            </h4>
        </div>
    </div>
    <div class="adhesive-row" style="padding-top: 1mm; border-top: 1px solid #000">
        <div class="adhesive-block" style="height: 29mm;">
            <p style="margin-bottom: 0">
                <span style="font-size: 11px;">Remetente: {{ $shipment->sender_name }}</span><br/>
            </p>
            <p style="font-size: 16px; line-height: 18px; width: 100%; margin: 0; height: 20mm;">
                @if($shipment->recipient_attn)
                    <span style="font-size: 11px">A/C: {{ $shipment->recipient_attn }}</span><br/>
                @endif
                <b style="font-weight: bold">{{ substr($shipment->recipient_name, 0, 50) }}</b><br/>
                <span style="font-weight: bold">{{ $shipment->recipient_address }}</span><br/>
                <b style="font-weight: bold">{{ $shipment->recipient_zip_code }} {{ substr($shipment->recipient_city, 0, 28) }} ({{ strtoupper($shipment->recipient_country) }})</b>
            </p>
        </div>
    </div>
    <div class="adhesive-row" style="margin-bottom: -11mm;">
        @if($shipment->charge_price)
            <span style="font-weight: bold">&nbsp;Cobrança {{ $shipment->charge_price }}</span>
            {{ $shipment->reference  ? '&bull;' : ''}}
        @endif
        {{ $shipment->reference }}
    </div>
</div>