{{--@if(in_array(config('app.source'), ['activos24','scandilog']))--}}
<div style="position: absolute; right: 0; top: 2mm">
    <img src="{{ asset('assets/img/logo/logo_black_sm.png') }}" style="width: 50mm; margin-left: -30px;"/>
    <br/>
    <div style="margin-left: -30px; line-height: 15px; font-size: 13px; margin-top: 10px; border: 1px solid #000; padding: 3px; margin-right: 10px">
        <small>
            E-mail: {{ $shipment->agency->email }}<br/>
            Telemóvel: {{ $shipment->agency->mobile }}<br/>
            Website: {{ $shipment->agency->web }}
        </small>
    </div>
</div>
{{--@endif--}}
<div class="adhesive-label" style="padding: 5px 10px 0 10px">
    <div style="width: 100mm; float: left;">
        <div>
            <h4 style="font-weight: bold; margin-top: 0; margin-bottom: 5px; font-size: 15px; width: 60mm; float: left">Origem/Expedidor</h4>
            <div style="float: left; font-size: 15px">{{ $shipment->date }}</div>
            @if(config('app.source') == 'activos24')
                <div>Activos 24, Lda</div>
            @else
                <div>{{ $shipment->agency->company }}</div>
            @endif
        </div>
        <div style="margin-top: 10px;">
            <h4 style="font-weight: bold; margin-top: 0; margin-bottom: 5px; font-size: 15px">Destinatário</h4>
            <div style="margin-bottom: 3px; font-size: 14px">{{ $shipment->recipient_name }}</div>
        </div>
    </div>
    <div style="width: 35mm; float: left; text-align: right">
       {{-- @if(!in_array(config('app.source'), ['activos24','scandilog']))
            <img style="width: 27mm" src="data:image/png;base64,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"/>
        @endif--}}
    </div>
    <div style="width: 150mm; float: left; margin-bottom: 5px">
        <div style="height: 10mm; line-height: 16px; font-size: 14px">
            {{ $shipment->recipient_address }}
        </div>
        <div style="width: 19mm; float: left">
            {{ $shipment->recipient_zip_code }}
        </div>
        <div style="width: 63mm; float: left;">
            {{ str_limit($shipment->recipient_city, 27) }}
        </div>
        <div style="width: 18mm; float: left; font-weight: bold;">
            N.º Vol
        </div>
        <div style="width: 15mm; float: left; font-weight: bold;">
            Peso
        </div>
        <div style="width: 20mm; float: left; font-weight: bold;">
            Cobrança
        </div>
    </div>
    <div style="width: 150mm; float: left">
        <div style="width: 82mm; float: left">
            <span style="font-weight: bold">Tel.:</span> {{ $shipment->recipient_phone }}
        </div>
        <div style="width: 18mm; float: left; font-weight: bold; font-size: 16px">
            {{ $count }} / {{ $shipment->volumes }}
        </div>
        <div style="width: 15mm; float: left; font-weight: bold;">
            {{ money($shipment->weight) }}
        </div>
        <div style="width: 20mm; float: left; font-weight: bold;">
            {{ $shipment->charge_price ? '€' . money($shipment->charge_price) : ''  }}
        </div>
    </div>
    <div style="height: 1px; border-bottom: 1px dashed #000; margin-top: 10px"></div>
    <div style="height: 12mm; font-size: 15px; text-align: center; padding: 10px">
        {{ $shipment->obs }}
    </div>
    <div style="height: 1px; border-bottom: 1px dashed #000; margin-top: 10px"></div>
    <div>
        <h5 style="font-size: 16px; margin-top: 5px">TRK{{ $shipment->tracking_code }} {{ $shipment->reference }} </h5>
    </div>
    <div style="text-align: center">
        <h4 style="font-weight: bold; margin-top: -10px; margin-bottom: 2px">*{{ $shipment->provider_tracking_code }}*</h4>
        <barcode code="{{ $shipment->provider_tracking_code }}" type="C128A" size="1.5" height="1.05"/>
    </div>
</div>