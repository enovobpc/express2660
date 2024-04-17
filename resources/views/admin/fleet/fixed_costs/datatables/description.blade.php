<a href="{{ route('admin.fleet.fixed-costs.edit', [$row->id, 'vehicle' => $row->vehicle_id]) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->description }}
</a>