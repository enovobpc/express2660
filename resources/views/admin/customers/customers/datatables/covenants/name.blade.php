<a href="{{ route('admin.customers.covenants.edit', [$row->customer_id, $row->id]) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->name }}
</a>