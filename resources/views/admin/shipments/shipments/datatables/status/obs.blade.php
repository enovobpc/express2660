{{ $row->obs }}
@if($row->incidence)
    @if($row->obs)
    <br/>
    @endif
    Motivo de Incidência: {{ $row->incidence->name }}
@endif