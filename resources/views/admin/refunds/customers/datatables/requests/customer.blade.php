<a href="{{ route('admin.refunds.requests.show', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ @$row->customer->name }}
</a>