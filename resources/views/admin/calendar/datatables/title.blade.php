<i class="fas fa-square" style="color: {{ $row->color }}"></i>
<a href="{{ route('admin.calendar.events.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
    {{ $row->title }}
</a>