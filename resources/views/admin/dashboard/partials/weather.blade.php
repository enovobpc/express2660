@if(@$weather)
<?php
    $hours = ['2','5','8','11','14','17','20','22'];
    $curHour = date('H');
    $hourIndex = 0;
    foreach ($hours as $key => $hour) {
        $myValue    = $curHour;
        $minValue   = $hour;
        $maxValue   = isset($hours[$key + 1]) ? $hours[$key + 1] : $hours[0];

        if ($myValue >= $minValue && $myValue < $maxValue) {
            $hourIndex = $key;
        }
    }
?>
<div class="weather-panel">
    <div class="weather-today weather-{{ @$weather->day->{1}->symbol_value2 }}" style="background-color: {{ trans('admin/weather.' . @$weather->day->{1}->symbol_value2) }}">
        <a href="#" class="weather-settings" data-toggle="modal" data-target="#modal-weather-settings">
            <i class="fas fa-cog"></i>
        </a>
        <div class="row row-0">
            <div class="col-sm-12">
                <h4 class="weather-city">{{ $weather->city }}</h4>
                <p class="weather-description" data-toggle="tooltip" title="{{ @$weather->day->{1}->symbol_description2 }}">{{ @$weather->day->{1}->symbol_description2 }}</p>
            </div>
            <div class="col-sm-6">
                <h1 class="weather-temperature">
                    {{ @$weather->day->{1}->hour[$hourIndex]->temp  }}°
                </h1>
                <p class="temp">
                    <span class="text-blue">{{ @$weather->day->{1}->tempmin }}º</span>/
                    <span class="text-red">{{ @$weather->day->{1}->tempmax }}º</span>
                </p>
                <div class="wind">
                    <i class="fas fa-wind"></i>
                    <span>{{ @$weather->day->{1}->wind->speed }} km/h</span>
                </div>
            </div>
            <div class="col-sm-6">
                <img src="{{ asset('assets/img/default/weather/color/' . @$weather->day->{1}->symbol_value2 . '.svg') }}" class="icon"/>
            </div>
        </div>
    </div>
    <div class="weather-hours">
        <div class="row row-5">
            @foreach(@$weather->day->{1}->hour as $key => $hour)
                @if(!in_array($key, ['0', '7']))
                    <?php
                    $minValue   = $hour->interval;
                    $maxValue   = isset($hours[$key + 1]) ? $hours[$key + 1] : $hours[0];
                    $curInterval = $minValue.'-'.$maxValue.':00';
                    ?>
                    <div class="col-xs-2 {{ $hourIndex == $key ? 'bold' : '' }}">
                        <div data-toggle="tooltip" title="{{ $curInterval }} {{ $hour->symbol_description }}">
                            <img src="{{ asset('assets/img/default/weather/color/' . $hour->symbol_value2 . '.svg') }}" class="w-100"/>
                            {{ $hour->temp }}º
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <div class="weather-next-days">
        <div class="row row-0">
            @for($day = 2 ; $day <= 5 ; $day++)
            <div class="col-md-3">
                <div class="day">
                    <h1>{{ substr(removeAccents(@$weather->day->{$day}->name), 0, 3) }}</h1>
                    <div class="icon" data-toggle="tooltip" title="{{ @$weather->day->{$day}->symbol_description2 }}">
                        <img src="{{ asset('assets/img/default/weather/color/' . @$weather->day->{$day}->symbol_value2 . '.svg') }}"/>
                    </div>
                    <div class="temp">
                        <span class="text-blue">{{ @$weather->day->{$day}->tempmin }}º</span>/
                        <span class="text-red">{{ @$weather->day->{$day}->tempmax }}º</span>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
@else
    <div class="weather-panel">
        <div class="widget-loading">
            <i class="fas fa-spin fa-circle-notch"></i>
            @trans('A carregar previsão...')
        </div>
    </div>
@endif