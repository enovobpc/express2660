<a href="{{ route('admin.services.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
    {{ $row->name }}
    @if($row->priority_level)
        <span class="label" style="background-color: {{ $row->priority_color }}">Prioridade Nível {{ $row->priority_level }}</span>
    @endif
</a>
@if($row->zones)
<br/>
@foreach(@$row->zones as $zone)
<span class="label bg-gray">{{ strtoupper($zone) }}</span>
@endforeach 
@endif
