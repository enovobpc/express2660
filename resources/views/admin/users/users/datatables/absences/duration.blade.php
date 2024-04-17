{{ $row->duration > 0.5 ? $row->duration : '' }}

@if($row->period == 'days' && $row->duration > 0.5)
    Dias
@elseif($row->period == 'hours')
    Horas
@else
    Meio Dia
@endif
