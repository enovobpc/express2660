<a href="{{ route('account.contacts.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->name }}
</a>
<br/>
<small><i class="text-muted">{{ $row->department }}</i></small>