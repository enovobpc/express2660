<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th>N.º Envio</th>
        <th>Serv.</th>
        <th>Remetente</th>
        <th>Destinatário</th>
        <th>Cobrança</th>
        <th class="w-80px">Recebido</th>
        <th class="w-80px">Reembolsado</th>
        <th class="w-120px">Observações</th>
    </tr>
    <?php $documentTotal = 0; $countTotal = 0; ?>
    @foreach($shipments as $shipment)
        <?php
        $documentTotal+= $shipment->charge_price;
        $countTotal++;
        ?>
        <tr>
            <td>
                {{ $shipment->tracking_code }}
                <br/>
                {{ $shipment->date }}
            </td>
            <td class="text-center">{{ @$shipment->service->display_code }}</td>
            <td>{{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>
            <td>{{ $shipment->recipient_name }}<br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
            <td><b>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</b></td>
            <td>
                @if($shipment->refund_agencies)
                    <b>{{ trans('admin/refunds.payment-methods.'.$shipment->refund_agencies->received_method) }}</b>
                    <br/>{{ $shipment->refund_agencies->received_date }}
                @endif
            </td>
            <td>
                @if($shipment->refund_agencies && $shipment->refund_agencies->payment_method)
                    <b>{{ trans('admin/refunds.payment-methods.'.$shipment->refund_agencies->payment_method) }}</b>
                    <br/>{{ $shipment->refund_agencies->payment_date }}
                @endif
            </td>
            <td>{{ @$shipment->refund_agencies->obs }}</td>
        </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    <small>Total de Envios/Recolhas: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Total a Reembolsar:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
</h4>