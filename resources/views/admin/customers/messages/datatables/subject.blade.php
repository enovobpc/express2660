<a href="{{ route('admin.customers.messages.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ $row->subject }}
</a>
<br/>
<small>{{ str_limit($row->message) }}</small>