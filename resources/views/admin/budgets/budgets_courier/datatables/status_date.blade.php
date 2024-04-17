@if($row->status_date)
    {{ @$row->status_date->format('Y-m-d') }}
    <br/>
    <small class="text-muted">{{ $row->status_date->format('H:i') }}</small>
@else
    {{ @$row->created_at->format('Y-m-d') }}
    <br/>
    <small class="text-muted">{{ $row->created_at->format('H:i') }}</small>
@endif