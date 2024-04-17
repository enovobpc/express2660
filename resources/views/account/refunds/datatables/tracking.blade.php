<a href="{{ route('account.shipments.show', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ $row->tracking_code }}
</a>
<br/>
<small><i class="far fa-calendar-alt"></i> {{ $row->date }}</small>
