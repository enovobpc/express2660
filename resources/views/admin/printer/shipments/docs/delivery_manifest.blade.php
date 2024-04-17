<?php
$totalCharges = 0;
$totalAtRecipient = 0;
$totalWeight = 0;
$totalVolumes = 0;
?>
@foreach($shipments as $shipment)

    <?php
    $totalWeight+= $shipment->weight;
    $totalVolumes+= $shipment->volumes;
    $totalCharges+= $shipment->charge_price;
    $totalAtRecipient+= $shipment->total_price_for_recipient;
    ?>
    @if(Setting::get('trip_summary_mode') == 'xlarge')
        <div class="fs-10" style="height: 47mm; margin-top: 14px">
            <div class="pull-left lh-1-3" style="width: 121mm;">
                <div class="text-left" style="margin-bottom: 5px">
                    <div style="display: inline-block; margin-left: -15px; float: left; width: 72mm;">
                        <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="1" height="0.8"/>
                    </div>
                    <div style="float: left; width: 15mm;">
                        <div style="border: 1px solid #000; font-size: 15px; padding: 4px 0; text-align: center;">
                            {{ @$shipment->service->display_code }}
                        </div>
                    </div>

                    <div style="float: left; width: 27mm; margin-left: 10px">
                        <span style="font-size: 14px;">{{ $shipment->tracking_code }}</span><br/>
                        <span>{{ $shipment->date }}</span>
                    </div>
                </div>
                <div style="float: right; width: 35%; font-size: 11px;">
                    <b class="bold">Obs.:</b> {{ $shipment->obs_internal }}<br/>
                </div>
                <div style="float: left; width: 55%; margin-bottom: 5px">
                    De: {{ $shipment->sender_name }}<br/>
                    {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                </div>
                @if(!empty($shipment->recipient_attn))
                    <div style="float: left; width: 100%; font-size: 11px;">
                        <b class="bold">A/C:<b> {{ $shipment->recipient_attn }}
                    </div>
                @endif
                <div style="float: left; width: 100%; font-size: 11px;">
                <span class="bold">
                    {{ $shipment->recipient_name }}<br/>
                    {{ $shipment->recipient_address }}<br/>
                    {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                </span>
                    <div style="font-size: 12px; margin-top: 4px">
                        <b class="bold">Tlf.:</b> {{ $shipment->recipient_phone }}
                    </div>
                </div>
                <div class="pull-left" style="width: 25mm; margin-top: 0">
                <span class="fs-12">
                    <b class="bold">Volumes:</b> {{ $shipment->volumes }} &nbsp;
                    <b class="bold">Peso:</b> {{ $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight }} kg<br/>
                </span>
                </div>
                <div class="pull-left" style="width: 92mm; margin-top: -10px">
                    @if($shipment->charge_price)
                        <div style="border: 1px solid #000; float: right; width: 15mm; margin-right: 5px; padding: 2px; text-align: center; background: #000; color: #fff; font-weight: bold">
                            Cobrança<br/>{{ money($shipment->charge_price, Setting::get('app_currency')) }}
                        </div>
                    @endif
                    @if($shipment->payment_at_recipient)
                        <div style="border: 1px solid #000; float: right; width: 10mm; margin-right: 5px; padding: 2px; text-align: center; background: #000; color: #fff; font-weight: bold">
                            Portes<br/>{{ money($shipment->total_price_for_recipient, Setting::get('app_currency')) }}
                        </div>
                    @endif
                    @if($shipment->has_return)
                        @foreach($shipment->has_return as $key => $item)
                            <?php $returnLabel = trans('admin/shipments.return_types.' .$item) ?>
                            <div style="border: 1px solid #000; float: right; width: 15mm; margin-right: 5px; padding: 2px; text-align: center; background: #000; color: #fff; font-weight: bold">
                                Ret. {{ $returnLabel }}
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="pull-left" style="width: 70mm">
                <div style="border: 1px solid #000; height: 18mm; border-radius: 3px">
                    <div style="height: 40mm;"></div>
                    &nbsp;Data: _____ / _____ / _________ &nbsp;&nbsp;&nbsp;Hora:______:_______
                </div>
            </div>
        </div>
    @else
        <div class="fs-9" style="height: 10mm">
            <div class="pull-left lh-1-3" style="width: 65mm;">
                De: {{ $shipment->sender_name }}<br/>
                <span class="bold">{{ $shipment->recipient_name }}</span><br/>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                <div class="text-left">
                    <div style="display: inline-block; margin-left: -12px">
                        <barcode code="{{ $shipment->tracking_code }}" type="C128C" size="1" height="0.5"/>
                    </div>
                </div>
                @if(config('app.source') == 'intercourier')
                    <div style="margin: 2px 0;">
                        <b style="font-weight: bold;">TRK:{{ $shipment->provider_tracking_code }}</b> {{ $shipment->reference2 ? 'AWB: '. $shipment->reference2 : '' }}
                    </div>
                @endif
            </div>
            <div class="pull-left lh-1-4 m-l-10" style="width: 50mm;">
                <div class="pull-left" style="width: 25mm;">
            <span class="fs-10">
                <b class="bold">Tlf.:</b> {{ $shipment->recipient_phone }}
            </span><br/>
                    <b class="bold">TRK:</b> {{ $shipment->tracking_code }}<br/>
                    <b class="bold">Ref:</b> {{ $shipment->reference }}<br/>
                    <b class="bold">Serviço: </b> {{ @$shipment->service->display_code }}<br/>
                    <b class="bold">Data:</b> {{ $shipment->date }}
                </div>
                <div class="pull-left" style="width: 25mm;">
                    <b class="bold">Volumes:</b> {{ $shipment->volumes }}<br/>
                    <b class="bold">Peso:</b> {{ $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight }} kg<br/>
                    @if($shipment->charge_price)
                        <b class="bold" style="background: #000; color: #fff">Cobrar:  {{ money($shipment->charge_price, Setting::get('app_currency')) }}</b><br/>
                    @endif
                    @if($shipment->payment_at_recipient)
                        <b class="bold" style="background: #000; color: #fff">Pg. Dest: {{ money($shipment->total_price_for_recipient, Setting::get('app_currency')) }}</b>
                    @endif
                    @if($shipment->has_return)
                        @foreach($shipment->has_return as $key => $item)
                            <?php $returnLabel = trans('admin/shipments.return_types.' .$item) ?>
                            <b class="bold text-uppercase" style="background: #000; color: #fff">RET. {{ $returnLabel }}</b>
                        @endforeach
                    @endif
                </div>
            </div>

            @if(empty($shipment->total_price_for_recipient) && empty($shipment->charge_price))
                <div class="pull-left" style="width: 70mm">
                    <div style="border: 1px solid #000; height: 18mm; margin-top: 2px">
                        <div style="height: 13mm"></div>
                        &nbsp;Data: ______ / ______ / __________ &nbsp;&nbsp;&nbsp;Hora:______:_______
                    </div>
                </div>
            @else
                <div class="pull-left" style="width: 52mm">
                    <div style="border: 1px solid #000; height: 18mm; margin-top: 2px">
                        <div style="height: 13mm"></div>
                        &nbsp;Data: ______ / ______ / ________ Hora:____:____
                    </div>
                </div>
                <div class="pull-left" style="width: 18mm">
                    <div style="height: 18mm; margin-top: 5px; margin-left: 3px; line-height: 15px">
                        <div style="height: 12px"></div>
                        <div style="font-size: 20px; float: left; width: 20px">&#9634;</div>
                        <div style="float: left; width: 45px; margin-top: 2px;">Numerário</div>
                        <div style="height: 6px"></div>
                        <div style="font-size: 20px; float: left; width: 20px">&#9634;</div>
                        <div style="float: left; width: 45px; margin-top: 2px;">Cheque</div>
                    </div>
                </div>
            @endif
        </div>
    @endif
    <hr style="margin-top: 2px; margin-bottom: 1px"/>
@endforeach
<div class="clearfix"></div>
<div style="float: right; text-align: right; width: 130px; height: 30px;">
    <h4 style="font-weight: bold">
        <small>Valor Total</small><br/>
        {{ money($totalCharges + $totalAtRecipient, Setting::get('app_currency')) }}
    </h4>
</div>
<div style="float: right; text-align: right; width: 130px; height: 30px;">
    <h4>
        <small>Cobranças</small><br/>
        {{ money($totalCharges, Setting::get('app_currency')) }}
    </h4>
</div>
<div style="float: right; text-align: right; width: 130px; height: 30px;">
    <h4>
        <small>Pag. Destino</small><br/>
        {{ money($totalAtRecipient, Setting::get('app_currency')) }}
    </h4>
</div>
<div style="float: right; text-align: right; width: 130px; height: 30px;">
    <h4>
        <small>Peso Total</small><br/>
        {{ money($totalWeight, 'kg') }}
    </h4>
</div>
<div style="float: right; text-align: right; width: 130px; height: 30px;">
    <h4>
        <small>Volumes</small><br/>
        {{ $totalVolumes}}
    </h4>
</div>