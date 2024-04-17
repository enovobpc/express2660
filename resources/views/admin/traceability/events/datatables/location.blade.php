@if(@$row->location_id)
[{{ @$row->location->code }}] {{ @$row->location->name }}
@endif