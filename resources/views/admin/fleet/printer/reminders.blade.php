<div>
    <?php $today = Date::today(); ?>
    @if($reminders)
        <table class="table table-bordered table-pdf m-b-5">
            <tr>
                <th class="w-250px">@trans('Viatura')</th>
                <th class="w-100px">@trans('Nome')</th>
                <th class="w-50px">@trans('Dia Limite')</th>
                <th class="w-50px">@trans('Restante')</th>
                <th class="w-65px">@trans('Km Limite')</th>
                <th class="w-50px">@trans('Restante')</th>
            </tr>
            @foreach($reminders as $reminder)
                <?php 
                    $date = new \Jenssegers\Date\Date($reminder->date);
                    $countDays = $date->diffInDays();
                    $kmLeft   = $reminder['km'] - $reminder->vehicle->counter_km;
                    $dateExtense = $reminder['date']->day . ' ' . trans('datetime.month.'.$reminder['date']->month) . ' ' . $reminder['date']->year;
                ?>
                <tr>
                    <td>{{ @$reminder->vehicle->license_plate }}</td>  
                    <td>{{ @$reminder['title'] }}</td>
                    @if($reminder['countDays'] >= 0)
                        <td class="text-center">{{ $dateExtense }}</td>
                        <td class="text-center">{{ $countDays }} @trans('dias')</td>
                    @else
                        <td class="text-center" style="color: red">{{ $dateExtense }}</td>
                        <td class="text-center" style="color: red">{{ $countDays }} @trans('dias')</td>
                    @endif


                    @if( $kmLeft >= 0)
                        <td class="text-center">{{ @$reminder['km'] }}</td>
                        <td class="text-center">{{ @$kmLeft }}</td>
                    @else
                        <td class="text-center" style="color: red">{{ @$reminder['km'] }}</td>
                        <td class="text-center" style="color: red">{{ @$kmLeft }}</td>
                    @endif
                </tr>
                @endforeach
        </table>
        <div class="clearfix"></div>
    @endif
</div>