<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th class="w-40px">N.º Envio</th>
        <th class="w-30px">Serv.</th>
        <th>Referência</th>
        <th>Remetente</th>
        <th>Destinatário</th>
        <th class="w-70px">Remessa</th>
        <th class="w-65px">Devolução</th>
    </tr>
    <?php $countTotal = $countVolumes = 0; ?>
    @foreach($shipments as $shipment)
        <?php
        $countTotal++;
        $countVolumes+= $shipment->volumes;
        ?>
    <tr>
        <td>
            <strong style="font-weight: bold">{{ $shipment->tracking_code }}</strong><br/>
            {{ $shipment->date }}
        </td>
        <td class="text-center">{{ $shipment->service->code }}</td>
        <td>{{ $shipment->reference }}</td>
        <td>{{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>
        <td>{{ $shipment->recipient_name }}<br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
        <td>
            {{ @$shipment->volumes }} Vol.<br/>
            {{ money($shipment->weight, 'kg') }}
        </td>
        <td>{{ @$shipment->last_history->created_at ? @$shipment->last_history->created_at->format('Y-m-d') : '' }}</td>
    </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    <small>Total de Envios/Recolhas: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
    <small>&nbsp;&nbsp;&nbsp;Volumes: <b class="bold" style="color: #000;">{{ $countVolumes }}</b></small>
</h4>