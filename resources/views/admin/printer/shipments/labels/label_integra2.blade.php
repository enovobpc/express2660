<div style="position:absolute; font-size: 8pt; rotate: -90; left: 2mm; margin-top: 28mm; font-weight: bold;">
    <div style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;Remetente</div>
</div>
<div style="position:absolute; font-size: 8pt; rotate: -90; left: 2mm; margin-top: 47mm; font-weight: bold">
    <div style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;Destinatário</div>
</div>
<div style="height: 149mm">
<div class="adhesive-label" style="padding: 10px 20px 10px 10px; color: #000">
    <div>
        <div style="height: 49px; width: 100%; float: left;">
            <div style="font-weight: bold; font-size: 30px; line-height: 20px">
                @if(File::exists(public_path('assets/img/logo/logo_black.png')))
                    <img src="{{ asset('assets/img/logo/logo_black.png') }}" style="height: 38px"/>
                @elseif(File::exists(public_path('assets/img/logo/logo_sm.png')))
                    <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="height: 38px"/>
                @else
                    <div style="background: #000; width: 100%; color: #fff; padding: 10px"> Integra2</div>
                @endif
            </div>
        </div>
    </div>
    <div style="border-bottom: 1px solid #000">
        <div style="width: 45%; float: left;">
            <div style="font-weight: bold; letter-spacing: -1px; font-size: 13px; line-height: 15px; margin-top: 20px">{{ strtoupper($senderAgency['agency_country']) .' ' . $senderAgency['agency_code'] }} &nbsp;&nbsp;{{ $senderAgency['agency_name'] }}</div>
        </div>
        <div style="width: 55%; float: left;">
            <span style="font-size: 12px; line-height: 13px;">{{ strtoupper($recipientAgency['agency_country']) .' ' . $recipientAgency['agency_code'] }}
                &nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;
                {{ $shipment->date }}
            </span>
            <div style="font-weight: bold; letter-spacing: -2px; font-size: 25px; line-height: 27px">{{ $recipientAgency['agency_name'] }}</div>
        </div>
    </div>
    <div>
        <div style="height: 52px; width: 100%; margin-left: 12px; padding: 5px 0 5px 5px; border-left: 1px solid #000; float: left; font-size: 12px; line-height: 13px;">
            <span style="font-size: 14px; font-weight: bold;">{{ $shipment->sender_name }}</span><br/>
            {{ $shipment->sender_address }}<br/>
            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
        </div>
    </div>
    <div style="border-top: 1px solid #000">
        <div style="height: 80px; padding: 5px 0 5px 5px; margin-left: 12px;  width: 100%; float: left; font-size: 13px; line-height: 16px; font-weight: bold;  border-left: 1px solid #000">
            <span style="font-size: 16px">{{ $shipment->recipient_name }}</span><br/>
            {{ $shipment->recipient_address }}<br/>
            <span style="background: #111; color: #fff; font-size: 16px;">
            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
            </span>
            <br/>
            Tlf.:{{ $shipment->recipient_phone }}
        </div>
    </div>
    <div style="border-top: 1px solid #000">
        <div style="height: 45px; width: 60%; float: left; font-size: 13px; line-height: 20px;padding-top: 5px">
            <div style="font-size: 25px; line-height: 25px; font-weight: bold;">AMBIPAQ</div>
            <span style="background: black; padding: 2px 10px; width: 20px; font-size: 14px; color: #fff">
                @if($shipment->price_for_recipient)
                    &nbsp;Portes Debidos&nbsp;
                @else
                    &nbsp;Portes Pagos&nbsp;
                @endif
            </span>
            @if($shipment->charge_price)
                &nbsp;&nbsp;<span style="font-weight: bold">REEMBOLSO</span>
            @endif
            <br/>
            <span style="font-size: 12px;">{{ $shipment->obs }}</span>
        </div>
        <div style="height: 45px; width: 40%; float: left; font-size: 13px; line-height: 20px; text-align: right; padding-top: 2px;">
            <span style="font-size: 16px; font-weight: bold; padding-top: 2px">Ref:: {{ substr($shipment->tracking_code, -9) }}</span><br/>
            <span>Peso: {{ $shipment->weight }}kg</span><br/>
            <span style="font-size: 23px; font-weight: bold">{{ str_pad($volume, 3, '0', STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) }}</span>
        </div>

    </div>
    <div style="margin-left: -15px; margin-top: 10px;">
        <barcode code="{{ $barcode }}" type="C128C" size="1.1" height="2.4" />
    </div>


   {{-- <div style="margin-top: 10px">
        @if($shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
            <img src="{{ asset($shipment->agency->filepath_black) }}" style="height: 55px; max-width: 56mm;" class="margin-left"/>
        @elseif($shipment->agency->filepath && File::exists($shipment->agency->filepath))
            <img src="{{ asset($shipment->agency->filepath) }}" style="height: 55px; max-width: 56mm;" class="margin-left"/>
        @else
            <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
        @endif
    </div>--}}
</div>
</div>