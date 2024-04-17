<a href="{{ route('core.provider.agencies.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->name }}
</a>
@if($row->is_hidden)
    <span class="label" style="background: #ccc">Oculto</span>
@endif