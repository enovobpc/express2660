
<tr data-id="{{ $shipmentAddress->id }}" class="{{ $hash == 'main' ? 'active' : '' }}" data-target="#modal-{{ $hash }}">
    <td class="addr-sname">{{ $shipmentAddress->sender_name }}</td>
    <td class="addr-saddr">
        <i class="flag-icon flag-icon-{{ $shipmentAddress->sender_country }}"></i> {{ $shipmentAddress->sender_zip_code }} {{ $shipmentAddress->sender_city }}
    </td>
    <td class="addr-rname">{{ $shipmentAddress->recipient_name }}</td>
    <td class="addr-raddr">
        <i class="flag-icon flag-icon-{{ $shipmentAddress->recipient_country }}"></i> {{ $shipmentAddress->recipient_zip_code }} {{ $shipmentAddress->recipient_city }}
    </td>
    <td class="addr-ref" style="border-left: 2px solid #333">
        {{ $shipmentAddress->reference }}
        {!! $shipmentAddress->reference2 ? '<br/>'.$shipmentAddress->reference2 : '' !!}
    </td>
    <td class="addr-date">{{ $shipmentAddress->date }}</td>
    <td class="addr-vol text-right">{{ $shipmentAddress->volumes }}</td>
    <td class="addr-kg text-right">{{ money($shipmentAddress->weight) }}</td>
    <td class="addr-ldm text-right">{{ $shipmentAddress->ldm }}</td>
    <td class="addr-fm3 text-right">{{ money($shipmentAddress->fator_m3, '', 3) }}</td>
    <td class="nowrap">
        <div class="text-center">
            <i class="fas fa-level-down-alt text-blue m-r-5" data-title="Confirma a duplicação da linha?" data-action="copy-addr" data-toggle="tootltip" title="Duplicar"></i>
            <i class="fas fa-times text-red {{ $hash == 'main' ? 'hide' : '' }}" data-action="del-addr" data-toggle="tootltip" title="Remover"></i>
        </div>
        @include('admin.shipments.shipments.partials.edit.multiple_addr_modal')
    </td>
</tr>

