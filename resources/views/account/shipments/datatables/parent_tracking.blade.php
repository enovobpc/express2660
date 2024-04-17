<a href="{{ route('account.shipments.show', $row->children_tracking_code) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl"
   class="fs-13">
    {{ $row->children_tracking_code }}
</a>