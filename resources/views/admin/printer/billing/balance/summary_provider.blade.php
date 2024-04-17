<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th>Fornecedor</th>
        <th class="w-70px">Ult. Fatura</th>
        <th class="w-70px">Pagamento</th>
        <th class="w-50px">Vencidos</th>
        <th class="w-70px">Débito</th>
        <th class="w-70px">Crédito</th>
        <th class="w-70px">Saldo</th>
    </tr>

    <?php $docDebit = $docCredit = $docBalance = 0; ?>
    @foreach($providers as $provider)
        <?php
        $balance = ($provider->debit * -1) - $provider->credit;

        $docDebit+= ($provider->debit * -1);
        $docCredit+= $provider->credit;
        $docBalance+= $balance;


        ?>
        <tr>
            <td>{{ @$provider->code }} - {{ @$provider->company ? @$provider->company : @$provider->name }}</td>
            <td>{{ $provider->last_invoice }}</td>
            <td>{{ @$provider->paymentCondition->name }}</td>
            <td class="text-center">{{ @$provider->balance_count_expired ? $provider->balance_count_expired : '' }}</td>
            <td class="text-right">{{ money($provider->debit * -1, Setting::get('app_currency')) }}</td>
            <td class="text-right">{{ money($provider->credit, Setting::get('app_currency')) }}</td>
            <td class="text-right bold">
                @if($balance < 0.00)
                    <span style="color: red">{{ money($balance, Setting::get('app_currency')) }}</span>
                @elseif($balance > 0.00)
                    <span style="color: #4bb200">{{ money($balance, Setting::get('app_currency')) }}</span>
                @else
                    <span>{{ money(0, Setting::get('app_currency')) }}</span>
                @endif
            </td>
        </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Débito: <b class="bold" style="color: #000;">{{ money($docDebit, Setting::get('app_currency')) }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Crédito: <b class="bold" style="color: #000;">{{ money($docCredit, Setting::get('app_currency')) }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Saldo: <b class="bold" style="color: #000;">{{ money($docBalance, Setting::get('app_currency')) }}</b></small>
</h4>
<hr style="margin-bottom: 10px"/>
<p class="text-center">Documento gerado em {{ date('Y-m-d') }} às {{ date('H:i:s') }}</p>
