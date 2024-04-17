<a href="{{ route('admin.shipments.show', $row->shipment_id) }}" data-toggle="modal" data-target="#modal-remote-xl">
    {{ @$row->shipment->tracking_code }}
</a>