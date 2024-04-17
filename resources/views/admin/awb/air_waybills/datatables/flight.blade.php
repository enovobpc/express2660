@if($row->flight_no_1)
    <i class="fas fa-plane"></i> {{ $row->flight_no_1 }}
@endif
@if($row->flight_no_2)
    <br/><i class="fas fa-plane"></i> {{ $row->flight_no_2 }}
@endif

@if($row->flight_no_3)
    <br/><i class="fas fa-plane"></i> {{ $row->flight_no_3 }}
@endif