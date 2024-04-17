<h1>Resumo de Faturação e Recebimentos</h1>
<table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
    <tr>
        <th style="background: #fff; border: none"></th>
        <th class="text-center" colspan="4">Faturado</th>
        @if($nodoc)
        <th class="w-70px text-right">Sem Doc.</th>
        @endif
        <th class="text-center" colspan="2">Créditos</th>
        <th class="text-center" colspan="2">Recebimentos</th>
        <th style="background: #fff; border: none"></th>
    </tr>
    <tr>
        <th>Cliente</th>
        <th class="w-70px text-right">Subtotal</th>
        <th class="w-50px text-right">IVA</th>
        <th class="w-70px text-right">Total</th>
        <th class="w-15px">Qtd</th>
        @if($nodoc)
        <th class="w-70px text-right">Sem Doc.</th>
        @endif
        <th class="w-70px text-right">Total</th>
        <th class="w-15px">Qtd</th>
        <th class="w-70px text-right">Total</th>
        <th class="w-15px">Qtd</th>
        <th class="w-70px text-right">Saldo</th>
    </tr>
   
    <?php
        $totalInvoices = $countInvoices = 0;
        $totalReceipts = $countReceipts = 0;
        $totalCredits  = $countCredits  = 0;
        $totalVat = $totalDiff = $totalNodoc = 0;
        $totalDoc = 0;
    ?>
    @foreach($data['gains'] as $customerId => $row)
        <?php

        $totalInvoices+= @$row['invoices']['total'];
        $totalVat     += @$row['invoices']['vat'];
        $countInvoices+= @$row['invoices']['count'];
        
        $totalReceipts+= @$row['receipts']['total'];
        $countReceipts = @$row['receipts']['count'];
        
        $totalCredits+= @$row['credit-notes']['total'];
        $countCredits = @$row['credit-notes']['count'];

        $totalRow = @$row['total'];
        $totalDoc+= $totalRow
        ?>
        <tr>
            <td>{{ @$row['code'] }} - {{ @$row['name'] }}</td>
            <td class="text-right">{{ money(@$row['invoices']['subtotal']) }}</td>
            <td class="text-right">{{ money(@$row['invoices']['vat']) }}</td>
            <td class="text-right">{{ money(@$row['invoices']['total']) }}</td>
            <td class="text-center">{{ number(@$row['invoices']['count'], 0) }}</td>
            @if($nodoc)
            <td class="text-right">{{ money(@$row['nodoc']['total']) }}</td>
            @endif
            <td class="text-right">{{ @$row['credit-notes']['total'] ? money(@$row['credit-notes']['total']) : '0.00' }}</td>
            <td class="text-right">{{ $countCredits}}</td>
            <td class="text-right">{{ money(@$row['receipts']['total']) }}</td>
            <td class="text-right">{{ $countReceipts }}</td>
            <td class="text-right" style="{{ $totalRow <= 0.00 ? 'color: red' : 'color:green' }}">{{ money($totalRow) }}</td>
        </tr>
    @endforeach
    <tr>
        <td class="text-right" style="border: none">
            Total
        </td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalInvoices) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalVat) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalInvoices + $totalVat) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ $countInvoices }}</td>
        @if($nodoc)
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalNodoc) }}</td>
        @endif
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalCredits) }}</td>
        <td class="text-right" style="font-size: 12px;">{{ $countCredits}}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalReceipts) }}</td>
        <td class="text-right" style="font-size: 12px;">{{ $countReceipts }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px; {{ $totalDoc <= 0.00 ? 'color: red' : 'color:green' }}">{{ money($totalDoc) }}</td>
    </tr>
</table>