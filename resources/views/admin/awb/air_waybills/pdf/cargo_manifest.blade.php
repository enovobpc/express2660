
<br/>
<br/>
<div style="">
    <div style="float: left; width: 30%; font-size: 8pt">
        <span style="font-weight: bold">AGENT</span><br/>
        {{ $waybill->agent->name }}<br/>
        {{ $waybill->agent->address }}<br/>
        {{ $waybill->agent->zip_code }} {{ $waybill->agent->city }}<br/>
        {{ $waybill->agent->country }}
    </div>
    <div style="float: left; width: 30%">
        <table class="table table-condensed" style="font-size: 8pt">
            <tr>
                <td class="w-70px"><span style="font-weight: bold">CARRIER</span></td>
                <td>{{ @$waybill->provider->name }}</td>
            </tr>
            <tr>
                <td><span style="font-weight: bold">ORIGIN</span></td>
                <td>{{ @$waybill->source_airport }}</td>
            </tr>
            <tr>
                <td><span style="font-weight: bold">FLIGHT NO</span></td>
                <td>{{ @$waybill->flight_no_1 }}</td>
            </tr>
            <tr>
                <td><span style="font-weight: bold">ROUTING</span></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div style="float: left; width: 30%">
        <table class="table table-condensed" style="font-size: 8pt">
            <tr>
                <td class="w-60px"><span style="font-weight: bold">MAWB</span></td>
                <td>{{ @$waybill->awb_no }}</td>
            </tr>
            <tr>
                <td><span style="font-weight: bold">DESTINATION</span></td>
                <td>{{ @$waybill->recipient_airport }}</td>
            </tr>
        </table>
    </div>
</div>
<div>
    @foreach($houseWaybills as $waybill)
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 7pt;">
        <tr>
            <th class="w-100px">HAWB</th>
            <th>Shipper</th>
            <th>Consignee</th>
            <th class="w-40px">Destination</th>
            <th class="w-70px">N. Pieces</th>
            <th class="w-80px">Gross Weight</th>
            <th class="w-40px">Commodity</th>
            <th class="w-70px">Goods Value</th>
            <th class="w-50px">Status</th>
        </tr>
        <tr>
            <td>{{ $waybill->awb_no }}</td>
            <td>{{ $waybill->sender_name }}<br/>{!! nl2br($waybill->sender_address) !!}</td>
            <td>{{ $waybill->consignee_name }}<br/>{!! nl2br($waybill->consignee_address) !!}</td>
            <td class="text-center">{{ $waybill->recipient_airport }}</td>
            <td class="text-center">{{ $waybill->volumes }}</td>
            <td class="text-center">{{ $waybill->weight }}</td>
            <td class="text-center"></td>
            <td class="text-center">{{ money($waybill->total_goods_price, Setting::get('app_currency')) }}</td>
            <td class="text-center">{{ $waybill->customs_status }}</td>
        </tr>
    </table>
    @endforeach
</div>