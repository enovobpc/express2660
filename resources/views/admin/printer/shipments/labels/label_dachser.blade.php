
@if($barcodeInt)
<div style="position:absolute; font-size: 6.5pt; rotate: -90; right: 2mm; margin-top: 78mm; font-weight: bold">
    <div style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;{{ $barcodeIntLabel }}</div>
    <barcode code="{{ $barcodeInt }}" type="C128C" size="0.8" height="1.2" />
</div>
@endif
<div style="position:absolute; font-size: 6pt; rotate: -90; right: 1mm; margin-top: 2mm; font-weight: bold">
    <div style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;IMPRESCINDIBLE EMBALAJE ORIGINAL PARA POSIBLE RECLAMACION</div>
</div>
<div style="position:absolute; font-size: 6.5pt; right: 5mm; margin-top: 18mm; font-weight: bold; text-align: center">
    <div style="background: black; padding: 2px 10px; width: 20px; font-size: 20px; color: #fff">
        @if($shipment->price_for_recipient)
            &nbsp;D&nbsp;
        @else
            &nbsp;P&nbsp;
        @endif
    </div>
</div>
<div style="height: 149mm">
<div class="adhesive-label" style="padding: 10px 20px 10px 10px; color: #000">
    <div>
        <div style="height: 30px; width: 45%; float: left">
            &nbsp;
            @if($barcodeInt)
            <div style="font-size: 40px; padding-top: 35px; text-align: center"><br/>{{ $sourceAgencyCode }}</div>
            @endif
        </div>
        <div style="width: 55%; float: left; margin-bottom: 20px">
            <div style="font-weight: bold; font-size: 30px; line-height: 40px">DACHSER</div>
            <div style="font-size: 15px; height: 30px">{{ $serviceName }}</div>
            <div style="font-size: 18px; font-weight: bold;">{{ $sourceAgencyName }}</div>
        </div>
    </div>
    <div>
        <div style="height: 30px; width: 62%; float: left; font-size: 13px; line-height: 13px;">
            {{ $shipment->sender_name }}<br/>
            {{ $shipment->sender_address }}<br/>
            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
        </div>
        <div style="height: 30px; width: 38%; float: left; font-size: 13px; line-height: 15px; font-weight: bold">
            Exp. {{ $shipment->provider_tracking_code }}<br/><br/>
            Ref.: <span style="font-size: 19px">{{ $shipment->tracking_code }}</span>
        </div>
    </div>
    <div style="border-top: 1px solid #000">
        <div style="height: 30px; padding: 5px 0; width: 73%; float: left; font-size: 13px; line-height: 15px; font-weight: bold;  border-right: 1px solid #000">
            {{ $shipment->recipient_name }}<br/>
            {{ $shipment->recipient_address }}<br/>
            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
        </div>
        <div style="height: 30px; width: 25.5%; float: left; font-size: 13px; line-height: 20px; font-weight: bold; padding: 5px 0">
            &nbsp;C.P. <br/>
            <div style="font-size: 30px; margin-left: 5px">{{ $shipment->recipient_zip_code }}</div>
        </div>
    </div>
    <div style="border-top: 1px solid #000">
        <div style="height: 45px; width: 73%; float: left; font-size: 13px; line-height: 55px; font-weight: bold; border-right: 1px solid #000">
            <div style="font-size: 33px; margin-top: 4px; margin-bottom: 4px">{{ substr($destAgencyName, 0, 12) }}</div>
        </div>
        <div style="height: 45px; width: 25.5%; float: left; font-size: 13px; line-height: 20px; text-align: center;">
            Bultos: <br/>
            <span style="font-size: 30px; font-weight: bold">{{ $volume }}/{{ $shipment->volumes }}</span>
            @if($shipment->charge_price)
                <span style="font-weight: bold">REEMBOLSO</span>
            @endif
        </div>
    </div>
    <div style="border-top: 1px solid #000;">
        <div style="width: 90%">
        @if($shipment->recipient_attn)
            Attn: {{ $shipment->recipient_attn }}<br/>
        @endif
        {{ $shipment->obs }}
        </div>
    </div>
    <div style="margin-left: -15px; margin-top: 10px">
        <div style="float: left; width: 60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $barcodeLabel }}</div>
        <div style="float: left; width: 24%; text-align: right">{{ $shipment->date }}</div>
        <barcode code="{{ $barcode }}" type="C128C" size="1.30" height="2.4" />
    </div>
    <div style="margin-top: 10px">
        {{--@if($shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
            <img src="{{ asset($shipment->agency->filepath_black) }}" style="height: 55px; max-width: 56mm;" class="margin-left"/>
        @elseif($shipment->agency->filepath && File::exists($shipment->agency->filepath))
            <img src="{{ asset($shipment->agency->filepath) }}" style="height: 55px; max-width: 56mm;" class="margin-left"/>
        @else
            <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
        @endif--}}
    </div>
</div>
</div>