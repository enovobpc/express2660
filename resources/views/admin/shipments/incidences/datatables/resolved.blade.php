<div data-toggle="tooltip" title="Marcar/Desmarcar como Resolvido">
{{ Form::open(['route' => ['admin.shipments.incidences.resolve', $row->shipment_id], 'method' => 'POST', 'class' => 'resolve-checkbox']) }}
{{ Form::checkbox('resolved', 1, $row->resolved) }}
{{ Form::hidden('history_id', $row->history_id) }}
<i class="fas fa-spin fa-circle-notch" style="display: none"></i>
{{ Form::close() }}
</div>