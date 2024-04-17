@if($row->source_class == 'CalendarEvent')
    <a href="{{ route('admin.calendar.events.edit', $row->source_id) }}" data-toggle="modal" data-target="#modal-remote">
        {{ $row->message }}
    </a>
@elseif($row->source_class == 'ErrorLog')
    <a href="{{ route('admin.logs.errors.index') }}" target="_blank">
        {{ $row->message }}
    </a>
@elseif($row->source_class == 'Notice')
    <a href="{{ route('admin.notifications.show', $row->source_id) }}" data-toggle="modal" data-target="#modal-remote-lg">
        {{ $row->message }}
    </a>
@elseif($row->source_class == 'Shipment')
    <a href="{{ route('admin.shipments.edit', $row->source_id) }}" data-toggle="modal" data-target="#modal-remote-xl">
        {{ $row->message }}
    </a>
@elseif($row->source_class == 'ShipmentCustomer')
    <a href="{{ route('admin.shipments.index', ['customer' => $row->source_id, 'status' => '1']) }}">
        {{ $row->message }}
    </a>
@elseif($row->source_class == 'BudgetMessage')
    <a href="{{ route('admin.budgets.show', $row->source_id) }}">
        {{ $row->message }}
    </a>
@elseif($row->source_class == 'License')
    <a href="{{ route('admin.licenses.details') }}" data-toggle="modal" data-target="#modal-remote-xl">
        {{ $row->message }}
    </a>
@else
    {{ $row->message }}
@endif

@if(!$row->read)
    <span class="label label-warning">Não lido</span>
@endif