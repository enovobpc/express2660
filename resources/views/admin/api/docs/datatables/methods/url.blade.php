<a href="{{ route('admin.api.docs.methods.edit', [$row->id]) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ $row->url }}
</a>
<br/>
<span class="label label-default">{{ $row->method }}</span>
