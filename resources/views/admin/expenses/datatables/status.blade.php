@if($row->start_at > date('Y-m-d'))
    <span class="label label-warning">Agendado</span>
@elseif($row->end_at >= date('Y-m-d'))
    <span class="label label-success">Ativo</span>
@else
    <span class="label label-default">Inativo</span>
@endif
