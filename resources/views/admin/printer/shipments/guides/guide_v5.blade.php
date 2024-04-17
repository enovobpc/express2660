<style>
    .box {
        float: left;
        /*background-color: #eee;
        border: 1px solid #ccc;*/

        background-color: #fff;
        border: 1px solid #333;
        padding: 1px 5px;
    }

    .box-2px {
        border: 2px solid #333;
        padding: 2px 4px;
    }

    .bx-header {
        background: #999;
        color: #fff;
        padding: 0px 5px 1px;
        margin: -1px -5px 2px -5px;
        font-weight: bold;
    }
</style>
<?php
$codControl = null;
if($shipment->payment_at_recipient && !Setting::get('labels_show_cod')) {
    $codControl = random_int(1, 9999) . '00' . number_format($shipment->billing_subtotal, 2, '', '');
}

$isMensalCOD = false;
if(@$shipment->requested_by && $shipment->requested_by != $shipment->customer_id) {
    $isMensalCOD = true;
}

?>
<div style="position:absolute; font-size: 7pt; rotate: -90; right: 3mm; margin-top: 6mm; font-weight: bold">{{ $copyNumber }}</div>
<div style="position:absolute; font-size: 7px; rotate: -90; right: 3mm; margin-top: 61mm;">Conforme Deliberação n.º 813/2020</div>
<div style="position:absolute; font-size: 6.5pt; rotate: -90; left: 2mm; margin-top: 21mm; font-weight: bold">{{ app_brand('docsignature') }}</div>
<div style="padding: 6mm 7mm 2mm 7mm; height: 90mm; width: 204mm; font-size: 11px; color: #000; {{ $copyId < 3 ? 'border-bottom: 1px dashed #000' : ''}}">
    <div style="margin-bottom: 2mm">
        <div style="width: 86mm; float:left;">
            <table style="float: left;">
                <tr>
                   @if(File::exists(public_path(@$shipment->agency->filepath)))
                        <td style="width: 20mm">
                            <img src="{{ public_path(@$shipment->agency->filepath) }}" style="max-width: 40mm; max-height: 18mm;  margin-top: -5px"/>
                        </td>
                    @endif
                    <td style="width: 50mm; padding-left: 3mm">
                        <div style="line-height: 7px; font-size: 8px;">
                            {{ @$shipment->agency->company }}<br/>
                            {{ @$shipment->agency->address }}<br/>
                            {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}<br/>
                            NIF: {{ @$shipment->agency->vat }} @if(@$shipment->agency->charter) / Alvará: {{ @$shipment->agency->charter }} @endif<br/>
                            <span style="font-weight: bold; font-size: 10px; line-height: 11px">{{ @$shipment->agency->web }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div style="width: 45mm; float:left; text-align: center">
            <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.75" height="1"/>
            * {{ $shipment->tracking_code }} *
        </div>
        <div style="width:64mm; float:left;">
            <div style="text-align: right; font-size: 14px; font-weight: bold; line-height: 18px;">Guia Transporte #{{ $shipment->tracking_code }}</div>
            {{--<div style="margin:0; text-align: right; font-size: 12px; line-height: 14px;">Referência <span style="font-weight: bold">{{ $shipment->reference }}</span></div>--}}
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
    </div>

    <div style="float: left; width: 100mm; margin-bottom: 1.5mm;">
        <div class="box" style="height: 20mm; width: 100%; margin-bottom: 1mm; margin-top: 0mm">
            <div class="bx-header">
                <div style="width:19mm; float:left;" class="txlabel">Expedidor</div>
                <div style="width:77mm; float:left; text-align: right">NIF {{ $shipment->sender_vat ? $shipment->sender_vat : @$shipment->customer->vat }}</div>
            </div>
            <div style="text-transform:uppercase; line-height: 14px; height: 14.5mm;">
                <span style="font-weight: bold">{{ $shipment->sender_name }}</span><br/>
                {{ $shipment->sender_address }}<br/>
                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
            </div>
            {{--<div>Tlf: {{ $shipment->sender_phone }}</div>--}}
        </div>

        <div class="box" style="height: 25mm; margin-bottom: 1mm">
            <div class="bx-header">
                <div style="width:19mm; float:left;" class="txlabel">Destinatário</div>
                <div style="width:77mm; float:left; text-align: right">NIF {{ $shipment->recipient_vat ? $shipment->recipient_vat : '999999990' }}</div>
            </div>
            <div style="text-transform:uppercase; line-height: 14px; height: 14.5mm;">
                <span style="font-weight: bold">{{ $shipment->recipient_name }}</span><br/>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
            </div>
            <div>Tlf: {{ $shipment->recipient_phone }}</div>
        </div>
        <div style="line-height: 13px; margin-bottom: 1.5mm">
            <div class="box" style="width:46.3mm">
                <div class="bx-header">Local de Carga</div>
                <div style="text-transform:uppercase">{{ $shipment->sender_zip_code }} {{ substr($shipment->sender_city, 0, 18) }}</div>
            </div>
            <div class="box" style="width:46.3mm; margin-left: 1.5mm">
                <div>
                    <div class="bx-header">Local de Descarga</div>
                    <div style="text-transform:uppercase">{{ $shipment->recipient_zip_code }} {{ substr($shipment->recipient_city, 0, 18) }}</div>
                </div>
            </div>
        </div>
        <div class="box" style="width:46.3mm;  height: 11.3mm">
            <div class="bx-header text-center">Assinatura Expedidor</div>
            <div>&nbsp;</div>
        </div>
        <div class="box" style="width:46.3mm; height: 11.3mm; margin-left: 1.5mm">
            <div class="bx-header text-center">Assinatura Transportador</div>
            <div>&nbsp;</div>
        </div>
    </div>


    <div style="float: left; width: 94mm; margin-left: 1.5mm;">
        <div class="box" style="width: 32mm;">
            <div class="bx-header text-center">Referência</div>
            <div>{{ substr($shipment->reference, 0, 15) }}&nbsp;</div>
        </div>
        <div class="box" style="width: 15.5mm; margin-bottom: 1mm;  margin-left: 1mm">
            <div class="bx-header text-center">Volumes</div>
            <div style="text-align: center">{{ $shipment->volumes }}</div>
        </div>
        <div class="box" style="width: 15.5mm; margin-left: 1mm">
            <div class="bx-header text-center">Peso</div>
            <div style="text-align: center">{{ money($shipment->weight) }}</div>
        </div>
        <div class="box" style="width: 15.5mm; margin-left: 1mm">
            <div class="bx-header text-center">M3</div>
            <div style="text-align: center">{{ money($shipment->fator_m3, '', 4) }}</div>
        </div>
        <div class="box" style="width: 100%; margin-bottom: 1mm">
            <div class="bx-header">Natureza da mercadoria</div>
            <div>{{ @$shipment->pack_dimensions->first()->description ? @$shipment->pack_dimensions->first()->description : 'Items Diversos' }}</div>
        </div>
        <div class="box" style="width: 100%;  margin-bottom: 1mm">
            <div class="bx-header">Observações</div>
            <div style="height: 8.5mm">{{ @$shipment->obs }}</div>
        </div>
        <div class="box" style="width: 100%;  margin-bottom: 1mm">
            <div class="bx-header">Reservas do Destinatário</div>
            <div>&nbsp;</div>
        </div>

        <div class="box" style="width:29mm; height:21.4mm;">
            <div style="line-height: 18px; margin-bottom: 5px; text-align: center">
                @if($shipment->charge_price)
                    <div class="bx-header">Cobrança</div>
                    <div style="font-size:18px; font-weight: bold">{{ money($shipment->charge_price, Setting::get('app_currency')) }}</div>
                @elseif($shipment->cod == 'D' || $isMensalCOD)
                    @if($isMensalCOD)
                        <div class="bx-header">Portes Destino</div>
                        <div style="font-size:18px; font-weight: bold">Mensal</div>
                    @elseif($shipment->cod == 'D' && !Setting::get('labels_show_cod'))
                        <div class="bx-header">Portes Destino</div>
                        <div style="font-size:16px; font-weight: bold"><small>#{{ $codControl }}</small></div>
                    @elseif($shipment->cod == 'D' && Setting::get('labels_show_cod'))
                        <div class="bx-header">Portes Destino</div>
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

        <div class="box" style="float: left; width: 58mm; margin-left: 1mm">
            <div class="bx-header">Comprovativo Entrega</div>
            <div style="text-align: center; height: 12mm;"></div>
            Data <span style="font-weight: bold">_____/_____/_____</span> Hora:<span style="font-weight: bold">____:____</span>
        </div>

    </div>



            {{--@if($shipment->has_return && (in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return)))
                @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                    <div style="float: left; padding: 2px; line-height: 11px; width: 110px; border: 1px solid #000; border-radius: 4px; color:#000; font-weight: bold; text-align: center;">Retorno Encomenda</div>
                @endif
                @if($shipment->has_return && in_array('rguide', $shipment->has_return))
                    <div style="float: left; padding: 2px; line-height: 11px; width: 108px; border: 1px solid #000; border-radius: 4px; color:#000; font-weight: bold; text-align: center; margin-left: 1mm">Ret. Guia Assinada</div>
                @endif
            @endif--}}




    <div style="font-size: 7px; width: 204mm; margin-top: -3px; margin-bottom: -20px;">
        Preço ref. combustível: {{ money(Setting::get('guides_fuel_price'), Setting::get('app_currency')) }}/Litro.
        Material embalado não conferido. | A assinatura pressupõe a aceitação do contrato de de transporte.
    </div>
</div>
