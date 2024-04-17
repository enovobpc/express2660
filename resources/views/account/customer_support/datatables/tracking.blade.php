@if($row->shipment_id)
<a href="{{ route('admin.shipments.show', $row->shipment_id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    #{{ @$row->shipment->tracking_code }}
</a>
@endif

<div class="text-center">
    <small>
        @if(@$row->shipment->provider->name)
            {{ @$row->shipment->provider->name }}
        @endif
    </small>
</div>
