@if($shipments->isEmpty())
    <p class="text-muted text-center m-t-10 m-b-10">
        <i class="fas fa-info-circle"></i> @trans('Não existem serviços')<br/>
        <small>@trans('Todos os serviços estão atribuidos.')</small>
    </p>
@else
    <ul class="list-unstyled">
        @foreach ($shipments as $shipment)
        <li data-id="{{ $shipment->id }}" 
            data-trk="{{ $shipment->tracking_code }}" 
            data-lat="{{ $shipment->recipient_latitude }}" 
            data-lng="{{ $shipment->recipient_longitude }}"
            data-addr="{{ $shipment->recipient_full_address }}"
            data-assembly="{{ $shipment->has_assembly }}"
            data-marker-icon="{{ asset(@$shipment->service->marker_icon) }}"
            data-html="{{ view('admin.maps.partials.map_infowindow_shipment', compact('shipment')) }}">
            {{-- @if(empty($shipment->map_lng))
            <div><i class="fas fa-map-marker-alt"></i> Localização não encontrada</div>
            @endif  --}} 
            <img src="{{ asset(@$shipment->service->marker_icon) }}" class="list-marker"/>                   
            <b>{{ strtoupper($shipment->recipient_name) }}</b><br/>
            {{ $shipment->recipient_address }}<br/>
            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
            <small>
                {{-- <b>{{ $shipment->tracking_code }}</b> --}}
                    <span class="label" style="background: {{ @$shipment->status->color}}">{{ @$shipment->status->name}}</span>
                    &bull;
                    {{ @$shipment->service->name }}
                    &bull;
                    {{ @$shipment->date }}
            </small>
            
            {{-- <div class="empty-coords">
                @if(empty($shipment->map_lng))
                <i class="fas fa-map-marker-alt"></i> Localização não encontrada
                @else
                {{ $shipment->recipient_latitude }} / {{ $shipment->recipient_longitude }}
                @endif 
            </div> --}}
            {{ Form::hidden('recipient_latitude', $shipment->recipient_latitude) }}
            {{ Form::hidden('recipient_longitude', $shipment->recipient_longitude) }}
        </li>
        @endforeach
    </ul>
@endif