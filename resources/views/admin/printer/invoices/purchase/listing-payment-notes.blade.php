<?php

    $showPaymentColumn = false;
    if(config('app.source') == 'corridexcelente') {
        $showPaymentColumn = true;
    }
?>
<table class="table table-bordered table-pdf font-size-7pt" style="border: none">
    <tr>
        <th class="w-55px">N.º Doc.</th>
        <th class="w-70px">Data Doc.</th>
        <th class="w-70px">Referência</th>
        <th class="w-50px">NIF</th>
        <th>Fornecedor</th>
        <th class="w-70px text-right">Subtotal</th>
        <th class="w-60px text-right">IVA</th>
        <th class="w-70px text-right">Total</th>
    </tr>
    <?php $tableTotal = $tableVat = $tableSubtotal = $tableCount = $tableTotalUnpaid = 0; ?>
    @foreach($invoices as $invoice)
        <?php

        $signal = '';
        /*if($invoice->doc_type == 'provider-credit-note') {
            $signal = '-';
            $tableTotal-= $invoice->total;
            $tableVat-= $invoice->vat_total;
            $tableSubtotal-= $invoice->subtotal;
            $tableTotalUnpaid-= $invoice->total_unpaid;
        } else {*/
            $tableTotal+= $invoice->total;
            $tableVat+= $invoice->vat_total;
            $tableSubtotal+= $invoice->subtotal;
            $tableTotalUnpaid+= $invoice->total_unpaid;
        /* }*/

        $tableCount++;
        ?>
        <tr>
            <td>{{ $invoice->code }}</td>
            <td>{{ $invoice->doc_date }}</td>
            <td>{{ $invoice->reference }}</td>
            <td>{{ $invoice->vat }}</td>
            <td>{{ $invoice->billing_name }}</td>
            <td class="text-right">{{ $invoice->subtotal > 0.00 ? $signal : '' }}{{ money($invoice->subtotal) }}</td>
            <td class="text-right">{{ $invoice->vat_total > 0.00 ? $signal : '' }}{{ money($invoice->vat_total) }}</td>
            <td class="text-right bold">{{ $invoice->total > 0.00 ? $signal : '' }}{{ money($invoice->total) }}</td>

        </tr>
    @endforeach
    <tr>
        <td colspan="{{ $showPaymentColumn ? '5' : '5' }}" style="border: none;font-size: 12px" class="text-right">
            Total ({{ $tableCount }} documentos)
        </td>
        <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableSubtotal) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableVat) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableTotal) }}</td>
    </tr>
</table>