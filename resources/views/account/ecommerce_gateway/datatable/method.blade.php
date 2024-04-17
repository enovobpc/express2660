<a href="{{ route('account.ecommerce-gateway.edit', $row->id) }}"
    data-toggle="modal"
    data-target="#modal-remote">
    {{ trans('admin/ecommerce-gateway.methods.' . $row->method) }}
</a>