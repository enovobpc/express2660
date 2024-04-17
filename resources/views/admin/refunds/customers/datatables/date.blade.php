{{ $row->date }}<br/>
<a href="{{ route('admin.shipments.history.create', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
    <span class="label" style="background-color: {{ @$row->status->color }}">
        {{ @$row->status->name }}
    </span>
</a>