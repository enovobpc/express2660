@if(!is_null($row->end_date))
{{ $row->end_date->format('Y-m-d') }}
@endif