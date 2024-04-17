@if($row->source)
    <a href="{{ route('admin.billing.zones.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
        {{ $row->name }}
    </a>
@else
    {{ $row->name }}
@endif