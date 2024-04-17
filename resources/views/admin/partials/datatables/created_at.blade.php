@if($row->created_at)
{{ $row->created_at->format('Y-m-d') }}
<br/><small>{{ $row->created_at->format('H:i:s') }}</small>
@endif