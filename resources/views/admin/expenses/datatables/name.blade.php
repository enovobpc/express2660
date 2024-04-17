<a href="{{ route('admin.expenses.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    <b>{{ $row->internal_name ? $row->internal_name : $row->name }}</b>
</a>
<div>
    <small class="italic">
        {{ $row->name }}
    </small>
</div>