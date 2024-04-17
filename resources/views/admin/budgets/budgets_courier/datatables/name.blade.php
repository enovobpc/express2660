<a href="{{ route('admin.budgets.courier.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ $row->name }}
</a>
<br/>
<i class="text-muted">{{ $row->email }}</i>