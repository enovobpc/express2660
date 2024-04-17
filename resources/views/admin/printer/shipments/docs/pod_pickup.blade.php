<style>
    .box{
        float: left;
        background-color:#eee;
        padding: 3px 5px;
    }

    .txlabel {
        color: #444;
        font-size: 9.5px;
    }
</style>
<div style="padding: 4mm; height: 92mm; width: 205mm; font-size: 11px; color: #000;">
    <div style="margin-bottom: 1mm">
        <div style="width: 16mm; float:right;">
            <img src="{{ @$qrCode }}" style="width: 16mm; margin-top: -2px;" />
        </div>
        <div style="width:60mm; float:right; margin-right: 3mm;">
            <div style="margin:0; text-align: right; font-size: 20px; font-weight: bold; line-height: 22px;">TRK# {{ $shipment->tracking_code }}</div>
            <div style="margin:0; text-align: right; font-size: 12px; line-height: 14px;">Data {{ $shipment->date }}</div>
            <div style="margin:0; text-align: right; font-size: 12px; line-height: 14px;"></div>
        </div>
        <div style="max-width: 130mm; float:left; margin-top: -10px">
            <table style="width: 130mm; float: left;">
                <tr>
                    @if(File::exists(public_path(@$shipment->agency->filepath)))
                    <td style="max-width: 50mm">
                        <img src="{{ public_path(@$shipment->agency->filepath) }}" style="max-width: 43mm; max-height: 12mm"/>
                    </td>
                    @endif
                    <td style="width: 80mm;  line-height: 11px">
                        <div style="width:70mm; height:13mm; float:left; margin-left: 3mm; line-height: 8px; font-size: 9px">
                            {{ @$shipment->agency->company }}<br/>
                            {{ @$shipment->agency->address }}<br/>
                            {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}<br/>
                            NIF: {{ @$shipment->agency->vat }} @if(@$shipment->agency->charter) | Alvará: {{@$shipment->agency->charter}} @endif<br/>
                            <span style="font-weight: bold; font-size: 11px; line-height: 12px">{{ @$shipment->agency->web }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="width:204mm; height:20mm; margin-bottom: 1.5mm;">
        <div class="box" style="width: 98mm; height: 22mm;">
            <div>
                <div style="width:20mm; float:left;" class="txlabel">Remetente</div>
                <div style="width:76mm; float:left; text-align: right">NIF {{ $shipment->sender_vat ? $shipment->sender_vat : '999999990' }}</div>
            </div>
            <div style="text-transform:uppercase; line-height: 12.5px; height: 14.5mm;">
                <span style="font-weight: bold">{{ $shipment->sender_name }}</span><br/>
                {{ $shipment->sender_address }}<br/>
                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
            </div>
            <div>Tlf: {{ $shipment->sender_phone }}</div>
        </div>

        <div class="box" style="width: 98mm; height: 22mm; margin-left: 1.5mm">
            <div>
                <div style="width:20mm; float:left;" class="txlabel">Destinatário</div>
                <div style="width:76mm; float:left; text-align: right">NIF {{ $shipment->recipient_vat ? $shipment->recipient_vat : '999999990' }}</div>
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
            <div class="box" style="width:64.5mm;">
                <span style="font-size: 10px; color: #444">Local de Carga</span>
                <div style="text-transform:uppercase">{{ $shipment->sender_zip_code }} {{ substr($shipment->sender_city, 0, 28) }}</div>
            </div>
            <div class="box" style="width:64.5mm; margin-left: 1.5mm">
                <div>
                    <span style="font-size: 10px; color: #444">Local de Descarga</span>
                    <div style="text-transform:uppercase">{{ $shipment->recipient_zip_code }} {{ substr($shipment->recipient_city, 0, 28) }}</div>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 1.5mm; line-height: 12px">
            <div style="background-color:#f2f2f2;float:left;padding: 5px 8px">
                <div style="width:8mm; float:left;">
                    <span class="txlabel">Vol.</span><br/>
                    {{ $shipment->volumes }}
                </div>
                <div style="width:19mm;float:left">
                    <span class="txlabel">Tipo</span><br/>
                    {{ @$shipment->pack_dimensions->first()->type ? @$shipment->pack_dimensions->first()->type->name : 'Caixa' }}
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
                    {{ money(@$shipment->fator_m3, '', 4) }}
                </div>
            </div>
        </div>

        <div class="box" style="width:64.5mm; height:23mm;">
            <span class="txlabel">Observações de Recolha</span><br/>
            <div style="font-weight: bold; line-height: 12px; height: 14mm">{{ @$shipment->obs }}</div>

            @if($shipment->has_return && (in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return)))
                @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                    <div style="float: left; padding: 2px; line-height: 11px; width: 110px; border: 1px solid #000; border-radius: 4px; color:#000; font-weight: bold; text-align: center;">Retorno Encomenda</div>
                @endif
                @if($shipment->has_return && in_array('rguide', $shipment->has_return))
                        <div style="float: left; padding: 2px; line-height: 11px; width: 108px; border: 1px solid #000; border-radius: 4px; color:#000; font-weight: bold; text-align: center; margin-left: 1mm">Ret. Guia Assinada</div>
                @endif
            @endif
        </div>
        <div class="box" style="width:31mm; height:23mm; margin-left:1.5mm">
            <span class="txlabel">Referência</span>
            <div style="font-weight: bold; height: 6mm; line-height: 10px">{{ $shipment->reference }}</div>
            <div>Motorista: {{ @$shipment->last_history->operator->code }}</div>
            <div>Serviço: {{ @$shipment->service->display_code }}</div>
            <div>Cliente: {{ @$shipment->customer->code }}</div>
        </div>
        <div class="box" style="width:30mm; {{ $shipment->charge_price || $shipment->total_price_for_recipient ? 'background-color: #333; color: #fff' : '' }}; height:23mm;margin-left:1.5mm;">
            <div style="line-height: 18px; margin-bottom: 5px; text-align: center">
                @if($shipment->charge_price)
                    <span class="txlabel" style="color: #fff">Reembolso</span>
                    <div style="font-size:18px; font-weight: bold">{{ money($shipment->charge_price, Setting::get('app_currency')) }}</div>
                @elseif($shipment->total_price_for_recipient)
                    <span class="txlabel" style="color: #fff">Portes Destino</span>
                    <div style="font-size:18px; font-weight: bold">{{ money($shipment->total_price_for_recipient, Setting::get('app_currency')) }}</div>
                @else
                    <span class="txlabel">Cobrança</span>
                    <div style="font-size:18px; font-weight: bold; color: #444">N/A</div>
                @endif
            </div>
            <hr style="margin: 5px 0"/>
            @if($shipment->charge_price && $shipment->total_price_for_recipient)
                <div>
                    <div style="width: 10mm; float: left">Portes</div>
                    <div style="width: 17mm; float: left; font-weight: bold; text-align: right">{{ money($shipment->total_price_for_recipient, Setting::get('app_currency')) }}</div>
                </div>
            @endif
            <div>
                <div style="width: 10mm; float: left">Total</div>
                <div style="width: 17mm; float: left; font-weight: bold; text-align: right">{{ money($shipment->total_price_for_recipient + $shipment->charge_price, Setting::get('app_currency')) }}</div>
            </div>
        </div>

    </div>
    <div style="float: left; width: 65.4mm; height: 47mm; margin-left: 1.5mm;">
        <div class="box">
        <span style="font-weight: bold; font-size: 14px;">Comprovativo Recolha</span><br/>
            Responsável: <span style="color: blue; font-weight: bold;">{{ @$shipment->last_history->receiver }}</span><br/>
            @if(@$shipment->last_history->vat)
                NIF/CC: <span style="color: blue; font-weight: bold">{{ @$shipment->last_history->vat }}</span>
            @endif
        <div style="text-align: center; height: 29mm;">

        </div>
            Data/Hora Recolha: <span style="color: blue; font-weight: bold">{{ @$shipment->last_history->created_at }}</span>
        </div>
    </div>
    <div style="font-size: 9px; width: 204mm; margin-top: -5px; margin-bottom: -10px">
        <div style="float: left; width: 132mm; font-style: italic; font-weight: bold">
            Este comprovativo de recolha é uma imagem simulada, cuja assinatura foi recolhida em dispositivo electrónico.
        </div>
        <div style="float: left; text-align: right; width: 70mm;">
            {{ app_brand('docsignature') }}
        </div>
    </div>
</div>
<?php
$signature = @$shipment->last_history->signature;
$signature = str_replace('data:image/jpeg', 'data:image/png', $signature);
?>
@if($signature)
    <div style="position: absolute; top: 50mm; right: 8mm; height: 60mm; rotate: -90;">
        <img src="{{ $signature }}" style="height: 52mm"/>
    </div>
@else
    <div style="position: absolute; top: 58mm; right: 24mm; height: 20mm;">
        <div style="font-size: 17px; line-height: 22px; text-align: center; color: #777; font-style: italic">
            <br/>Entregue sem<br/>Assinatura
        </div>
    </div>
@endif