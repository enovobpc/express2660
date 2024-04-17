<div class="adhesive-label">
    <div class="adhesive-row">
        <div class="adhesive-block" style="width: 55mm; height: 12mm;">
            @if ($useAgenciesLogo)
                @if (@$shipment->recipientAgency->filepath_black && File::exists($shipment->recipientAgency->filepath_black))
                    <img src="{{ public_path(@$shipment->recipientAgency->filepath_black) }}" style="height: 45px;"
                        class="m-t-6" />
                @elseif(@$shipment->recipientAgency->filepath && File::exists($shipment->recipientAgency->filepath))
                    <img src="{{ public_path(@$shipment->recipientAgency->filepath) }}" style="height: 45px;"
                        class="m-t-6" />
                @else
                    <h4 style="margin:0px">{{ @$shipment->recipientAgency->company }}</h4>
                @endif
            @else
                @if (@$shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
                    <img src="{{ public_path(@$shipment->agency->filepath_black) }}" style="height: 45px;"
                        class="m-t-6" />
                @elseif(@$shipment->agency->filepath && File::exists($shipment->agency->filepath))
                    <img src="{{ public_path(@$shipment->agency->filepath) }}" style="height: 45px;"
                        class="m-t-6" />
                @else
                    <h4 style="margin:0px">{{ @$shipment->agency->company }}</h4>
                @endif
            @endif
        </div>
        <div class="adhesive-block" style="width: 81mm; height: 14mm;">
            <div style="float: right; width: 14mm; margin-left: 8px">
                <img src="{{ @$qrCode }}" height="50" />
            </div>
            <div class="text-right" style="width: 62mm;line-height: 1.5;">
                @if ($useAgenciesLogo)
                    <span class="fs-9pt bold">{{ @$shipment->recipientAgency->web }}</span><br />
                    <span class="fs-7pt">
                        Telef: {{ @$shipment->recipientAgency->phone }}
                        @if (@$shipment->recipientAgency->mobile)
                            / {{ @$shipment->recipientAgency->mobile }}
                        @endif
                        <br />
                        @if ($shipment->recipientAgency->email)
                            E-mail: {{ @$shipment->recipientAgency->email }}<br />
                        @endif
                    </span>
                @else
                    <span class="fs-9pt bold">{{ @$shipment->agency->web }}</span><br />
                    <span class="fs-7pt">
                        Telef: {{ @$shipment->agency->phone }}
                        @if (@$shipment->agency->mobile)
                            / {{ @$shipment->agency->mobile }}
                        @endif
                        <br />

                        @if (@$shipment->agency->email)
                            E-mail: {{ @$shipment->agency->email }}<br />
                        @endif
                    </span>
                @endif
            </div>

        </div>
    </div>

    <div class="text-center">

        @if ($shipment->provider_tracking_code && Setting::get('shipment_label_barcodes') == 2)
            <div style="display: inline-block">
                <barcode
                    code="{{ $shipment->provider_sender_agency . $shipment->provider_recipient_agency . $shipment->provider_tracking_code }}"
                    type="C128A" size="1.2" height="0.8" />
            </div>
            <div class="fs-10pt text-center m-t-0 m-b-3">
                {{ $shipment->provider_sender_agency . $shipment->provider_recipient_agency . $shipment->provider_tracking_code }}
            </div>
            <div style="display: inline-block">
                <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="1.1" height="0.6" />
            </div>
            <div class="fs-10pt bold text-center m-t-3">
                TRK {{ $shipment->tracking_code }}
                @if ($shipment->reference)
                    &bull; REF# {{ $shipment->reference }}
                @endif
            </div>
        @else
            <div style="display: inline-block; margin-top: 15px;">
                <barcode
                    code="{{ $shipment->tracking_code .str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) .str_pad($count, 3, '0', STR_PAD_LEFT) }}"
                    type="C128A" size="1.3" height="1.2" />
            </div>
            <div class="fs-10pt bold text-center m-t-3 m-b-20">
                TRK {{ $shipment->tracking_code }}
                @if ($shipment->reference)
                    &bull; REF# {{ $shipment->reference }}
                @endif
            </div>
        @endif

        <div class="fs-18pt bold text-left m-t-15 text-uppercase">
            @if (config('app.source') == 'rlrexpress' && $shipment->provider_id == 624)
                EXPEDIÇÃO ILHAS
            @else
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
            @endif
        </div>
    </div>
    <div style="height: 33mm; float: left;">
        <div class="adhesive-content m-t-5">
            <div class="adhesive-row pull-left" style="border-bottom: 0; width: 90mm">
                <div class="adhesive-block border-bottom border-right">
                    <div class="adhesive-block-title">
                        <div class="pull-left w-25">EXPEDIDOR</div>
                        <div class="w-75 text-right" style="font-size: 12px;">
                            @if ($shipment->sender_phone)
                                Tlf: {{ $shipment->sender_phone }}<br />
                            @endif
                        </div>
                    </div>
                    <div class="pull-left">
                        <div class="fs-7pt">
                            {{ str_limit($shipment->sender_name, 50) }}<br>
                            {{ $shipment->sender_address }}<br />
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                            ({{ strtoupper($shipment->sender_country) }})
                        </div>
                    </div>
                </div>
                <div class="adhesive-block border-right">
                    <div class="adhesive-block-title">
                        <div class="pull-left w-25">DESTINATÁRIO</div>
                        <div class="w-75 text-right" style="font-size: 12px;">
                            @if ($shipment->recipient_phone)
                                Tlf: {{ $shipment->recipient_phone }}<br />
                            @endif
                        </div>
                    </div>
                    <div class="pull-left">
                        <div class="fs-9pt bold lh-1-2">
                            {{ str_limit($shipment->recipient_name, 40) }}<br>
                            {{ $shipment->recipient_address }}<br />
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                            ({{ strtoupper($shipment->recipient_country) }})
                        </div>
                    </div>
                </div>
            </div>
            <div class="adhesive-row pull-left" style="border-bottom: 0; width: 50.4mm">
                <div class="adhesive-block border-bottom" style="height: 7.2mm">
                    <div class="fs-14pt bold text-center m-t-6">
                        {{ str_pad($count, 3, '0', STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) }}
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
                    @if ($shipment->recipient_attn)
                        <br />
                        <span class="font-size-8pt">
                            A/C: {{ str_limit($shipment->recipient_attn, 50) }}<br>
                        </span>
                    @endif
                    @if ($shipment->charge_price != 0.0)
                        <div class="fs-8pt p-t-6">
                            <span class="guide-payment fs-7pt" style="width: 21mm">
                                À cobrança
                            </span>
                            &nbsp;
                            @if (config('app.source') !== 'rapidix')
                                {{ str_pad($shipment->recipientAgency_id, 2, '0', STR_PAD_LEFT) }}{{ str_pad($shipment->customer_id, 4, '0', STR_PAD_LEFT) }}.{{ $shipment->charge_price }}
                            @else
                                {{ $shipment->charge_price }}
                            @endif
                        </div>
                    @endif

                    @if ($shipment->cod == 'D' || $shipment->cod == 'S')
                        <div class="fs-8pt p-t-6">
                            <span class="guide-payment fs-7pt" style="width: 21mm">
                                {{ $shipment->cod == 'D' ? 'Portes Destino' : 'Portes Remetente' }}
                            </span>
                            @if(Setting::get('labels_show_cod'))
                            &nbsp;
                            {{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}
                            @endif
                        </div>
                    @endif

                    @if (in_array('rpack', empty($shipment->has_return) ? [] : $shipment->has_return))
                        <div class="fs-8pt p-t-6">
                            <span class="guide-payment fs-7pt" style="width: 21mm">
                                Com retorno
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="fs-9pt m-t-0">{{ $shipment->obs }}</div>
    </div>
    @if (!env('APP_HIDE_CREDITS'))
        <div class="fs-6pt m-t-0">
            <div class="w-45 pull-left">
                {!! app_brand('docsignature') !!}
            </div>
            <div class="w-55 pull-left text-right">{{ @$shipment->agency->company }}</div>
        </div>
    @endif
</div>
