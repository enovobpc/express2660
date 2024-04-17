<a href="{{ route('admin.fleet.vehicles.edit', $row->id) }}">{{ $row->name }}</a>
<br/>
<small>
<i class="text-muted">{{ $row->brand->name }}
@if($row->model)
    &bull; {{ $row->model->name }}</i>
@endif
</small>