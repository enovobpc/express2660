<a href="{{ route('admin.webservices.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ @$row->webservice_method->name }}
</a>