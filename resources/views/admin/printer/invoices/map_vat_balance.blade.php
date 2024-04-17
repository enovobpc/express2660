<h1>Mapa de Resumo Vendas</h1>
<table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
    <tr>
        <th>Taxa de IVA</th>
        <th class="w-50px text-right">Docs</th>
        <th class="w-75px text-right">Total</th>
        <th class="w-75px text-right">Incidência</th>
        <th class="w-75px text-right">IVA Receber</th>
        <th class="w-75px text-right">IVA Pagar</th>
        <th class="w-75px text-right">Valor IVA</th>
    </tr>
    <?php
    $countDocs = $total = $totalIncidence = $totalVatReceive = $totalVatPay = $totalVat = 0;
    ?>
    @foreach($data as $customerId => $row)
        <?php
        $countDocs+= $row['count'];
        $total+= $row['total'];
        $totalIncidence+= $row['incidence'];
        $totalVatReceive+= $row['vat_receive'];
        $totalVatPay+= $row['vat_pay'];
        $totalVat+= $row['vat'];
        ?>
        <tr>
            <td>{{ @$row['rate_name'] }}</td>
            <td class="text-right">{{ @$row['count'] }}</td>
            <td class="text-right">{{ money(@$row['total']) }}</td>
            <td class="text-right">{{ money(@$row['incidence']) }}</td>
            <td class="text-right">{{ money(@$row['vat_receive']) }}</td>
            <td class="text-right">{{ money(@$row['vat_pay']) }}</td>
            <td class="text-right" style="font-weight: bold">{{ money(@$row['vat']) }}</td>
        </tr>
    @endforeach
    <tr>
        <td class="text-right" style="border: none;">
            Total
        </td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ $countDocs }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($total) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalIncidence) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalVatReceive) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalVatPay) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalVat) }}</td>
    </tr>
</table>
<hr/>
<h1>Mapa de Resumo Compras</h1>
<table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
    <tr>
        <th>Taxa de IVA</th>
        <th class="w-50px text-right">Docs</th>
        <th class="w-75px text-right">Total</th>
        <th class="w-75px text-right">Incidência</th>
        <th class="w-75px text-right">IVA Receber</th>
       {{--  <th class="w-75px text-right">IVA Pagar</th> --}} {{-- Descomentar quando tivermos a distinção de iva por tipo despesa (ex: refeições não desconta iva, combustivel tambem nao, etc.)--}}
        <th class="w-75px text-right">Valor IVA</th>
    </tr>
    <?php
    $purchaseCountDocs = 
    $purchaseTotal = 
    $purchaseTotalIncidence = 
    $purchaseotalVatReceive = 
    $purchaseTotalVatPay = 
    $purchaseTotalVat = 0;
    ?>
    @foreach($purchases as $customerId => $row)
        <?php
        $purchaseCountDocs+= $row['count'];
        $purchaseTotal+= $row['total'];
        $purchaseTotalIncidence+= $row['incidence'];
        $purchaseotalVatReceive+= $row['vat_receive'];
        $purchaseTotalVatPay+= $row['vat_pay'];
        $purchaseTotalVat+= $row['vat'];
        ?>
        <tr>
            <td>{{ @$row['rate_name'] }}</td>
            <td class="text-right">{{ @$row['count'] }}</td>
            <td class="text-right">{{ money(@$row['total']) }}</td>
            <td class="text-right">{{ money(@$row['incidence']) }}</td>
            <td class="text-right">{{ money(@$row['vat_receive']) }}</td>
            {{-- <td class="text-right">{{ money(@$row['vat_pay']) }}</td> --}}
            <td class="text-right" style="font-weight: bold">{{ money(@$row['vat']) }}</td>
        </tr>
    @endforeach
    <tr>
        <td class="text-right" style="border: none;">
            Total
        </td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ $purchaseCountDocs }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($purchaseTotal) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($purchaseTotalIncidence) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($purchaseotalVatReceive) }}</td>
        {{-- <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($purchaseTotalVatPay) }}</td> --}}
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($purchaseTotalVat) }}</td>
    </tr>
</table>
<hr style="margin-bottom:10px"/>
<div style="margin-top: -6px">
    <h4 class="text-left m-t-0" style="float: right; width: 100px; text-align: right">
        <small>Balanço IVA</small><br/>
        <b class="bold">{{ money($totalVat - $purchaseotalVatReceive) }}</b>
    </h4>
    <h4 class="text-left m-t-0" style="float: right; width: 140px; text-align: right">
        <small>Balanço</small><br/>
        <b class="bold">{{ money($total - $purchaseTotal) }}</b>
    </h4>
    <h4 class="text-left m-t-0" style="float: right; width: 100px; text-align: right">
        <small>IVA Compras</small><br/>
        {{ money($purchaseotalVatReceive) }}
    </h4>
    <h4 class="text-left m-t-0" style="float: right; width: 100px; text-align: right">
        <small>Compras</small><br/>
        {{ money($purchaseTotal) }}
    </h4>
    <h4 class="text-left m-t-0" style="float: right; width: 100px; text-align: right">
        <small>IVA Vendas</small><br/>
        {{ money($totalVat) }}
    </h4>
    <h4 class="text-left m-t-0" style="float: right; width: 100px; text-align: right">
        <small>Vendas</small><br/>
        {{ money($total) }}
    </h4>
</div>