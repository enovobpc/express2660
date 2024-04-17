<?php 
    $dateMove = explode(' ', $shipment->delivery_date);
?>
{{-- <div style="position:absolute; font-size: 7pt; rotate: -90; right: 7mm; margin-top: 4mm; font-weight: bold">{{ $copyNumber }}</div> --}}
<div class="cmr-label" style="height: 280mm; width: 210mm; padding: 25px">
    <div style="height: 240mm">
        <div class="cmr-row">
            <div class="cmr-block" style="width: 340px; padding: 8px 20px 8px 10px; float: left; height: 18mm;">
                <div class="text-left" style="height: 135px; ">
                    <div class="pull-left" style="width: 100%; text-align: left">
                        @if($shipment->agency->filepath)
                            <img src="{{ @$shipment->agency->filehost . @$shipment->agency->getCroppa(300) }}" style="max-width: 50mm; max-height: 15mm"/>
                        @else
                            <h5 style="margin:0px"><b>{{ $shipment->agency->company }}</b></h5>
                        @endif
                    </div>
                    <div style="width: 100%; margin-top: 5px; line-height: 14px" class="fs-8pt">
                        NIF: {{ $shipment->agency->vat }}
                        @if($shipment->agency->charter)
                            &bull; Alvará {{ $shipment->agency->charter }}
                        @endif
                        <br/>
                        <span class="bold">
                         {{ $shipment->agency->address }}<br/>
                         {{ $shipment->agency->zip_code }} {{ $shipment->agency->city }}
                     </span>
                        <br/>
                        Telef: {{ $shipment->agency->phone }}
                        @if($shipment->agency->mobile)
                            / {{ $shipment->agency->mobile }}
                        @endif
                        @if($shipment->agency->email)
                            <br/> E-mail: {{ $shipment->agency->email }}
                        @endif
                        <div class="fs-8pt lh-1-2">
                            <span class="fs-8pt bold">{{ $shipment->agency->web }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cmr-block" style="width: 350px; float: left;">
                <div style="float: left; width: 260px">
                    <div style="display: inline-block; margin-top: 0px; margin-top: 0px">
                        <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="1.1" height="1.1"/>
                    </div>
                </div>
                <div style="float: left; width: 90px; margin-left: 40px">
                    <img src="{{ @$qrCode }}" style="height: 12mm"/>
                </div>
                <div style="font-weight: bold; font-size: 15px; margin-top: 5px; text-align: left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guia Transporte N.º {{ $shipment->tracking_code }}</div>
                <div style="font-size: 9pt; line-height: 10pt; border: 1px solid #333; border-radius: 5px; padding: 5px; margin: 10px 0 0 10px; height: 60px">
                    <div>
                        <div style="float: left; width: 33.3%">
                            <div style="font-size: 7pt">Serviço</div> {{ @$shipment->service->name }}
                        </div>
                        @if($shipment->vehicle)
                            <div style="float: left; width: 33.3%">
                                <div style="font-size: 7pt;">Viatura</div>{{ @$shipment->vehicle }}
                            </div>
                        @endif
                        @if($shipment->route_id)
                            <div style="float: left; width: 33.3%">
                                <div style="font-size: 7pt">Rota</div> {{ @$shipment->route->name }}
                            </div>
                        @endif
                    </div>
                    <div style="margin-top: 5px">
                        <div style="float: left; width: 66.6%">
                            <div style="font-size: 7pt;">Referência</div> {{ @$shipment->reference }}
                            @if(@$shipment->reference2)
                                {{ @$shipment->reference ? ' / '. @$shipment->reference2 : @$shipment->reference2 }}
                            @endif
                        </div>
                        <div style="float: left; width: 33.3%">
                            <div style="font-size: 7pt">Portes/Cobrança</div>
                            @if($shipment->payment_at_recipient || $shipment->charge_price)
                                {{ money($shipment->payment_at_recipient + $shipment->charge_price) }}
                            @else
                                N/A
                            @endif
                        </div>
                        <div style="float: left; width: 33.3%">
                            {{--<div style="font-size: 7pt; margin-top: 5px">Descarga:</div>--}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <hr style="margin: 15px 0 5px"/>
        <div class="cmr-row">
            <div class="cmr-block" style="width: 47%; height: 120px; padding: 0px 10px; float: left;">
                <div style="font-size: 8pt">
                    <div style="text-align: right; float: right;  width: 100px">NIF {{ $shipment->sender_vat ? $shipment->sender_vat : @$shipment->customer->vat }}</div>
                    <div style="text-align: left; float: left; width: 100px">Remetente</div>
                </div>
                <div class="fs-9pt bold lh-1-2 text-uppercase">
                    {{ str_limit($shipment->sender_name, 45) }}<br>
                    {{ $shipment->sender_address }}<br/>
                    {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}<br/>
                    {{ trans('country.'. strtolower($shipment->sender_country)) }}
                </div>
                <span style="font-size: 9pt">
                Tlf: {{ $shipment->sender_phone }}<br/>
                @if($shipment->obs)
                    Obs: {{ $shipment->obs }}
                @endif
            </span>
            </div>
            <div class="cmr-block" style="width: 47%; padding: 0px 10px; float: left;">
                <div style="font-size: 8pt">
                    <div style="text-align: right; float: right;  width: 100px">NIF {{ $shipment->recipient_vat ? $shipment->recipient_vat : '999999990' }}</div>
                    <div style="text-align: left; float: left; width: 100px">Destinatário</div>
                </div>
                <div class="fs-9pt bold lh-1-1">
                    {{ str_limit($shipment->recipient_name, 47) }}<br>
                    {{ $shipment->recipient_address }}<br/>
                    {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                    {{ trans('country.'. strtolower($shipment->recipient_country)) }}
                </div>
                <span style="font-size: 9pt">
                Tlf: {{ $shipment->recipient_phone }}<br/>
                @if($shipment->obs_delivery)
                        Obs: {{ $shipment->obs_delivery }}
                    @endif
            </span>
            </div>
        </div>
        <hr style="margin: 5px 0"/>
        <div class="cmr-row">
            <div class="cmr-block" style="width: 47%; height: 30px; padding: 0px 10px; float: left;">
                <div style="font-size: 8pt">Local Carga</div>
                <div class="fs-9pt bold lh-1-4 text-uppercase">
                    {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} - {{ trans('country.'. strtolower($shipment->sender_country)) }}
                    <br/>
                    @if(Setting::get('app_mode') == 'move')
                        <small style="font-weight: normal">DATA:</small> {{ $shipment->delivery_date ? $shipment->delivery_date->format('Y-m-d') : '' }} &nbsp;&nbsp; <small style="font-weight: normal">HORA:</small> {{ $shipment->start_hour ? $shipment->start_hour . ' - ' . $shipment->end_hour : ($shipment->created_at ? $shipment->created_at->format('H:i') : '') }}
                    @else
                        <small style="font-weight: normal">DATA:</small> {{$shipment->shipping_date ? $shipment->shipping_date->format('Y-m-d') : '' }} &nbsp;&nbsp; <small style="font-weight: normal">HORA:</small> {{ $shipment->start_hour ? $shipment->start_hour : ($shipment->created_at ? $shipment->created_at->format('H:i') : '') }}
                    @endif
                </div>
            </div>
            <div class="cmr-block" style="width: 47%; padding: 0px 10px; float: left;">
                <div style="font-size: 8pt">Local Descarga</div>
                <div class="fs-9pt bold lh-1-4">
                    {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}, {{ trans('country.'. strtolower($shipment->recipient_country)) }}
                    <br/>
                    @if(Setting::get('app_mode') == 'move')
                        <small style="font-weight: normal">DATA:</small> {{ $dateMove[0] }} &nbsp;&nbsp; <small style="font-weight: normal">HORA:</small> {{ $shipment->start_hour_pickup ? $shipment->start_hour_pickup . ' - ' . $shipment->end_hour_pickup : ($shipment->created_at ? $shipment->created_at->format('H:i') : '') }}
                    @else
                        <small style="font-weight: normal">DATA:</small> {{ $shipment->delivery_date ? $shipment->delivery_date->format('Y-m-d') : '' }} &nbsp;&nbsp; @if($shipment->end_hour) <small style="font-weight: normal">HORA:</small> {{ $shipment->end_hour }}@endif
                    @endif
                </div>
            </div>
        </div>

        {{-- CARGA --}}
        <div class="cmr-row" style="margin-top: 20px">
            <table style="width: 100%; font-size: 9pt" cellpadding="4">
                <tr>
                    <th style="background: #333; color: #fff; width: 40px; padding: 3px 0 3px 5px;">Qtd</th>
                    @if(Setting::get('app_mode') == 'move')
                        <th style="background: #333; color: #fff; width: 80px;">Emb.</th>
                        <th style="background: #333; color: #fff; width: 80px;">Tipo</th>
                    @else
                        <th style="background: #333; color: #fff; width: 80px;">Emb.</th>
                    @endif
                    <th style="background: #333; color: #fff;">Descrição</th>
                    <th style="background: #333; color: #fff; width: 55px; text-align: right;">Peso</th>
                    <th style="background: #333; color: #fff; width: 55px; text-align: right;">Compr.</th>
                    <th style="background: #333; color: #fff; width: 55px; text-align: right;">Larg.</th>
                    <th style="background: #333; color: #fff; width: 55px; text-align: right;">Altura</th>
                    <th style="background: #333; color: #fff; width: 55px; text-align: right;">Volume</th>
                    <th style="background: #333; color: #fff; padding-left: 15px">Notas</th>
                </tr>
                <?php $dimension = null ?>
                @if(!$shipment->pack_dimensions->isEmpty())
                    @foreach($shipment->pack_dimensions as $key => $dimension)
                        <tr>
                            <td style="border-bottom: 1px solid #999; padding: 3px 0 3px 5px;">{{ $dimension->qty }}</td>
                            @if(Setting::get('app_mode') == 'move')
                                <td style="border-bottom: 1px solid #999;">{{ @$dimension->qty }}</td>
                                <td style="border-bottom: 1px solid #999;">{{ @$dimension->packType->name }}</td>
                            @else
                                <td style="border-bottom: 1px solid #999;">{{ @$dimension->packType->name }}</td>
                            @endif
                            <td style="border-bottom: 1px solid #999;">{{ $dimension->description }}</td>
                            <td style="border-bottom: 1px solid #999; text-align: right;">{{ money($dimension->weight) }}</td>
                            <td style="border-bottom: 1px solid #999; text-align: right;">{{ money($dimension->width) }}</td>
                            <td style="border-bottom: 1px solid #999; text-align: right;">{{ money($dimension->length) }}</td>
                            <td style="border-bottom: 1px solid #999; text-align: right;">{{ money($dimension->height) }}</td>
                            <td style="border-bottom: 1px solid #999; text-align: right;">{{ money($dimension->volume, '', 3) }}</td>
                            <td style="border-bottom: 1px solid #999; padding-left: 15px">
                                @if(!empty($dimension->optional_fields))
                                    @foreach($dimension->optional_fields as $key => $value)
                                        <div>{{ $key }}</div>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="border-bottom: 1px solid #999; padding: 3px 0 3px 5px;">{{ $shipment->volumes }}</td>
                        @if(Setting::get('app_mode') == 'move')
                            <td style="border-bottom: 1px solid #999;">{{ @$dimension->volumes }}</td>
                            <td style="border-bottom: 1px solid #999;">Diversos</td>
                        @else
                            <td style="border-bottom: 1px solid #999;">Diversos</td>
                        @endif
                        <td style="border-bottom: 1px solid #999;">Material diverso</td>
                        <td style="border-bottom: 1px solid #999; text-align: right;">{{ money($shipment->weight) }}</td>
                        <td style="border-bottom: 1px solid #999; text-align: right;">{{ money(0) }}</td>
                        <td style="border-bottom: 1px solid #999; text-align: right;">{{ money(0) }}</td>
                        <td style="border-bottom: 1px solid #999; text-align: right;">{{ money(0) }}</td>
                        <td style="border-bottom: 1px solid #999; text-align: right;">{{ money($shipment->fator_m3, '', 3) }}</td>
                        <td style="border-bottom: 1px solid #999; padding-left: 15px"></td>
                    </tr>
                @endif
            </table>
        </div>
        <div style="border-bottom: 1px solid #999; width: 100%; heigth: fit-content; margin-top: 30px;">
            <span style="font-size: 14px;">
                &emsp;
                Peso total:
                @if($shipment->weight != null)
                    
                    {{$shipment->weight}}
                @else
                    Não definido
                @endif
                &emsp; | &emsp; Total volumétrico: 
                @if($shipment->volume_m3 != null)
                    {{$shipment->volume_m3}}
                @else
                    Não definido
                @endif
            </span>
        </div>
    </div>
    <div style="font-size: 7pt">
        <div style="font-size: 7pt; text-align: center; padding: 10px 0">
            Preço ref. combustível: {{ money(Setting::get('guides_fuel_price'), Setting::get('app_currency')) }}/Litro. Material embalado não conferido.
        </div>
        <div style="width: 31%; border: 1px solid #ddd; border-radius: 5px; float: left; padding: 3px; text-align: center">
            <div style="float: left;">
                Expedidor<br/><br/><br/><br/>
                _________________________________<br/>
                Assinatura, Data e Hora de Carga
            </div>
        </div>
        <div style="width: 31%; border: 1px solid #ddd; border-radius: 5px; float: left; padding: 3px; margin-left: 10px; text-align: center">
            <div style="float: left;">
                Transportador<br/><br/><br/><br/>
                _________________________________<br/>
                Assinatura Transportador
            </div>
        </div>
        <div style="width: 31%; border: 1px solid #ddd; border-radius: 5px; float: left; padding: 3px; margin-left: 10px; text-align: center">
            <div style="float: left;">
                Destinatário<br/><br/><br/><br/>
                _________________________________<br/>
                Assinatura, Data e Hora de Entrega
            </div>
        </div>
    </div>
    <div class="cmr-row">
        <div style="font-size: 8pt; margin-top: 10px;">
            <div style="float: left; width: 60%">
                Processado por computador. Despacho DGTT N.º21 994/99 (2ª série)
            </div>
            <div style="float: right; width: 39%; text-align: right; font-size: 7pt">{{ app_brand('docsignature') }}</div>
        </div>
    </div>
</div>