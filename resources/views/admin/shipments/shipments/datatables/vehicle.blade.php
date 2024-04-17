@if(@$row->trip_code)
    <div><a href="{{ route('admin.trips.show', [@$row->trip_id]) }}" target="_blank">{{ @$row->trip_code }}</a></div>
@endif
{{--@if($row->route_id)
    @if(@$row->pickup_route_id)
    <div data-toggle="tooltip" title="{{ @$row->pickup_route->name }}">{{ @$row->pickup_route->code }}</div>
    @endif
    <div data-toggle="tooltip" title="{{ @$row->route->name }}">{{ @$row->route->code }}</div>
@endif--}}
<div><small onclick="CopyToClipboard('{{ $row->vehicle .' ' .$row->trailer }}')">{{ $row->vehicle }}</small></div>
<div><small>{{ $row->trailer }}</small></div>