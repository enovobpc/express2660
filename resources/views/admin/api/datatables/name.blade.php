<a href="{{ route('admin.api.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
    {{ $row->name }}
</a>
@if($row->user_id)
    <br/>
    <small class="text-muted italic">
        <i class="fas fa-user"></i> {{ @$row->customer->name }}
        @if(@$row->customer->deleted_at)
            <i class="fas fa-trash-alt text-red" data-toggle="tooltip" title="Cliente eliminado"></i>
        @endif
    </small>
@endif