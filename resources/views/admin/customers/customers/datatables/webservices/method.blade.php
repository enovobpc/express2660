<a href="{{ route('admin.customers.webservices.edit', [$row->customer_id, $row->id]) }}" data-toggle="modal" data-target="#modal-remote">
    {{ $row->webservice_method->name }}
</a>