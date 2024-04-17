@if($row->parent_id == \App\Models\FileRepository::FOLDER_CUSTOMERS)
    <a href="{{ route('admin.customers.edit', $row->source_id) }}" target="_blank">
        {{ @$row->customer->name }}
    </a>
@elseif($row->parent_id == \App\Models\FileRepository::FOLDER_USERS)
    <a href="{{ route('admin.users.edit', $row->source_id) }}" target="_blank">
        {{ @$row->user->name }}
    </a>
@elseif($row->parent_id == \App\Models\FileRepository::FOLDER_VEHICLES)
    <a href="{{ route('admin.fleet.vehicles.edit', $row->source_id) }}" target="_blank">
        {{ @$vehicles[$row->source_id] }}
    </a>
@elseif($row->parent_id == \App\Models\FileRepository::FOLDER_SHIPMENTS)
    <a href="{{ route('admin.shipments.edit', $row->source_id) }}" target="_blank">
        {{ @$row->shipment->tracking_code }}
    </a>
@endif