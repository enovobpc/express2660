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
        {{-- <th class="w-80px">Estado</th> --}}
        <th class="w-70px">Débito</th>
        <th class="w-70px">Crédito</th>
        <th class="w-70px">Pendente</th>
        <th class="w-70px">Saldo</th>
    </tr>

    <?php $totalCredit = $totalDebit = $totalPending = 0; ?>
    @foreach ($balance as $document)
        <?php
        if ($document->sense == 'credit') {
            $totalCredit += $document->total;
        } else {
            $totalDebit += $document->total;
        }
        
        $totalBalance = $totalDebit - $totalCredit;
        
        if (!$document->is_paid && !in_array($document->doc_type, ['receipt', 'regularization'])) {
            if (!$document->pending) {
                $document->pending = $document->total;
        
                if ($document->doc_type == 'credit-note') {
                    $document->pending = $document->pending * -1;
                }
            }
            $totalPending += $document->pending;
        } else {
            $document->pending = null;
        }
        
        ?>
        <tr>
            <td class="text-center">{{ $document->date ? $document->date->format('Y-m-d') : $document->date }}</td>
            <td>{{ trans('admin/billing.types.' . $document->doc_type) }}</td>
            <td>{{ $document->doc_serie }}
                {{ $document->doc_id }}{{ $document->receipt_part ? '-' . $document->receipt_part : '' }}</td>
            <td>{{ $document->reference }}</td>
            <td>{{ $document->due_date->format('Y-m-d') }}</td>
            {{-- @if ($document->sense == 'debit' || $document->doc_type == 'credit-note')
                 @if ($document->is_paid)
                     <td class="text-center" style="color: #00a65a">Liquidado</td>
                 @else
                     <td class="text-center" style="color: #ff0000">Por Liquidar</td>
                 @endif

             @else
                 <td class="text-center"></td>
             @endif --}}
            <td class="text-right">
                {{ $document->sense == 'debit' ? money($document->total, Setting::get('app_currency')) : '' }}</td>
            <td class="text-right">
                {{ $document->sense == 'credit' ? money($document->total, Setting::get('app_currency')) : '' }}</td>
            <td class="text-right bold" style="color: red">
                @if ($document->pending)
                    {{ money($document->pending, Setting::get('app_currency')) }}
                @endif
            </td>
            <td class="text-right bold">{{ money($totalBalance, Setting::get('app_currency')) }}</td>
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
