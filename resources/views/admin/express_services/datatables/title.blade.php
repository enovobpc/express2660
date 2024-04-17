<a href="{{ route('admin.express-services.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ $row->title }}
</a>
@if($row->customer_id)
    <br/>
    <i class="text-muted">{{ $row->customer->code }} - {{ $row->customer->name }}</i>
@endif