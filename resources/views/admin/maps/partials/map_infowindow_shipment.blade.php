
<div style="width: 220px">
    <div style="margin-bottom: 5px">
        <div style="float: left">
            <b><i class="fas fa-circle" style="color: {{ @$shipment->status->color }}"></i> {{ $shipment->tracking_code }}</b>
        </div>
        <div style="float: right" class="label label-default m-l-3">E</div>
        <div style="float: right">{{ $shipment->date }}</div>
        <div class="clearfix"></div>
    </div>
    <div style="margin-bottom: 3px; border-bottom: 1px solid #ddd; padding: 2px 0">
        <small><i class="fas fa-user"></i> {{ @$shipment->customer->name }}</small>
    </div>
    <b>{{ $shipment->recipient_name }}</b><br/>
    <small>
        {{ $shipment->recipient_address }}<br/>
        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
    </div>
    
    <div style="    border: 1px solid #ddd;
    padding: 4px;
    margin-top: 4px;
    margin-bottom: 2px;
    border-radius: 3px;
    background: #f2f2f2;">
        <div style="width: 120px; float: left;">
            <div class="bold m-b-5 text-uppercase">{{ @$shipment->service->name }}</div>
            {{ $shipment->delivery_date }}<br/>
            {{ $shipment->start_hour }} {{ $shipment->end_hour ? '-'.$shipment->end_hour : '' }}
        </div>
        <div style="width: 70px; float: left;">
            <div class="bold">ENTREGA</div>
            @if(is_array($shipment->packaging_type) && !empty($shipment->packaging_type))
                @foreach($shipment->packaging_type as $type => $qty)
                    <div>{{ $qty }} {{ @$packTypes[$type] ? @$packTypes[$type] : $type }}</div>
                @endforeach
            @else
                <div>{{ $shipment->volumes }} Volumes</div>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>
    @if(1)
    <button class="btn btn-xs btn-primary pull-right m-l-5 marker-add-shipment" data-id="{{ $shipment->id }}">Adicionar/Remover</button>
    <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="btn btn-xs btn- pull-right" data-toggle="modal" data-target="#modal-remote-xlg">Ver serviço</a>
    @else
    <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="btn btn-xs btn-primary pull-right" data-toggle="modal" data-target="#modal-remote-xlg">Ver serviço</a>
    @endif
</div>