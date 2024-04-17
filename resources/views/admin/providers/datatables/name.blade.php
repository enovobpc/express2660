@if($row->name)
<a href="{{ route('admin.providers.edit', $row->id) }}">
    <i class="fas fa-square" style="color: {{ $row->color }}"></i> {{ substr($row->name, 0, 15) }}
</a>
@endif
