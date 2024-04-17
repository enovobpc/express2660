<a href="{{ route('admin.api.docs.methods.edit', [$row->id]) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ $row->name }}
</a>
<br/>
<small class="text-muted">{{ str_limit(strip_tags($row->description)) }}</small>
