<a href="{{ route('admin.meetings.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
    {{ @$row->customer->name }}
    @if(!$row->is_prospect)
        <span class="label label-success">@trans('Cliente')</span>
    @else
        <span class="label label-info">@trans('Prospect')</span>
    @endif
</a>
@if($row->interlocutor)
    <br/>
    <i class="text-muted">Sr(a). {{ $row->interlocutor }}</i>
@endif