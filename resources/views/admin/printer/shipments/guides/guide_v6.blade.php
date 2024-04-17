<div style="position:absolute; font-size: 7pt; rotate: -90; right: 2mm; margin-top: 4mm; font-weight: bold">{{ $copyNumber }}</div>
<div style="position:absolute; font-size: 6.5pt; rotate: -90; left: 2mm; margin-top: 8mm; font-weight: bold">{{ app_brand('docsignature') }}</div>
<div class="transportation-guide" style="{{ $copyId == 3 ? 'border:none' : '' }}">
    <div class="guide-content">
        <div class="guide-row">
            <div class="guide-block" style="width: 110mm; height: 25.5mm; float: left">
                <div class="guide-block-title">{{ trans('admin/docs.guide.operator') }}</div>
                <div class="pull-left" style="width: 49%">
                    <div style="height: 14.8mm;">
                        @if(@$shipment->agency->filepath)
                            <img src="{{ @$shipment->agency->filehost . @$shipment->agency->getCroppa(300) }}" style="max-width: 45mm; max-height: 15mm"  class="m-t-6"/>
                        @else
                            <h5 style="margin:0px"><b>{{ @$shipment->agency->company }}</b></h5>
                        @endif
                    </div>
                    <div class="fs-7pt lh-1-2 m-0">
                        {{ trans('admin/docs.guide.vat') }}: {{ @$shipment->agency->vat }}
                        @if(@$shipment->agency->charter)
                        &bull; Alvará {{ @$shipment->agency->charter }}
                        @endif
                    </div>
                </div>
                <div style="width: 50%; margin-top: -10px;" class="fs-7pt">
                    {{ str_limit(@$shipment->agency->company, 28) }}
                    <br/>
                    {{ @$shipment->agency->address }}<br/>
                    {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}<br>
                    {{ trans('admin/docs.guide.tlf') }}: {{ @$shipment->agency->phone }}*
                    @if($shipment->agency->mobile)
                        / {{ @$shipment->agency->mobile }}**
                    @endif
                    <br/>
                    @if(@$shipment->agency->email)
                        E-mail: {{ $shipment->agency->email }}
                    @endif
                    <div class="{{ strlen(@$shipment->agency->web) > 23 ? 'fs-7pt' : 'fs-8pt' }} bold">{{ @$shipment->agency->web }}</div>
                </div>
            </div>
            <div class="guide-block" style="padding: 0; width: 45mm; height: 25.5mm; float: left; font-size: 8pt">
                <div style="border-bottom: 1px solid #111; height: 10px; margin: 0; padding: 4px 3px">
                    <span style="font-size: 6pt">DATA CARGA</span> <b style="font-weight: bold">{{ $shipment->date }} {{ $shipment->start_hour ? $shipment->start_hour : $shipment->created_at->format('H:i') }}</b>
                </div>
                <div style="border-bottom: 1px solid #111; height: 39px; padding: 0px 3px">
                    <div style="font-size: 6pt">LOCAL CARGA</div>
                    <b style="font-weight: bold">{{ $shipment->sender_zip_code }} {{ str_limit($shipment->sender_city, 35) }}</b>
                </div>
                <div style="height: 20px;  padding: 0px 3px; height: 39px;">
                    <div style="font-size: 6pt;">LOCAL DESCARGA</div>
                    <b style="font-weight: bold; font-weight: bold; line-height: 4pt; margin-top: -5px">{{ $shipment->recipient_zip_code }} {{ str_limit($shipment->recipient_city, 35) }}</b>
                </div>
            </div>
            <div class="guide-block" style="margin: 0; width: 39mm; height: 25.5mm; border-right: none; float: left; font-size: 8pt;">
                <div style="font-size: 9pt; text-align: center">PORTES</div>
                <div style="text-align: right; line-height: 19px; margin-top: 4px; padding-right: 5px">
                    Valor s/IVA: _______.___ €&nbsp;&nbsp;<br/>
                    Taxa: _______.___ €&nbsp;&nbsp;<br/>
                    Total: _______.___ €&nbsp;&nbsp;<br/>
                    <small><small>&nbsp;&nbsp;N.º Controlo: {{ random_int(1, 999999) . '00' . number_format($shipment->total_price_for_recipient + $shipment->total_expenses, 2, '', '') }}&nbsp;&nbsp;</small></small>
                </div>
            </div>
        </div>
        <div class="guide-row">
            <div class="guide-block" style="width: 48.6%; height: 18mm;">
                <div class="guide-block-title">
                    <div style="float: right; width: 2cm; text-align: right;" class="fs-7pt">
                        NIF {{ @$shipment->sender_vat ?? @$shipment->customer->vat }}
                    </div>
                    <div class="guide-block-title fs-6pt" style="color: #777">{{ trans('admin/docs.guide.expedition') }}</div>
                </div>
                <div class="pull-left"  style="width: 100%">
                    <div class="fs-8pt lh-1-1">
                        <span class="bold">{{ substr($shipment->sender_name, 0, 80) }}</span><br>
                        {{ substr($shipment->sender_address, 0, 45) }}
                        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                        <div style="margin-top: 2px">
                            @if($shipment->sender_phone)
                                Telf: {{ $shipment->sender_phone }}
                            @endif
                            @if($shipment->sender_attn)
                                @if($shipment->sender_phone) | @endif
                                A/C: {{ str_limit($shipment->sender_attn, 30) }}<br/>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="guide-block" style="width: 49%; height: 18mm; border-right: none;">
                <div style="float: right; width: 2cm; text-align: right;" class="fs-7pt">
                    NIF {{ @$shipment->recipient_vat ?? '999999990' }}
                </div>
                <div class="guide-block-title fs-6pt" style="color: #777">{{ trans('admin/docs.guide.destination') }}</div>
                <div class="pull-left"  style="width: 100%">
                    <div class="fs-8pt lh-1-1">
                        @if(strlen($shipment->recipient_address) > 45)
                            <span>
                                <span class="bold">{{ substr($shipment->recipient_name, 0, 45) }}</span><br/>
                                {{ $shipment->recipient_address }}
                                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                            </span>
                        @else
                            <span>
                                <span class="bold">{{ $shipment->recipient_name }}</span><br/>
                                {{ $shipment->recipient_address }}
                                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                            </span>
                        @endif
                        <div style="margin-top: 2px">
                            @if($shipment->recipient_phone)
                                Telf: {{ $shipment->recipient_phone }}
                            @endif
                            @if($shipment->recipient_attn)
                                @if($shipment->recipient_phone) | @endif
                                A/C: {{ str_limit($shipment->recipient_attn, 30) }}<br/>
                            @endif
                        </div>
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
                <div class="pull-left" style="width: 23%">
                    <div class="fs-7pt lh-1-1">{{ trans('admin/docs.guide.packing') }}</div>
                    <div class="fs-8pt bold">{{ $shipment->packing }}</div>
                </div>
                <div class="pull-left" style="width: 30%">
                    <div class="fs-7pt lh-1-1">{{ trans('admin/docs.guide.description') }}</div>
                    <div class="fs-8pt bold">{{ $shipment->packing_description }}</div>
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
                <div class="guide-block-title lh-1-3" style="margin-top: -3px">{{ trans('admin/docs.guide.inst_operator') }}</div>
                <div class="fs-6pt lh-1-1" style="margin-bottom: -2px">
                    Preço ref. combustível: {{ money(Setting::get('guides_fuel_price'), Setting::get('app_currency')) }}/Litro (n.º4 art. 4-A, DL 239/2003 de 4/10, alterado pelo DL 145/2008 de 28/7).
                    Material embalado não conferido.
                    </span>
                </div>
            </div>
        </div>
        <div class="guide-row">
            <div class="guide-block p-t-0 p-b-0" style="width: 116mm; height: 20mm; border-bottom: 0;">
                <div class="guide-block-title">{{ trans('admin/docs.guide.declaration_instrutions') }}</div>
                <div style="height: 10mm; width: 49%; float: left">
                    <strong class="fs-7pt lh-1-0">
                        {{ $shipment->obs }}
                    </strong>
                    @if($shipment->cod == 'D' || $shipment->cod == 'S')
                        <div class="guide-payment" style="float:left; width: 45%; margin-right: 15px; font-size: 10px">
                            {{ $shipment->cod == 'D' ? 'PORTES DESTINO' : 'PORTES RECOLHA' }}
                        </div>
                    @endif
                    @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                        <div class="guide-payment" style="float:left; width: 80%; margin-right: 15px; font-size: 10px">
                            {{ trans('admin/docs.guide.return_shipping') }}
                        </div>
                    @endif

                    @if($shipment->has_return && in_array('rguide', $shipment->has_return))
                        <div class="guide-payment" style="float:left; width: 80%; margin-right: 15px; font-size: 10px">
                            {{ trans('admin/docs.guide.return_guide') }}
                        </div>
                    @endif
                 </div>
                <div style="height: 10mm; width: 20%; float: left; padding-left: 6px;">
                    @if($shipment->charge_price > 0.00)
                        <div style="float: left; font-size: 7pt; margin-top: -5px;">
                            <div style="float:left; width: 20px; height: 20px; font-size: 25pt;">&#9633;</div>
                            <div style="float:left; width: 65px; height: 15px; padding-top: 4px">Numerário</div>
                        </div>
                        <div style="float: left; font-size: 7pt;">
                            <div style="float:left; width: 20px; height: 20px; font-size: 25pt;">&#9633;</div>
                            <div style="float:left; width: 65px; height: 15px; padding-top: 4px">Cheque</div>
                        </div>
                        <div style="float: left; font-size: 7pt;">
                            <div style="float:left; width: 20px; height: 20px; font-size: 25pt;">&#9633;</div>
                            <div style="float:left; width: 65px; height: 15px; padding-top: 4px">Transferência</div>
                        </div>
                    @endif
                </div>
                <div style="height: 10mm; width: 28%; float: left; margin-top: -10px;">
                    @if($shipment->charge_price != 0.00)
                        <div style="float:left; width: 100%; margin-right: 15px; text-align: center; background: #222; color: #fff; padding: 4px">
                            <small>Cobrança:</small>
                            {{ money($shipment->charge_price, Setting::get('app_currency')) }}
                        </div>
                        <div class="fs-5pt lh-1-0" style="margin-top: 2px; float: left; text-align: center">
                            Devolução Cobrança
                            <div style="height: 8mm"></div>
                            _______/_______/_______  _____:_____
                        </div>
                    @endif
                </div>

            </div>
            <div class="guide-block-right" style="width: 80mm; height: 14mm; padding: 0;">
                <div class="guide-block-right">
                    <div class="text-center">
                        <div style="width: 20mm; float: right; text-align: right; margin-left: -8px; margin-top: 5px">
                            <img src="{{ @$qrCode }}"/>
                        </div>
                        <div style="display: inline-block; width: 64.2mm; float: left; margin-right: -30px">
                            <div class="fs-12pt bold text-center m-b-4">
                                Guia N.º {{ $shipment->tracking_code }}
                            </div>
                            <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.9" height="0.8"/>
                            @if($shipment->reference || @$shipment->route->code)
                                <div class="fs-8pt text-center" style="margin: 2px 13px -4px; background: #222; color: #fff; padding: 3px">
                                &nbsp;{{ @$shipment->route->code ? @$shipment->route->code : "" }}
                                @if($shipment->reference)
                                    @if(@$shipment->route->code)
                                    &bull;
                                    @endif
                                 REF: <b class="bold">{{ substr($shipment->reference, 0, 17) }}</b>
                                @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="guide-row" style="border-bottom: none">
            <div class="guide-block p-t-0 p-b-0" style="width: 116mm; font-size: 6px; border-bottom: 0;">
                <div class="fs-5pt lh-1-0" style="width: 50%; margin-top: 0mm; float: left; text-align: center">
                    {{ trans('admin/docs.guide.signature_expedition') }}
                    <br/><br/><br/><br/>
                    _______/_______/_______  _____:_____
                </div>
                <div class="fs-5pt text-center" style="width: 49%; float: left; height: 10mm; border-left: 1px solid #000; margin: -2px 0 -8px 0">
                    {{ trans('admin/docs.guide.signature_operator') }}<br/><br/>
                    <div style="margin-bottom: -2px">{{ @$shipment->operator->name ? 'Operador: '. split_name(@$shipment->operator->name) : '' }}</div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 80mm; height: 5mm; padding: 0;">
                <div class="guide-block-right">
                    <div class="p-l-4" style="font-size: 6px">
                        <div class="fs-5pt lh-1-0" style="text-align: center; margin-bottom: -3px; padding: 25px 0 0 0;">
                            ______/______/______  ____:____ &nbsp;{{ trans('admin/docs.guide.signature_destiny') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="pull-left fs-6pt" style="width: 65.5%"><b>{{ trans('admin/docs.guide.signature') }} {{ @$shipment->recipientAgency->web }}</b></div>
        <div class="pull-left fs-6pt text-right" style="width: 34%;">Processado por computador  @if($shipment->agency->phone)*custo rede fixa @endif  @if($shipment->agency->mobile)**custo rede móvel @endif</div>
    </div>
</div>
