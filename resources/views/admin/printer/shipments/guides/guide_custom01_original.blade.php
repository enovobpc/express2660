<div style="position:absolute; font-size: 8pt; rotate: -90; right: 1mm; margin-top: 4mm">{{ $copyNumber }}</div>
<div style="position:absolute; font-size: 6pt; rotate: -90; right: 1mm; margin-top: 27mm">Processado por computador / Despacho DGTT N.º21 994/99 (2ª série)</div>
<div style="position:absolute; font-size: 8pt; rotate: -90; left: 2mm; margin-top: 4mm">
    <b style="font-weight: bold; font-size: 7pt; margin-top: 4mm">QUICKBOX - Software para Transportes e Logística - www.quickbox.pt</b>
</div>
<div class="transportation-guide">
    <div class="guide-content" style="margin-left: 5mm; margin-right: 3mm">
        <div class="guide-row">
            <div class="guide-block" style="width: 95mm; height: 50mm; padding: 0">
                <div style="height: 25mm; border-bottom: 1px solid #000; padding-left: 5px">
                    <div class="guide-block-title">
                        <div style="width: 50%; float: left">EXPEDIDOR/LOCAL DE CARGA</div>
                        <div style="width: 49%; float: left; text-align: right; font-size: 8pt">
                            @if($shipment->sender_phone && $shipment->sender_phone != '.')
                                Tlf: {{ $shipment->sender_phone }}
                            @endif
                        </div>
                    </div>
                    <div class="pull-left"  style="width: 100% ">
                        <div class="fs-8pt bold">
                            {{ str_limit($shipment->sender_name, 45) }}<br>
                            {{ $shipment->sender_address }}
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                        </div>
                    </div>
                </div>
                <div style="height: 25mm; padding-left: 5px">
                    <div class="guide-block-title">
                        <div style="width: 50%; float: left">DESTINATÁRIO/LOCAL DE DESCARGA</div>
                        <div style="width: 49%; float: left; text-align: right; font-size: 8pt">
                            @if($shipment->recipient_phone && $shipment->recipient_phone != '.')
                                Tlf: {{ $shipment->recipient_phone }}
                            @endif
                        </div>
                    </div>
                    <div class="pull-left"  style="width: 100% ">
                        <div class="fs-8pt bold lh-1-2">
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
            <div class="guide-block-right" style="width: 98.6mm;">
                <div class="guide-row" style="border: none">
                    <div style="height: 34mm; width: 48.5%; float: left; border-right: 1px solid #000; margin-top: -5px">
                        <div style="text-align: center">
                            @if($shipment->recipientAgency->filepath)
                                <img src="{{ asset(@$shipment->recipientAgency->filehost . $shipment->recipientAgency->getCroppa(300)) }}" style="max-width: 40mm; height: 15mm" class="m-t-6"/>
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
                        </div>
                    </div>
                    <div style="width: 50.2%; float: left;">
                        <div class="text-center">
                            <div style="display: inline-block; margin-top: 5px">
                                <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.7" height="1.4" style="margin-right: -10px"/>
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
                <div class="guide-row fs-9pt" style="border-bottom: none; border-top: 1px solid #000; margin-left: -4px; margin-right: -5px; padding-left: 5px">
                    <div style="height: 15mm; width: 26.5%; float: left;">
                        <div class="guide-block-title">Designação</div>
                        <br/>
                    </div>
                    <div style="height: 15mm; width: 24%; float: left;">
                        <div class="guide-block-title">Peso Bruto</div><br/>
                        <div class="fs-8pt bold">{{ money($shipment->weight) }}</div>
                    </div>
                    <div style="height: 15mm; width: 24%; float: left;">
                        <div class="guide-block-title">Nº Volumes</div>
                        <br/>
                        <div class="fs-8pt bold">{{ $shipment->volumes }}</div>
                    </div>
                    <div style="height: 15mm; width: 25%; float: left;">
                        <div class="guide-block-title">Tipo Embalagem</div>
                    </div>
                </div>
            </div>
            {{--<div class="guide-block-right" style="width: 52mm">

            </div>--}}
        </div>
        <div class="guide-row" style="border-bottom: 0;">
            <div class="guide-block" style="width: 105mm; padding: 0px; border-bottom: 1px solid #000">
                <div class="guide-row" style="border-bottom: 0;">
                    <div class="guide-block pull-left" style="width: 50mm; height: 5mm; padding-top: 5px;">
                        <div class="guide-block-title" style="width: 22mm; float: left">REEMBOLSO</div>
                        <div class="fs-8pt bold" style="float: left">{{ $shipment->charge_price ? money($shipment->charge_price, Setting::get('app_currency')) : '' }}</div>
                    </div>
                    <div class="guide-block pull-left" style="width: 50mm; height: 5mm; padding-top: 5px; border: none;">
                        <div class="guide-block-title" style="width: 18mm; float: left">MATRÍCULA</div>
                        <div class="fs-8pt bold" style="float: left">00-AA-00</div>
                    </div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 93mm; border-bottom: 1px solid #000; padding: 0;">
                <div class="guide-row" style="border-bottom: 0;">
                    <div class="guide-block pull-left" style="width: 62mm; height: 5mm; padding-top: 5px;">
                        <div class="guide-block-title" style="width: 36mm; float: left">GUIA DE TRANSPORTE Nº</div>
                        <div class="fs-8pt bold" style="float: left; text-align: right">{{ $shipment->tracking_code }}</div>
                    </div>
                    <div class="guide-block pull-left" style="width: 26mm; height: 5mm; padding-top: 5px; border: none;">
                        <div class="guide-block-title" style="width: 3mm; float: left">DATA</div>
                        <div class="fs-7pt bold" style="width: 20mm; float: right; margin-right: -15px;">{{ $shipment->date }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="guide-row">
            <div class="guide-block" style="width: 105mm; padding: 0px;">
                <div class="guide-row" style="border-bottom: 0; height: 22mm;">
                    <div class="guide-block-right pull-left" style="width: 100%; height: 11mm; border-bottom: 1px solid #000">
                        <div class="guide-block-title">OBSERVAÇÕES DO EXPEDIDOR</div>
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
                                <small><small>&nbsp;&nbsp;A cobrar: {{ money($shipment->total_price_for_recipient + $shipment->total_expenses, Setting::get('app_currency')) }}</small></small>
                                <div class="clearfix"></div>
                            </div>
                        @endif
                    </div>
                    <div class="guide-block pull-left" style="width: 100%; height: 5mm; border: none;">
                        <div class="guide-block-title">OBSERVAÇÕES DO TRANSPORTADOR</div>
                        <div style="height: 12px"></div>
                        <small class="fs-6pt">Para efeitos do Art. 145/2008 de 28 de julho o preço de Refª do Combustível é de ................ Euros/Litro</small>
                    </div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 93mm; padding: 0;">
                <div class="guide-row" style="border: none">
                    <div class="guide-block-right pull-left" style="width: 100%; height: 13mm; border-bottom: 1px solid #000">
                        <div class="guide-block-title">OBSERVAÇÕES DO DESTINATÁRIO</div>
                        <div class="fs-8pt bold">{{ $shipment->obs }}</div>
                    </div>
                </div>
                <div class="guide-ro" style="border: none">
                    <div class="guide-block-right" style="width: 62mm; height: 5mm; ">
                        <div class="guide-block-title" style="width: 32mm; float: left">DATA/HORA CARGA</div>
                        <div class="fs-8pt bold" style="float: left">{{ $shipment->created_at }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="guide-row">
            <div class="guide-block" style="width: 64mm; height: 10mm; padding: 0px; padding-left: 5px;">
                <div class="guide-block-title">ASSINATURA DO EXPEDIDOR</div>
                <div class="fs-8pt bold">{{ $shipment->obs }}</div>
            </div>
            <div class="guide-block" style="width: 58mm; height: 10mm; padding: 0px; padding-left: 5px;">
                <div class="guide-block-title">ASSINATURA DO TRANSPORTADOR</div>
                <div class="fs-8pt bold">{{ $shipment->obs }}</div>
            </div>
            <div class="guide-block-right" style="width: 69mm; height: 10mm; padding: 0px; padding-left: 5px;">
                <div class="guide-block-title">HORA, DATA E ASSINATURA DO DESTINATÁRIO</div>
                <div class="fs-8pt bold m-t-6">_______, _____/_____/_____ _____________________</div>
            </div>
        </div>
    </div>
</div>