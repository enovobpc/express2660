<div class="adhesive-label" style="width: 100mm; height: 100mm">
    <div class="adhesive-row">
        <div class="adhesive-block" style="float: left; width: 76mm; height: 16mm; text-align: left">
            @if($useAgenciesLogo)
                @if($shipment->recipientAgency->filepath_black && File::exists($shipment->recipientAgency->filepath_black))
                    <img src="{{ asset(@$shipment->recipientAgency->filehost . $shipment->recipientAgency->filepath_black) }}" style="height: 40px;" class="m-t-7"/>
                @elseif($shipment->recipientAgency->filepath && File::exists($shipment->recipientAgency->filepath))
                    <img src="{{ asset(@$shipment->recipientAgency->filehost . $shipment->recipientAgency->filepath) }}" style="height: 40px;" class="m-t-7"/>
                @else
                    <h4 style="margin:0px">{{ $shipment->recipientAgency->company }}</h4>
                @endif
            @else
                @if($shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
                    <img src="{{ asset($shipment->agency->filepath_black) }}" style="height: 40px;" class="m-t-15"/>
                @elseif($shipment->agency->filepath && File::exists($shipment->agency->filepath))
                    <img src="{{ asset($shipment->agency->filepath) }}" style="height: 40px;" class="m-t-15"/>
                @else
                    <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
                @endif
            @endif
        </div>
        <div class="adhesive-block" style="float: left;text-align: right; width: 15mm;">
            <img src="{{ @$qrCode }}" height="50"/>
        </div>
    </div>

    <div class="fs-18pt bold text-left text-uppercase" style="border-top: 1px solid #000; padding: 10px 0 0 0; line-height: 22px; height: 2mm;">
        @if(config('app.source') == 'rlrexpress' && $shipment->provider_id == 624)
            EXPEDIÇÃO ILHAS
        @else
            @if(@$shipment->route->code)
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
        @endif
    </div>

    <div style="height: 41mm; float: left;">
        <div class="adhesive-content m-t-10">
            <div class="adhesive-row pull-left" style="border-bottom: 0; width: 100mm">
                <div class="adhesive-block border-bottom">
                    <div class="adhesive-block-title">
                        <div style="float: left; width: 50%">EXPEDIDOR</div>
                        <div class="fs-8pt text-right" style="float: right; width: 50%">
                            @if($shipment->sender_phone)
                                Tlf: {{ $shipment->sender_phone }}
                            @else
                                &nbsp;
                            @endif
                        </div>
                    </div>
                    <div class="{{ config('app.source') == 'africanoa' ? 'fs-12pt bold lh-1-2' : 'fs-7pt' }}">
                        {{ str_limit($shipment->sender_name, 60) }}<br>
                        {{ $shipment->sender_address }}<br/>
                        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                    </div>
                </div>

                <div class="adhesive-block">
                    <div class="adhesive-block-title"></div>
                    <div style="float: left; width: 50%; font-size: 7pt">DESTINATÁRIO</div>
                    <div class="fs-8pt text-right" style="float: right; width: 50%">
                        @if($shipment->recipient_phone)
                            Tlf: {{ $shipment->recipient_phone }}
                        @else
                            &nbsp;
                        @endif
                    </div>
                    <div>
                        <div class="fs-12pt bold lh-1-2">
                            {{ str_limit(strtoupper($shipment->recipient_name), 34) }}<br>
                            {{ $shipment->recipient_address }}<br/>
                            {{ zipcodeCP4($shipment->recipient_zip_code) }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                        </div>
                    </div>
                </div>
            </div>
                <div class="adhesive-row" style="border-top: 1px solid #000">
                    <div class="adhesive-block border-right" style="width: 38%; height: 6.1mm;">
                        <div class="fs-9pt m-t-5 text-center">
                            {{ $shipment->date }}
                        </div>
                    </div>
                    <div class="adhesive-block border-right" style="width: 25%; height: 6.1mm">
                        <div class="fs-9pt m-t-5 text-center">{{ $shipment->volumes }} vol.</div>
                    </div>
                    <div class="adhesive-block" style="width: 27.1%; height: 6.1mm">
                        <div class="fs-9pt m-t-5 text-center">{{ $shipment->weight }}kg</div>
                    </div>
                </div>
        </div>
    </div>
     <div class="adhesive-row">
        <div class="adhesive-block" style="height: 5mm">
            @if($shipment->obs)
                <div class="fs-9pt m-t-3 p-b-6 lh-1-2">{{ $shipment->obs }}</div>
            @endif

            @if($shipment->charge_price != 0.00)
                <div class="fs-8pt p-t-6">
                    <span class="guide-payment fs-7pt" style="width: 21mm">
                        À cobrança
                    </span>
                    &nbsp;
                    {{--{{ str_pad($shipment->recipientAgency_id, 2, "0", STR_PAD_LEFT) }}{{ str_pad($shipment->customer_id, 4, "0", STR_PAD_LEFT) }}.--}}{{ $shipment->charge_price }}EUR
                </div>
           @endif

            @if ($shipment->cod == 'D' || $shipment->cod == 'S')
                <div class="fs-8pt p-t-6">
                     <span class="guide-payment fs-7pt" style="width: 21mm">
                        {{ $shipment->cod == 'D' ? 'Portes Destino' : 'Portes Recolha' }}
                     </span>
                    @if(Setting::get('labels_show_cod'))
                        &nbsp;
                        {{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}
                    @endif
                </div>
            @endif

            @if(!empty($shipment->has_return))
                @if(in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return))
                    <div class="fs-8pt p-t-6">
                    <span class="guide-payment fs-7pt" style="width: 21mm">
                        Com retorno
                    </span>
                    </div>
                @endif
            @endif
        </div>
    </div>


</div>
<div class="text-center">
    <div class="adhesive-row" style="margin-top: 10px; margin-bottom: 10px; padding: 2px 10px 2px 10px; background: #333; color: #fff; border-bottom: 0; width: 100mm">
        <div class="adhesive-block" style="width: 6.5cm; height: 6.1mm; float: left">
            <div class="fs-14pt bold text-left">
                {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
            </div>
        </div>
        <div class="adhesive-block" style="width: 2.5cm; height: 6.1mm;">
            <div class="fs-14pt bold text-right">
                {{ @$shipment->service->display_code }}
            </div>
        </div>
    </div>
    <div style="display: inline-block; margin-top: 5px;">
        @if(strlen($shipment->recipientAgency->print_name) > 24)
            <barcode code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="1.06" height="1.3"/>
        @else
            <barcode code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="1.06" height="1.5"/>
        @endif
    </div>
    <div class="fs-10pt bold text-center m-t-3 m-b-5">
        {{ $shipment->tracking_code }}
        @if($shipment->reference)
        &bull; REF# {{ $shipment->reference }}
        @endif
    </div>
    <div class="fs-7pt m-t-0">{{ app_brand('docsignature') }}</div>
</div>