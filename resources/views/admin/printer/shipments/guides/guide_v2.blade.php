<div style="position:absolute; font-size: 7pt; rotate: -90; right: 2mm; margin-top: 4mm; font-weight: bold">{{ $copyNumber }}</div>
<div style="position:absolute; font-size: 6.5pt; rotate: -90; left: 2mm; margin-top: 8mm; font-weight: bold">{{ app_brand('docsignature') }}</div>
 <div class="transportation-guide" style="{{ $copyId == 3 ? 'border:none' : '' }}">
    <div class="guide-content">
        <div class="guide-row">
            <div class="guide-block" style="width: 113.5mm; height: 13mm;">
                <div class="guide-block-title">
                    @if($shipment->customer_id)
                    <div style="float: right; width: 4cm; text-align: right;" class="fs-8pt">Cliente {{ @$shipment->customer->code_abbrv ? @$shipment->customer->code_abbrv : @$shipment->customer->code }}</div>
                    @endif
                    <div class="pull-left">EXPEDIDOR (denominação social ou nome, sede ou domicílio)</div>
                </div>
                <div class="pull-left"  style="width: 69%">
                    <div class="fs-8pt bold lh-1-1">
                        {{ str_limit($shipment->sender_name, 45) }}<br>
                        {{ $shipment->sender_address }}
                        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                    </div>
                </div>
                <div class="fs-8pt text-right" style="width: 30% ">
                    NIF: {{ $shipment->sender_vat ? $shipment->sender_vat : @$shipment->customer->vat }}
                    @if($shipment->sender_phone && $shipment->sender_phone != '.')
                        Tlf:{{ $shipment->sender_phone }}
                    @endif
                    <br/>
                </div>
            </div>
            <div class="guide-block-right" style="width: 80.2mm;">
                <div class="text-center">
                    <div style="display: inline-block; width: 64.2mm; float: left;">
                        <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.93" height="0.8"/>
                        <div class="fs-9pt bold text-center m-t-4">
                            Guia Transporte N.º {{ $shipment->tracking_code }}
                        </div>
                    </div>
                    <div style="width: 16mm; float: left; text-align: right">
                        <img src="{{ @$qrCode }}" style="height: 12mm"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="guide-row">
            <div class="guide-block" style="width: 115.6mm; padding: 0px">
                <div class="guide-row" style="border-bottom: 0;">
                    <div class="guide-block-right border-bottom" style="padding-top: 2px; padding-bottom: 2px">
                        <div class="guide-block-title">DESTINATÁRIO (denominação social ou nome, sede ou domicílio)</div>
                        <div class="pull-left"  style="width: 74%">
                            <div class="fs-8pt bold lh-1-1">
                                {{ str_limit($shipment->recipient_name, 47) }}<br>
                                {{ $shipment->recipient_address }}<br/>
                                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                            </div>
                        </div>
                        <div class="fs-8pt text-right" style="width: 25%;">
                            @if($shipment->recipient_attn)
                            <b>A/C: {{ $shipment->recipient_attn }}</b>
                            @endif
                            <br/>
                            @if($shipment->recipient_phone && $shipment->recipient_phone != '.')
                            Tlf: {{ $shipment->recipient_phone }}
                            @endif<br/>
                        </div>
                    </div>
                    <div class="guide-block-right border-bottom" style="padding-top: 2px; padding-bottom: 2px;  height: 6.8mm">
                        <div class="guide-block-title  lh-1-1">
                           <div class="pull-left" style="width: 32mm">LOCAL DE CARGA</div>
                           <div class="pull-left text-right" style="width: 80mm;">
                               DATA: {{ $shipment->date }} &nbsp;&nbsp; HORA: {{ $shipment->created_at->format('H:i') }}
                           </div>
                        </div>
                        <div class="fs-8pt bold ">
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                        </div>
                    </div>
                    <div class="guide-block-right" style="padding-top: 2px; padding-bottom: 2px; height: 7mm">
                        <div class="guide-block-title">LOCAL DE DESCARGA</div>
                        <div class="fs-8pt bold lh-1-1">
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                        </div>
                    </div>
                </div>
            </div>
            <div class="guide-block-right" style="width: 83mm; padding: 0;">
                @if(@$customerAccount && Setting::get('customer_block_provider_labels'))
                    <div class="guide-block-right border-bottom lh-1-1" style="height: 19.9mm;">
                        <div class="guide-block-title">TRANSPORTADOR</div>
                        <div class="pull-left" style="width: 49%">
                            @if($shipment->agency->filepath)
                                <img src="{{ @$shipment->agency->filehost . @$shipment->agency->getCroppa(300) }}" style="max-width: 38mm; max-height: 10.5mm"  class="m-t-4"/>
                            @else
                                <h5 style="margin:0px"><b>{{ $shipment->agency->company }}</b></h5>
                            @endif
                            <div class="fs-7pt lh-1-2 m-0">
                                <span class="{{ strlen($shipment->agency->web) > 23 ? 'fs-6pt' : 'fs-8pt' }} bold">{{ $shipment->agency->web }}</span>
                            </div>
                        </div>
                        <div style="width: 50%; margin-top: -20px;" class="fs-7pt lh-1-2">
                            NIF: {{ $shipment->agency->vat }}
                            @if($shipment->agency->charter)
                            &bull; Alvará {{ $shipment->agency->charter }}
                            @endif
                            {{--@if($shipment->agency->capital)
                            &bull; Cap.Soc. {{ $shipment->agency->capital }}
                            @endif--}}
                            <br/>
                            {{ str_limit($shipment->agency->company, 30) }}
                            <br/>
                            {{ $shipment->agency->address }}
                            {{ $shipment->agency->zip_code }} {{ $shipment->agency->city }}<br>
                            Telef: {{ $shipment->agency->phone }}*
                            @if($shipment->agency->mobile)
                                / {{ $shipment->agency->mobile }}**
                            @endif
                            <br/>
                            @if($shipment->agency->email)
                                {{ $shipment->agency->email }}
                            @endif
                        </div>
                    </div>
                @else
                    <div class="guide-block-right border-bottom" style="height: 19.9mm">
                        <div class="guide-block-title">TRANSPORTADOR</div>
                        <div class="pull-left" style="width: 49%">
                            @if($shipment->recipientAgency->filepath)
                                <img src="{{ @$shipment->recipientAgency->filehost . @$shipment->recipientAgency->getCroppa(300) }}" style="max-width: 38mm; max-height: 10.5mm"  class="m-t-4"/>
                            @else
                            <h5 style="margin:0px"><b>{{ $shipment->recipientAgency->company }}</b></h5>
                            @endif
                                <div class="fs-7pt lh-1-2 m-0">
                                    <span class="{{ strlen($shipment->agency->web) > 23 ? '' : 'fs-8pt' }} bold">{{ $shipment->agency->web }}</span>
                                </div>
                        </div>
                        <div style="width: 50%; margin-top: -12px;" class="fs-7pt lh-1-2">
                            NIF: {{ $shipment->recipientAgency->vat }}
                            @if($shipment->agency->charter)
                            &bull; Alvará {{ $shipment->agency->charter }}
                            @endif
                            {{--@if($shipment->agency->capital)
                            &bull; Cap.Soc. {{ $shipment->agency->capital }}
                            @endif--}}
                            <br/>
                            {{ str_limit($shipment->recipientAgency->company, 28) }}
                            <br/>
                            {{ $shipment->recipientAgency->address }}
                            {{ $shipment->recipientAgency->zip_code }} {{ $shipment->recipientAgency->city }}<br>
                            Telef: {{ $shipment->recipientAgency->phone }}*
                            @if($shipment->recipientAgency->mobile)
                            / {{ $shipment->recipientAgency->mobile }}**
                            @endif
                            <br/>
                            @if($shipment->recipientAgency->email)
                            E-mail: {{ $shipment->recipientAgency->email }}
                            @endif
                        </div>
                    </div>
                @endif
                <div class="guide-block-right lh-1-1" style="height: 6mm">
                    <div class="pull-left" style="width: 33.3%">
                        <div class="guide-block-title">MATRÍCULA</div>
                        <div class="fs-8pt bold">{{ $shipment->car_registration }}</div>
                    </div>
                    <div class="pull-left" style="width: 33.3%">
                        <div class="guide-block-title">PESO BRUTO (kg)</div>
                        <div class="fs-8pt bold">{{ $shipment->vehicle_kg ? money($shipment->vehicle_kg) : '' }}</div>
                    </div>
                    <div class="pull-left"  style="width: 33%">
                        <div class="guide-block-title">CARGA ÚTIL (kg)</div>
                        <div class="fs-8pt bold">{{ $shipment->vehicle_kg_usefull ? money($shipment->vehicle_kg_usefull) : '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="guide-row" style="height: 6px">
            <div class="guide-block" style="width: 113.5mm; border-bottom: 0;">
                <div class="guide-block-title">MERCADORIA TRANSPORTADA</div>
                <div class="pull-left lh-1-0" style="width: 12%;">
                    <div class="fs-7pt">Objetos</div>
                    <div class="fs-8pt bold">{{ $shipment->volumes }}</div>
                </div>
                <div class="pull-left lh-1-0" style="width: 20%;">
                    <div class="fs-7pt">Tipo embalagem</div>
                    <div class="fs-8pt bold">{{ $shipment->packing }}</div>
                </div>
                <div class="pull-left lh-1-0" style="width: 32%;">
                    <div class="fs-7pt">Designação corrente</div>
                    <div class="fs-8pt bold">{{ $shipment->packing_description }}</div>
                </div>
                <div class="pull-left lh-1-0" style="width: 17%;">
                    <div class="fs-7pt">Peso Bruto (kg)</div>
                    <div class="fs-8pt bold">{{ money($shipment->weight) }}</div>
                </div>
                <div class="pull-left lh-1-0" style="width: 13%;">
                    <div class="fs-7pt">Volume (m3)</div>
                    <div class="fs-8pt bold">{{ $shipment->volume_m3 ?  money($shipment->volume_m3, '', 4) : money($shipment->fator_m3, '', 4) }}</div>
                </div>
            </div>
            <div class="guide-block" style="border-bottom: 0; border: none">
                <div class="guide-block-title">CLASSIFICAÇÃO MERCADORIAS PERIGOSAS (ADR)</div>
                <div class="pull-left lh-1-0" style="width: 47mm;">
                    <div class="fs-7pt">Designação Técnica</div>
                </div>
                <div class="pull-left lh-1-0" style="width: 10mm;">
                    <div class="fs-7pt">Classe</div>
                    <div class="fs-8pt bold">{{ @$dimension->adr_class }}</div>
                </div>
                <div class="pull-left lh-1-0" style="width: 10mm;">
                    <div class="fs-7pt">Nº</div>
                    <div class="fs-8pt bold">{{ @$dimension->adr_number }}</div>
                </div>
                <div class="pull-left lh-1-0" style="width: 10mm;">
                    <div class="fs-7pt">Alinea</div>
                    <div class="fs-8pt bold">{{ @$dimension->adr_letter }}</div>
                </div>
            </div>
        </div>
        <div class="guide-row">
            <div class="guide-block lh-1-1" style="width: 115.6mm; height: 22mm; padding: 2px 0">
                <div style="border-bottom: 1px solid #000; padding: 2px 3px 0 3px;  height: 10mm">
                    <div class="guide-block-title">DECLARAÇÃO/INSTRUÇÕES DO EXPEDIDOR</div>
                    <span style="font-weight: bold; font-size: 10px" class="lh-1-0">{{ $shipment->obs }}</span>
                </div>
                <div style="border-bottom: 1px solid #000; height: 7mm">
                    <div style="padding: 3px">
                        <div class="guide-block-title lh-1-2">DECLARAÇÃO/INSTRUÇÕES DO TRANSPORTADOR</div>
                        <div class="fs-8pt lh-1-1 italic">
                            Preço ref. combustível: {{ money(Setting::get('guides_fuel_price'), Setting::get('app_currency')) }}/Litro.
                            Material embalado não conferido.
                        </div>
                    </div>
                </div>
                <div>
                    <div style="padding: 2px 3px 0 3px; height: 7mm">
                        <div class="guide-block-title">DECLARAÇÃO/INSTRUÇÕES DO DESTINATÁRIO</div>
                        <span style="font-weight: bold; font-size: 10px" class="lh-1-0">{{ $shipment->obs_delivery }}</span>
                    </div>
                </div>
            </div>
            <div class="guide-block-right lh-1-1" style="width: 80.5mm;">
                <div>
                    <div class="guide-block-title m-b-5">OUTRAS INDICAÇÕES</div>
                    @if($shipment->charge_price != 0.00)
                        <div class="guide-payment" style="float:left; width: 30%; margin-right: 2px">
                            <small>Reembolso:</small><br/>
                            <small>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</small>
                        </div>
                    @endif

                    @if(!empty($shipment->requested_by) && $shipment->requested_by != $shipment->customer_id)
                        <div class="guide-payment" style="float:left; width: 70%">
                            <small>Pagamento mensal pelo destinatário</small>
                        </div>
                        <div class="clearfix"></div>
                    @elseif($shipment->cod == 'D' && !Setting::get('labels_show_cod'))
                        <div class="guide-payment" style="float:left; width: 30%; margin-right: 15px">
                            <small>Portes Envio:</small><br/>
                            <small>{{ random_int(1, 999999) . '0' . number_format($shipment->billing_subtotal, 2, '', '') }}</small>
                        </div>
                        <div class="clearfix"></div>
                    @elseif($shipment->cod == 'D' && Setting::get('labels_show_cod'))
                        <div class="guide-payment" style="float:left; width: 30%; margin-right: 15px">
                            <small>Portes Envio:</small><br/>
                            <small>{{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}</small>
                        </div>
                    @endif

                    @if($shipment->has_return && (in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return)))
                        <div class="guide-payment" style="float:left; width: 36%; margin-right: 15px">
                            @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                                <small>Ret. Encomenda</small><br/>
                            @endif
                            @if($shipment->has_return && in_array('rguide', $shipment->has_return))
                            <small>Ret. Guia Assinada</small>
                            @endif
                        </div>
                    @endif
                    <div class="fs-8pt lh-1-2" style="width: 100%;">
                    <b class="bold m-b-5">{{ @$shipment->service->name }}</b>
                        @if($shipment->start_hour || $shipment->end_hour)
                            @if(!$shipment->start_hour && $shipment->end_hour)
                            &bull; Entrega até às {{ $shipment->end_hour }}
                            @else
                            &bull; Horario: {{ $shipment->start_hour }} {{ $shipment->end_hour ? '-'.$shipment->end_hour : '' }}
                            @endif
                        @endif
                    </div>

                    @if($shipment->reference)
                        <div class="fs-8pt" style="width: 100%;">
                            Ref: {{ substr($shipment->reference, 0, 17) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="guide-row" style="border-bottom: 0">
            <div class="guide-block lh-1-1" style="width: 55.6mm; border-bottom: 0; height: 7mm">
                <div class="guide-block-title">ASSINATURA EXPEDIDOR</div>
            </div>
            <div class="guide-block lh-1-1" style="width: 55.5mm; border-bottom: 0; height: 7mm">
                <div class="guide-block-title">ASSINATURA TRANSPORTADOR</div>
            </div>
            <div class="guide-block-right lh-1-1" style="width: 80mm; border-bottom: 0;">
                <div class="guide-block-title">LOCAL, DATA E ASSINATURA DO DESTINATÁRIO</div>
            </div>
        </div>
    </div>
     <div class="fs-6pt">
         <div class="pull-left fs-6pt" style="width: 65.5%"><b>A assinatura pressupõe conhecimento e aceitação das condições de transporte. Consulte-as em {{ $shipment->recipientAgency->web }}</b></div>
         <div class="pull-left fs-6pt text-right" style="width: 34%;">Processado por computador / @if($shipment->agency->phone)*custo rede fixa @endif  @if($shipment->agency->mobile)**custo rede móvel @endif</div>

{{--         <div class="pull-left" style="width: 57%"><b style="font-weight: bold">ENOVO TMS - Software Transportes e Logísitica | tms.enovo.pt</b></div>
         <div class="pull-left text-right" style="width: 42%">Documento processado por computador / Despacho DGTT N.º21 994/99 (2ª série)</div>--}}
     </div>
</div>