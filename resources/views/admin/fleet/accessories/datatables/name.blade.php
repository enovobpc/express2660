<a href="{{ route('admin.fleet.accessories.edit', [$row->id, 'vehicle' => $row->vehicle_id]) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->name }}
</a>