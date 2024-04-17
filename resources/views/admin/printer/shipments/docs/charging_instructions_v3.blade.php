@if($page == 1)
 <div class="shipping-instructions" style="width: 210mm; padding: 10mm; font-size: 10pt; height: 250mm">
    <div class="guide-content">
        <div class="guide-row" style="padding-top: 5mm;">
            <div class="fs-10 lh-1-2" style="width: 45%; height: 14mm; float: left;">
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
                <p style="text-transform: uppercase">
                    {{ @$shipment->provider->company }}<br/>
                    {{ @$shipment->provider->address }}<br/>
                    {{ @$shipment->provider->zip_code }} {{ @$shipment->provider->city }}<br/>
                    @if($shipment->provider->country)
                    {{ trans('country.' . @$shipment->provider->country) }}
                    @endif
                </p>
                @if(@$shipment->provider->attn)
                <br/>
                ATT: {{ @$shipment->provider->attn }}
                @endif
            </div>
        </div>
        <div class="guide-row" style="margin-top: 10mm">
            <div class="font-size-10pt lh-1-6">
                <div style="float: left; width: 75%;">
                    Ref. para Faturação / Billing Ref.: TRK{{ $shipment->tracking_code }}
                    <p class="font-size-9pt">
                        Ref. Carga / Cargo Ref.: {{ $shipment->reference }}
                    </p>
                    <p class="font-size-9pt">
                        Matrículas / Plate Nr.: {{ $shipment->vehicle }}
                        <br/>
                        Valor Acordado / Agreed Amount: {{ money($shipment->cost_price, ' EUR') }}
                        <br/>
                        Obs. / Notes: {{ $shipment->obs }}
                    </p>
                </div>
                <div style="float: left;  width: 24%; text-align: right">
                    Data / Date: {{ $shipment->date }}
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="guide-row" style="height: 90mm">
            <div class="lh-1-6">
                <table class="table table-bordered fs-10">
                    <tr>
                        <th style="width: 30%; background: #f2f2f2" rowspan="2" class="text-center">Descrição das Mercadorias<br/>Marks and Numbers</th>
                        <th colspan="7" class="text-center p-5" style="background: #f2f2f2">Detalhe da Carga / Cargo Description</th>
                        @if(Setting::get('show_adr_fields'))
                        <th colspan="3" class="text-center p-5" style="background: #f2f2f2">ADR</th>
                        @endif
                    </tr>
                    <tr>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Comp.<br/>Lenght</th>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Larg.<br/>Width</th>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Alt.<br/>Height</th>
                        <th class="text-center" style="background: #f2f2f2; width: 60px;">Nº Vol.<br/>No Pakgs</th>
                        <th class="text-center" style="background: #f2f2f2; width: 80px;">Tipo Vols.<br/>Packs. Type</th>
                        <th class="text-center" style="background: #f2f2f2; width: 55px">Peso<br/>Weight</th>
                        <th class="text-center" style="background: #f2f2f2; width: 45px">M3<br/>BM</th>
                        @if(Setting::get('show_adr_fields'))
                        <th class="text-center" style="background: #f2f2f2; width: 30px">Classe<br/>Class</th>
                        <th class="text-center" style="background: #f2f2f2; width: 30px">Letra<br/>Leter</th>
                        <th class="text-center" style="background: #f2f2f2; width: 40px">Nº<br/>No</th>
                        @endif
                    </tr>
                    @if(!$shipment->pack_dimensions->isEmpty())
                        @foreach($shipment->pack_dimensions as $pack)
                        <tr>
                            <td>{{ $pack->description }}</td>
                            <td class="text-center" style="padding: 5px 0">{{ money($pack->length) }}</td>
                            <td class="text-center">{{ money($pack->width) }}</td>
                            <td class="text-center">{{ money($pack->height) }}</td>
                            <td class="text-center">{{ money($pack->qty ? $pack->qty : 1) }}</td>
                            <td class="text-center text-uppercase">{{ $pack->type ? @$pack->packtype->name : 'Volume' }}</td>
                            <td class="text-center">{{ money($pack->weight) }}</td>
                            <td class="text-center">0</td>
                            @if(Setting::get('show_adr_fields'))
                            <td class="text-center">{{ $pack->adr_class }}</td>
                            <td class="text-center">{{ $pack->adr_letter }}</td>
                            <td class="text-center">{{ $pack->adr_number }}</td>
                            @endif
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>Volumes diversos</td>
                            <td class="text-center" style="padding: 5px 0">0,00</td>
                            <td class="text-center">0,00</td>
                            <td class="text-center">0,00</td>
                            <td class="text-center">{{ money($shipment->volumes) }}</td>
                            <td class="text-center">VOL</td>
                            <td class="text-center">{{ money($shipment->weight) }}</td>
                            <td class="text-center">0,00</td>
                            @if(Setting::get('show_adr_fields'))
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            @endif
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="guide-row">
            <div class="fs-10 lh-1-2" style="width: 48%; height: 14mm; float: left;">
                <div class="font-size-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Local de Carga / Place of Loading
                </div>
                <div class="p-10px lh-1-4">
                    <p>
                        Data/Hora Date/Hour: {{ $shipment->date }} {{ $shipment->start_hour }}
                    </p>
                    <p>
                        {{ @$shipment->sender_name }}<br>
                        {{ @$shipment->sender_address }}<br/>
                        {{ @$shipment->sender_zip_code }} {{ @$shipment->sender_city }}
                        <br/>
                        {{ trans('country.' . @$shipment->sender_country) }}
                    </p>
                    <p>
                        Telefone / Phone {{ $shipment->sender_phone }}
                    </p>
                </div>
            </div>
            <div class="fs-10 lh-1-2" style="width: 49%; height: 14mm; float: left;  margin-left: 2mm;">
                <div class="font-size-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Local de Descarga / Place of Delivery
                </div>
                <div class="p-10px lh-1-4">
                    <p>
                        Data/Hora Date/Hour: {{ $shipment->delivery_date ? $shipment->delivery_date : getNextUsefullDate($shipment->date) }} {{ $shipment->end_hour }}
                    </p>
                    <p>
                        {{ @$shipment->recipient_name }}<br>
                        {{ @$shipment->recipient_address }}<br/>
                        {{ @$shipment->recipient_zip_code }} {{ @$shipment->recipient_city }}
                        <br/>
                        {{ trans('country.' . @$shipment->recipient_country) }}
                    </p>
                    <p>
                        Telefone / Phone {{ $shipment->recipient_phone }}
                        @if($shipment->recipient_attn)
                            - {{ $shipment->recipient_attn }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="guide-row">
            <div class="fs-10 lh-1-2" style="width: 48%; float: left;">
                <div class="font-size-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Observações de Carga / Loading Notes
                </div>
                <div class="p-10px lh-1-4">{{ $shipment->loading_notes ? $shipment->loading_notes : $shipment->obs }}</div>
            </div>
            <div class="fs-10 lh-1-2" style="width: 49%; float: left;  margin-left: 2mm;">
                <div class="font-size-10pt p-5" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
                    Observações de Descarga / Delivery Notes
                </div>
                <div class="p-10px lh-1-4">{{ $shipment->obs_delivery }}</div>
            </div>
        </div>
    </div>
</div>
<div class="font-size-6pt" style="padding-left: 10mm; padding-right: 10mm;">
    {{--<div class="pull-left" style="width: 60%">Software desenvolvido por <b style="font-weight: bold">ENOVO, Web Design, E-commerce e Aplicações Online - www.enovo.pt</b></div>--}}
    <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ app_brand('docsignature') }}</b></div>
    <div class="pull-left text-right" style="width: 42%">Preparado por / Issued by: {{ Auth::user()->name }}</div>
</div>
@else
    <?php
    $conditions = Setting::get('prices_table_general_conditions');
    $conditions = str_replace('<strong>', '<b style="font-weight:bold">', str_replace('</strong>', '</b>', $conditions))
    ?>
    <div class="shipping-instructions" style="width: 210mm; padding: 10mm; font-size: 10pt; height: 250mm">
        <div class="guide-content">
            <div class="guide-row" style="padding-top: 5mm;">
                <h1 style="text-align: center; text-transform: uppercase; margin-top:0; margin-bottom: 30px">
                    Condições Gerais de Serviço<br/>
                    <small>General Conditions of Service</small>
                </h1>
                {!! $conditions !!}
            </div>
        </div>
    </div>
@endif