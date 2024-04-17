<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th class="w-40px">N.º Envio</th>
        <th class="w-30px">Serv.</th>
        <th>Referência</th>
        <th>Remetente</th>
        <th>Destinatário</th>
        <th class="w-50px">Portes</th>
        <th class="w-80px">Recebido em</th>
        <th class="w-150px">Observações</th>
    </tr>
    <?php $documentTotal = 0; $countTotal = 0; ?>
    @foreach($shipments as $shipment)
    <?php 
        $documentTotal+= $shipment->charge_price; 
        $countTotal++;
    ?>
    <tr>
        <td>
            {{ $shipment->tracking_code }}<br/>
            {{ $shipment->date }}
        </td>
        <td class="text-center">{{ $shipment->service->code }}</td>
        <td>{{ $shipment->reference }}</td>
        <td>{{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>
        <td>{{ $shipment->recipient_name }}<br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
        <td><b>{{ money($shipment->total_price_for_recipient, Setting::get('app_currency')) }}</b></td>
        <td>
            @if($shipment->cod_control)
            <b>{{ trans('admin/refunds.payment-methods.'.$shipment->cod_control->payment_method) }}</b>
            <br/>{{ $shipment->cod_control->payment_date }}
            @endif
        </td>
        <td>{{ @$shipment->cod_control->obs }}</td>
    </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    <small>Total de Envios/Recolhas: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small> 
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Total dos Portes:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
</h4>