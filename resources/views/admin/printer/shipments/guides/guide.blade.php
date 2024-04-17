<div style="position:absolute; font-size: 7pt; rotate: -90; right: 2mm; margin-top: 4mm; font-weight: bold">{{ $copyNumber }}</div>
<div style="position:absolute; font-size: 6.5pt; rotate: -90; left: 2mm; margin-top: 8mm; font-weight: bold">{{ app_brand('docsignature') }}</div>
<div class="transportation-guide" style="{{ $copyId == 3 ? 'border:none' : '' }}">
    <div class="guide-content">
        <div class="guide-row">
            <div class="guide-block" style="width: 113.5mm; height: 14mm;">
                <div class="guide-block-title">
                    @if($shipment->customer_id)
                        <div style="float: right; width: 4.2cm; text-align: right;" class="fs-8pt">{{ trans('admin/docs.guide.client') }} {{ @$shipment->customer->code_abbrv ? @$shipment->customer->code_abbrv : @$shipment->customer->code }}</div>
                    @endif
                    <div class="pull-left">{{ trans('admin/docs.guide.expedition') }}</div>
                </div>
                <div class="pull-left"  style="width: 74%">
                    <div class="fs-8pt bold lh-1-1">
                        {{ substr($shipment->sender_name, 0, 80) }}<br>
                        {{ substr($shipment->sender_address, 0, 45) }}
                        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                    </div>
                </div>
                <div class="fs-8pt text-right" style="width: 25% ">
                    {{ trans('admin/docs.guide.vat') }}: {{ @$shipment->sender_vat ? @$shipment->sender_vat : @$shipment->customer->vat }}
                    <br/>
                    @if($shipment->sender_phone && $shipment->sender_phone != '.')
                        {{ trans('admin/docs.guide.tlf') }}: {{ substr($shipment->sender_phone, 0, 15) }}
                    @endif
                    <br/>
                </div>
            </div>
            <div class="guide-block-right" style="width: 80.2mm;">
                <div class="text-center">
                    <div style="display: inline-block; width: 64.2mm; float: left;">
                        <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.93" height="0.8"/>
                        <div class="fs-9pt bold text-center m-t-4">
                            Guia Nº {{ $shipment->tracking_code }} {{ @$shipment->route->code ? ' | '.@$shipment->route->code : '' }}
                            {{--@if($shipment->reference)
                            &bull; REF# {{ substr($shipment->reference, 0, 17) }}
                            @endif--}}
                        </div>
                    </div>
                    <div style="width: 16mm; float: left; text-align: right">
                        <img src="{{ @$qrCode }}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="guide-row">
            <div class="guide-block" style="width: 115.6mm; padding: 0px">
                <div class="guide-row" style="border-bottom: 0;">
                    <div class="guide-block-right border-bottom">
                        <div class="guide-block-title">{{ trans('admin/docs.guide.destination') }}</div>
                        <div class="pull-left"  style="width: 74%">
                            <div class="fs-8pt bold lh-1-1">
                                @if($shipment->recipient_attn)
                                    <div style="font-size: 7pt;">
                                        A/C: {{ str_limit($shipment->recipient_attn, 30) }}<br/>
                                    </div>
                                @endif

                                @if(strlen($shipment->recipient_address) > 55)
                                    <span style="font-size: 7pt">
                                        {{ substr($shipment->recipient_name, 0, 45) }}<br>
                                        {{ $shipment->recipient_address }}
                                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                                    </span>
                                @else
                                    <span>
                                        {{ substr($shipment->recipient_name, 0, 45) }}<br>
                                        {{ $shipment->recipient_address }}
                                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="fs-8pt text-right" style="width: 25%;">
                            @if($shipment->recipient_vat)
                                <b>{{ trans('admin/docs.guide.vat') }}: {{ $shipment->recipient_vat }}</b>
                            @endif
                            <br/>
                            @if($shipment->recipient_phone && $shipment->recipient_phone != '.')
                                {{ trans('admin/docs.guide.tlf') }}: {{ substr($shipment->recipient_phone, 0, 15) }}
                            @endif<br/>
                        </div>
                    </div>
                    <div class="guide-block-right border-bottom">
                        <div class="guide-block-title">
                            <div class="pull-left" style="width: 25mm">{{ trans('admin/docs.guide.from') }}</div>
                            <div class="pull-left text-right" style="width: 79mm;">
                                <b class="bold">{{ @$shipment->route->code ? @$shipment->route->code.' | ' : '' }} {{ @$shipment->service->name }}</b>  &nbsp;&nbsp; {{ trans('admin/docs.guide.date') }}: {{ $shipment->date }} &nbsp;&nbsp;{{ config('app.source') == 'corridexcelente' ? '' : trans('admin/docs.guide.hour').':'. $shipment->created_at->format('H:i') }}
                            </div>
                        </div>
                        <div class="fs-8pt bold">
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                        </div>
                    </div>
                    <div class="guide-block-right">
                        <div class="guide-block-title">
                            <div class="pull-left" style="width: 27mm">{{ trans('admin/docs.guide.to') }}</div>
                            <div class="pull-left text-right" style="width: 77mm;">
                                {{ trans('admin/docs.guide.date') }}: {{ $shipment->delivery_date->format('Y-m-d') }} &nbsp;&nbsp; {{ trans('admin/docs.guide.hour') }}: {{ $shipment->delivery_date->format('H:i') == '00:00' ? '19:00' : $shipment->delivery_date->format('H:i') }}
                            </div>
                        </div>
                        <div class="fs-8pt bold">
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                        </div>
                    </div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 83mm; padding: 0;">
                @if(@$customerAccount && Setting::get('customer_block_provider_labels'))
                    <div class="guide-block-right border-bottom" style="height: 22.6mm">
                        <div class="guide-block-title">{{ trans('admin/docs.guide.operator') }}</div>
                        <div class="pull-left" style="width: 49%">
                            <div style="height: 14.5mm;">
                                @if(@$shipment->agency->filepath)
                                    <img src="{{ @$shipment->agency->filehost . @$shipment->agency->getCroppa(300) }}" style="max-width: 35mm; max-height: 12mm"  class="m-t-6"/>
                                @else
                                    <h5 style="margin:0px"><b>{{ @$shipment->agency->company }}</b></h5>
                                @endif
                            </div>
                            <div class="fs-7pt lh-1-2 m-0">
                                <span class="{{ strlen(@$shipment->agency->web) > 23 ? 'fs-7pt' : 'fs-8pt' }} bold">{{ @$shipment->agency->web }}</span>
                            </div>
                        </div>
                        <div style="width: 50%; margin-top: -20px;" class="fs-7pt">
                            {{ trans('admin/docs.guide.vat') }}: {{ @$shipment->agency->vat }}
                            @if(@$shipment->agency->charter)
                            &bull; Alvará {{ @$shipment->agency->charter }}
                            @endif
                            {{-- @if($shipment->agency->capital)
                             &bull; Cap.Soc. {{ $shipment->agency->capital }}
                             @endif--}}
                            <br/>
                            {{ str_limit(@$shipment->agency->company, 28) }}
                            <br/>
                            {{ @$shipment->agency->address }}
                            {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}<br>
                            {{ trans('admin/docs.guide.tlf') }}: {{ @$shipment->agency->phone }}*
                            @if($shipment->agency->mobile)
                                / {{ @$shipment->agency->mobile }}**
                            @endif
                            <br/>
                            @if(@$shipment->agency->email)
                                E-mail: {{ $shipment->agency->email }}
                            @endif
                        </div>
                    </div>
                @else
                    <div class="guide-block-right border-bottom" style="height: 22.6mm">
                        <div class="guide-block-title">{{ trans('admin/docs.guide.operator') }}</div>
                        <div class="pull-left" style="width: 49%">
                            <div style="height: 14.5mm;">
                                @if(@$shipment->recipientAgency->filepath)
                                    <img src="{{ asset(@$shipment->recipientAgency->filehost.@$shipment->recipientAgency->getCroppa(300)) }}" style="max-width: 38mm; max-height: 12mm"  class="m-t-6"/>
                                @else
                                    <h5 style="margin:0px"><b>{{ @$shipment->recipientAgency->company }}</b></h5>
                                @endif
                            </div>
                            <div class="fs-7pt lh-1-2 m-0">
                                <span class="{{ strlen(@$shipment->recipientAgency->web) > 23 ? 'fs-7pt' : 'fs-8pt' }} bold">{{ @$shipment->agency->web }}</span>
                            </div>
                        </div>
                        <div style="width: 50%; margin-top: -12px;" class="fs-7pt">
                            {{ trans('admin/docs.guide.vat') }}: {{ @$shipment->recipientAgency->vat }}
                            @if(@$shipment->agency->charter)
                            &bull; Alvará {{ @$shipment->agency->charter }}
                            @endif
                            {{--@if($shipment->agency->capital)
                            &bull; Cap.Soc. {{ $shipment->agency->capital }}
                            @endif--}}
                            <br/>
                            {{ str_limit(@$shipment->recipientAgency->company, 30) }}
                            <br/>
                            {{ $shipment->recipientAgency->address }}
                            {{ $shipment->recipientAgency->zip_code }} {{ $shipment->recipientAgency->city }}<br>
                            {{ trans('admin/docs.guide.tlf') }}: {{ $shipment->recipientAgency->phone }}*
                            @if($shipment->recipientAgency->mobile)
                                / {{ $shipment->recipientAgency->mobile }}**
                            @endif
                            <br/>
                            @if($shipment->recipientAgency->email)
                                {{ $shipment->recipientAgency->email }}
                            @endif
                        </div>
                    </div>
                @endif
                <div class="guide-block-right">
                    <div class="pull-left"  style="width: 33.3%">
                        <div class="guide-block-title">{{ trans('admin/docs.guide.registration') }}</div>
                        <div class="fs-8pt bold">{{ $shipment->car_registration }}</div>
                    </div>
                    <div class="pull-left" style="width: 33.3%">
                        <div class="guide-block-title">{{ trans('admin/docs.guide.weight') }}</div>
                        <div class="fs-8pt bold">{{ $shipment->vehicle_kg ? money($shipment->vehicle_kg) : '' }}</div>
                    </div>
                    <div class="pull-left"  style="width: 33%">
                        <div class="guide-block-title">{{ trans('admin/docs.guide.charge_weight') }}</div>
                        <div class="fs-8pt bold">{{ $shipment->vehicle_kg_usefull ? money($shipment->vehicle_kg_usefull) : '' }}</div>
                    </div>

                </div>
            </div>

        </div>

        <div class="guide-row">
            <div class="guide-block" style="width: 131mm; height: 10mm; border-bottom: 0;">
                <div class="guide-block-title">{{ trans('admin/docs.guide.merchandise') }}</div>
                <div class="pull-left" style="width: 11%">
                    <div class="fs-7pt lh-1-1">{{ trans('admin/docs.guide.volumes') }}</div>
                    <div class="fs-8pt bold">{{ $shipment->volumes }}</div>
                </div>
                <div class="pull-left" style="width: 16%">
                    <div class="fs-7pt lh-1-1">{{ trans('admin/docs.guide.packing') }}</div>
                    <div class="fs-8pt bold">{{ substr($shipment->packing, 0, 10) }}</div>
                </div>
                <div class="pull-left" style="width: 37%">
                    <div class="fs-7pt lh-1-1">{{ trans('admin/docs.guide.description') }}</div>
                    <div class="fs-8pt bold">{{ substr($shipment->packing_description, 0, 23) }}</div>
                </div>
                <div class="pull-left" style="width: 17%;">
                    <div class="fs-7pt lh-1-1">{{ trans('admin/docs.guide.all_weight') }}</div>
                    <div class="fs-8pt bold">{{ money($shipment->weight) }} Kg</div>
                </div>
                <div class="pull-left" style="width: 12%;">
                    <div class="fs-7pt lh-1-1">{{ trans('admin/docs.guide.volume') }}</div>
                    <div class="fs-8pt bold">{{ $shipment->volume_m3 ?  money($shipment->volume_m3, '', 4) : money($shipment->fator_m3, '', 4) }} m3</div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 60mm;">
                <div class="guide-block-title lh-1-2">{{ trans('admin/docs.guide.transport_subcontract') }}</div>
            </div>
        </div>
        <div class="guide-row" style="border-bottom: none">
            <div class="guide-block p-t-0 p-b-0" style="width: 73mm; height: 26mm; border-bottom: 0;">
                <div class="guide-block-title">{{ trans('admin/docs.guide.declaration_instrutions') }}</div>
                <div style="height: 20mm">
                    <strong class="bold fs-7pt lh-1-0">
                        @if($shipment->reference)
                            REF# {{ substr($shipment->reference, 0, 17) }}
                            @if($shipment->reference2)
                                / REF2# {{ substr($shipment->reference2, 0, 17) }}
                            @endif
                            @if($shipment->reference3)
                                / REF3# {{ substr($shipment->reference3, 0, 17) }}
                                @endif
                                @if($shipment->obs)
                                &bull;
                            @endif
                        @endif
                        {{ $shipment->obs }}
                    </strong>

                    @if($shipment->charge_price != 0.00)
                        <div class="guide-payment" style="float:left; width: 50%; margin-right: 15px">
                            <small>{{ trans('admin/docs.guide.return') }}:</small> {{ money($shipment->charge_price, Setting::get('app_currency')) }}
                        </div>
                    @endif

                    @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                        <div class="guide-payment" style="float:left; width: 30%; margin-right: 15px">
                            {{ trans('admin/docs.guide.return_shipping') }}
                        </div>
                    @endif

                    @if($shipment->has_return && in_array('rguide', $shipment->has_return))
                        <div class="guide-payment" style="float:left; width: 30%; margin-right: 15px">
                            {{ trans('admin/docs.guide.return_guide') }}
                        </div>
                    @endif

                    @if(!empty($shipment->requested_by) && $shipment->requested_by != $shipment->customer_id)
                        <div class="guide-payment" style="float:left; width: 70%">
                            <small>{{ trans('admin/docs.guide.payment_mensal') }}</small>
                        </div>
                        <div class="clearfix"></div>
                    @elseif($shipment->cod == 'D' && !Setting::get('labels_show_cod'))
                        <div class="guide-payment" style="float:left; width: 45%">
                            <small>{{ trans('admin/docs.guide.payment_destiny') }}</small>
                        </div>
                        <small><small>&nbsp;&nbsp;N.º Controlo: {{ random_int(1, 999999) . '0' . number_format($shipment->billing_subtotal, 2, '', '') }}</small></small>
                        <div class="clearfix"></div>
                    @elseif($shipment->cod == 'D' && Setting::get('labels_show_cod'))
                        <div class="guide-payment" style="float:left; width: 45%">
                            <small>{{ trans('admin/docs.guide.payment_destiny') }}</small>
                            <small><small>&nbsp;&nbsp;A cobrar: {{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}</small></small>
                            <div class="clearfix"></div>
                        </div>
                    @endif
                    {{--@if($shipment->customer->is_parts_shop && $shipment->requested_by != $shipment->customer_id)
                        <div class="guide-payment" style="float:left; width: 100%">
                            {{ trans('admin/docs.guide.payment_to') }}
                        </div>
                    @endif--}}
                </div>
                <div class="fs-7pt lh-1-0" style="margin-top: 0mm">
                    _______/_______/_______  _____:_____ {{ trans('admin/docs.guide.signature_expedition') }}
                </div>
            </div>
            <div class="guide-block" style="width: 57.6mm; height: 24mm; padding: 0">
                <div class="guide-row" style="height: 18mm">
                    <div class="guide-block-right">
                        <div class="guide-block-title lh-1-3">{{ trans('admin/docs.guide.inst_operator') }}</div>
                        <div class="fs-6pt lh-1-1">
                            Preço ref. combustível: {{ money(Setting::get('guides_fuel_price'), Setting::get('app_currency')) }}/Litro (n.º4 art. 4-A, DL 239/2003 de 4/10, alterado pelo DL 145/2008 de 28/7).
                            Material embalado não conferido.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="fs-6pt text-center" style="margin-top: 20px">{{ @$shipment->operator->name ? 'Operador: '. split_name(@$shipment->operator->name) : trans('admin/docs.guide.signature_operator') }}</div>
            </div>
            <div class="guide-block-right" style="width: 64mm; height: 26mm; padding: 0;">

                <div class="guide-block-right">
                    <div class="guide-block-title lh-1-3">{{ trans('admin/docs.guide.declaration') }}</div>
                    <div style="height: 19mm">
                        @if($shipment->charge_price > 0.00)
                            <div style="float: left; font-size: 7pt; margin-top: 4px"><span style="font-size: 13pt">&#9633;</span> {{ trans('admin/docs.guide.refund_money') }}</div>
                            <div style="float: left; font-size: 7pt"><span style="font-size: 13pt">&#9633;</span> {{ trans('admin/docs.guide.refund_che') }}</div>
                        @endif
                        {{ $shipment->obs_delivery }}
                    </div>
                    <div class="p-l-4">
                        <div class="fs-7pt lh-1-0">
                            ______/______/______  ____:____ &nbsp;{{ trans('admin/docs.guide.signature_destiny') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        {{--<div class="pull-left" style="width: 60%">Software desenvolvido por <b style="font-weight: bold">ENOVO, Web Design, E-commerce e Aplicações Online - www.enovo.pt</b></div>--}}
        {{--<div class="pull-left" style="width: 57%"><b style="font-weight: bold">ENOVO TMS - Software Transportes e Logísitica | tms.enovo.pt</b></div>--}}
        <div class="pull-left fs-6pt" style="width: 65.5%"><b>{{ trans('admin/docs.guide.signature') }} {{ @$shipment->recipientAgency->web }}</b></div>
        <div class="pull-left fs-6pt text-right" style="width: 34%;">Processado por computador / @if($shipment->agency->phone)*custo rede fixa @endif  @if($shipment->agency->mobile)**custo rede móvel @endif</div>
    </div>

</div>
