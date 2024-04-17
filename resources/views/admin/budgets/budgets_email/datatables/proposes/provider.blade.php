@if($row->read)
    <b class="text-black">{{ $row->to_name ? $row->to_name : $row->email }}</b>
@else
    <span class="text-yellow bold">{{ $row->to_name ? $row->to_name : $row->email }}</span>
@endif
<i class="tex-muted">
    @if($row->to_name)
        <br/>
        {{ $row->email }}
    @endif
    <br/>
    <small>
        {{ $row->created_at->format('Y-m-d') }}
    </small>
</i>