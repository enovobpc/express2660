<a href="{{ route('admin.users.expenses.edit', [$row->user_id, $row->id]) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->description }}
</a>