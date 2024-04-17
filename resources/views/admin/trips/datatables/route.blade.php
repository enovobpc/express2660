@if($row->delivery_route_id)
    <small>
        <span class="label" style="background: {{ @$row->delivery_route->color }}">{{ @$row->delivery_route->code }}</span> {{ @$row->delivery_route->name }}
    </small>
@endif