<?php
    $row->weekdays   = json_decode($row->weekdays, true);
    $row->month_days = json_decode($row->month_days, true);
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
        @endif
    @else
        Repete a cada {{ $row->repeat_every }} {{ trans('admin/shipments.schedule.frequencies.'.$row->frequency ) }}
    @endif
</b>
<br/>
<small class="text-muted">
    @if(in_array($row->frequency, ['week', 'month']))
        <i>
        @if($row->frequency == 'week' && @$row->weekdays)
            Dias:
            @foreach(@$row->weekdays as $weekday)
                {{ trans('datetime.weekday-tiny.'.$weekday) . '; ' }}
            @endforeach
        @elseif($row->frequency == 'month')
            @if($row->repeat == 'day')
                Dias: {{ implode(',', $row->month_days) }}
            @else
                {{ trans('admin/shipments.schedule.month-frequencies.' . $row->repeat) }}:
                @foreach($row->weekdays as $weekday)
                    {{ trans('datetime.weekday-tiny.'.$weekday).'; ' }}
                @endforeach
            @endif
        @endif
        </i>
        <br/>
    @endif
    <i>
    @if($row->end_repetitions)
        Termina ao fim de {{ $row->end_repetitions }} repetições
    @else
        Termina no dia {{ $row->end_date }}
    @endif
    </i>
</small>