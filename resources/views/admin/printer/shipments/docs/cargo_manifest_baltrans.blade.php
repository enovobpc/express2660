<?php
$docCountTotal   = 0;
$docTotalWeight  = 0;
$docTotalVolumes = 0;
$docTotalCharges = 0;
$countPaymentRecipient = 0;
$totalPaymentRecipient = 0;

$first = true;
?>
<div>
    @if($customers)
        @php
            if (is_array($customers)) {
                $firstShipment = @$customers[1]->first();
            } else {
                $firstShipment = @$customers->first()->first();
            }
        @endphp

        <div style="margin-bottom: 10px; font-size: 10pt">
            {{ @$firstShipment->agency->address }}
            <br>
            {{ @$firstShipment->agency->zip_code }}, {{ @$firstShipment->agency->city }}
            <br>
            Telef: {{ @$firstShipment->agency->phone }}
        </div>

        @foreach($customers as $customeId => $shipments)
        @if($groupBy == 'customers')
            <h4>{{ @$shipments->first()->customer->code }} - {{ @$shipments->first()->customer->name }}</h4>
        @elseif($groupBy == 'providers')
            <h4>
                {{ @$shipments->first()->provider->name }}
                @if (@$shipments->first()->provider->webservice_config->user)
                    <span class="text-muted">({{ @$shipments->first()->provider->webservice_config->user }})</span>
                @endif
            </h4>
        @endif
        <table class="table table-bordered table-pdf m-b-3" style="font-size: 6.3pt;">
            <tr>
                <th>{{ trans('account/global.word.shipments') }}</th>
                <th style="width: 80px">{{ trans('account/global.word.service') }}</th>
                {{--<th>Carga</th>--}}
                <th>Destinatario</th>
                <th class="w-50px">Bultos</th>
                <th class="w-50px">{{ trans('account/global.word.charge') }}</th>
                <th style="width: 100px">{{ trans('account/global.word.reference') }}</th>
                {{--<th class="w-50px">Portes</th>--}}
                {{--<th class="w-100px">Obs.</th>--}}
            </tr>
            <?php
            $countTotal  = 0;
            $totalWeight = 0;
            $totalVolumes = 0;
            $totalCharges = 0;
            $countCharges = 0;
            ?>
            @foreach($shipments as $shipment)
                <?php
                $countTotal++;
                $totalWeight+= $shipment->weight;
                $totalVolumes+= $shipment->volumes;
                $countCharges+= $shipment->charge_price > 0.00 ? 1 : 0;
                $totalCharges+= $shipment->charge_price > 0.00 ? $shipment->charge_price : 0;
                $countPaymentRecipient+= $shipment->total_price_for_recipient > 0.00 ? 1 : 0;
                $totalPaymentRecipient+= $shipment->total_price_for_recipient > 0.00 ? $shipment->total_price_for_recipient : 0;
                ?>
                <tr>
                    <td style="width: 120px; font-size: 8pt" class="text-center">
                        {{-- <div style="display: inline-block">
                            <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.8" height="0.5"/>
                        </div> --}}
                        <span class="bold">{{ $shipment->provider_tracking_code }}</span>
                        {{ $shipment->date }} {{ $shipment->start_hour ? $shipment->start_hour : @$shipment->created_at->format('H:i') }}
                    </td>
                    <td class="text-center">
                        @if (@$shipments->first()->provider->webservice_config)
                            @php
                                $country = $shipment->recipient_country;
                                $country = in_array($country, ['pt', 'es']) ? $country : 'int';

                                $value = @$shipments->first()->provider->webservice_config->mapping_services[$shipment->service_id][$country]
                            @endphp

                            {{ trans('admin/webservices.services.' . $shipment->webservice_method. '.' . $value) }}
                            <br>
                        @endif

                        {{-- {{ @$shipment->service->display_code }} {{ strtoupper($shipment->recipient_country) }} --}}
                    </td>
                    {{--<td>{{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>--}}
                    <td>{{ $shipment->recipient_name }}<br/>
                        {{ $shipment->recipient_address }}<br/>
                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}, {{ trans('country.' . $shipment->recipient_country) }}
                    </td>
                    <td class="text-center">
                        {{ $shipment->volumes }} {{ $shipment->volumes == 1 ? 'Bulto' : 'Bultos' }}<br/>
                        {{ $shipment->weight }}kg
                    </td>
                    <td>{{ $shipment->charge_price > 0.00 ? money($shipment->charge_price, Setting::get('app_currency')) : '' }}</td>
                    <td>
                        <b style="font-weight: bold">{{ $shipment->tracking_code }}</b><br>
                        {{ $shipment->reference }}<br/>
                        @if(Setting::get('shipments_reference2_visible') && $shipment->reference2 && config('app.source') != 'intercourier')
                            <br/><b class="bold">{{ Setting::get('shipments_reference2_name') }}</b> {{ $shipment->reference2 }}
                        @endif
                    </td>
                    {{--<td>{{ $shipment->total_price_for_recipient > 0.00 ? money($shipment->total_price_for_recipient, Setting::get('app_currency')) : '' }}</td>--}}
                </tr>
            @endforeach
        </table>
        <div style="width: 100%">
            <h4 class="pull-right text-right m-t-0" style="width: 85%">
                <small>{{ trans('account/global.word.expeditions') }}: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <small>Bultos: <b class="bold" style="color: #000;">{{ $totalVolumes }}</b></small>
            </h4>
        </div>
        <div class="clearfix"></div>
        <hr class="m-b-10 m-t-10"/>
        <?php
        $docCountTotal+=$countTotal;
        $docTotalWeight+=$totalWeight;
        $docTotalVolumes+=$totalVolumes;
        $docTotalCharges+=$totalCharges + $totalPaymentRecipient;
        ?>
        @endforeach
        <div style="width: 33.5%; border: 1px solid #ddd; float: left; padding: 3px; text-align: center">
            <div style="float: left; width: 60%">
                {{ trans('account/shipments.cargo_manifest.date-time-cargo') }}<br/><br/>
                ________________________
            </div>
            <div style="float: left; width: 38%">
                &nbsp;{{ trans('admin/global.word.vehicle') }}<br/><br/>
                 &nbsp;&nbsp;_______________
            </div>
        </div>
        <div style="width: 20%; border: 1px solid #ddd; margin-left: 5px; float: left; padding: 3px; text-align: center">
            <div style="">
                {{ trans('account/shipments.cargo_manifest.signature') }}<br/><br/>
                _______________________
            </div>
        </div>
        <div style="width: 43%; float: left; margin-top:5px">
            <h4 class="pull-right text-right m-t-0">
                <small>{{ trans('account/global.word.total') }} {{ trans('account/global.word.expeditions') }}: <b class="bold" style="color: #000;">{{ $docCountTotal }}</b></small>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <small>Total Bultos: <b class="bold" style="color: #000;">{{ $docTotalVolumes }}</b></small>
                <br/>
                <small>{{ trans('account/global.word.value') }} {{ trans('account/global.word.total') }}: <b class="bold" style="color: #000;">{{ money($docTotalCharges, Setting::get('app_currency')) }}</b></small>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <small>{{ trans('account/global.word.weight') }} {{ trans('account/global.word.total') }}: <b class="bold" style="color: #000;">{{ money($docTotalWeight, 'kg') }}</b></small>
            </h4>
        </div>
    @endif
</div>
