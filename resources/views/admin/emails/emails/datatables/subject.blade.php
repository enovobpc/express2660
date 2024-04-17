@if($row->is_draft)
<a href="{{ route('admin.emails.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ $row->subject }} <span class="label label-warning">RASCUNHO <i class="fas fa-pencil-alt"></i></span>
</a>
@else
<a href="{{ route('admin.emails.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ $row->subject }}
</a>
@endif