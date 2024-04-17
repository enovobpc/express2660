@if($row->balance_liter_km)
    <?php
    $diff = $row->balance_liter_km - $row->vehicle->average_consumption;
    $avg  = $row->vehicle->average_consumption;
    ?>
    <div data-toggle="tooltip" title="Valor de referência: {{ $row->vehicle->average_consumption }} l/100">
        @if($row->balance_liter_km > $avg && $diff <= 1)
            <b class="text-yellow">{{ money($row->balance_liter_km) }}</b>
        @elseif($row->balance_liter_km > $avg )
                <b class="text-red">{{ money($row->balance_liter_km) }}</b>
        @else
            <b class="text-green">{{ money($row->balance_liter_km) }}</b>
        @endif
    </div>
@endif