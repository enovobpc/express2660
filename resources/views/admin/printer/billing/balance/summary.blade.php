<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th class="w-50px">Código</th>
        <th>Cliente</th>
        <th class="w-90px">Doc. Vencidos</th>
        <th class="w-90px">Doc. Expirados</th>
        <th class="w-100px">Por Liquidar</th>
        <th class="w-80px">Última Act.</th>
        <th class="w-80px">Último Envio</th>
    </tr>

    <?php $totalUnpaid = $countUnpaid = $countExpired = 0; ?>
    @foreach($customers as $customer)
        <?php
            $totalUnpaid+= $customer->balance_total_unpaid;
            $countUnpaid+= $customer->balance_count_unpaid;
            $countExpired+= $customer->balance_count_expired;
        ?>
        <tr>
            <td class="text-center">{{ $customer->code }}</td>
            <td>{{ $customer->name }}</td>
            <td class="text-center">{{ $customer->balance_count_unpaid }}</td>
            <td class="text-center">{{ $customer->balance_count_expired }}</td>
            <td class="text-center">{{ money($customer->balance_total_unpaid, Setting::get('app_currency')) }}</td>
            <td class="text-center">{{ @$customer->balance_last_update ? $customer->balance_last_update->format('Y-m-d') : '' }}</td>
            <td class="text-center">{{ $customer->last_shipment }}</td>
        </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Total Docs: <b class="bold" style="color: #000;">{{ $countUnpaid }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Docs Expirados: <b class="bold" style="color: #000;">{{ $countExpired }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Por Liquidar:</small> <b class="bold">{{ money($totalUnpaid, Setting::get('app_currency')) }}</b>
</h4>
<hr style="margin-bottom: 10px"/>
<p class="text-center">Documento gerado em {{ date('Y-m-d') }} às {{ date('H:i:s') }}</p>