@if($row->finished)
    <span class="label label-default">Inativo</span>
@else
    @if($row->start_date > date('Y-m-d'))
        <span class="label label-warning">Agendado</span><br/>
        <small>{{ $row->start_date }}</small>
    @else
        <span class="label label-success">Ativo</span>
    @endif
@endif