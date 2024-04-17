<a href="{{ route('admin.fleet.brands.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
    {{ $row->name }}
</a>
<br/>
<i class="text-muted">{{ trans('admin/fleet.brands.types.'.$row->type) }}</i>