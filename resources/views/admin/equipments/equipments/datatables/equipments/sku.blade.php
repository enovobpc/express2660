<a href="{{ route('admin.equipments.show', $row->id) }}"
    data-toggle="modal"
    data-target="#modal-remote-lg">
    {{ @$row->sku }}
</a>