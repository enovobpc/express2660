<a href="{{ route('admin.users.absences.edit', [$row->user_id, $row->id]) }}"
   class="btn btn-sm btn-default"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->type }}
</a>