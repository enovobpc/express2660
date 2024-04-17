@if($row->sended_by)
    {{ @$row->user->name }}
@else
    Sistema
@endif
<br/>
<small class="text-muted">
    {{ $row->created_at }}
</small>