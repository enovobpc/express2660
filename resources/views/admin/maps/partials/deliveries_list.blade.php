@if($shipments->isEmpty())
    <div class="text-center m-t-30 text-muted">
        <i class="fas fa-info-circle bigger-140"></i>
        <br/>
        Não há entregas para o operador e data selecionados.
    </div>
@else
<div class="delivery-traject" style="display: none">
    <ul class="list-unstyled">
        <?php $totalRows = count($locations); ?>
        @foreach($locations as $key => $location)
            <li data-trjlat="{{ $location->latitude }}"
                data-trjlng="{{ $location->longitude }}"
                data-trjid="{{ $location->id }}"></li>
            <?php $totalRows-- ?>
        @endforeach
    </ul>
</div>
<ul class="list-unstyled">
    @foreach($shipments as $shipment)
    <?php
        $name    = $shipment->recipient_name;
        $address = $shipment->recipient_address;
        $zipCode = $shipment->recipient_zip_code;
        $city    = $shipment->recipient_city;

        if($shipment->is_collection) {
            $name    = $shipment->sender_name;
            $address = $shipment->sender_address;
            $zipCode = $shipment->sender_zip_code;
            $city    = $shipment->sender_city;
        }
    ?>
    <li data-lat="{{ $shipment->map_lat }}"
        data-lng="{{ $shipment->map_lng }}"
        data-id="{{ $shipment->id }}"
        data-icon="{{ $shipment->status->map_icon }}"
        data-html="<b>{{ $name }}</b>
            <br/>{{ $address }}<br/>
            {{ $zipCode }} {{ $city }}
            <hr style='margin: 3px 0'/>
            <b style='color: #018ad1'>{{ 'Data/Hora: ' . @$shipment->last_history->created_at }}
            {{ $shipment->status_id == 9 ? '<br/>'.@$shipment->last_history->incidence->name : ($shipment->status_id == 5 ? '<br/>Recebido por: '.@$shipment->last_history->receiver : '' ) }}
            </b><hr style='margin: 3px 0'/>
            <span class='label' style='background: {{ $shipment->status->color }}'>{{ $shipment->status->name }}</span> | <b>{{ $shipment->tracking_code }}</b>"

        class="{{ empty($shipment->map_lat) ? 'disabled' : '' }}">
        {{--<div class="pull-left delivery-list-left" style="width: 7%">
            @if(empty($shipment->map_lat))
                <input type="checkbox" name="" disabled/>
            @else
                <input type="checkbox" name="delivery_marker" checked/>
            @endif
        </div>--}}
        <div class="pull-left" style="width: 93%">
            <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="external-link" data-toggle="modal" data-target="#modal-remote-xl">
                <i class="fas fa-external-link-square-alt"></i>
            </a>
            @if(empty($shipment->lastHistory->latitude))
                <span class="label label-default">Sem localização</span><br/>
            @endif
            <b>{{ $name }}</b>
            <br/>{{ $address }}<br/>
            {{ $zipCode }} {{ $city }}
            <br/>
            <span class="label" style="background: {{ $shipment->status->color }}">{{ $shipment->status->name }}</span>&nbsp;&nbsp;&nbsp;<b>{{ $shipment->tracking_code }}</b>
        </div>
        <div class="clearfix"></div>
    </li>
    @endforeach
</ul>
@endif