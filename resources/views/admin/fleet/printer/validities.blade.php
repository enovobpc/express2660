<div>
    <?php $today = Date::today(); ?>
    @if($vehicles)
        @foreach($vehicles as $vehicle)
        <h4 style="margin-top: 25px; margin-bottom: 5px">
            <div style="float: right; width: 300px; font-size: 14px; text-align: right">
                {{ trans('admin/fleet.vehicles.types.'. $vehicle->type) }}
                @if($vehicle->km)
                &bull; {{ money($vehicle->km, '', 0) }}Km
                @endif
            </div>
            <div style="font-weight: bold; float: left; width: 300px">{{ $vehicle->license_plate }} - {{ @$vehicle->brand->name }} {{ @$vehicle->model->name }}</div>
        </h4>

        <table class="table table-bordered table-pdf m-b-5">
            <tr>
                <th class="w-250px">@trans('Encargo')</th>
                <th class="w-100px">@trans('Data Limite')</th>
                <th class="w-50px">@trans('Restante')</th>
                <th class="w-65px">@trans('Km Limite')</th>
                <th class="w-50px">@trans('Restante')</th>
                <th>Observações</th>
            </tr>
            @if($vehicle->notifications)
                @foreach($vehicle->notifications as $reminder)
                    <?php
                        $km       = $reminder['km'] ? money($reminder['km'], '', 0) : '';
                        $timeLeft = @$reminder['days_left'] . ' dias';
                        $kmLeft   = @$reminder['km_left'] > 0 ? money(@$reminder['km_left'], '', 0) : '';
                        $dateExtense = $reminder['date']->day . ' ' . trans('datetime.month.'.$reminder['date']->month) . ' ' . $reminder['date']->year;
                    ?>
                    <tr>
                        <td>{{ @$reminder['title'] }}</td>
                        @if($reminder['days_left'] >= 0)
                            <td class="text-center">{{ $dateExtense }}</td>
                            <td class="text-center">{{ $timeLeft }}</td>
                        @else
                            <td class="text-center" style="color: red">{{ $dateExtense }}</td>
                            <td class="text-center" style="color: red">{{ $timeLeft }}</td>
                        @endif


                        @if($reminder['km_left'] >= 0)
                            <td class="text-center">{{ $km }}</td>
                            <td class="text-center">{{ $kmLeft }}</td>
                        @else
                            <td class="text-center" style="color: red">{{ $km }}</td>
                            <td class="text-center" style="color: red">{{ $kmLeft }}</td>
                        @endif
                        <td></td>
                    </tr>
                @endforeach
            @endif

        </table>
        @endforeach
        <div class="clearfix"></div>
    @endif
</div>