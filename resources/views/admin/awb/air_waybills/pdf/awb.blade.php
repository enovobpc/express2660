<div class="awb-label" style="font-weight: bold">
    <div class="awb-row">
        <div class="awb-no" style="width: 90mm; padding-top: 1mm;">
            <div style="float: left; width: 8.5mm; margin-left: -3mm">{{ $waybillParts[1] }}</div>
            <div style="float: left; width: 15mm; text-align: center; margin-left:-1.5mm">{{ $waybill->sourceAirport->code }}&nbsp;</div>
            <div style="float: left; width: 30mm; margin-left:-1.5mm">{{ $waybillParts[2] }} {{ $waybillParts[3] }}</div>
        </div>
        <div class="awb-no" style="float: right; width: 50mm; text-align: right; font-size: 12pt;">
            {{ $is_hawb ? 'Hawb:' : '' }} {{ str_replace('-', ' - ', $waybill->awb_no) }}
        </div>
    </div>
    <div style="height: 1.4mm;"></div>
    <div class="awb-row" style="height: 25mm;">
        <div class="awb-block" style="width: 48%; padding-top: 5mm; height: 18mm;">
            <p>
                {{ $waybill->sender_vat }}&nbsp;
                <br/>
                {{ $waybill->sender_name }}&nbsp;
                <br/>
                {!! nl2br($waybill->sender_address) !!}
            </p>
        </div>
        <div class="awb-block" style="width: 34%; padding-left: 30mm; padding-top: 1mm; height: 20mm;">
            <p>
                {{ $waybill->issuer_name }}<br/>
                {!! nl2br($waybill->issuer_address) !!}
            </p>
        </div>
    </div>
    <div class="awb-row" style="height: 26mm;">
        <div class="awb-block" style="width: 48%; padding-top: 5mm; height: 20mm;">
            <p>
                {{ $waybill->consignee_vat }}&nbsp;
                <br/>
                {{ $waybill->consignee_name }}&nbsp;
                <br/>
                {!! nl2br(trim($waybill->consignee_address)) !!}&nbsp;
            </p>
        </div>
        <div class="awb-block" style="width: 48%;"></div>
    </div>
    <div class="awb-row" style="height: 14mm;">
        <div class="awb-block" style="width: 48%; padding-top: 5mm; height: 8mm;">
            <p>
                {{ $waybill->agent->name }}&nbsp;
                <br/>
                {{ $waybill->agent->address }} - {{ $waybill->agent->country }}&nbsp;
                <br/>
                @if($waybill->agent->phone)
                    {{ $waybill->agent->phone }}
                @endif
                &nbsp;&nbsp;
            </p>
        </div>
        <div class="awb-block" style="width: 48%; height: 20px; padding-top: 3mm; margin-bottom: -29px; height: 21mm; overflow: hidden; display: block; position: fixed;">
            <p>{{ str_limit($waybill->accounting_info, 300) }}&nbsp;</p>
        </div>
    </div>
    <div>
        <div class="awb-row">
            <div class="awb-block" style="width: 22%; padding-top: 2mm; height: 2mm;">
                <p>{{ $waybill->agent->iata_code }}&nbsp;</p>
            </div>
            <div class="awb-block" style="width: 22%; padding-top: 2mm">
            </div>
        </div>
    </div>
    <div class="awb-row">
        <div class="awb-block" style="width: 48%; padding-top: 4mm; height: 2mm;">
            <p style="margin: 0">{{ strtoupper($waybill->sourceAirport->code) }} - {{ strtoupper($waybill->sourceAirport->airport) }}</p>
        </div>
        <div class="awb-block" style="width: 17%; padding-top: 3mm; height: 4mm;">
            <p style="margin: 0">{{ $waybill->reference }}&nbsp;</p>
        </div>
        <div class="awb-block" style="width: 31%; padding-top: 3mm; height: 4mm;">
            <p style="margin: 0">&nbsp;</p>
        </div>
    </div>
    <div class="awb-row">
        <div class="awb-block" style="width: 7.5mm; padding-top: 4mm; height: 2mm;">
            <?php
            if($is_hawb) {
                $scales = $waybill->scales;
            }
            ?>
            <p style="margin: 0">{{ @$scales[0]['airport'] }}&nbsp;</p>
        </div>
        <div class="awb-block" style="width: 45mm; padding-top: 4mm;">
            <p style="margin: 0">{{ @$scales[0]['provider'] }}&nbsp;</p>
        </div>
        {{--<div class="awb-block" style="width: 33mm; padding-top: 4mm;">

        </div>--}}
        <div class="awb-block" style="width: 7.5mm; padding-top: 4mm; height: 2mm;">
            <p style="margin: 0">{{ @$scales[1]['airport'] }}&nbsp;</p>
        </div>
        <div class="awb-block" style="width: 22.5mm; padding-top: 4mm;">
            <p style="margin: 0">{{ @$scales[1]['provider_code'] }}&nbsp;</p>
        </div>
        <div class="awb-block" style="width: 12mm; padding-top: 4mm; height: 2mm;">
            <p style="margin: 0">{{ $waybill->currency }}</p>
        </div>
        <div class="awb-block" style="width: 7mm; padding-top: 4.5mm;">
            <p style="margin: 0">{{ $waybill->charge_code == 'PP' ? 'PP' : '' }}</p>
        </div>
        <div class="awb-block" style="width: 8mm; padding-top: 4.5mm; margin-left: -4.5mm;">
            <p style="margin: 0">{{ $waybill->charge_code == 'CC' ? 'CC' : '' }}</p>
        </div>
        <div class="awb-block" style="width: 7mm; padding-top: 4.5mm; margin-left: -4.8mm;">
            <p style="margin: 0">{{ $waybill->charge_code == 'PP' ? 'PP' : '' }}</p>
        </div>
        <div class="awb-block" style="width: 7mm; padding-top: 4.5mm; margin-left: -4.5mm;">
            <p style="margin: 0">{{ $waybill->charge_code == 'CC' ? 'CC' : '' }}</p>
        </div>
        <div class="awb-block" style="width: 30mm; padding-top: 4mm;  margin-left: -4mm; height: 4mm; text-align: center">
            <p style="margin: 0">{{ empty($waybill->value_for_carriage) ? 'NVD' : money($waybill->value_for_carriage) }}&nbsp;</p>
        </div>
        <div class="awb-block" style="width: 29mm; padding-top: 4mm;  height: 4mm; text-align: center">
            <p style="margin: 0">{{ empty($waybill->value_for_customs) ? '' : money($waybill->value_for_customs) }}&nbsp;</p>
        </div>
    </div>
    <div class="awb-row">
        <div class="awb-block" style="width: 23%; padding-top: 4mm; height: 2mm;">
            <p style="margin: 0">{{ $waybill->recipientAirport->code }} - {{ strtoupper($waybill->recipientAirport->airport) }}</p>
        </div>
        <div class="awb-block" style="width: 23.5%; padding-top: 4mm; height: 2mm;">
            <p style="margin: 0">{{ $waybill->flight_no_1 }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $waybill->flight_no_2 }}</p>
        </div>
        <div class="awb-block" style="width: 32%; padding-top: 4mm; height: 2mm;">
            <p style="margin: 0">{{ $waybill->flight_no_3 }}{{ empty($waybill->value_insurance) ? '' : money($waybill->value_insurance) }}&nbsp;</p>
        </div>
    </div>
    <div class="awb-row">
        <div class="awb-block" style="width: 83%; padding-top: 4mm; height: 15mm; font-size: 7pt">
            {{ $waybill->handling_info }}&nbsp;
        </div>
        <div class="awb-block" style="width: 13%; padding-top: 13mm; height: 3mm; text-align: center;">
            &nbsp;STATUS {{ $waybill->customs_status }}
        </div>
    </div>
    <div style="height: 5mm"></div>
    <div class="awb-row">
        <div class="awb-block" style="width: 130mm; height: 52mm; overflow: hidden; padding-top: 1mm;">
            @if(@$waybill->goods)
                @foreach($waybill->goods as $good)
                    <div class="awb-row">
                        <div class="awb-block" style="width: 7mm; margin-left: -1mm; text-align: right;">
                            <p style="margin: 0">{{ $good->volumes }}</p>
                        </div>
                        <div class="awb-block" style="width: 16mm; text-align: right;">
                            <p style="margin: 0">{{ money($good->weight) }}</p>
                        </div>
                        <div class="awb-block" style="width: 6mm; margin-left: -0.8mm;">
                            <p style="margin: 0">{{ $good->unity }}</p>
                        </div>
                        <div class="awb-block" style="width: 6mm; margin-left: -3.5mm;">
                            <p style="margin: 0">{{ $good->rate_class }}</p>
                        </div>
                        <div class="awb-block" style="width: 15mm; margin-left: -3.4mm; text-align: right;">
                            <p style="margin: 0">{{ $is_hawb && $good->rate_class == 'A' ? 'as agreed' : $good->rate_no }}</p>
                        </div>
                        <div class="awb-block" style="width: 18mm; text-align: right;">
                            <p style="margin: 0">{{ money($good->chargeable_weight) }}</p>
                        </div>
                        <div class="awb-block" style="width: 20mm; text-align: right;">
                            <p style="margin: 0">{{ $is_hawb && $good->rate_class == 'A' ? 'as agreed' : money($good->rate_charge) }}</p>
                        </div>
                        <div class="awb-block" style="width: 31mm; text-align: right;">
                            <p style="margin: 0">{{ $is_hawb && $good->rate_class == 'A' ? 'as agreed' : money($good->total) }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="awb-block" style="width: 54mm; padding-top: 2mm; font-size: 7pt">
            <p style="margin: 0; line-height: 10px">
                {!! nl2br($waybill->nature_quantity_info) !!}
            </p>
        </div>
    </div>
    <div class="awb-row">
        <div class="awb-block" style="width: 130mm; padding-top: 1mm;">
            <div class="awb-row">
                <div class="awb-block" style="width: 7mm; margin-left: -1mm; text-align: right;">
                    <p style="margin: 0">{{ $waybill->volumes }}</p>
                </div>
                <div class="awb-block" style="width: 16mm; text-align: right;">
                    <p style="margin: 0">{{ money($waybill->weight) }}</p>
                </div>
                <div class="awb-block" style="width: 6mm; margin-left: -0.5mm;"></div>
                <div class="awb-block" style="width: 6mm; margin-left: -3.4mm;"></div>
                <div class="awb-block" style="width: 15mm; margin-left: -3.4mm;"></div>
                <div class="awb-block" style="width: 17mm;"></div>
                <div class="awb-block" style="width: 20mm;"></div>
                <div class="awb-block" style="width: 32mm; text-align: right;">
                    <p style="margin: 0">{{ $is_hawb && @$good->rate_class == 'A' ? 'as agreed' : money($waybill->total_goods_price) }}</p>
                </div>
            </div>
        </div>
        <div style="position: absolute; width: 130mm; margin-top: -25mm; text-align: center; font-weight: normal;">
            {{ $waybill->adicional_info }}
        </div>
    </div>
    <div class="awb-row">
        <div class="awb-block" style="width: 255px; padding-top: 0mm; height: 2mm;">
            <div class="awb-row">
                <div class="awb-block" style="width: 46%; padding-top: 5.5mm; text-align: right">
                    <p style="margin: 0">{{ $is_hawb && @$good->rate_class == 'A' ? 'as agreed' : money($waybill->total_goods_price) }}</p>
                </div>
                <div class="awb-block" style="width: 46%; padding-top: 5.5mm; height: 2mm; text-align: center">
                    <p style="margin: 0">&nbsp;</p>
                </div>
            </div>
            <div class="awb-row">
                <div class="awb-block" style="width: 46%; padding-top: 5mm; text-align: right">
                    <p style="margin: 0">&nbsp;</p>
                </div>
                <div class="awb-block" style="width: 46%; padding-top: 5mm; height: 2mm; text-align: center">
                    <p style="margin: 0">&nbsp;</p>
                </div>
            </div>
            <div class="awb-row">
                <div class="awb-block" style="width: 46%; padding-top: 5mm; text-align: right">
                    <p style="margin: 0">&nbsp;</p>
                </div>
                <div class="awb-block" style="width: 46%; padding-top: 5mm; height: 2mm; text-align: center">
                    <p style="margin: 0">&nbsp;</p>
                </div>
            </div>
            <div class="awb-row">
                <div class="awb-block" style="width: 46%; padding-top: 4mm; text-align: right">
                    <p style="margin: 0">{{ $is_hawb && @$good->rate_class == 'A' ? 'as agreed' : money($totalAgentPrice) }}&nbsp;</p>
                </div>
                <div class="awb-block" style="width: 46%; padding-top: 4mm; height: 2mm; text-align: center">
                    <p style="margin: 0">&nbsp;</p>
                </div>
            </div>
            <div class="awb-row">
                <div class="awb-block" style="width: 46%; padding-top: 5mm; text-align: right">
                    <p style="margin: 0">{{ $is_hawb && @$good->rate_class == 'A'  ? 'as agreed' : money($totalCarrierPrice) }}&nbsp;</p>
                </div>
                <div class="awb-block" style="width: 46%; padding-top: 5mm; height: 2mm; text-align: center">
                    <p style="margin: 0">&nbsp;</p>
                </div>
            </div>
            <div class="awb-row">
                <div class="awb-block" style="width: 46%; padding-top: 5mm; text-align: right">
                    <p style="margin: 0">&nbsp;</p>
                </div>
                <div class="awb-block" style="width: 46%; padding-top: 5mm; height: 2mm; text-align: center">
                    <p style="margin: 0">&nbsp;</p>
                </div>
            </div>
            <div class="awb-row">
                <div class="awb-block" style="width: 46%; padding-top: 4mm; text-align: right">
                    <p style="margin: 0">{{ $is_hawb && @$good->rate_class == 'A'  ? 'as agreed' : money($waybill->total_goods_price + $waybill->total_price) }}&nbsp;</p>
                </div>
                <div class="awb-block" style="width: 46%; padding-top: 4mm; height: 2mm; text-align: center">
                    <p style="margin: 0">&nbsp;</p>
                </div>
            </div>
        </div>

        <div class="awb-block" style="width: 61%; height: 24mm; padding-top: 4mm; padding-left: 2mm;">
            <div class="awb-row" style="height: 24mm;">
                <div class="awb-block" style="width: 40%;text-align: left;">
                    @if(!$waybill->expenses->isEmpty())
                        @foreach($waybill->expenses as $key => $expense)
                            <div class="awb-row">
                                <div class="awb-block" style="width: 40%;text-align: left">
                                    {{ $expense->code }}
                                </div>
                                <div class="awb-block" style="width: 40%; text-align: right">
                                    {{ money($expense->pivot->price) }}
                                </div>
                            </div>
                            @if(($key + 1) == 5)
                </div>
                <div class="awb-block" style="width: 40%; text-align: right;">
                    @endif
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="awb-block" style="width: 100%; height: 10mm; padding-top: 16mm; padding-left: 2mm;">
                <p class="text-center">
                    {{ $waybill->agent->name }} on shipper's behalf
                </p>
            </div>
            <div class="awb-block" style="width: 100%; height: 2mm; padding-top: 4mm;">
                <div class="awb-row">
                    <div class="awb-block" style="width: 30%;text-align: left">
                        {{ $waybill->date->format('d/m/Y') }}
                    </div>
                    <div class="awb-block" style="width: 34%; text-align: center">
                        {{ $waybill->agent->city }}, {{ strtoupper($waybill->agent->country) }}
                    </div>
                    <div class="awb-block" style="width:30%; text-align: right">
                        {{ $waybill->user->name }}
                    </div>
                </div>
            </div>
            <div class="awb-block" style="width: 100%; margin-top: 8mm; height: 5mm; text-align: right; font-size: 12pt;">
                {{ $is_hawb ? 'Hawb:' : '' }} {{  str_replace('-', ' - ', $waybill->awb_no) }}
            </div>
        </div>
    </div>
</div>
<p class="text-center" style="font-size: 8.6pt">{{ $copyNumber }}</p>