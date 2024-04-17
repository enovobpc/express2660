@if($row->counter_consumption)
    <div data-toggle="tooltip" title="Valor de referência: {{ @$row->vehicle->average_consumption }} l/100">
        <b class="{{ @$row->vehicle->consumption_color }}">
            {{ money($row->counter_consumption) }}
        </b>
    </div>
@endif
<div>
    <span data-toggle="tooltip" title="Nivel de combustível">
        @if($row->fuel_level)
            {{ $row->fuel_level_html }}
        @endif
    </span>
</div>