@if($row->route_id)
    <span class="label" style="background: {{ @$row->route->color }}" data-toggle="tooltip" title="{{ @$row->route->name }}">
        {{ @$row->route->code }}
    </span>
@endif