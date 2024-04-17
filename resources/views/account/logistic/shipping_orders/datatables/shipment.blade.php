@if($row->shipment_id)
    @if(@$row->shipment->tracking_code)
        <a href="{{ route('account.shipments.show', $row->shipment_id) }}" data-toggle="modal" data-target="#modal-remote-xl">
            {{ @$row->shipment->tracking_code }}
        </a>
    </span>
    @else
        <span class="text-red">
            <i class="fas fa-exclamation-triangle"></i> Anulado
        </span>
    @endif
@endif