<a href="{{ route('admin.allowances.edit', [$row->id, 'month' => $month, 'year' => $year]) }}"
   data-toggle="modal"
   data-target="#modal-remote-lg">
    {{ $row->fullname ? $row->fullname : $row->name }}
</a>