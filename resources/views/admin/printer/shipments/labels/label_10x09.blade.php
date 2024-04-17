<div class="adhesive-label">
    <div class="adhesive-row">
        <div class="adhesive-block" style="width: 45mm; height: 10mm;">
            @if ($useAgenciesLogo)
                @if (@$shipment->recipientAgency->filepath_black && File::exists($shipment->recipientAgency->filepath_black))
                    <img src="{{ public_path(@$shipment->recipientAgency->filepath_black) }}" style="height: 30px;"
                         class="m-t-6" />
                @elseif(@$shipment->recipientAgency->filepath && File::exists($shipment->recipientAgency->filepath))
                    <img src="{{ public_path(@$shipment->recipientAgency->filepath) }}" style="height: 30px;"
                         class="m-t-6" />
                @else
                    <h4 style="margin:0px">{{ @$shipment->recipientAgency->company }}</h4>
                @endif
            @else
                @if (@$shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
                    <img src="{{ public_path(@$shipment->agency->filepath_black) }}" style="height: 30px;"
                         class="m-t-6" />
                @elseif(@$shipment->agency->filepath && File::exists($shipment->agency->filepath))
                    <img src="{{ public_path(@$shipment->agency->filepath) }}" style="height: 30px;"
                         class="m-t-6" />
                @else
                    <h4 style="margin:0px">{{ @$shipment->agency->company }}</h4>
                @endif
            @endif
        </div>
        <div class="adhesive-block" style="width: 30mm; height: 10mm; text-align: right">
            <div style="font-size: 9pt; font-weight: bold; text-transform: uppercase">{{ @$shipment->service->name }}</div>
            <div style="font-size: 9pt">{{ $shipment->weight }}KG</div>
            <div style="font-size: 9pt">{{ $shipment->date }}</div>
        </div>
        <div class="adhesive-block" style="width: 14mm; height: 10mm;">
            <div style="float: right; width: 14mm; margin-left: 8px">
                <img src="{{ @$qrCode }}" height="45" />
            </div>
        </div>
    </div>

    <div class="text-center">
        <div style="display: inline-block; margin-top: 5px;">
            <barcode
                    code="{{ $shipment->tracking_code .str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) .str_pad($count, 3, '0', STR_PAD_LEFT) }}"
                    type="C128A" size="1" height="1.2" />
        </div>
        <div class="fs-10pt bold text-center m-t-3 m-b-20">
            TRK {{ $shipment->tracking_code }}
            @if ($shipment->reference)
            &bull; REF# {{ $shipment->reference }}
            @endif
        </div>

        <div class="fs-14pt bold text-left m-t-15 text-uppercase">
            @if (@$shipment->route->code)
                {{ @$shipment->route->code }} - {{ @$shipment->route->name }}
            @else
                @if ($shipment->recipient_country == 'pt')
                    @if ($shipment->sender_agency_id != $shipment->recipient_agency_id)
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
    <div style="height: 33mm; float: left;">
        <div class="adhesive-content m-t-5">
            <div class="adhesive-row pull-left" style="border-bottom: 0; border-right: 0; width: 100%; margin-bottom: 0">
                <div class="adhesive-block border-bottom">
                    <div class="pull-left"  style="width: 69%">
                        <div class="fs-7pt">
                            {{ str_limit($shipment->sender_name, 50) }}<br>
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                        </div>
                    </div>
                    <div class="pull-left text-center" style="width: 30.7%; padding: 1px 0 0; height: 7mm; background: #111; color: #fff">
                        <div style="font-size: 20pt; float: right; font-weight: bold; margin-top: 5px">{{ str_pad($count, 3, '0', STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
                <div class="adhesive-block">
                    <div class="adhesive-block-title">
                        <div class="pull-left w-25">DESTINATÁRIO</div>
                        <div class="w-75 text-right" style="font-size: 12px;">
                            @if ($shipment->recipient_phone)
                                Tlf: {{ $shipment->recipient_phone }}<br />
                            @endif
                        </div>
                    </div>
                    <div class="pull-left">
                        <div class="fs-11pt bold lh-1-1">
                            {{ str_limit($shipment->recipient_name, 40) }}<br>
                            {{ $shipment->recipient_address }}<br />
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                            ({{ strtoupper($shipment->recipient_country) }})
                        </div>
                    </div>
                </div>
            </div>
            <div class="adhesive-row pull-left" style="border-bottom: 0; border-left: 0; width: 100%">
                <div class="adhesive-block" style="height: 5mm">
                    @if ($shipment->charge_price != 0.0)
                        <div class="fs-8pt p-t-6 pull-left" style="width: 40%">
                            <span class="guide-payment fs-7pt bold">
                               COBRANÇA
                            </span>
                            &nbsp;
                            {{ $shipment->charge_price }}
                        </div>
                    @endif

                    @if ($shipment->cod == 'D' || $shipment->cod == 'S')
                        <div class="fs-8pt p-t-6 pull-left" style="width: 30%;">
                            <span class="guide-payment fs-7pt" style="width: 21mm">
                                {{ $shipment->cod == 'D' ? 'PORTES DST' : 'PORTES REC' }}
                            </span>
                            @if(Setting::get('labels_show_cod'))
                                &nbsp;
                                {{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}
                            @endif
                        </div>
                    @endif

                    @if (in_array('rpack', empty($shipment->has_return) ? [] : $shipment->has_return))
                        <div class="fs-8pt p-t-6 pull-left" style="width: 28%">
                            <span class="guide-payment fs-7pt" style="width: 21mm">
                                RETORNO
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="fs-9pt m-t-0">{{ $shipment->obs }}</div>
    </div>
   {{-- @if (!env('APP_HIDE_CREDITS'))
        <div class="fs-6pt m-t-0">
            <div class="w-45 pull-left">
                {!! app_brand('docsignature') !!}
            </div>
            <div class="w-55 pull-left text-right">{{ @$shipment->agency->company }}</div>
        </div>
    @endif--}}
</div>
