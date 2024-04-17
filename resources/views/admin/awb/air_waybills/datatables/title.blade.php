<a href="{{ route('admin.air-waybills.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
    {{ $row->title }}
</a>
@if($row->reference)
<br/>
<span>Ref: {{ $row->reference }}</span>
@endif
