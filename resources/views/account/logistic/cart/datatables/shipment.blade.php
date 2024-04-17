@if(isset($row->shipment->tracking_code))
    @if(Auth::guard('customer')->user()->id == $row->submitted_by)
    <a href="{{ route('account.shipments.show', $row->shipment->id) }}"
        data-toggle="modal"
        data-target="#modal-remote-xl"
        class="fs-13">
        {{ @$row->shipment->tracking_code }}
    </a>
    @else
        {{ @$row->shipment->tracking_code }}
    @endif
@else
 N/A
 @endif