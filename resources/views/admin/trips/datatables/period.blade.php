{{--
@if($row->period_id)
    <div>{{ @$row->period->name }}</div>
@endif
@if($row->delivery_route_id)
    <small>
        <span class="label" style="background: {{ @$row->delivery_route->color }}">{{ @$row->delivery_route->code }}</span> {{ @$row->delivery_route->name }}
    </small>
@endif--}}

{{ $row->start_hour }}
@if($row->end_hour)
    - {{ $row->end_hour }}
@endif