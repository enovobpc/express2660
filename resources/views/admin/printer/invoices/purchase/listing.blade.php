<?php

    $showPaymentColumn = false;
    if(config('app.source') == 'corridexcelente') {
        $showPaymentColumn = true;
    }
?>
@if($grouped)
    <?php
        $allInvoices = $invoices;
        $docTotal = $docVat = $docSubtotal = $docCount = $docTotalUnpaid = 0;
    ?>
    @foreach($types as $typeId => $typeName)
        <?php
        $invoices = $allInvoices->filter(function ($item) use ($typeId) {
            return $item->type_id == $typeId;
        });
        ?>

        @if(!$invoices->isEmpty())
        <h4>{{ $typeName }}</h4>
        <table class="table table-bordered table-pdf font-size-7pt" style="border: none">
            <tr>
                <th class="w-160px">Tipo Despesa</th>
                <th class="w-55px">N.º Doc.</th>
                <th class="w-70px">Data Doc.</th>
                <th class="w-70px">Vencimento</th>
                @if($showPaymentColumn)
                <th class="w-65px">Pagamento</th>
                @endif
                <th class="w-70px">Referência</th>
                <th class="w-50px">NIF</th>
                <th>Fornecedor</th>
                <th class="w-70px text-right">Subtotal</th>
                <th class="w-60px text-right">IVA</th>
                <th class="w-70px text-right">Total</th>
                <th class="w-70px text-right">Pendente</th>
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

                $docTotal+= $tableTotal;
                $docVat+= $tableVat;
                $docSubtotal+= $tableSubtotal;
                $docTotalUnpaid+= $tableTotalUnpaid;
                $docCount+= $tableCount;
                ?>
                <tr>
                    <td>{{ @$invoice->type->name }}</td>
                    <td>{{ $invoice->code }}</td>
                    <td>{{ $invoice->doc_date }}</td>
                    <td>{{ $invoice->due_date }}</td>
                    @if($showPaymentColumn)
                    <td>{{ $invoice->payment_date }}</td>
                    @endif
                    <td>{{ $invoice->reference }}</td>
                    <td>{{ $invoice->vat }}</td>
                    <td>{{ $invoice->billing_name }}</td>
                    <td class="text-right">{{ $invoice->subtotal > 0.00 ? $signal : '' }}{{ money($invoice->subtotal) }}</td>
                    <td class="text-right">{{ $invoice->vat_total > 0.00 ? $signal : '' }}{{ money($invoice->vat_total) }}</td>
                    <td class="text-right bold">{{ $invoice->total > 0.00 ? $signal : '' }}{{ money($invoice->total) }}</td>
                    @if($invoice->is_settle)
                        <td></td>
                    @else
                        <td class="text-right bold" style="color: #FF0000">{{ money($invoice->total_unpaid) }}</td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="{{ $showPaymentColumn ? '8' : '7' }}" style="border: none;font-size: 12px" class="text-right">
                    Total ({{ $tableCount }} documentos)
                </td>
                <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableSubtotal) }}</td>
                <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableVat) }}</td>
                <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableTotal) }}</td>
                <td class="text-right" style="font-weight: bold; font-size: 12px; {{ $tableTotalUnpaid > 0.00 ?? 'color: #FF0000' }}">{{ $tableTotalUnpaid > 0.00 ?? money($tableTotalUnpaid) }}</td>
            </tr>
        </table>
        @endif
    @endforeach
    <div style="border-top: 1px solid #333; padding-top: 10px">
        <h4 class="pull-right text-right m-t-0" style="width: 100%">

            <div style="width: 140px; float: right; font-size: 22px">
                <small style="width: 150px; float: left">Por pagar: <br/>
                    <b class="bold" style="color: red;">{{ money($docTotalUnpaid, Setting::get('app_currency')) }}</b>
                </small>
            </div>
            <div style="width: 140px; float: right; font-size: 22px">
                <small style="width: 130px; float: left">Total: <br/>
                    <b class="bold" style="color: #000;">{{ money($docTotal, Setting::get('app_currency')) }}</b></small>
            </div>
            <div style="width: 100px; float: right">
                <small>IVA:<br/>
                    <b class="bold" style="color: #000;">{{ money($docVat, Setting::get('app_currency')) }}</b>
                </small>
            </div>
            <div style="width: 130px; float: right">
                <small>Subtotal<br/>
                    <b class="bold" style="color: #000;">{{ money($docSubtotal, Setting::get('app_currency')) }}</b>
                </small>
            </div>
            <div style="width: 130px; float: right">
                <small>Documentos<br/>
                    <b class="bold" style="color: #000;">{{ $docCount }}</b>
                </small>
            </div>
        </h4>
    </div>
@else
    <table class="table table-bordered table-pdf font-size-7pt" style="border: none">
        <tr>
            <th class="w-160px">Tipo Despesa</th>
            <th class="w-55px">N.º Doc.</th>
            <th class="w-70px">Data Doc.</th>
            <th class="w-70px">Vencimento</th>
            @if($showPaymentColumn)
                <th class="w-65px">Pagamento</th>
            @endif
            <th class="w-70px">Referência</th>
            <th class="w-50px">NIF</th>
            <th>Fornecedor</th>
            <th class="w-70px text-right">Subtotal</th>
            <th class="w-60px text-right">IVA</th>
            <th class="w-70px text-right">Total</th>
            <th class="w-70px text-right">Pendente</th>
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
                <td>{{ @$invoice->type->name }}</td>
                <td>{{ $invoice->code }}</td>
                <td>{{ $invoice->doc_date }}</td>
                <td>{{ $invoice->due_date }}</td>
                @if($showPaymentColumn)
                <td>{{ $invoice->payment_date }}</td>
                @endif
                <td>{{ $invoice->reference }}</td>
                <td>{{ $invoice->vat }}</td>
                <td>{{ $invoice->billing_name }}</td>
                <td class="text-right">{{ $invoice->subtotal > 0.00 ? $signal : '' }}{{ money($invoice->subtotal) }}</td>
                <td class="text-right">{{ $invoice->vat_total > 0.00 ? $signal : '' }}{{ money($invoice->vat_total) }}</td>
                <td class="text-right bold">{{ $invoice->total > 0.00 ? $signal : '' }}{{ money($invoice->total) }}</td>
                @if($invoice->is_settle)
                    <td></td>
                @else
                    <td class="text-right bold" style="color: #FF0000">{{ money($invoice->total_unpaid) }}</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <td colspan="{{ $showPaymentColumn ? '8' : '7' }}" style="border: none;font-size: 12px" class="text-right">
                Total ({{ $tableCount }} documentos)
            </td>
            <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableSubtotal) }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableVat) }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($tableTotal) }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px; {{ $tableTotalUnpaid > 0.00 ?? 'color: #FF0000' }}">{{ $tableTotalUnpaid > 0.00 ?? money($tableTotalUnpaid) }}</td>
        </tr>
    </table>
@endif