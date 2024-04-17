<h4 style="margin-bottom: 3px; font-size: 16px; font-weight: bold">
    {{ $customer->code }} - {{ $customer->billing_name }}
</h4>
<p style="font-size: 12px">
    Contribuinte: {{ strtoupper($customer->billing_country) }} {{ $customer->vat }}
    &bull;
    Pagamento: {{ @$customer->paymentCondition->name }}
    &bull;
    Contacto: {{ $customer->mobile ?? ($customer->phone ?? '') }}
</p>
<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th class="w-50px">Data</th>
        <th class="w-80px">Tipo</th>
        <th class="w-100px">Documento</th>
        <th>Referência</th>
        <th class="w-75px">Vencimento</th>
        <th class="w-70px text-right">Subtotal</th>
        <th class="w-70px text-right">Total</th>
        <th class="w-70px text-right">Pendente</th>
        <th class="w-70px text-right">Saldo</th>
    </tr>

    <?php $totalCredit = $totalDebit = $totalPending = $totalBalance = 0; ?>
    @foreach ($invoices as $invoice)
        <?php
        $totalDebit   += $invoice->doc_total_debit;
        $totalCredit  += $invoice->doc_total_credit;
        $totalPending += $invoice->doc_total_pending;
        $totalBalance += $invoice->doc_total_balance;
        
        ?>
        <tr>
            <td class="text-center">{{ $invoice->doc_date }}</td>
            <td>{{ trans('admin/billing.types.' . $invoice->doc_type) }}</td>
            <td>{{ $invoice->doc_serie }}
                {{ $invoice->name }}</td>
            <td>{{ $invoice->reference }}</td>
            <td class="text-center">{{ $invoice->due_date }}</td>
            <td class="text-right">{{ money($invoice->doc_subtotal, $currency) }}</td>
            <td class="text-right">{{ money($invoice->doc_total, $currency) }}</td>
            <td class="text-right bold" style="color: red">
                @if(!$invoice->is_settle)
                {{ money($invoice->doc_pending, $currency) }}
                @endif
            </td>
            <td class="text-right bold">{{ money($invoice->customer_balance, $currency) }}</td>
        </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Débito: <b class="bold"
            style="color: #000;">{{ money($totalDebit, Setting::get('app_currency')) }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Crédito: <b class="bold"
            style="color: #000;">{{ money($totalCredit, Setting::get('app_currency')) }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Pendente: <b class="bold"
            style="color: #000;">{{ money($totalPending, Setting::get('app_currency')) }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Saldo:</small> <b class="bold">{{ money($totalBalance, Setting::get('app_currency')) }}</b>
</h4>
<hr style="margin-bottom: 10px" />
<p class="text-center">Documento gerado em {{ date('Y-m-d') }} às {{ date('H:i:s') }}</p>
