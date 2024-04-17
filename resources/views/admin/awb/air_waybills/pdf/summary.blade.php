<?php
    $documentTotal = 0;
?>
<div>
    @foreach($waybills as $waybill)
    <?php
        $documentTotal+= $waybill->total_price + $waybill->total_goods_price;

        $expensesTotal = $waybill->expenses->filter(function($item){
            return $item->type != 'other';
        })->sum('pivot.price');

        $otherExpenses = $waybill->expenses->filter(function($item){
            return $item->type == 'other';
        });

        $otherExpensesTotal = $otherExpenses->sum('pivot.price');


    ?>
    <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 3px">AWB {{ $waybill->awb_no }}</h4>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt;">
        <tr>
            <th>AWB nº</th>
            <th>Expedidor</th>
            <th>Consignatário</th>
            <th>Carga</th>
            <th>Aeroporto</th>
            <th>Vôo</th>
            <th class="w-40px">Preço</th>
        </tr>
        <tr>
            <td>
                <b class="bold">{{ $waybill->awb_no }}</b><br/>
                <i>{{ $waybill->date->format('Y-m-d') }}</i>
            </td>
            <td>{{ $waybill->sender_name }}<br/>{!! nl2br($waybill->sender_address) !!}</td>
            <td>{{ $waybill->consignee_name }}<br/>{!! nl2br($waybill->consignee_address) !!}  }}</td>
            <td>
                {{ $waybill->volumes }} volume<br/>
                {{ $waybill->weight }} kg Bruto<br/>
                {{ $waybill->chargable_weight }} kg Taxável
            </td>
            <td>
                    <span data-toggle="tooltip" title="{{ @$waybill->sourceAirport->airport }}">{{ @$waybill->sourceAirport->code }}</span>
                    >
                    <span data-toggle="tooltip" title="{{ @$waybill->recipientAirport->airport }}">{{ @$waybill->recipientAirport->code }}</span>

                @if($waybill->flight_scales)
                    <br/>
                    <span class="text-muted">{{ count($waybill->flight_scales) }} {{ count($waybill->flight_scales) == 1 ? 'Escala' : 'Escalas' }}</span>
                @endif
                <br/>
                <span class="text-muted">{{ @$waybill->provider->name }}</span>
            </td>
            <td>
                @if($waybill->flight_no_1)
                    {{ $waybill->flight_no_1 }}
                @endif
                @if($waybill->flight_no_2)
                    <br/>{{ $waybill->flight_no_2 }}
                @endif

                @if($waybill->flight_no_3)
                    <br/>{{ $waybill->flight_no_3 }}
                @endif
            </td>
            <td>{{ money($waybill->total_goods_price + $expensesTotal, Setting::get('app_currency')) }}</td>
        </tr>
    </table>
    @if($otherExpenses)
        <table class="table table-condensed table-bordered m-b-10" style="font-size: 6.3pt; border: none;">
            <tr>
                <th style="padding: 3px 0; border: none; font-size: 11px">Encargos e Serviços Adicionais</th>
                <th class="w-40px" style="padding: 3px 0; border: none; font-size: 11px">Valor</th>
            </tr>
            @foreach($otherExpenses as $expense)
                <tr>
                    <td style="padding: 3px">
                        {{ $expense->name }}
                    </td>
                    <td class="w-40px" style="padding: 3px">
                        {{ money($expense->pivot->price, Setting::get('app_currency')) }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
    <h4 class="pull-right text-right m-t-0" style="width: 100%">
        <div style="width: 140px; float: right">
            <small>Total:<br/>
                <b class="bold" style="color: #000;">{{ money($waybill->total_price + $waybill->total_goods_price, Setting::get('app_currency')) }}</b>
            </small>
        </div>
        <div style="width: 140px; float: right">
            <small>Total Encargos:<br/>
                <b class="bold" style="color: #000;">{{ money($otherExpensesTotal, Setting::get('app_currency')) }}</b>
            </small>
        </div>
        <div style="width: 100px; float: right">
            <small style="width: 100px; float: left">Total Aéreo: <br/>
                <b class="bold" style="color: #000;">{{ money($waybill->total_goods_price + $expensesTotal, Setting::get('app_currency')) }}</b></small>
        </div>
    </h4>
    <hr class="m-b-10 m-t-0"/>
    @endforeach
    <h3 class="text-right m-t-0" style="float: left">
        <small>Total a Pagar:</small>
        <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
    </h3>
</div>