@if($row->km)
    {{ $row->km }}
    <br/>
    @if($row->km_alert)
        <?php $diff = $row->km - $row->vehicle->counter_km; ?>
        @if($diff > $row->km_alert)
            <small class="bold text-green">{{ money($diff, 'km', 0) }}</small>
        @elseif($diff <= $row->km_alert && $row->km > $row->vehicle->counter_km)
            <small class="bold text-yellow"><i class="far fa-clock"></i> {{ money($diff, 'km', 0) }}</small>
        @else
            <small class="bold text-red"><i class="fas fa-exclamation-triangle"></i>{{ money($diff, 'km', 0) }}</small>
        @endif
    @endif
@endif