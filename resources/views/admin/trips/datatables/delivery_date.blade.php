@if($row->delivery_date)
    <div>{{ $row->delivery_date->format('Y-m-d') }}</div>
    <small class="text-muted">{!! $row->end_hour ? '<i class="far fa-clock"></i> '. $row->end_hour : '' !!}</small>
@endif