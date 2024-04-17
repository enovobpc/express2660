<div style="width: 100mm; height: 150mm; padding: 10px">
    <div style="float: left; width: 35%; padding: 0 20px 0 0px">
        {{--@if(File::exists(public_path('assets/img/logo/logo_black.png')))
            <img src="{{ asset('assets/img/logo/logo_black.png') }}" style="height: 38px"/>
        @elseif(File::exists(public_path('assets/img/logo/logo_sm.png')))
            <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="height: 38px"/>
        @else
            <div style="background: #000; width: 100%; color: #fff; padding: 10px"> Integra2</div>
        @endif--}}
        <img style="height: 60px" src="data:image/png;base64,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"/>
    </div>
    <div style="float: left; width: 57%;">
        <p style="margin: 0; font-size: 12px">
            <span style="font-weight: bold">Org:</span> {{ $senderAgency }}<br/>
            {{ str_limit(strtoupper($shipment->sender_name), 25) }}
        </p>
        <h4 style="margin: 0; font-size: 18px; font-weight: bold;">
            Envio: {{ $shipment->provider_tracking_code }}
        </h4>
    </div>
    <div style="width: 100%; font-size: 30px; letter-spacing: -2px; font-weight: bold; margin-top: 10px;">
        {{ substr($recipientAgency, 0, 20) }}
    </div>
    <div style="width: 100%; text-align: center; padding: 5px 0; border-top: 1px solid #000; border-bottom: 1px solid #000;">
        {{--<barcode code="{{ $barcode }}" type="C128A" size="0.75" height="2.8" pr="2"/>--}}
        <barcode code="{{ $barcode }}" type="C128C" size="1.1" height="1.5" pr="2"/>
        <span style="font-size: 11px">{{ $barcode }}</span>
    </div>
    <div style="width: 100%; padding: 5px 0; border-bottom: 1px solid #000; height: 75px;">
        <div style="font-size: 9px; font-weight: bold;">DESTINATÁRIO</div>
        <div style="text-transform: uppercase; font-size: 14px">
            {{ $shipment->recipient_name }}<br/>
            {{ $shipment->recipient_address }}<br/>
            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
        </div>
        <div style="position: absolute; right: 0; text-align: center; margin-top: -20px; float: right; width: 100px; border: 1px solid #000">
            {{ $route }}
        </div>
    </div>
    <div style="float: left; width: 49.5%; padding: 5px 0 0 0; border-bottom: 1px solid #000; border-right: 1px solid #000; height: 72px; font-size: 11px">
        <span style="font-size: 9px; font-weight: bold; text-transform: uppercase">Observações</span><br/>
        <div style="height: 45.5px;">
            {{ $shipment->obs }}&nbsp;
        </div>
        <div style="background: #333; text-align: center; color: #fff; font-size: 14px; font-weight: bold">
            {{ $serviceName }}&nbsp;
        </div>
        <div style="float: left; width: 29%; padding: 2px 0; text-align: center; font-weight: bold; border-bottom: 1px solid #000; font-size: 10px; border-right: 1px solid #000">
           VOLS
        </div>
        <div style="float: left; width: 24.1%; padding: 2px 0; text-align: center; font-weight: bold; border-bottom: 1px solid #000; font-size: 10px; border-right: 1px solid #000">
            PESO
        </div>
        <div style="float: left; width: 45.6%; padding: 2px 0; text-align: center; font-weight: bold; border-bottom: 1px solid #000; font-size: 10px">
            DATA
        </div>
        <div style="float: left; width: 29%; font-size: 14px; padding: 5px 0; height: 20px; text-align: center; border-right: 1px solid #000">
           {{ $volume }}/{{ $shipment->volumes }}
        </div>
        <div style="float: left; width: 24.1%; font-size: 14px; padding: 5px 0; height: 20px;  text-align: center; border-right: 1px solid #000">
            {{ (int) $shipment->weight }}
        </div>
        <div style="float: left; width: 45.6%; font-size: 14px; padding: 5px 0; height: 20px; text-align: center;">
            {{ $shipment->date }}
        </div>
    </div>
    <div style="float: left; width: 50%; padding: 5px 0 0 0; height: 75px; font-size: 10px;">
        <div style="height:78.6px; border-bottom: 1px solid #000; ">
            &nbsp;&nbsp;<span style="font-weight: bold">TEL:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $shipment->recipient_phone }}<br/>
            &nbsp;&nbsp;<span style="font-weight: bold">CONTACTO:</span> {{ $shipment->recipient_attn }}<br/>
            <div style="text-align: center">
                &nbsp;
            </div>
            <div style="text-align: center">
                &nbsp;
            </div>
            {{--<div style="text-align: center">
                &nbsp;
            </div>--}}
            <div style="text-align: center">
                @if($shipment->charge_price > 0.00)
                    ** REEMBOLSO **
                @else
                    &nbsp;
                @endif
            </div>
        </div>
        <div style="float: left; width: 70%; height: 50.5px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-size: 14px; text-align: center;">
          <div style="width: 100%; padding: 2px 0; text-align: center; font-weight: bold; border-bottom: 1px solid #000; font-size: 10px">
                REFERÊNCIA
            </div>
            <div style="width: 100%; font-size: 14px; padding: 5px 0; height: 20px; text-align: center;">
                {{ $shipment->reference }}
            </div>
        </div>
        <div style="float: left; text-align: right; width: 50px; height: 45.5px; font-size: 26px; padding-top: 5px; text-align: center; font-weight: bold; border-bottom: 1px solid #000;">
           {{ $shipment->payment_at_recipient ? 'D' : 'P' }}
        </div>
    </div>
</div>
