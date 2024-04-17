@if(!is_null($row->start_date))
    {{ $row->start_date->format('Y-m-d') }}
@endif