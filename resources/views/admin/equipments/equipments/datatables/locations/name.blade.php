<a href="{{ route('admin.equipments.show', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-lg">
    <i class="fas fa-square" style="color: {{ @$row->color }}"></i> {{ @$row->name }}
</a>