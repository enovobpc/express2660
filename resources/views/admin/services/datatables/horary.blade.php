<div style="white-space: nowrap">
    <div>
    @if(@$row->pickup_weekdays['0'] && @$row->pickup_weekdays['1'] && @$row->pickup_weekdays['2'] && @$row->pickup_weekdays['3'] && @$row->pickup_weekdays['4'] && @$row->pickup_weekdays['5'] && isset($row->pickup_weekdays['6']))
        Todos Dias
    @elseif(@$row->pickup_weekdays['0'] && @$row->pickup_weekdays['1'] && @$row->pickup_weekdays['2'] && @$row->pickup_weekdays['3'] && @$row->pickup_weekdays['4'] && @$row->pickup_weekdays['5'] && !isset($row->pickup_weekdays['6']))
        Dias Úteis + <u>Sab</u>
    @elseif(@$row->pickup_weekdays['0'] && @$row->pickup_weekdays['1'] && @$row->pickup_weekdays['2'] && @$row->pickup_weekdays['3'] && @$row->pickup_weekdays['4'])
        Dias Úteis
    @else
        @if(@$row->pickup_weekdays['0'])
            Seg&nbsp;
        @endif
        @if(@$row->pickup_weekdays['1'])
            Ter&nbsp;
        @endif
        @if(@$row->pickup_weekdays['2'])
            Qua&nbsp;
        @endif
        @if(@$row->pickup_weekdays['3'])
            Qui&nbsp;
        @endif
        @if(@$row->pickup_weekdays['4'])
            Sex&nbsp;
        @endif
        @if(@$row->pickup_weekdays['5'])
            Sab&nbsp;
        @endif
        @if(@$row->pickup_weekdays['6'])
            Dom&nbsp;
        @endif
    @endif
    </div>
    <small class="text-muted">
        @if($row->min_hour == '00:00' && $row->max_hour >= '23:50')
            Disp. Todo Dia
        @else
        {{ $row->min_hour ? str_replace(':', 'h', $row->min_hour) : '00h00' }} -
        {{ $row->max_hour ? str_replace(':', 'h', $row->max_hour) : '23h59' }}
        @endif
    </small>
</div>