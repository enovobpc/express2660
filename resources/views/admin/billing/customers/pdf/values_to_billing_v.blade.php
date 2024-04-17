<?php
$documentTotal = $documentTotalShipmentsVat = $documentTotalShipmentsNoVat = $documentTotalExpenses = 0;
$documentCountShipmentsVat = $documentCountExpenses = $documentCountShipmentsNoVat = $documentTotalShipmentsNoVat = 0;
?>
<div>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt;">
        <tr>
            <th>Nº</th>
            <th>Cliente</th>
            <th colspan="2">Envios Nac.</th>
            <th colspan="2">Envios Int.</th>
            {{--<th colspan="2">Reembolsos</th>--}}
            <th colspan="2">Encargos</th>
        </tr>

        @foreach($customers as $customer)
            <?php
            $billing = App\Models\CustomerBilling::getBilling($customer->id, $month, $year);

            $documentTotalShipmentsVat+= $billing->total_shipments_vat;
            $documentCountShipmentsVat+= $billing->count_shipments_vat;

            $documentTotalShipmentsNoVat+= $billing->total_shipments_no_vat;
            $documentCountShipmentsNoVat+= $billing->count_shipments_no_vat;

            $documentTotalExpenses+= $billing->total_expenses;
            $documentCountExpenses+= $billing->count_expenses;
            ?>

            @if($billing->total_month > 0.00)
            <tr>
                <td class="w-30px">{{ $billing->code }}</td>
                <td>{{ $billing->name }}</td>
                <td class="w-10px">{{ $billing->count_shipments_vat }}</td>
                <td class="w-50px">{{ money($billing->total_shipments_vat) }}</td>

                <td class="w-10px">{{ $billing->count_shipments_no_vat }}</td>
                <td class="w-50px">{{ money($billing->total_shipments_no_vat) }}</td>

                {{--<td class="w-10px">{{ $billing->count_charges }}</td>
                <td class="w-50px">{{ money($billing->total_charges) }}</td>
--}}
                <td class="w-10px">{{ $billing->count_expenses }}</td>
                <td class="w-50px">{{ money($billing->total_expenses) }}</td>

            </tr>
            @endif
        @endforeach
        <tr>
            <td class="text-right" colspan="2">TOTAIS</td>
            <td class="bold">{{ $documentCountShipmentsVat }}</td>
            <td class="bold">{{ $documentTotalShipmentsVat }}</td>
            <td class="bold">{{ $documentCountShipmentsNoVat }}</td>
            <td class="bold">{{ $documentTotalShipmentsNoVat }}</td>
            <td class="bold">{{ $documentCountExpenses }}</td>
            <td class="bold">{{ $documentTotalExpenses }}</td>
        </tr>
    </table>
</div>