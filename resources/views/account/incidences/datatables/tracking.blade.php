<?php
    $service = @$servicesList[$row->shipment_id][0];
?>
<a href="{{ route('account.shipments.show', $row->shipment_id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl"
   class="fs-13">
    {{ $row->tracking_code }}
</a>

@if($row->type == \App\Models\Shipment::TYPE_RETURN)
    <span class="label bg-green" data-toggle="tooltip" title="Envio associado">
        <i class="fas fa-undo"></i> {{ $row->parent_tracking_code }}
    </span>
@endif

<div data-toggle="tooltip" title="{{ @$service->name }}">
    {{ @$service->display_code }}
</div>

<a href="{{ route('account.shipments.show', [$row->id, 'tab' => 'status']) }}"  data-toggle="modal" data-target="#modal-remote-xl">
    <span class="label" style="background-color: {{ $row->status->color }}">
        {{ $row->status->name }}
    </span>
</a>