<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th>N.º Envio</th>
        <th>Data</th>
        <th>Serviço</th>
        <th>Referência</th>
        <th>Remetente</th>
        <th>Destinatário</th>
        <th>Cobrança</th>
        <th class="w-80px">Recebido em</th>
        <th class="w-300px">Observações</th>
    </tr>
    <?php $documentTotal = 0; $countTotal = 0; ?>
    @foreach($shipments as $shipment)
    <?php 
        $documentTotal+= $shipment->charge_price; 
        $countTotal++;
    ?>
    <tr>
        <td>{{ $shipment->tracking_code }}</td>
        <td>{{ $shipment->date }}</td>
        <td>{{ $shipment->service->display_code }}</td>
        <td>{{ $shipment->reference }}</td>
        <td>{{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>
        <td>{{ $shipment->recipient_name }}<br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
        <td><b>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</b></td>
        <td>
            @if($shipment->refund_control)
            <b>{{ trans('admin/shipments.charge_payment_methods.'.$shipment->refund_control->method) }}</b>
            <br/>{{ $shipment->refund_control->paid_at }}
            @endif
        </td>
        <td>{{ @$shipment->refund_control->obs }}</td>
    </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    <small>Total de Envios/Recolhas: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small> 
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Total a Reembolsar:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
</h4>