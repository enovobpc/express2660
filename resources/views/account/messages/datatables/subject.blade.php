<a href="{{ route('account.messages.show', $row->id) }}" data-toggle="modal" data-target="#modal-remote">{{ $row->subject }}</a><br/>
<small class="text-muted">
    {{ str_limit($row->message) }}
</small>