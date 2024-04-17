@if($row->pickup_date)
    <div>{{ $row->pickup_date->format('Y-m-d') }}</div>
    <small class="text-muted">{!! $row->start_hour ? '<i class="far fa-clock"></i> '. $row->start_hour : '' !!}</small>
@endif