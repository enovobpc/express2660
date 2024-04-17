<?php
    $row->weekdays   = json_decode($row->weekdays, true);
    $row->month_days = json_decode($row->month_days, true);
    $row->year_days  = json_decode($row->year_days, true);
?>

<b>
    <i class="fas fa-clock"></i>
    @if($row->repeat_every == 1)
        @if($row->frequency == 'day')
            Repete todos os dias
        @elseif($row->frequency == 'week')
            Repete todas as semanas
        @elseif($row->frequency == 'month')
            Repete todos os meses
        @elseif($row->frequency == 'year')
            Repete todos os anos
        @endif
    @else
        Repete a cada {{ $row->repeat_every }} {{ trans('admin/billing.schedule.frequencies.'.$row->frequency ) }}
    @endif
</b>
<br/>
<small class="text-muted">
    @if(in_array($row->frequency, ['week', 'month', 'year']))
        <i>
        @if($row->frequency == 'week')
            Dias:
            @foreach(@$row->weekdays as $weekday)
                {{ trans('datetime.weekday-tiny.'.$weekday) . '; ' }}
            @endforeach
        @elseif($row->frequency == 'month')
            @if($row->repeat == 'day')
                    No Dia: <b>{{ implode(',', $row->month_days) }}</b>
            @else
                {{ trans('admin/shipments.schedule.month-frequencies.' . $row->repeat) }}:
                @foreach($row->weekdays as $weekday)
                    {{ trans('datetime.weekday-tiny.'.$weekday).'; ' }}
                @endforeach
            @endif
        @elseif($row->frequency == 'year')
            Dias:
            @foreach(@$row->year_days as $yearDay)

                <?php
                    $dt = explode('-', $yearDay);
                ?>
                {{ $dt[1].'/'.trans('datetime.month-tiny.'.$dt[0]) . '; ' }}
            @endforeach
        @endif
        </i>
        <br/>
    @endif
    <i>
    @if($row->end_repetitions)
        Termina ao fim de {{ $row->end_repetitions }} repetições
    @elseif($row->end_date)
        Termina no dia {{ $row->end_date }}
    @else
        Sem data para terminar
    @endif
    </i>
</small>