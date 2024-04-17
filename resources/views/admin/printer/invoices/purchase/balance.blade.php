<h4 style="margin-bottom: 3px; font-size: 16px; font-weight: bold">
    {{ $provider->code }} - {{ $provider->company }}
</h4>
<p style="font-size: 12px">
    Contribuinte: {{ strtoupper($provider->country) }} {{ $provider->vat }}
    &bull;
    Pagamento: {{ @$provider->paymentCondition->name }}
</p>
<hr style="margin: 0"/>
 <table class="table table-bordered table-pdf font-size-7pt" style="border: none">
    <tr>
        <th class="w-70px">Data Doc.</th>
        <th class="w-70px">Data Recep.</th>
        <th class="w-90px">Tipo Doc.</th>
        <th>Documento</th>
        <th class="w-70px">Vencimento</th>
        <th class="w-70px text-right">Débito</th>
        <th class="w-70px text-right">Crédito</th>
        <th class="w-70px text-right">Por Liquidar</th>
        <th class="w-70px text-right">Saldo</th>
    </tr>
    <?php $totalDebit = $totalCredit = $totalPending = $totalBalance = $tableCount = $tableTotalUnpaid = $lineTotal = 0; ?>
    @foreach($invoices as $invoice)
        <?php

         if($invoice->sense == 'debit' && $invoice->total > 0.00) {
             $invoice->total = $invoice->total * -1;
         }

             if($invoice->sense == 'debit') {
                 $totalDebit+= $invoice->total;
             } else {
                 $totalCredit+= $invoice->total;

             }

             $totalPending+= $invoice->total_unpaid;
             $totalBalance+= $invoice->total;
             $tableCount++;
        ?>
        <tr>
            <td>{{ $invoice->doc_date }}</td>
            <td>{{ $invoice->received_date }}</td>
            <td>{{ trans('admin/billing.types.' . $invoice->doc_type) }}</td>
            <td>{{ $invoice->reference }}</td>
            <td>{{ $invoice->due_date }}</td>
            <td class="text-right">
                @if($invoice->sense == 'debit')
                    {{ money($invoice->total * -1, $invoice->currency) }}
                @endif
            </td>
            <td class="text-right">
                @if($invoice->sense == 'credit')
                    {{ money($invoice->total, $invoice->currency) }}
                @endif
            </td>
            <td class="text-right" style="color: #FF0000">
                @if(!$invoice->is_settle)
                {{ money($invoice->total_unpaid, $invoice->currency) }}
                @endif
            </td>
            <td class="text-right bold">{{ money($totalBalance, $invoice->currency) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="5" style="border: none;font-size: 12px" class="text-right">
            Total ({{ $tableCount }} documentos)
        </td>
        <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($totalDebit, @$invoice->currency) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($totalCredit, @$invoice->currency) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px; color: #FF0000">{{ money($totalPending, @$invoice->currency) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalBalance, @$invoice->currency) }}</td>
    </tr>
</table>
 <h4 class="text-right m-t-0">
     &nbsp;&nbsp;&nbsp;&nbsp;
     <small>Débito: <b class="bold" style="color: #000;">{{ money($totalDebit, Setting::get('app_currency')) }}</b></small>
     &nbsp;&nbsp;&nbsp;&nbsp;
     <small>Crédito: <b class="bold" style="color: #000;">{{ money($totalCredit, Setting::get('app_currency')) }}</b></small>
     &nbsp;&nbsp;&nbsp;&nbsp;
     <small>Pendente: <b class="bold" style="color: #000;">{{ money($totalPending, Setting::get('app_currency')) }}</b></small>
     &nbsp;&nbsp;&nbsp;&nbsp;
     <small>Saldo:</small> <b class="bold">{{ money($totalBalance, Setting::get('app_currency')) }}</b>
 </h4>
 <hr style="margin-bottom: 10px"/>
 <p class="text-center">Documento gerado em {{ date('Y-m-d') }} às {{ date('H:i:s') }}</p>
