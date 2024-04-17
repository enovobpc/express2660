@if($shipmentTraceability->isEmpty())
    <div class="text-center text-muted p-5">
        <h4><i class="fas fa-info-circle"></i> Este envio nunca foi rastreado pelo leitor de códigos de barras.</h4>
    </div>
@else
<table id="datatable" class="table table-hover">
    <tr>
        <th class="bg-gray-light w-90px">Data</th>
        <th class="bg-gray-light w-50px">Hora</th>
        <th class="bg-gray-light w-250px">Ponto de Leitura</th>
        <th class="bg-gray-light">Localização</th>
        <th class="bg-gray-light w-1">Volume</th>
        <th class="bg-gray-light">Operador</th>
        <th class="bg-gray-light w-150px">Cód. Barras</th>
    </tr>
    @foreach($shipmentTraceability as $item)
    <tr>
        <td>{{ $item->created_at->format('Y-m-d') }}</td>
        <td>{{ $item->created_at->format('H:i') }}</td>
        <td>
            @if($item->event_id)
            {{ @$item->event->name }}
            @else
            {{ trans('admin/traceability.read-points.'.$item->read_point) }} -
            {{ @$item->agency->name }}
            @endif
        </td>
        <td>{{ @$item->location->name }}</td>
        
        <td>{{ $item->volume == 'all' ? 'Todos' : $item->volume }}</th>
        <td>{{ @$item->operator->name }}</td>
        <td>{{ $item->barcode ? $item->barcode : ($item->volume == 'all' ? $shipment->tracking_code : $shipment->tracking_code.str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT).$item->volume) }}</td>
    </tr>
    @endforeach
</table>
@endif