<a href="{{ route('admin.billing.balance.show', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ $row->name }}
</a>
<br/>
<i class="text-muted">{{ $row->zip_code }} {{ $row->city }}</i>