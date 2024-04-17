<a href="{{ route('admin.fleet.brands.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
    <i class="fas fa-circle" style="color: {{ $row->color }}"></i>
    {{ $row->name }}
</a>
<br/>
<i class="text-muted">{{ trans('admin/fleet.brands.types.'.$row->type) }}</i>