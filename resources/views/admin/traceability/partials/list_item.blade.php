@if($shipment->exists)
<?php
    $bgColor = '';
    if($shipment->counter < $shipment->volumes) {
        $bgColor = '#ff000036';
    }
?>
<tr data-id="{{ $shipment->tracking_code }}"
    data-readed-code="{{ $readedTrk }}"
    data-volumes="{{ $shipment->volumes }}"
    data-weight="{{ $shipment->weight }}"
    data-charge="{{ $shipment->charge_price }}"
    data-cod="{{ $shipment->total_price_for_recipient }}"
    style="background: {{ $bgColor }}">
    <td>
        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'shipments'))
        <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="bold" data-toggle="modal" data-target="#modal-remote-xl">
            {{ $shipment->tracking_code }}
        </a>
        @else
        {{ $shipment->tracking_code }}
        @endif
    </td>
    <td>{{ $shipment->reference }}</td>
    <td>{{ $shipment->sender_name }}</td>
    <td>{{ @$shipment->operator->name }}</td>
    <td>
        <span class="label" style="background: {{ @$shipment->provider->color }}">
        {{ @$shipment->provider->name }}
        </span>
    </td>
    <td>
        <span class="label" style="background: {{ @$shipment->status->color }}">
        {{ @$shipment->status->name }}
        </span>
    </td>
    <td>
        @if($shipment->counter >= $shipment->volumes)
            <span class="text-green reader-vols"><i class="fas fa-check"></i> {{ $shipment->volumes }}/{{ $shipment->volumes }}</span>
        @else
            <span class="text-red reader-vols"><i class="fas fa-exclamation-triangle"></i> {{ $shipment->counter }}/{{ $shipment->volumes }}</span>
        @endif
        <input type="hidden" name="check_list" value="{{ $shipment->check_list }}">
        <input type="hidden" name="code[]" value="{{ $shipment->tracking_code }}">
    </td>
</tr>
@elseif(0)
    <tr data-id="{{ $shipment->tracking_code }}" class="text-orange" style="background: rgba(255,128,0,0.21)">
        <td>{{ $shipment->tracking_code }}</td>
        <td colspan="5">
            <i class="fas fa-exclamation-circle"></i> Mal canalizado.
        </td>
    </tr>
@else
    <tr data-id="{{ $shipment->tracking_code }}" class="text-red" style="background: #ff000036">
        <td>{{ $shipment->tracking_code }}</td>
        <td colspan="5">
            <i class="fas fa-exclamation-circle"></i> Não foi possível encontrar o envio.
        </td>
    </tr>
@endif