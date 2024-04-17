@if($row->shipment_id)
    @if(@$row->shipment->tracking_code)
    <a href="{{ route('admin.shipments.show', $row->shipment_id) }}" data-toggle="modal" data-target="#modal-remote-xl">
        {{ @$row->shipment->tracking_code }}
    </a>
    <br/>
    <span class="label" style="background: {{ @$row->shipment->provider->color }}">
            {{ @$row->shipment->provider->name }}
    </span>
    @else
        <span class="text-red">
            <i class="fas fa-exclamation-triangle"></i> @trans('Apagado')
        </span>
    @endif
@endif