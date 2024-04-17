<a href="{{ route('admin.users.edit', $row->id) }}" class="text-uppercase nowrap">
    {{ $row->fullname ? $row->fullname : $row->name }}
</a>
@if($row->professional_role)
    <br/><small class="italic text-muted">{{ $row->professional_role }}</small>
@endif