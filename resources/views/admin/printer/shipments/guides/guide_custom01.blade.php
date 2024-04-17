@if(@$copy == 1)
    <div class="transportation-guide" style="margin-left: -4mm; padding-top: 0; padding-bottom: 0; padding-right: 0; height: 99mm; border-bottom: none">
@elseif(@$copy == 2)
    <div class="transportation-guide" style="margin-left: -4mm; padding-top: 4mm; padding-bottom: 0; padding-right: 0; height: 80mm; border-bottom: none">
@else
    <div class="transportation-guide" style="margin-left: -4mm; padding-top: 18mm; padding-bottom: 0; padding-right: 0; height: 60mm; border-bottom: none">
@endif
    <div class="guide-content" style="margin-left: 2mm; margin-right: 0; border: 1px solid #fff; padding: 0;">
        <div class="guide-row" style="border-color: #fff">
            <div class="guide-block-right" style="width: 95mm; height: 50mm; padding: 0">
                <div style="height: 25mm; border-bottom: 1px solid #fff; padding-left: 5px">
                    <div class="guide-block-title">
                        <div style="width: 50%; float: left; color: #fff">EXPEDIDOR/LOCAL DE CARGA</div>
                        <div style="width: 49%; float: left; text-align: right; font-size: 8pt">
                            @if($shipment->sender_phone && $shipment->sender_phone != '.')
                                Tlf: {{ $shipment->sender_phone }}
                            @endif
                        </div>
                    </div>
                    <div class="pull-left"  style="width: 100% ">
                        <div class="fs-9pt bold">
                            {{ str_limit($shipment->sender_name, 45) }}<br>
                            {{ $shipment->sender_address }}
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                        </div>
                    </div>
                </div>
                <div style="height: 25mm; padding-left: 5px">
                    <div class="guide-block-title">
                        <div style="width: 50%; float: left; color: #fff">DESTINATÁRIO/LOCAL DE DESCARGA</div>
                        <div style="width: 49%; float: left; text-align: right; font-size: 8pt">
                            @if($shipment->recipient_phone && $shipment->recipient_phone != '.')
                                Tlf: {{ $shipment->recipient_phone }}
                            @endif
                        </div>
                    </div>
                    <div class="pull-left"  style="width: 100% ">
                        <div class="fs-9pt bold lh-1-2">
                            @if($shipment->recipient_attr)
                                {{ $shipment->recipient_attr }}<br/>
                            @endif
                            {{ str_limit($shipment->recipient_name, 47) }}<br>
                            {{ $shipment->recipient_address }}<br/>
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                        </div>
                    </div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 108mm;">
                <div class="guide-row" style="border: none">
                    <div style="height: 34mm; width: 51%; float: left; border-right: 1px solid #fff; margin-top: -5px;">
                        {{--<div style="text-align: center">
                            @if($shipment->recipientAgency->filepath)
                                <img src="{{ asset(@$agency->filehost . $shipment->recipientAgency->getCroppa(300)) }}" style="max-width: 40mm; height: 15mm" class="m-t-6"/>
                            @else
                                <h5 style="margin:0px"><b>{{ $shipment->recipientAgency->company }}</b></h5>
                            @endif
                            <div class="fs-6pt lh-1-2 m-t-8">
                                <span class="fs-10pt bold">{{ $shipment->recipientAgency->web }}</span>
                            </div>
                        </div>
                        <div style="width: 100%; text-align: center" class="fs-6pt lh-1-2 m-t-5">
                            NIF: {{ $shipment->recipientAgency->vat }}
                            @if($shipment->recipientAgency->charter)
                            &bull; Alvará {{ $shipment->recipientAgency->charter }}
                            @endif
                            <br/>
                            {{ $shipment->recipientAgency->address }}
                            {{ $shipment->recipientAgency->zip_code }} {{ $shipment->recipientAgency->city }}<br>
                            Telef: {{ $shipment->recipientAgency->phone }}
                            @if($shipment->recipientAgency->mobile)
                                / {{ $shipment->recipientAgency->mobile }}
                            @endif
                            <br/>
                            @if($shipment->recipientAgency->email)
                                E-mail: {{ $shipment->recipientAgency->email }}<br/>
                            @endif
                        </div>--}}
                    </div>
                    <div style="width: 48.5%; float: left;">
                        <div class="text-center">
                            <div style="display: inline-block; margin-top: 5px">
                                <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.8" height="1.4" style="margin-right: -10px"/>
                            </div>
                            <div class="fs-9pt bold text-center m-t-4">
                                TRK# {{ $shipment->tracking_code }}
                                @if($shipment->reference)
                                &bull; REF# {{ $shipment->reference }}
                                @endif
                                <br/>
                                {{ @$shipment->service->name }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="guide-row fs-9pt" style="border-bottom: none; border-top: 1px solid #fff; margin-left: -4px; margin-right: -5px; padding-left: 5px">
                    <div style="height: 15mm; width: 34%; float: left;">
                        <div class="guide-block-title" style="color: #fff">Designação</div>
                        <br/>
                    </div>
                    <div style="height: 15mm; width: 20%; float: left;">
                        <div class="guide-block-title" style="color: #fff">Peso Bruto</div><br/>
                        <div class="fs-9pt bold">{{ money($shipment->weight) }}</div>
                    </div>
                    <div style="height: 15mm; width: 20%; float: left;">
                        <div class="guide-block-title" style="color: #fff">Nº Volumes</div>
                        <br/>
                        <div class="fs-9pt bold" style="color: #000">{{ $shipment->volumes }}</div>
                    </div>
                </div>
            </div>
            {{--<div class="guide-block-right" style="width: 52mm">

            </div>--}}
        </div>
        <div class="guide-row" style="border-bottom: 0; border-color: #fff">
            <div class="guide-block-right" style="width: 105mm; padding: 0px; border-bottom: 1px solid #fff">
                <div class="guide-row" style="border-bottom: 0; border-color: #fff">
                    <div class="guide-block-right pull-left" style="width: 50mm; height: 5mm; padding-top: 5px;">
                        <div class="guide-block-title" style="width: 22mm; float: left; color: #fff">REEMBOLSO</div>
                        <div class="fs-9pt bold" style="float: left">{{ $shipment->charge_price ? money($shipment->charge_price, '€') : '' }}</div>
                    </div>
                    <div class="guide-block-right pull-left" style="width: 50mm; height: 5mm; padding-top: 5px; border: none;">
                        <div class="guide-block-title" style="width: 18mm; float: left; color: #fff">MATRÍCULA</div>
                        <div class="fs-9pt bold" style="float: left"></div>
                    </div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 100mm; border-bottom: 1px solid #fff; padding: 0;">
                <div class="guide-row" style="border-bottom: 0;">
                    <div class="guide-block-right pull-left" style="width: 64mm; height: 5mm; padding-top: 5px;">
                        <div class="guide-block-title" style="width: 39mm; float: left; color: #fff">GUIA DE TRANSPORTE Nº</div>
                        <div class="fs-9pt bold" style="float: left; text-align: right">{{ $shipment->tracking_code }}</div>
                    </div>
                    <div class="guide-block-right pull-left" style="width: 31.7mm; height: 5mm; padding-top: 5px; border: none;">
                        <div class="guide-block-title" style="width: 3mm; float: left; color: #fff">DATA</div>
                        <div class="fs-9pt bold" style="width: 25mm; text-align: right; float: right;">{{ $shipment->date }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="guide-row" style="border-color: #fff">
            <div class="guide-block-right" style="width: 110mm; padding: 0px;">
                <div class="guide-row" style="border-bottom: 0; height: 22mm;">
                    <div class="guide-block-right pull-left" style="width: 100%; height: 11mm; border-color: #fff">
                        <div class="guide-block-title" style="color: #fff">OBSERVAÇÕES DO EXPEDIDOR</div>
                        @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                            <div class="guide-payment" style="float:left; width: 30%; margin-right: 15px">
                                Com Retorno
                            </div>
                        @endif

                        @if($shipment->payment_at_recipient && Setting::get('labels_hide_payment_at_recipient'))
                            <div class="guide-payment" style="float:left; width: 45%">
                                <small>Pag no destino</small>
                            </div>
                            {{--<small><small>&nbsp;&nbsp;N.º Controlo: {{ random_int(1, 999999) . '0' . number_format($shipment->total_price_for_recipient + $shipment->total_expenses, 2, '', '') }}</small></small>--}}
                            <div class="clearfix"></div>
                        @elseif($shipment->payment_at_recipient && !Setting::get('labels_hide_payment_at_recipient'))
                            <div class="guide-payment" style="float:left; width: 45%">
                                <small>Pagamento no destino</small>
                                <small><small>&nbsp;&nbsp;A cobrar: {{ money($shipment->total_price_for_recipient + $shipment->total_expenses, '€') }}</small></small>
                                <div class="clearfix"></div>
                            </div>
                        @endif
                    </div>
                    <div class="guide-block-right pull-left" style="width: 100%; height: 5mm; border: none;">
                        <div class="guide-block-title" style="color: #fff">OBSERVAÇÕES DO TRANSPORTADOR</div>
                        <div style="height: 3px"></div>
                        <div style="margin-left: 330px; margin-bottom: -40px; float: left; font-size: 8pt">{{ Setting::get('guides_fuel_price') }}</div>
                        <small class="fs-6pt" style="color: #fff">Para efeitos do Art. 145/2008 de 28 de julho o preço de Refª do Combustível é de ................ Euros/Litro</small>
                    </div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 93mm; padding: 0;">
                <div class="guide-row" style="border: none">
                    <div class="guide-block-right pull-left" style="width: 100%; height: 13mm; border-bottom: 1px solid #fff">
                        <div class="guide-block-title" style="color: #fff">OBSERVAÇÕES DO DESTINATÁRIO</div>
                        <div class="fs-8pt bold">{{ $shipment->obs }}</div>
                    </div>
                </div>
                <div class="guide-ro" style="border: none">
                    <div class="guide-block-right" style="width: 62mm; height: 5mm; ">
                        <div class="guide-block-title" style="width: 32mm; float: left; color: #fff">DATA/HORA CARGA</div>
                        <div class="fs-8pt bold" style="float: left">
                            {{ $shipment->shipping_date }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>