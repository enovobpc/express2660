<?php
$locale = $shipment->provider->locale;
$locale = $locale ? $locale : 'pt';
?>
@if($page == 1)
    <div class="shipping-instructions" style="width: 210mm; padding: 10mm; font-size: 9pt; height: 250mm">
        <div class="guide-content">
            <div class="guide-row" style="padding-top: 5mm;">
                <div class="fs-12 lh-1-3 p-t-10" style="width: 45%; height: 14mm; float: left;">
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
                <div class="guide-block-right" style="width: 50%; border: 1px solid #000; border-radius: 10px; padding: 10px; float: left; height: 35mm">
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
                <p class="lh-1-2 fs-12">
                    {!! nl2br(Setting::get('charging_instructions_presentation_' . $locale)) !!}
                </p>
                <h4 style="font-weight: bold;">{{ strtoupper(transLocale('admin/global.word.dossier_summary', $locale)) }}</h4>
                <div class="guide-row">
                    <div style="width: 100%; float: left; border: 1px solid #999; border-radius: 4px">
                        <div style="width: 15%; float: left;padding: 2px 10px 2px 5px">
                            <div style="font-weight: bold">{{ transLocale('admin/global.word.dossier', $locale) }}</div>
                            <div style="font-size: 14px"></div>{{ $shipment->tracking_code }}
                        </div>
                        <div style="width: 38%; float: left;padding: 2px 10px">
                            <div style="border-bottom: 1px solid #777;">
                                {{ transLocale('admin/global.word.from', $locale) }}: <b style="font-weight: bold; text-transform: uppercase">{{ $shipment->sender_city }}</b>
                            </div>
                            <div style="border-bottom: 1px solid #777;">
                                {{ transLocale('admin/global.word.followed_by', $locale) }}: {{ @$shipment->operator->name }}<br/>
                            </div>
                            <div>
                                {{ transLocale('admin/global.word.vehicle', $locale) }}: {{ $shipment->vehicle }} {{ $shipment->trailer ? '+'.$shipment->trailer : '' }}
                            </div>
                        </div>
                        <div style="width: 38%; float: left;padding: 2px 10px">
                            <div style="border-bottom: 1px solid #777;">
                                {{ transLocale('admin/global.word.to', $locale) }}: <b style="font-weight: bold; text-transform: uppercase">{{ $shipment->recipient_city }}</b>
                            </div>
                            <div style="border-bottom: 1px solid #777;">
                                {{ transLocale('admin/global.word.shipped_by', $locale) }}: {{ Auth::user()->name }}<br/>
                            </div>
                            <div>
                                {{ transLocale('admin/global.word.total_price', $locale) }}: {{ money($shipment->cost_price + $shipment->total_expenses_cost, 'EUR') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <h4 style="font-weight: bold; margin-top: 5mm">{{ strtoupper(transLocale('admin/global.word.transport', $locale)) }}</h4>
            <div style="background: #777; color: #fff; font-size: 12px; font-weight: bold; padding: 2px 10px; border: 2px solid #333; border-bottom: none; border-radius: 4px 4px 0 0">
                ETAPA: {{ $shipment->sender_city }},{{ strtoupper($shipment->sender_country) }}  - {{ $shipment->recipient_city }},{{ strtoupper($shipment->recipient_country) }}
            </div>
            <div class="guide-row m-b-5" style="border: 2px solid #333; border-top: none; border-radius: 0 0 4px 4px">

                <div style="width: 49.8%;float: left; border-right: 1px solid #ccc;">
                    <div style="float: left;">
                        <div style="border-bottom: 1px solid #ccc; background: #f2f2f2">
                            <div style="text-align: right; float: right; width: 60%; padding: 2px 5px 0 0;">
                                {{ transLocale('admin/global.word.datetime', $locale) }}: {{ $shipment->date }} {{ $shipment->start_hour }}
                            </div>
                            <div style="font-size: 13px; float: left; font-weight: bold;  width: 37%; padding: 0 0 0 5px">{{ strtoupper(transLocale('admin/global.word.pickup', $locale)) }}</div>
                        </div>
                        <div style="padding: 5px 10px; border-bottom: 1px solid #ccc;">
                            <p class="lh-1-3 m-0" style="height: 21mm">
                                {{ @$shipment->sender_name }}<br>
                                {{ @$shipment->sender_address }}<br/>
                                {{ @$shipment->sender_zip_code }} {{ @$shipment->sender_city }}<br/>
                                {{ trans('country.' . @$shipment->sender_country) }}
                            </p>
                        </div>
                        <div style="padding: 2px 10px; height: 9mm">
                            <div style="width: 100%; line-height: 1.2">
                                <span style="font-weight: bold;">Obs.:</span> {{ $shipment->obs }}
                            </div>
                        </div>
                        <div style="padding: 2px 10px; border-bottom: 1px solid #ccc">
                            <div style="float: left; width: 49%">
                                <span style="font-weight: bold;">Ref.:</span><br/>{{ $shipment->reference }}
                            </div>
                            <div style="float: right; width: 50%">
                                <span style="font-weight: bold;">{{ transLocale('admin/global.word.contact', $locale) }}:</span> {{ $shipment->sender_phone }}
                            </div>
                        </div>
                    </div>
                </div>
                <div style="width: 50%;float: left;">
                    <div style="float: left;">
                        <div style="border-bottom: 1px solid #ccc; background: #f2f2f2">
                            <div style="text-align: right; float: right; width: 60%; padding: 2px 5px 0 0;">
                                {{ transLocale('admin/global.word.datetime', $locale) }}: {{ $shipment->delivery_date }}
                            </div>
                            <div style="font-size: 13px; float: left; font-weight: bold;  width: 37%; padding: 0 0 0 5px">{{ strtoupper(transLocale('admin/global.word.delivery', $locale)) }}</div>
                        </div>
                        <div style="padding: 5px 10px; border-bottom: 1px solid #ccc">
                            <p class="lh-1-3 m-0" style="height: 21mm">
                                {{ @$shipment->recipient_name }}<br>
                                {{ @$shipment->recipient_address }}<br/>
                                {{ @$shipment->recipient_zip_code }} {{ @$shipment->recipient_city }}<br/>
                                {{ trans('country.' . @$shipment->recipient_country) }}
                            </p>
                        </div>
                        <div style="padding: 2px 10px; height: 9mm">
                            <div style="width: 100%; line-height: 1.2">
                                <span style="font-weight: bold;">Obs.:</span> {{ $shipment->obs_delivery }}
                            </div>
                        </div>
                        <div style="padding: 2px 10px; border-bottom: 1px solid #ccc">
                            <div style="float: left; width: 49%">
                                <span style="font-weight: bold;">Ref.:</span><br/>{{ $shipment->ref2 }}
                            </div>
                            <div style="float: right; width: 50%">
                                <span style="font-weight: bold;">{{ transLocale('admin/global.word.contact', $locale) }}:</span> {{ $shipment->recipient_phone }}
                            </div>
                        </div>
                    </div>
                </div>
                @if(!$shipment->pack_dimensions->isEmpty())
                <div style="width: 100%; float: left; padding: 10px;">
                    <p style="font-weight: bold; margin: 0">{{ transLocale('admin/global.word.goods', $locale) }}</p>
                    <table class="table table-bordered fs-10" style="margin: 0">
                        <tr>
                            <th style="background: #f2f2f2; width: 260px">{{ transLocale('admin/global.word.description', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 5px;">{{ transLocale('admin/global.word.qty', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 60px;">{{ transLocale('admin/global.word.type', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.width_abrv', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.length_abrv', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.height_abrv', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.weight', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 20px">{{ transLocale('admin/global.word.class', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 20px">{{ transLocale('admin/global.word.letter', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 30px">Nº</th>
                        </tr>
                        @foreach($shipment->pack_dimensions as $pack)
                            <tr>
                                <td>{{ $pack->description }}</td>
                                <td class="text-center">{{ money($pack->qty ? $pack->qty : 1) }}</td>
                                <td class="text-center text-uppercase">{{ $pack->type ? @$pack->packtype->name : 'Volume' }}</td>
                                <td class="text-center" style="padding: 5px 0">{{ money($pack->length) }}</td>
                                <td class="text-center">{{ money($pack->width) }}</td>
                                <td class="text-center">{{ money($pack->height) }}</td>
                                <td class="text-center">{{ money($pack->weight) }}</td>
                                <td class="text-center">{{ $pack->adr_class }}</td>
                                <td class="text-center">{{ $pack->adr_letter }}</td>
                                <td class="text-center">{{ $pack->adr_number }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                @endif
            </div>

            <?php $i = 0 ?>
            @if($shipment->addresses)
                @foreach($shipment->addresses as $address)
                    @if($i == 1)
                        <div style="height: 43px"></div>
                    @endif
                        <div style="background: #777; color: #fff; font-size: 12px; font-weight: bold; padding: 2px 10px; border: 2px solid #333; border-bottom: none; border-radius: 4px 4px 0 0">
                            ETAPA: {{ $address->sender_city }},{{ strtoupper($address->sender_country) }}  - {{ $address->recipient_city }},{{ strtoupper($address->recipient_country) }}
                        </div>
                        <div class="guide-row m-b-5" style="border: 2px solid #333; border-top: none; border-radius: 0 0 4px 4px">

                            <div style="width: 49.8%;float: left; border-right: 1px solid #ccc;">
                                <div style="float: left;">
                                    <div style="border-bottom: 1px solid #ccc; background: #f2f2f2">
                                        <div style="text-align: right; float: right; width: 60%; padding: 2px 5px 0 0;">
                                            {{ transLocale('admin/global.word.datetime', $locale) }}: {{ $address->date }} {{ $address->start_hour }}
                                        </div>
                                        <div style="font-size: 13px; float: left; font-weight: bold;  width: 37%; padding: 0 0 0 5px">{{ strtoupper(transLocale('admin/global.word.pickup', $locale)) }}</div>
                                    </div>
                                    <div style="padding: 5px 10px; border-bottom: 1px solid #ccc">
                                        <p class="lh-1-3 m-0" style="height: 21mm">
                                            {{ @$address->sender_name }}<br>
                                            {{ @$address->sender_address }}<br/>
                                            {{ @$address->sender_zip_code }} {{ @$address->sender_city }}<br/>
                                            {{ trans('country.' . @$address->sender_country) }}
                                        </p>
                                    </div>
                                    <div style="padding: 2px 10px; height: 9mm">
                                        <div style="width: 100%; line-height: 1.2">
                                            <span style="font-weight: bold;">Obs.:</span> {{ $address->obs }}
                                        </div>
                                    </div>
                                    <div style="padding: 2px 10px; border-bottom: 1px solid #ccc">
                                        <div style="float: left; width: 49%">
                                            <span style="font-weight: bold;">Ref.:</span><br/>{{ $address->ref }}
                                        </div>
                                        <div style="float: right; width: 50%">
                                            <span style="font-weight: bold;">{{ transLocale('admin/global.word.contact', $locale) }}:</span> {{ $address->sender_phone }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 50%;float: left;">
                                <div style="float: left;">
                                    <div style="border-bottom: 1px solid #ccc; background: #f2f2f2">
                                        <div style="text-align: right; float: right; width: 60%; padding: 2px 5px 0 0;">
                                            {{ transLocale('admin/global.word.datetime', $locale) }}: {{ $address->delivery_date }}
                                        </div>
                                        <div style="font-size: 13px; float: left; font-weight: bold;  width: 37%; padding: 0 0 0 5px">{{ strtoupper(transLocale('admin/global.word.delivery', $locale)) }}</div>
                                    </div>
                                    <div style="padding: 5px 10px; border-bottom: 1px solid #ccc">
                                        <p class="lh-1-3 m-0" style="height: 21mm">
                                            {{ @$address->recipient_name }}<br>
                                            {{ @$address->recipient_address }}<br/>
                                            {{ @$address->recipient_zip_code }} {{ @$address->recipient_city }}<br/>
                                            {{ trans('country.' . @$address->recipient_country) }}
                                        </p>
                                    </div>
                                    <div style="padding: 2px 10px; height: 9mm">
                                        <div style="width: 100%; line-height: 1.2">
                                            <span style="font-weight: bold;">Obs.:</span> {{ $address->obs_delivery }}
                                        </div>
                                    </div>
                                    <div style="padding: 2px 10px; border-bottom: 1px solid #ccc">
                                        <div style="float: left; width: 49%">
                                            <span style="font-weight: bold;">Ref.:</span><br/>{{ $address->ref2 }}
                                        </div>
                                        <div style="float: right; width: 50%">
                                            <span style="font-weight: bold;">{{ transLocale('admin/global.word.contact', $locale) }}:</span> {{ $address->recipient_phone }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!$address->pack_dimensions->isEmpty())
                            <div style="width: 100%; float: left; padding: 10px;">
                                <p style="font-weight: bold; margin: 0">{{ transLocale('admin/global.word.goods', $locale) }}</p>
                                <table class="table table-bordered fs-10" style="margin: 0">
                                    <tr>
                                        <th style="background: #f2f2f2; width: 260px">{{ transLocale('admin/global.word.description', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 5px;">{{ transLocale('admin/global.word.qty', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 60px;">{{ transLocale('admin/global.word.type', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.width_abrv', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.length_abrv', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.height_abrv', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.weight', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 20px">{{ transLocale('admin/global.word.class', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 20px">{{ transLocale('admin/global.word.letter', $locale) }}</th>
                                        <th class="text-center" style="background: #f2f2f2; width: 30px">Nº</th>
                                    </tr>
                                    @foreach($address->pack_dimensions as $pack)
                                        <tr>
                                            <td>{{ $pack->description }}</td>
                                            <td class="text-center">{{ money($pack->qty ? $pack->qty : 1) }}</td>
                                            <td class="text-center text-uppercase">{{ $pack->type ? @$pack->packtype->name : 'Volume' }}</td>
                                            <td class="text-center" style="padding: 5px 0">{{ money($pack->width) }}</td>
                                            <td class="text-center">{{ money($pack->length) }}</td>
                                            <td class="text-center">{{ money($pack->height) }}</td>
                                            <td class="text-center">{{ money($pack->weight) }}</td>
                                            <td class="text-center">{{ $pack->adr_class }}</td>
                                            <td class="text-center">{{ $pack->adr_letter }}</td>
                                            <td class="text-center">{{ $pack->adr_number }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            @endif
                        </div>
                    <?php $i++ ?>
                @endforeach
            @endif



            <h4 style="font-weight: bold; margin-top: 20px">{{ strtoupper(transLocale('admin/global.word.service_billing', $locale)) }}</h4>

            <div class="guide-row m-b-10">
                <div style="width: 100%; float: left; border: 1px solid #333; border-radius: 4px">
                    <table class="table table-bordered fs-10" style="margin: 0">
                        <tr>
                            <th style="background: #f2f2f2;">{{ transLocale('admin/global.word.expenses', $locale) }}</th>
                            <th class="text-center" style="background: #f2f2f2; width: 30px">Qtd</th>
                            <th class="text-center" style="background: #f2f2f2; width: 60px">Total</th>
                        </tr>
                        <tr>
                            <td>{{ transLocale('admin/global.word.transport_service', $locale) }} ({{ transLocale('admin/global.word.agreed_price', $locale) }})</td>
                            <td class="text-center">1,00</td>
                            <td class="text-center">{{ money($shipment->cost_price) }}</td>
                        </tr>
                        @foreach($shipment->expenses as $expense)
                            @if($expense->pivot->cost_price > 0.00)
                            <tr>
                                <td>{{ $expense->name }}</td>
                                <td class="text-center">{{ money($expense->pivot->qty) }}</td>
                                <td class="text-center">{{ money($expense->pivot->cost_price) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="guide-row">
                <div style="width: 100%; float: left; border: 1px solid #333; border-radius: 4px">
                    <div style="width: 100%; float: left;padding: 2px 10px">
                        <p style="font-weight: bold; margin: 0">{{ transLocale('admin/global.word.payment_method', $locale) }}</p>
                        <p>{{ @$shipment->provider->payment_method ? ($locale == 'pt' ? $shipment->provider->paymentCondition->name : $shipment->provider->paymentCondition->{'name_'.$locale}) : '30 dias' }}</p>
                    </div>
                    <div style="width: 47%; float: left;padding: 2px 10px">
                        <p style="font-weight: bold; margin: 0">{{ transLocale('admin/global.word.billing_data', $locale) }}</p>
                        <p style="margin: 0">
                            NIPC: {{ @$shipment->agency->vat }} <br/>
                            {{ @$shipment->agency->company }}<br>
                            {{ @$shipment->agency->billing_address }}<br/>
                            {{ @$shipment->agency->billing_zip_code }} {{ @$shipment->agency->billing_city }}
                            <br/>
                            {{ trans('country.' . @$shipment->agency->billing_country) }}
                        </p>
                    </div>
                    <div style="width: 47%; float: left;padding: 2px 10px">
                        <p style="font-weight: bold; margin: 0">{{ transLocale('admin/global.word.billing_address', $locale) }}</p>
                        <p style="margin: 0">
                            {{ @$shipment->agency->company }}<br>
                            {{ @$shipment->agency->address }}<br/>
                            {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}
                            <br/>
                            {{ trans('country.' . @$shipment->agency->country) }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="font-size-6pt" style="padding-left: 10mm; padding-right: 10mm;">
        {{--<div class="pull-left" style="width: 60%">Software desenvolvido por <b style="font-weight: bold">ENOVO, Web Design, E-commerce e Aplicações Online - www.enovo.pt</b></div>--}}
        <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ app_brand('docsignature') }}/b></div>
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
                    {{ transLocale('admin/global.word.general_conditions', $locale) }}
                </h1>
                {!! $conditions !!}
            </div>
        </div>
    </div>
@endif