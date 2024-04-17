{{ $row->start_date }}
@if($row->start_date > date('Y-m-d'))
<div>
    <div class="label label-warning">Agendado</div>
</div>
@endif