<table class="table table-condensed nicescroll" style="height: 235px; overflow-y: auto; display: block;">
    <thead>
        <tr style="background: #f9f9f9">
            <th>@trans('Envios pendentes de aceitação')</th>
            <th class="w-1">@trans('Envios')</th>
        </tr>
    </thead>
    <tbody>
    @foreach($pendingShipments as $customer)
        <tr>
            <td>{{ @$customer->customer->name }}</td>
            <td class="text-center">{{ $customer->total_pending }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@if($pendingShipments->isEmpty())
    <div class="widget-loading">
        <i class="fas fa-check fs-18"></i><br/>
        @trans('Não há envios ou recolhas pendentes')
    </div>
@endif
