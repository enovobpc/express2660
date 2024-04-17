<div style="position:absolute; font-size: 8pt; rotate: -90; right: 2mm; margin-top: 4mm">{{ $copyNumber }}</div>
<div class="transportation-guide">
    <div class="guide-content">
        <div class="guide-row">
            <div class="guide-block" style="width: 113.5mm; height: 14mm">
                <div class="guide-block-title">
                    <div style="float: right; width: 2cm; text-align: right;" class="fs-8pt">Cliente {{ $shipment->customer->code }}</div>
                    <div class="pull-left">EXPEDIDOR (denominação social ou nome, sede ou domicílio)</div>
                </div>
                <div class="pull-left"  style="width: 74%">
                    <div class="fs-8pt bold lh-1-1">
                        {{ substr($shipment->sender_name, 0, 45) }}<br>
                        {{ substr($shipment->sender_address, 0, 45) }}
                        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                    </div>
                </div>
                <div class="fs-8pt text-right" style="width: 25% ">
                    <br/>
                    @if($shipment->sender_phone && $shipment->sender_phone != '.')
                        Tlf: {{ $shipment->sender_phone }}
                    @endif
                    <br/>
                </div>
            </div>
            <div class="guide-block-right" style="width: 80.2mm">
                <div class="text-center">
                    <div style="display: inline-block; width: 64.2mm; float: left;">
                        <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.93" height="0.8"/>
                        <div class="fs-9pt bold text-center m-t-4">
                            {{ $shipment->tracking_code }}
                            @if($shipment->reference)
                            &bull; REF# {{ substr($shipment->reference, 0, 17) }}
                            @endif
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
                        <div class="guide-block-title">DESTINATÁRIO (denominação social ou nome, sede ou domicílio)</div>
                        <div class="pull-left"  style="width: 74% ">
                            <div class="fs-8pt bold lh-1-2">
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
                        <div class="fs-8pt text-right" style="width: 25% ">
                            @if($shipment->recipient_vat)
                                <b>NIF: {{ $shipment->recipient_vat }}</b>
                            @endif
                            <br/>
                            @if($shipment->recipient_phone && $shipment->recipient_phone != '.')
                                Tlf: {{ $shipment->recipient_phone }}
                            @endif<br/>
                        </div>
                    </div>
                    <div class="guide-block-right border-bottom">
                        <div class="guide-block-title">
                            <div class="pull-left" style="width: 20mm">LOCAL DE CARGA</div>
                            <div class="pull-left text-right" style="width: 89mm;">
                                <b class="bold">{{ @$shipment->service->name }}</b>  &nbsp;&nbsp; DATA: {{ $shipment->date }} &nbsp;&nbsp; HORA: {{ $shipment->created_at->format('H:i') }}
                            </div>
                        </div>
                        <div class="fs-8pt bold">
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                        </div>
                    </div>
                    <div class="guide-block-right">
                        <div class="guide-block-title">LOCAL DE DESCARGA</div>
                        <div class="fs-8pt bold">
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                        </div>
                    </div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 83mm; padding: 0;">
                @if(@$customerAccount && Setting::get('customer_block_provider_labels'))
                    <div class="guide-block-right" style="height: 22.6mm">
                        <div class="guide-block-title">TRANSPORTADOR</div>
                        <div class="pull-left" style="width: 49%; margin-top: 15px">
                            @if($shipment->agency->filepath)
                                <img src="{{ @$shipment->agency->filehost . @$shipment->agency->getCroppa(300) }}" style="max-width: 40mm; max-height: 12mm"  class="m-t-6"/>
                            @else
                                <h5 style="margin:0px"><b>{{ $shipment->agency->company }}</b></h5>
                            @endif
                            <div class="fs-7pt lh-1-2 m-t-8">
                                <span class="{{ strlen($shipment->agency->web) > 23 ? 'fs-7pt' : 'fs-8pt' }} bold">{{ $shipment->agency->web }}</span>
                            </div>
                        </div>
                        <div style="width: 50%; margin-top: -20px;" class="fs-7pt">
                            NIF: {{ $shipment->agency->vat }}
                            @if($shipment->agency->capital)
                            &bull; Cap.Soc. {{ $shipment->agency->capital }}
                            @endif
                            <br/>
                            {{ str_limit($shipment->agency->company, 28) }}
                            <br/>
                            {{ $shipment->agency->address }}
                            {{ $shipment->agency->zip_code }} {{ $shipment->agency->city }}<br>
                            Telef: {{ $shipment->agency->phone }}
                            @if($shipment->agency->mobile)
                                / {{ $shipment->agency->mobile }}
                            @endif
                            <br/>
                            @if($shipment->agency->email)
                                E-mail: {{ $shipment->agency->email }}
                            @endif
                        </div>
                    </div>
                @else
                    <div class="guide-block-right" style="height: 22.6mm">
                        <div class="guide-block-title">TRANSPORTADOR</div>
                        <div class="pull-left" style="width: 49%; margin-top: 15px">
                            @if($shipment->recipientAgency->filepath)
                                <img src="{{ @$shipment->recipientAgency->filehost . @$shipment->recipientAgency->getCroppa(300) }}" style="max-width: 38mm; max-height: 12mm"  class="m-t-6"/>
                            @else
                                <h5 style="margin:0px"><b>{{ $shipment->recipientAgency->company }}</b></h5>
                            @endif
                            <div class="fs-7pt lh-1-2 m-t-8">
                                <span class="fs-9pt bold">{{ $shipment->recipientAgency->web }}</span>
                            </div>
                        </div>
                        <div style="width: 50%; margin-top: -12px;" class="fs-7pt">
                            NIF: {{ $shipment->recipientAgency->vat }}
                            @if($shipment->agency->capital)
                            &bull; Cap.Soc. {{ $shipment->agency->capital }}
                            @endif
                            <br/>
                            {{ str_limit($shipment->recipientAgency->company, 28) }}
                            <br/>
                            {{ $shipment->recipientAgency->address }}
                            {{ $shipment->recipientAgency->zip_code }} {{ $shipment->recipientAgency->city }}<br>
                            Telef: {{ $shipment->recipientAgency->phone }}
                            @if($shipment->recipientAgency->mobile)
                                / {{ $shipment->recipientAgency->mobile }}
                            @endif
                            <br/>
                            @if($shipment->recipientAgency->email)
                                E-mail: {{ $shipment->recipientAgency->email }}
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <div class="guide-row">
            <div class="guide-block" style="width: 131mm; height: 10mm; border-bottom: 0;">
                <div class="guide-block-title">MERCADORIA TRANSPORTADA</div>
                <div class="pull-left" style="width: 17%">
                    <div class="fs-7pt lh-1-1">N.º Volumes ou Objetos</div>
                    <div class="fs-8pt bold">{{ $shipment->volumes }}</div>
                </div>
                <div class="pull-left" style="width: 23%">
                    <div class="fs-7pt lh-1-1">Tipo embalagem ou acondicionamento</div>
                    <div class="fs-8pt bold">{{ $shipment->packing }}</div>
                </div>
                <div class="pull-left" style="width: 28%">
                    <div class="fs-7pt">Designação corrente</div>
                    <div class="fs-8pt bold">{{ $shipment->packing_description }}</div>
                </div>
                <div class="pull-left" style="width: 17%;">
                    <div class="fs-7pt">Peso Bruto (kg)</div>
                    <div class="fs-8pt bold">{{ money($shipment->weight) }}</div>
                </div>
                <div class="pull-left" style="width: 12%;">
                    <div class="fs-7pt">Volume (m3)</div>
                    <div class="fs-8pt bold">{{ money($shipment->m3) }}</div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 60mm;">
                <div class="guide-block-title lh-1-2">DATA E ASSINATURA DO DESTINATÁRIO</div>
                <br/>
                ____________________ &nbsp;&nbsp; ____/____/____
            </div>
        </div>
        <div class="guide-row">
            <div class="guide-block p-t-0 p-b-0" style="width: 73mm; height: 24mm; border-bottom: 0;">
                <div class="guide-block-title">DECLARAÇÃO/INSTRUÇÕES DO EXPEDIDOR</div>
                <div style="height: 17mm">
                    <strong class="bold fs-9pt">{{ $shipment->obs }}</strong>
                    @if($shipment->charge_price != 0.00)
                        <div class="guide-payment" style="float:left; width: 50%; margin-right: 15px">
                            <small>Reembolso:</small> {{ money($shipment->charge_price, Setting::get('app_currency')) }}
                        </div>
                    @endif

                    @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                        <div class="guide-payment" style="float:left; width: 30%; margin-right: 15px">
                            Com Retorno
                        </div>
                    @endif

                    @if(!empty($shipment->requested_by) && $shipment->requested_by != $shipment->customer_id)
                        <div class="guide-payment" style="float:left; width: 70%">
                            <small>Pagamento mensal pelo destinatário</small>
                        </div>
                        <div class="clearfix"></div>
                    @elseif($shipment->payment_at_recipient && Setting::get('labels_hide_payment_at_recipient'))
                        <div class="guide-payment" style="float:left; width: 45%">
                            <small>Pagamento no destino</small>
                        </div>
                        <small><small>&nbsp;&nbsp;N.º Controlo: {{ random_int(1, 999999) . '0' . number_format($shipment->total_price_for_recipient + $shipment->total_expenses, 2, '', '') }}</small></small>
                        <div class="clearfix"></div>
                    @elseif($shipment->payment_at_recipient && !Setting::get('labels_hide_payment_at_recipient'))
                        <div class="guide-payment" style="float:left; width: 45%">
                            <small>Pagamento no destino</small>
                            <small><small>&nbsp;&nbsp;A cobrar: {{ money($shipment->total_price_for_recipient + $shipment->total_expenses, Setting::get('app_currency')) }}</small></small>
                            <div class="clearfix"></div>
                        </div>
                    @endif
                </div>
                <div class="fs-7pt lh-1-0" style="margin-top: 0mm">
                    _______/_______/_______ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Assinatura do Expedidor
                </div>
            </div>
            <div class="guide-block" style="width: 57.6mm; height: 25mm; padding: 0">
                <div class="guide-row" style="height: 14mm">
                    <div class="guide-block-right">
                        <div class="guide-block-title lh-1-3">DECLARAÇÃO/INSTRUÇÕES DO TRANSPORTADOR</div>
                        <div class="fs-7pt lh-1-1">
                            Autorização para o exercício da actividade de prestador de serviços postais
                            Nº ICP-ANACOM- 2/2013-SP
                            </span>
                        </div>
                    </div>

                </div>
                <div class="fs-6pt text-center" style="margin-top: 24px">Assinatura do Transportador</div>
            </div>
            <div class="guide-block-right" style="width: 50mm;  height: 25mm; padding: 0;">
                <div class="p-l-4 p-t-4">
                    <div class="guide-block-title lh-1-2">OBSERVAÇÕES</div>
                </div>
            </div>
        </div>
    </div>

    <div class="fs-6pt">
        <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ app_brand('docsignature') }}</b></div>
        <div class="pull-left text-right" style="width: 42%">Documento processado por computador / Deliberação n.º 813/2020</div>
    </div>

</div>