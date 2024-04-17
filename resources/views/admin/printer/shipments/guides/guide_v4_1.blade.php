<style>
    .box {
        float: left;
        background-color: #eee;
        border: 1px solid #ccc;
        padding: 2px 4px;
    }

    .box-2px {
       /* border: 2px solid #333;
        padding: 2px 4px;*/
    }

    .txlabel {
        color: #444;
        font-size: 9.5px;
    }
</style>
<?php
$codControl = null;
if($shipment->cod == 'D' && !Setting::get('labels_show_cod')) {
    $codControl = random_int(1, 9999) . '00' . number_format($shipment->total_price_for_recipient + $shipment->total_expenses, 2, '', '');
}

$isMensalCOD = false;
if(@$shipment->requested_by && $shipment->requested_by != $shipment->customer_id) {
    $isMensalCOD = true;
}

?>
<div style="position:absolute; font-size: 7pt; rotate: -90; right: 2.5mm; margin-top: 6mm; font-weight: bold">{{ $copyNumber }}</div>
<div style="position:absolute; font-size: 6.5pt; rotate: -90; left: 2mm; margin-top: 21mm; font-weight: bold">{{ app_brand('docsignature') }}</div>
<div style="padding: 6mm 7mm 2mm 7mm; height: 90mm; width: 204mm; font-size: 11px; color: #000; {{ $copyId < 3 ? 'border-bottom: 1px dashed #000' : ''}}">
    <div style="margin-bottom: 2mm">
        <div style="width: 16mm; float:right;">
            <img src="{{ @$qrCode }}" style="width: 12mm; margin-top: 1px;" />
        </div>
        <div style="width:60mm; float:right; margin-right: 3mm;">
            <div style="margin-top: -2px; text-align: right; font-size: 18px; font-weight: bold; line-height: 23px;">Guia N.º {{ $shipment->tracking_code }}</div>
            <div style="margin:0; text-align: right; font-size: 12px; line-height: 14px;">Data/Hora <span style="font-weight: bold">{{ $shipment->date }} {{ $shipment->created_at->format('H:i') }}</span></div>
            <div style="margin:0; text-align: right; font-size: 12px; line-height: 14px;">
                @if(@$shipment->route->code)
                    {{ @$shipment->route->code }} - {{ @$shipment->route->name }}
                @else
                    @if($shipment->recipient_country == 'pt')
                        RN - Expedição Nacional
                    @elseif($shipment->recipient_country == 'es')
                        RE - Expedição Espanha
                    @else
                        RI - Expedição Internacional
                    @endif
                @endif
            </div>
        </div>
        <div style="max-width: 130mm; float:left;">
            <table style="width: 120mm; float: left;">
                <tr>
                   @if(File::exists(public_path(@$shipment->agency->filepath)))
                        <td style="max-width: 60mm">
                            <img src="{{ public_path(@$shipment->agency->filepath) }}" style="max-width: 43mm; max-height: 12mm;  margin-top: -5px"/>
                        </td>
                    @endif
                    <td style="width: 60mm;">
                        <div style="width:70mm; height:13mm; float:left; margin-left: 3mm; line-height: 7px; font-size: 8px">
                            {{ @$shipment->agency->company }}<br/>
                            {{ @$shipment->agency->address }}
                            {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}<br/>
                            Tlf: {{ @$shipment->agency->phone }} / Tlm: {{ @$shipment->agency->mobile }}<br/>
                            NIF: {{ @$shipment->agency->vat }} @if(@$shipment->agency->charter) / Alvará: {{ @$shipment->agency->charter }} @endif<br/>
                            <span style="font-weight: bold; font-size: 10px; line-height: 11px">{{ @$shipment->agency->web }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="width:204mm; height:20mm; margin-bottom: 1.5mm;">
        <div class="box box-2px" style="width: 94.4mm; height: 22mm;">
            <div>
                <div style="width:20mm; float:left;" class="txlabel">Remetente</div>
                <div style="width:73mm; float:left; text-align: right">NIF {{ $shipment->sender_vat ? $shipment->sender_vat : @$shipment->customer->vat }}</div>
            </div>
            <div style="text-transform:uppercase; line-height: 12.5px; height: 14.5mm;">
                <span style="font-weight: bold">{{ $shipment->sender_name }}</span><br/>
                {{ $shipment->sender_address }}<br/>
                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
            </div>
            <div>Tlf: {{ $shipment->sender_phone }}</div>
        </div>

        <div class="box box-2px" style="width: 94.4mm; height: 22mm; margin-left: 1.5mm">
            <div>
                <div style="width:20mm; float:left;" class="txlabel">Destinatário</div>
                <div style="width:73mm; float:left; text-align: right">NIF {{ $shipment->recipient_vat ? $shipment->recipient_vat : '999999990' }}</div>
            </div>
            <div style="text-transform:uppercase; line-height: 12.5px; height: 14.5mm;">
                <span style="font-weight: bold">{{ $shipment->recipient_name }}</span><br/>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
            </div>
            <div>Tlf: {{ $shipment->recipient_phone }}</div>
        </div>
    </div>

    <div style="float: left; width: 135mm; height: 47mm;">
        <div style="line-height: 13px; margin-bottom: 1.5mm">
            <div class="box" style="width:64mm;">
                <span style="font-size: 10px; color: #444">Local de Carga</span>
                <div style="text-transform:uppercase">{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</div>
            </div>
            <div class="box" style="width:64mm; margin-left: 1.5mm">
                <div>
                    <span style="font-size: 10px; color: #444">Local de Descarga</span>
                    <div style="text-transform:uppercase">{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</div>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 1.5mm; line-height: 12px">
            <div class="box">
                <div style="width:8mm; float:left;">
                    <span class="txlabel">Vol.</span><br/>
                    {{ $shipment->volumes }}
                </div>
                <div style="width:19mm;float:left">
                    <span class="txlabel">Tipo</span><br/>
                    {{ @$shipment->pack_dimensions->first()->type ? trans('admin/global.packages_types.' . @$shipment->pack_dimensions->first()->type) : 'Caixa' }}
                </div>
                <div style="width:70mm;float:left">
                    <span class="txlabel">Mercadoria</span><br/>
                    {{ @$shipment->pack_dimensions->first()->description ? @$shipment->pack_dimensions->first()->description : 'Items Diversos' }}
                </div>
                <div style="width:18mm;float:left">
                    <span class="txlabel">Peso</span><br/>
                    {{ money($shipment->weight, 'Kg') }}
                </div>
                <div style="width:14mm;float:left">
                    <span class="txlabel">M3</span><br/>
                    {{ $shipment->volume_m3 ?  money($shipment->volume_m3, '', 4) : money($shipment->fator_m3, '', 4) }}
                </div>
            </div>
        </div>

        <div class="box" style="width:64mm; height:21mm;">
            <span class="txlabel">Observações Entrega / Notas Expedidor</span><br/>
            <div style="font-weight: bold; line-height: 12px; height: 12mm">{{ @$shipment->obs }}</div>

            @if($shipment->has_return && (in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return)))
                @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                    <div style="float: left; padding: 2px; line-height: 11px; width: 110px; border: 1px solid #000; border-radius: 4px; color:#000; font-weight: bold; text-align: center;">Retorno Encomenda</div>
                @endif
                @if($shipment->has_return && in_array('rguide', $shipment->has_return))
                    <div style="float: left; padding: 2px; line-height: 11px; width: 108px; border: 1px solid #000; border-radius: 4px; color:#000; font-weight: bold; text-align: center; margin-left: 1mm">Ret. Guia Assinada</div>
                @endif
            @endif
        </div>
        <div class="box" style="width:30mm; height:21mm; margin-left:1.5mm">
            <div style="font-weight: bold">Serviço: {{ @$shipment->service->display_code }}</div>
            @if($shipment->reference)
            <span class="txlabel">Referência</span>
            <div style="font-weight: bold; height: 5mm; line-height: 8px">{{ $shipment->reference }}</div>
            @else
                <span class="txlabel">&nbsp;</span>
                <div style="font-weight: bold; height: 5mm; line-height: 8px">&nbsp;</div>
            @endif
            <div>&nbsp;</div>
            <div style="font-size: 8px; padding-top: 0.3mm; text-align: center">
                Assinatura Transportador
            </div>
        </div>
        <div class="box" style="width:29mm; {{ $shipment->charge_price || $shipment->total_price_for_recipient || $isMensalCOD ? 'background-color: #333; color: #fff; border: 1px solid #333' : '' }}; height:21mm; margin-left:1.5mm;">
            <div style="line-height: 18px; margin-bottom: 5px; text-align: center">
                @if($shipment->charge_price)
                    <span class="txlabel" style="color: #fff">Reembolso</span>
                    <div style="font-size:18px; font-weight: bold">{{ money($shipment->charge_price, Setting::get('app_currency')) }}</div>
                @elseif($shipment->cod == 'D' || $isMensalCOD)
                    @if($isMensalCOD)
                        <span class="txlabel" style="color: #fff">Portes Destino</span>
                        <div style="font-size:18px; font-weight: bold">Mensal</div>
                    @elseif($shipment->cod == 'D' && !Setting::get('labels_show_cod'))
                        <span class="txlabel" style="color: #fff">Portes Destino</span>
                        <div style="font-size:16px; font-weight: bold"><small>#{{ $codControl }}</small></div>
                    @elseif($shipment->cod == 'D' && Setting::get('labels_show_cod'))
                        <span class="txlabel" style="color: #fff">Portes Destino</span>
                        <div style="font-size:18px; font-weight: bold">{{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}</div>
                    @endif
                @else
                    <span class="txlabel">Cobrança</span>
                    <div style="font-size:18px; font-weight: bold; color: #444">N/A</div>
                @endif
            </div>
            <hr style="margin: 2px 0"/>

            @if($shipment->charge_price)
                @if($isMensalCOD)
                    <div>
                        <div style="width: 10mm; float: left">Portes</div>
                        <div style="width: 17mm; float: left; font-weight: bold; text-align: right">Mensal</div>
                    </div>
                @elseif($shipment->cod == 'D' && !Setting::get('labels_show_cod'))
                    <div>
                        <div style="width: 9mm; float: left">Portes</div>
                        <div style="width: 18mm; float: left; font-weight: bold; text-align: right;"><small style="font-size: 11px">#{{ $codControl }}</small></div>
                    </div>
                @elseif($shipment->cod == 'D' && Setting::get('labels_show_cod'))
                    <div>
                        <div style="width: 9mm; float: left">Portes</div>
                        <div style="width: 18mm; float: left; font-weight: bold; text-align: right">{{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}</div>
                    </div>
                @else
                    <div>
                        <div style="width: 10mm; float: left">&nbsp;</div>
                        <div style="width: 17mm; float: left; font-weight: bold; text-align: right"></div>
                    </div>
                @endif
            @else
                <div>
                    <div style="width: 10mm; float: left">&nbsp;</div>
                    <div style="width: 17mm; float: left; font-weight: bold; text-align: right"></div>
                </div>
            @endif

            <div>
                <div style="width: 10mm; float: left">Cliente</div>
                <div style="width: 17mm; float: left; font-weight: bold; text-align: right">{{ @$shipment->customer->code }}</div>
            </div>

        </div>
    </div>
    <div style="float: left; width: 59mm; height: 49mm; margin-left: 1.5mm;">
        <div class="box" style="line-height: 12.6px; margin-bottom: 1.5mm">
            <div style="float: left; width: 19mm">
                <span style="font-size: 10px; color: #444">Matrícula</span>
                <div>{{ $shipment->vehicle }}&nbsp;</div>
            </div>
            <div style="float: left; width: 19mm">
                <span style="font-size: 10px; color: #444">Peso Bruto</span>
                <div>{{ $shipment->vehicle_kg ? money($shipment->vehicle_kg) : '' }}</div>
            </div>
            <div style="float: left; width: 17mm">
                <span style="font-size: 10px; color: #444">Carga Util</span>
                <div>{{ $shipment->vehicle_kg_usefull ? money($shipment->vehicle_kg_usefull) : '' }}</div>
            </div>
        </div>
        <div class="box">
            <span style="font-weight: bold; font-size: 12px;">Comprovativo Entrega</span><br/>
            <div style="text-align: center; height: 22mm;"></div>
            Data <span style="font-weight: bold">_____/_____/_____</span> Hora:<span style="font-weight: bold">____:____</span>
        </div>
    </div>
    <div style="font-size: 7px; width: 204mm; margin-top: -19px; margin-bottom: -20px;">
        <div style="float: left; width: 120mm;">
            Preço ref. combustível: {{ money(Setting::get('guides_fuel_price'), Setting::get('app_currency')) }}/Litro.
            Material embalado não conferido. | A assinatura pressupõe a aceitação das condições de transporte.
        </div>
        <div style="float: left; text-align: right; width: 76mm;">
            Processado por computador / Despacho DGTT N.º21 994/99 (2ª série)
        </div>
    </div>
</div>
