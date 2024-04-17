<?php
    $routeName = \App\Models\Webservice\Expresso24::getRouteName($shipment->recipient_zip_code, $shipment->recipient_country);

    $routeCode = explode('-', $routeName);
    $routeCode = trim(@$routeCode[0]);

    $barcode = \App\Models\Webservice\Expresso24::getBarcode($shipment->recipient_country, $shipment->provider_tracking_code, $count, $routeCode);
    $barcodeE24 = \App\Models\Webservice\Expresso24::getBarcode('pt', $shipment->provider_tracking_code, $count);
?>

@if($shipment->recipient_country != 'pt')
<div style="position: fixed; right: 0mm; bottom: 0mm; rotate: -90;">
    <barcode code="{{ $barcodeE24 }}" type="C128A" size="0.82" height="2.8"/>
</div>
@endif

<div class="adhesive-label">
    <div class="adhesive-row">
        <div class="adhesive-block" style="width: 55mm; height: 14mm;">
            @if($shipment->agency->filepath)
                <img src="{{ asset(@$agency->filehost . $shipment->agency->filepath) }}" style="height: 45px;" class="m-t-0"/>
            @else
                <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
            @endif
        </div>
        <div class="adhesive-block" style="width: 78mm; height: 14mm;">
            <div class="text-right">
                <span class="fs-9pt bold">{{ $shipment->agency->web }}</span><br/>
                <span class="fs-7pt">
                Telef: {{ $shipment->agency->phone }}
                    @if($shipment->agency->mobile)
                        / {{ $shipment->agency->mobile }}
                    @endif<br/>

                    @if($shipment->agency->email)
                        E-mail: {{ $shipment->agency->email }}<br/>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="text-center">
        @if($shipment->recipient_country == 'pt')
            <div style="display: inline-block; margin-top: 4px;">
                <barcode code="{{ $barcode }}" type="C128A" size="1.5" height="1.6"/>
            </div>
            <div class="fs-10pt text-center m-t-3 m-b-10">
                <span class="bold">
                TRK {{ $shipment->tracking_code }}
                </span>
                @if($shipment->provider_tracking_code)
                &bull; REF# {{ $shipment->provider_tracking_code }}
                @endif
            </div>
        @else
            <div style="display: inline-block; margin-top: 0px;">
                <barcode code="{{ $barcode }}" type="C128A" size="1.1" height="1.9"/>
            </div>
            <div class="fs-10pt text-center m-t-3 m-b-10">
            <span class="bold">
            TRK {{ $shipment->tracking_code }}
            </span>
                @if($shipment->provider_tracking_code)
                &bull; REF# {{ $shipment->provider_tracking_code }} / SENDING: {{ str_replace('IB', '604100', $shipment->provider_tracking_code) }}
                @endif
            </div>
        @endif
        <div class="fs-18pt bold text-left m-t-15 text-uppercase">
            {{ $routeName }}
        </div>
    </div>
    @if($shipment->recipient_country == 'pt')
    <div style="height: 43mm; float: left;">
    @else
    <div style="height: 43mm; float: left; width: 116mm; padding-right: 20mm;">
    @endif
        <div class="adhesive-content m-t-8">
            <div class="adhesive-row pull-left" style="border-bottom: 0; width: 90mm;">
                <div class="adhesive-block border-bottom border-right">
                    <div class="adhesive-block-title">
                        <div class="pull-left w-25">EXPEDIDOR</div>
                        <div class="w-75 text-right">
                            @if($shipment->sender_phone)
                                Tlf: {{ $shipment->sender_phone }}<br/>
                            @endif
                        </div>
                    </div>
                    <div class="pull-left">
                        <div class="fs-7pt">
                            {{ str_limit($shipment->sender_name, 50) }}<br>
                            {{ $shipment->sender_address }}<br/>
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                        </div>
                    </div>
                </div>
                <div class="adhesive-block border-right">
                    <div class="adhesive-block-title">
                        <div class="pull-left w-25">DESTINATÁRIO</div>
                        <div class="w-75 text-right">
                            @if($shipment->recipient_phone)
                                Tlf: {{ $shipment->recipient_phone }}<br/>
                            @endif
                        </div>
                    </div>
                    <div class="pull-left">
                        @if($shipment->recipient_attn)
                        <div class="fs-9pt lh-1p2">
                            Att: {{ str_limit($shipment->recipient_attn, 50) }}<br>
                        </div>
                        @endif
                        <div class="fs-9pt bold lh-1p2">
                            {{ str_limit($shipment->recipient_name, 50) }}<br>
                            {{ $shipment->recipient_address }}<br/>
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                        </div>
                    </div>
                </div>
            </div>
            @if($shipment->recipient_country == 'pt')
            <div class="adhesive-row pull-left" style="border-bottom: 0; width: 50.4mm">
                <div class="adhesive-block border-bottom" style="height: 7.2mm">
                    <div class="fs-14pt bold text-center m-t-6">
                        {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
                    </div>
                </div>
                <div class="adhesive-block border-bottom border-right" style="width: 38%; height: 6.1mm">
                    <div class="fs-9pt m-t-3 text-center">
                        {{ $shipment->date }}
                    </div>
                </div>
                <div class="adhesive-block border-bottom border-right" style="width: 23%; height: 6.1mm">
                    <div class="fs-9pt m-t-3 text-center">{{ $shipment->volumes }} vol.</div>
                </div>
                <div class="adhesive-block border-bottom " style="width: 25.3%; height: 6.1mm">
                    <div class="fs-9pt m-t-3 text-center">{{ $shipment->weight }}kg</div>
                </div>

                <div class="adhesive-block" style="height: 5mm">
                    <span class="fs-8pt p-t-4 bold text-uppercase">{{ @$shipment->service->name }}</span>
                    @if($shipment->charge_price != 0.00)
                        <div class="fs-8pt p-t-6">
                        <span class="guide-payment fs-7pt" style="width: 21mm">
                            À cobrança
                        </span>
                            &nbsp;
                            {{ str_pad($shipment->recipientAgency_id, 2, "0", STR_PAD_LEFT) }}{{ str_pad($shipment->customer_id, 4, "0", STR_PAD_LEFT) }}.{{ $shipment->charge_price }}
                        </div>
                    @endif

                    @if($shipment->payment_at_recipient)
                        <div class="fs-8pt p-t-6">
                            <span class="guide-payment fs-7pt" style="width: 21mm">
                                Portes no Destino
                            </span>
                            &nbsp;
                            {{ money($shipment->total_price_for_recipient, Setting::get('app_currency')) }}
                        </div>
                    @endif

                    @if($shipment->return_type == 'pack')
                        <div class="fs-8pt p-t-6">
                            <span class="guide-payment fs-7pt" style="width: 21mm">
                                Com retorno
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="adhesive-row pull-left" style="border-bottom: 0;  width: 25.3mm;">
                <div class="adhesive-block border-bottom" style="height: 7.2mm">
                    <div class="fs-14pt bold text-center m-t-6">
                        {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
                    </div>
                </div>
                <div class="adhesive-block border-bottom" style="width: 100%; height: 6.1mm">
                    <div class="fs-9pt m-t-3 text-center">
                        {{ $shipment->date }}
                    </div>
                </div>
                <div class="adhesive-block border-bottom" style="width: 100%; height: 6.1mm">
                    <div class="fs-9pt m-t-3 text-center">{{ $shipment->weight }}kg</div>
                </div>

                <div class="adhesive-block" style="height: 5mm">
                    <span class="fs-8pt p-t-4 bold text-uppercase">{{ @$shipment->service->name }}</span>
                    @if($shipment->charge_price != 0.00)
                        <div class="fs-8pt p-t-6">
                    <span class="guide-payment fs-7pt" style="width: 21mm">
                        À cobrança
                    </span>
                            &nbsp;
                            {{ str_pad($shipment->recipientAgency_id, 2, "0", STR_PAD_LEFT) }}{{ str_pad($shipment->customer_id, 4, "0", STR_PAD_LEFT) }}.{{ $shipment->charge_price }}
                        </div>
                    @endif

                    @if($shipment->payment_at_recipient)
                        <div class="fs-8pt p-t-6">
                        <span class="guide-payment fs-7pt" style="width: 21mm">
                            Portes no Destino
                        </span>
                            &nbsp;
                            {{ money($shipment->total_price_for_recipient, Setting::get('app_currency')) }}
                        </div>
                    @endif

                    @if($shipment->return_type == 'pack')
                        <div class="fs-8pt p-t-6">
                        <span class="guide-payment fs-7pt" style="width: 21mm">
                            Com retorno
                        </span>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        <div class="fs-9pt m-t-3">{{ $shipment->obs }}</div>
    </div>
    <div class="fs-6pt m-t-0">
        <div class="w-60 pull-left">Processado por QUICKBOX - Software para Transportes e Logística. www.quickbox.pt.</div>
        @if($shipment->recipient_country == 'pt')
        <div class="w-40 pull-left text-right">{{ $shipment->agency->company }}</div>
        @else
        <div class="w-40 pull-left text-left">| &nbsp;&nbsp;{{ $shipment->agency->company }}</div>
        @endif
    </div>
</div>