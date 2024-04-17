<div class="collection-manifest">
    <div class="guide-row border-bottom p-b-10">
        <div class="guide-block" style="width: 95mm; height: 11mm">
            <h4 class="m-t-0 bold" style="margin-left: 14mm">MANIFESTO DE RECOLHA</h4>
            <div class="guide-block-right" style="width: 86.4mm">
                <div class="text-center">
                    <div style="display: inline-block">
                        <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="1.1" height="0.8"/>
                    </div>
                    <div class="font-size-9pt bold text-center m-t-4">
                        Nº PEDIDO# {{ $shipment->tracking_code }}
                    </div>
                </div> 
            </div>
        </div>
        <div class="guide-block" style="width: 95mm; height: 11mm; float: left;">
            <div class="pull-left" style="width: 50%">
                @if(@$shipment->senderAgency->filepath && File::exists(public_path($shipment->senderAgency->filepath)))
                <img src="{{ asset(@$shipment->senderAgency->getCroppa(350, 60)) }}" style="height: 23px;" class="m-t-6"/>
                @else
                <h5 style="margin:0px"><b>{{ @$shipment->senderAgency->company }}</b></h5>
                @endif
                <div class="font-size-7pt line-height-1p2 m-t-8">
                    <span class="font-size-10pt bold">{{ @$shipment->senderAgency->web }}</span>
                </div>
            </div>
            <div style="width: 50%;" class="font-size-7pt">
                NIF: {{ @$shipment->senderAgency->vat }}
                @if(@$shipment->senderAgency->charter)
                &bull; Alvará {{ @$shipment->senderAgency->charter }}
                @endif
                <br/>
                {{ @$shipment->senderAgency->address }}
                {{ @$shipment->senderAgency->zip_code }} {{ @$shipment->senderAgency->city }}<br>
                Telef: {{ @$shipment->senderAgency->phone }}
                @if(@$shipment->senderAgency->mobile)
                / {{ @$shipment->senderAgency->mobile }}
                @endif
                <br/>
                @if(@$shipment->senderAgency->email)
                E-mail: {{ @$shipment->senderAgency->email }}<br/>
                @endif
            </div>
        </div>
    </div>
    <div class="guide-row border-bottom border-right border-left">
        <div class="guide-block">
            <div class="m-t-5 m-b-5" style="float: right; text-align: right; width: 40%">
                @if($shipment->reference)
                    <strong style="font-weight: bold">REFERÊNCIA</strong> {{ $shipment->reference }}
                @endif
            </div>
            <div class="m-t-0 m-b-5" style="width: 59%; float: left;">
                DATA/HORA PEDIDO: <strong class="bold">{{ $shipment->date }} / {{ $shipment->created_at->format('H:i') }}</strong>
                &nbsp;&nbsp; Serviço: <strong class="bold">{{ @$shipment->service->name }}</strong>
            </div>
        </div>
    </div>
    <div class="guide-row border-bottom border-right border-left">
        <div class="guide-block" style="width: 134.07mm; border-right: 1px solid #000">
            <div class="p-b-10">
                <div class="guide-block-title  p-t-5 m-b-3">SOLICITADO POR</div>
                <div class="pull-left" style="width: 74% ">
                    <div class="font-size-8pt bold">
                        {{ str_limit(@$shipment->customer->name, 45) }}<br>
                        {{ @$shipment->customer->address }}<br/>
                        {{ @$shipment->customer->zip_code }} {{ @$shipment->customer->city }} ({{ strtoupper(@$shipment->customer->country) }})
                        <br/>
                    </div>
                </div>
            </div>
            <div class="border-bottom m-b-3" style="margin-left: -5px; margin-right: -5px"></div>
            <div class="p-b-10">
                <div class="guide-block-title p-t-5 p-b-3">EXPEDIDOR (LOCAL RECOLHA)</div>
                <div class="pull-left"  style="width: 50% ">
                    <div class="font-size-8pt bold">
                        {{ str_limit($shipment->sender_name, 45) }}<br>
                        {{ $shipment->sender_address }}<br/>
                        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                    </div>
                </div>
                <div class="font-size-8pt text-right" style="width: 25%">
                    @if($shipment->recipient_attr)
                    {{ $shipment->recipient_attr }}
                    @endif
                    <br/>
                    @if($shipment->sender_phone && $shipment->sender_phone != '.')
                    Tlf: {{ $shipment->sender_phone }}
                    @endif
                    <br/>
                </div>
            </div>
            <div class="border-bottom m-b-10" style="margin-left: -5px; margin-right: -5px"></div>
            <div class="p-b-10 pull-left">
                <div class="guide-block-title p-b-3">DESTINATÁRIO</div>
                <div class="pull-left"  style="width: 74% ">
                    <div class="font-size-8pt bold line-height-1p2">
                        {{ str_limit($shipment->recipient_name, 47) }}<br>
                        {{ $shipment->recipient_address }}<br/>
                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                    </div>
                </div>
                <div class="font-size-8pt text-right" style="width: 25% ">
                    <br/>
                    @if(@$shipment->customer->code)
                    N.º Cliente: {{ @$shipment->customer->code }}<br/>
                    @endif
                    @if($shipment->recipient_phone && $shipment->recipient_phone != '.')
                    Tlf: {{ $shipment->recipient_phone }}
                    @endif<br/>
                </div>
            </div>
        </div>
        <div class="guide-blok" style="width: 59mm; height: 20mm; float: left;">
            <div style="border-bottom: 1px solid #000; height: 15.65mm" class="p-b-5 p-t-10 m-b-10">
                <div style="width: 33.3%" class="pull-left text-center">
                    {{ $shipment->volumes ? $shipment->volumes : '_ _ _ _ _ _ _' }}
                    <br/>
                    <span class="font-size-8pt">VOLUMES</span>
                </div>
                <div style="width: 33.3%" class="pull-left text-center">
                    {{ $shipment->weight ? $shipment->weight. 'kg' : '_ _ _ _ _ _ _' }}
                    <br/>
                    <span class="font-size-8pt">PESO</span>
                </div>
                <div style="width: 33.3%" class="pull-left text-center">
                    {{ $shipment->charge_price ? money($shipment->charge_price,  Setting::get('app_currency')) : '_ _ _ _ _ _ _' }}
                    <br/>
                    <span class="font-size-8pt">COBRANÇA</span>
                </div>
            </div>
            <div style="border-bottom: 1px solid #000">
                <div class="p-l-4" style="height: 17.1mm">
                    <div class="guide-block-title line-height-1p0">ASSINATURA TRANSPORTADOR</div>


                </div>
            </div>
            <div class="p-l-4 p-t-5">
                <div class="guide-block-title line-height-1p0">ASSINATURA EXPEDIDOR</div>


            </div>
        </div>
    </div>
    <div class="guide-row border-bottom border-left border-right">
        <div class="guide-block" style="height: 30mm">
            <p class="m-t-5 font-size-8pt">
                Observações: <strong class="bold">{{ $shipment->obs }}</strong>
            </p>
        </div>
    </div>
    <div class="fs-9">
        <div class="pull-left" style="width: 50%">{{ app_brand('docsignature') }}</div>
        <div class="pull-left text-right" style="width: 50%">Documento processado por computador / Despacho DGTT N.º21 994/99 (2ª série)</div>
    </div>
</div>
