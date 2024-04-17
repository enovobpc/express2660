<h5 class="bold text-uppercase m-t-0 m-b-3">
    {{ $groupName }}
</h5>
<table class="table table-condensed">
    <thead>
    <tr>
        <th class="w-70px text-center" style="padding: 3px 0; border: 1px solid {{ env('APP_MAIL_COLOR_PRIMARY') }}; background: {{ env('APP_MAIL_COLOR_PRIMARY') }};"></th>
        @foreach($pricesTableData[$unity] as $service)
            <?php $serviceUnity = $service->unity; ?>
            <th class="text-center bg-gray" style="padding: 3px 0; white-space: nowrap; background: {{ env('APP_MAIL_COLOR_PRIMARY') }}; color: #fff; border-right: 1px solid #999; border-bottom: 1px solid {{ env('APP_MAIL_COLOR_PRIMARY') }}; border-top: 1px solid {{ env('APP_MAIL_COLOR_PRIMARY') }};" colspan="{{ count($service->zones) }}">
                <span data-toggle="tooltip" title="{{ $service->name }}">{{ $fullServiceName ? $service->name : $service->display_code }}</span>
            </th>
        @endforeach
    </tr>
    <tr style="background: #999;">
        <td class="w-0px text-center bold" style="padding: 3px 0; background: #333; color: #fff; border-bottom: 1px solid #999; border-top: 1px solid #999; border-right: 1px solid #999; border-left: 1px solid #999;">
            @if($serviceUnity == 'volume')
                VOLUMES
            @else
                PESO
            @endif
        </td>
        @foreach($pricesTableData[$unity] as $service)
            @if($service->zones)
                @foreach($service->zones as $key => $zone)
                    <td class="text-center text-uppercase bold" style="padding: 3px 0; border-bottom: 1px solid #999; border-right: 1px solid #999; background: #333; color: #fff">
                        <span data-toggle="tooltip" title="{{ @$billingZones[$zone] }}">{{ $zone }}</span>
                    </td>
                @endforeach
            @else
                <td class="text-center text-red bold bg-gray-light">PT</td>
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($rowsWeight[$unity] as $weightValue => $tableServices)
        <tr>
            <td class="column-weight text-center bold" style="background: #eee; border-bottom: 1px solid #999; color: #333; border-right: 1px solid #999; border-left: 1px solid #999; ">
                @if($serviceUnity == 'volume')
                    {{ $weightValue > 99999 ? 'Adicional' : money($weightValue, ' Vol.', 0)}}
                @elseif($serviceUnity == 'km')
                    {{ $weightValue > 99999 ? 'KM Adic.' : $weightValue . ' KM' }}
                @else
                    {{ $weightValue > 99999 ? 'KG Adic.' : $weightValue . ' KG' }}
                @endif
            </td>
            @foreach($pricesTableData[$unity] as $service)
                @if(empty($service->zones))
                    <?php $service->zones = ['pt'] ?>
                @endif

                @foreach($service->zones as $key => $zone)
                    <?php $data = @$tableServices[@$service->id][$zone][0]; ?>
                    <td class="text-center" style="border-bottom: 1px solid #999; border-right: 1px solid #999;">
                        {{ money($data['price'], Setting::get('app_currency')) }}
                    </td>
                @endforeach
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>