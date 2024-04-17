<b>
    <span data-toggle="tooltip" title="{{ @$row->sourceAirport->airport }}">{{ @$row->sourceAirport->code }}</span>
    <i class="fas fa-arrow-right"></i>
    <span data-toggle="tooltip" title="{{ @$row->recipientAirport->airport }}">{{ @$row->recipientAirport->code }}</span>
</b>
@if($row->flight_scales)
<br/>
<span class="text-muted">{{ count($row->flight_scales) }} {{ count($row->flight_scales) == 1 ? 'Escala' : 'Escalas' }}</span>
@endif
<br/>
<span class="text-muted">{{ @$row->provider->name }}</span>