<a href="{{ route('admin.billing.balance.show', [$row->id, 'source' => 'providers']) }}"
   class="text-uppercase"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ $row->company ? $row->company : $row->name }}
</a>
<br/>
<i class="text-muted">{{ $row->zip_code }} {{ $row->city }}</i>