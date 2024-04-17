<a href="{{ route('admin.fleet.vehicles.edit', $row->vehicle_id) }}">
    {{ @$row->vehicle->code ? $row->vehicle->code . ' - ' : '' }}{{ @$row->vehicle->name }}
</a>
<br/>
<small>{{ @$row->vehicle->license_plate }}</small>

