<style>
    table td,
    table th {
        padding: 3px;
    }
    div {
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    }
</style>
<div class="shipping-instructions" style="width: 210mm; padding: 10mm; font-size: 10pt; height: 230mm">
    <div class="guide-content">
        <div class="guide-row" style="padding-top: 5mm;">
            <div class="fs-9px lh-1-2" style="width: 45%; height: 14mm; float: left;">
                <p>
                    {{ @$shipment->agency->company }}<br>
                    {{ @$shipment->agency->address }}<br/>
                    {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}
                    <br/>
                    {{ trans('country.' . @$shipment->agency->country) }}
                </p>
                <p>
                    @if(@$shipment->agency->vat)
                        NIPC: {{ @$shipment->agency->vat }} <br/>
                    @endif
                    @if(@$shipment->agency->charter)
                        Alvará N: {{ @$shipment->agency->charter }} <br/>
                    @endif
                    @if(@$shipment->agency->phone)
                        Phone: {{ @$shipment->agency->phone }}<br/>
                    @endif
                    @if(@$shipment->agency->email)
                        Mail: {{ @$shipment->agency->email }}<br/>
                    @endif
                </p>
                {{--<p>
                    Contas Bancárias<br/>
                    Novo Banco: PT50 0007 0392 00021680001 15 <br/>
                    BCP: PT50 0033 0000 45445555167 05<br/>
                </p>--}}
            </div>
            <div class="guide-block-right" style="width: 50%; border: 1px solid #000; border-radius: 10px; padding: 15px; float: left; height: 35mm">
                @if($shipment->payment_at_recipient)
                    <p style="text-transform: uppercase">
                        {{ @$shipment->recipient_name }}<br/>
                        {{ @$shipment->recipient_address }}<br/>
                        {{ @$shipment->recipient_zip_code }} {{ @$shipment->recipient_city }}<br/>
                        @if($shipment->recipient_country)
                            {{ trans('country.' . @$shipment->recipient_country) }}
                        @endif
                    </p>
                @else
                    <p style="text-transform: uppercase">
                        {{ @$shipment->customer->billing_name }}<br/>
                        {{ @$shipment->customer->billing_address }}<br/>
                        {{ @$shipment->customer->billing_zip_code }} {{ @$shipment->customer->billing_city }}<br/>
                        @if(@$shipment->customer->billing_country)
                            {{ trans('country.' . @$shipment->customer->billing_country) }}
                        @endif
                    </p>
                @endif
            </div>
        </div>
        <div class="guide-row" style="margin-top: 10mm">
            <div class="fs-10pt lh-1-6">
                <div style="float: left; width: 10%;">
                    <img src="{{ @$qrCode }}" style="width: 50px">
                </div>
                <div style="float: left; width: 55%;">
                    Expedição <span style="font-weight: bold">{{ $shipment->tracking_code }}</span>
                    <p class="fs-9pt">
                        Referência: {{ $shipment->reference }}
                        @if($shipment->reference2)
                            <br/>
                            {{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Referência 2'}}: {{ $shipment->reference2 }}
                        @endif

                        @if($shipment->reference3)
                            <br/>
                            {{ Setting::get('shipments_reference3_name') ? Setting::get('shipments_reference3_name') : 'Referência 3'}}: {{ $shipment->reference3 }}
                        @endif
                        <br/>
                        Viatura: {{ $shipment->vehicle }}
                        @if($shipment->trailer)
                            / Reboque: {{ $shipment->trailer }}
                        @endif
                    </p>
                    {{--<p class="fs-9pt">
                        Cobrança: {{ ($shipment->charge_price + $shipment->payment_at_recipient) > 0.00 ? money($shipment->charge_price + $shipment->payment_at_recipient, ' EUR') : '' }}
                    </p>--}}
                </div>
                <div style="float: left;  width: 34%; text-align: right">
                    Carga: {{ $shipment->date }}<br/>
                    @if(!empty($shipment->delivery_date))
                    Descarga: {{ $shipment->delivery_date->format('Y-m-d') }}
                    @endif
                    <p class="fs-9pt">
                        Serviço: {{ @$shipment->service->name }}<br/>
                        @if($shipment->start_hour)
                        Horário: {{ $shipment->start_hour }} {{ $shipment->end_hour ? ' - ' . $shipment->end_hour : $shipment->end_hour }}
                        @endif
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="guide-row">
            <div class="fs-10 lh-1-2" style="width: 48%; height: 14mm; float: left;">
                <div class="fs-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Local de Carga
                </div>
                <div class="p-t-10 fs-10pt lh-1-4">
                    <p>
                        @if($shipment->sender_attn)
                            A/Cargo de: {{ $shipment->sender_attn }}<br/>
                        @endif
                        {{ @$shipment->sender_name }}<br>
                        {{ @$shipment->sender_address }}<br/>
                        {{ @$shipment->sender_zip_code }} {{ @$shipment->sender_city }}
                        <br/>
                        {{ trans('country.' . @$shipment->sender_country) }}
                    </p>
                    @if($shipment->sender_phone)
                        <p class="fs-11">
                        Contacto {{ $shipment->sender_phone }}
                    </p>
                    @endif
                </div>
            </div>
            <div class="fs-10 lh-1-2" style="width: 49%; height: 14mm; float: left;  margin-left: 2mm;">
                <div class="fs-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Local de Descarga
                </div>
                <div class="p-t-10 fs-10pt lh-1-4">
                    <p>
                        @if($shipment->recipient_attn)
                            A/Cargo de: {{ $shipment->recipient_attn }}<br/>
                        @endif
                        {{ @$shipment->recipient_name }}<br>
                        {{ @$shipment->recipient_address }}<br/>
                        {{ @$shipment->recipient_zip_code }} {{ @$shipment->recipient_city }}
                        <br/>
                        {{ trans('country.' . @$shipment->recipient_country) }}
                    </p>
                    @if($shipment->recipient_phone)
                    <p class="fs-11">
                        Contacto {{ $shipment->recipient_phone }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        <br/>
        <div class="guide-row">
            <div class="fs-10 lh-1-2" style="width: 25%; float: left;">
                <div class="fs-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Remessa
                </div>
                <div class="p-10px lh-1-4">
                    <table class="fs-11 w-100">
                        <tr>
                            <th>Volumes</th>
                            <th class="text-right">{{ money($shipment->volumes) }}</th>
                        </tr>
                        <tr>
                            <th>Peso Real</th>
                            <td class="text-right">{{ money($shipment->weight) }}kg</td>
                        </tr>
                        <tr>
                            <th>Peso Volumétrico</th>
                            <td class="text-right">{{ money($shipment->volumetric_weight) }}kg</td>
                        </tr>
                        <tr>
                            <th>Peso Taxável</th>
                            <th class="text-right">{{ money($shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight ) }}kg</th>
                        </tr>
                        <tr>
                            <th>Fator Volumetria</th>
                            <td class="text-right">{{ money($shipment->fator_m3) }}</td>
                        </tr>
                        @if($shipment->kms)
                        <tr>
                            <th>Quilometros</th>
                            <th class="text-right">{{ money($shipment->kms) }}km</th>
                        </tr>
                        @endif

                        @if($shipment->m3)
                        <tr>
                            <th>Volume M3</th>
                            <th class="text-right">{{ money($shipment->volume_m3) }}m<sup>3</sup></th>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            <div class="fs-10 lh-1-2 m-l-8" style="width: 25%; float: left;">
                <div class="fs-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Serviços Adicionais
                </div>
                <div class="p-10px lh-1-4">
                    <table class="fs-11">
                        @if(empty($shipment->charge_price) && empty($shipment->payment_at_recipient) && empty($shipment->complementar_services))
                            <tr>
                                <td>Sem serviços complementares</td>
                            </tr>
                        @else
                            @if($shipment->charge_price)
                            <tr>
                                <th>Envio à Cobrança:</th>
                                <th>{{ money($shipment->charge_price, '€') }}</th>
                            </tr>
                            @endif
                            @if($shipment->payment_at_recipient)
                            <tr>
                                <td colspan="2">Pagamento no Destino</td>
                            </tr>
                            @endif
                            @if($shipment->complementar_services)
                                @foreach($shipment->complementar_services as $key => $id)
                                    <?php
                                        $complementarService = $complementarServices->filter(function ($item) use($id) {
                                            return $item->id == $id && $item->type != 'charge';
                                        })->first();
                                    ?>
                                    <tr>
                                        <td colspan="2">{{ @$complementarService->name }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endif
                    </table>
                </div>
            </div>
            <div class="fs-10 lh-1-2 m-l-8" style="width: 45%; float: left;">
                <div class="fs-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Taxas Adicionais
                </div>
                <div class="p-10px lh-1-4">
                    <table class="fs-11 w-100">
                        @if($shipment->expenses->isEmpty())
                            <tr>
                                <td>Sem taxas adicionais</td>
                            </tr>
                        @else
                            <tr>
                                <th>Encargo</th>
                                <th class="text-right">Qt</th>
                                <th class="text-right">Valor</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                            @foreach($shipment->expenses as $expense)
                                <tr>
                                    <td>{{ $expense->name }}</td>
                                    <td class="text-right">{{ $expense->unity == 'euro' ? $expense->pivot->qty : ''}}</td>
                                    <td class="text-right">{{ money($expense->pivot->price, $expense->unity == 'euro' ? Setting::get('app_currency') : '%') }}</td>
                                    <td class="text-right">{{ money($expense->pivot->subtotal, Setting::get('app_currency')) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <br/>
        @if(!$shipment->pack_dimensions->isEmpty())
        <div class="guide-row" style="height: 35mm">
            <div class="lh-1-6">
                <table class="table table-bordered fs-10">
                    <tr>
                        <th style="width: 30%; background: #f2f2f2" rowspan="2" class="text-center">Descrição das Mercadorias</th>
                        <th colspan="8" class="text-center p-5" style="background: #f2f2f2">Detalhe da Carga</th>
                    </tr>
                    <tr>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Volumes</th>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Comprimento</th>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Largura</th>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Altura</th>
                        <th class="text-center" style="background: #f2f2f2">Tipo</th>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Peso</th>
                        <th class="text-center" style="background: #f2f2f2; width: 45px">M3</th>
                        <th class="text-center" style="background: #f2f2f2; width: 65px">Nº Paletes</th>
                    </tr>
                    @foreach($shipment->pack_dimensions as $pack)
                        <tr>
                            <td>{{ $pack->description }}</td>
                            <td class="text-center">{{ $pack->qty }}</td>
                            <td class="text-center" style="padding: 5px 0">{{ money($pack->length) }}</td>
                            <td class="text-center">{{ money($pack->width) }}</td>
                            <td class="text-center">{{ money($pack->height) }}</td>
                            <td class="text-center text-uppercase">{{ @$pack->packtype->name }}</td>
                            <td class="text-center">{{ money($pack->weight) }}</td>
                            <td class="text-center">{{ money($pack->volume) }}</td>
                            <td class="text-center">0</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
       @endif

        <div class="guide-row">
            <div class="fs-10 lh-1-2" style="width: 48%; float: left;">
                <div class="fs-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Observações do cliente
                </div>
                <div class="p-10px lh-1-4">{{ $shipment->obs }}</div>
            </div>
            <div class="fs-10 lh-1-2 m-l-8" style="width: 25%; float: left;">
                <div class="fs-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    RESUMO
                </div>
                <div class="p-10px lh-1-0">
                    <table class="fs-11 w-100">
                        <tr>
                            <th>Valor envio</th>
                            <td class="text-right">{{ money($shipment->total_price, Setting::get('app_currency')) }}</td>
                        </tr>
                        <tr>
                            <th>Valor taxas</th>
                            <td class="text-right">{{ money($shipment->total_expenses, Setting::get('app_currency')) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="fs-10 lh-1-2 m-l-8" style="width: 25%; float: left;">
                <div class="fs-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    TOTAL DOCUMENTO
                </div>
                <div class="p-10px lh-1-0">
                    <table class="fs-11 w-100">
                        <tr>
                            <th>SUBTOTAL</th>
                            <th class="text-right">
                                @if($shipment->total_price_for_recipient)
                                    {{ money($shipment->total_price_for_recipient + $shipment->total_expenses, Setting::get('app_currency')) }}
                                @else
                                    {{ money($shipment->total_price + $shipment->total_expenses, Setting::get('app_currency')) }}
                                @endif
                            </th>
                        </tr>
                        <tr>
                            <th>IVA ({{ money($shipment->getVatRate(true), '%') }})</th>
                            <th class="text-right">
                                @if($shipment->total_price_for_recipient)
                                    @if($shipment->isVatExempt())
                                        {{ money(0, Setting::get('app_currency')) }}
                                    @else
                                        {{ money(getVat($shipment->total_price_for_recipient + $shipment->total_expenses), Setting::get('app_currency')) }}
                                    @endif
                                @else
                                    @if($shipment->isVatExempt())
                                        {{ money(0, Setting::get('app_currency')) }}
                                    @else
                                        {{ money(getVat($shipment->total_price + $shipment->total_expenses), Setting::get('app_currency')) }}
                                    @endif
                                @endif
                            </th>
                        </tr>
                        <tr>
                            <th>TOTAL PAGAR</th>
                            <th class="fs-14 text-right">
                                @if($shipment->total_price_for_recipient)
                                    @if($shipment->isVatExempt())
                                        {{ money($shipment->total_price_for_recipient + $shipment->total_expenses, Setting::get('app_currency')) }}
                                    @else
                                        {{ money(valueWithVat($shipment->total_price_for_recipient + $shipment->total_expenses), Setting::get('app_currency')) }}
                                    @endif
                                @else
                                    @if($shipment->isVatExempt())
                                        {{ money($shipment->total_price + $shipment->total_expenses, Setting::get('app_currency')) }}
                                    @else
                                        {{ money(valueWithVat($shipment->total_price + $shipment->total_expenses), Setting::get('app_currency')) }}
                                    @endif
                                @endif
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="text-center fs-10 w-100 m-b-50">
    * * * ESTE DOCUMENTO NÃO SERVE DE FATURA * * *
</div>
<div class="fs-6pt" style="padding-left: 10mm; padding-right: 10mm;">
    <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ app_brand('docsignature') }}</b></div>
    <div class="pull-left text-right" style="width: 42%">Emitido por: {{ Auth::user()->name }}</div>
</div>