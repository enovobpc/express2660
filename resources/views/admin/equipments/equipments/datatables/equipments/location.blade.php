@if($row->location_id)
<i class="fas fa-square" style="color: {{ @$row->location->color }}"></i> {{ @$row->location->name }}<br/>
<small class="text-muted italic">{{ @$row->warehouse->name }}</small>
@endif