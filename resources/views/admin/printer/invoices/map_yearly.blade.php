<h1>Resumo de Faturação e Recebimentos Anual</h1>
<table class="table table-bordered table-pdf font-size-8pt" style="border: none; margin: 0">
    <tr>
        <th class="w-30px">Ano</th>
        <th>Mês</th>
        <th class="w-50px text-right">Docs</th>
        <th class="w-80px text-right">Faturado</th>
        @if($noDocs)
            <th class="w-80px text-right">Sem Doc.</th>
            <th class="w-80px text-right">Total</th>
        @endif
        <th class="w-80px text-right">Recebido</th>
        <th class="w-70px text-right">IVA</th>
        <th class="w-80px text-right">Balanço</th>
    </tr>
    <?php
    $totalBilled = $totalReceived = $totalVat = $totalDiff = $totalNodoc = $count = 0;
    ?>
    @foreach($data as $key => $row)
        <?php
        $month = trans('datetime.month.'.$row['month']);
        $diff = @$row['received'] - (@$row['billed'] + @$row['nodoc'] + @$row['vat']);

        $count+= @$row['count'];
        $totalBilled+= @$row['billed'];
        $totalNodoc+= @$row['nodoc'];
        $totalReceived+= @$row['received'];
        $totalVat+= @$row['vat'];
        $totalDiff+= $diff;
        ?>
        <tr>
            <td>{{ $row['year'] }}</td>
            <td>{{ $month }}</td>
            <td class="text-right">{{ @$row['count'] }}</td>
            <td class="text-right">{{ money(@$row['billed']) }}</td>
            @if($noDocs)
                <td class="text-right">{{ money(@$row['nodoc']) }}</td>
                <td class="text-right">{{ money(@$row['billed'] + @$row['nodoc']) }}</td>
            @endif
            <td class="text-right">{{ money(@$row['received']) }}</td>
            <td class="text-right">{{ money(@$row['vat']) }}</td>
            <td class="text-right" style="{{ $diff >= 0.00 ? 'color: green' : 'color:red' }}">{{ money($diff) }}</td>
        </tr>
    @endforeach
    <tr>
        <td class="text-right" colspan="2" style="border: none">
            Total
        </td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ $count }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalBilled) }}</td>
        @if($noDocs)
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalNodoc) }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalBilled + $totalNodoc) }}</td>
        @endif
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalReceived) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($totalVat) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px; {{ $totalDiff >= 0.00 ? 'color: green' : 'color:red' }}">{{ money($totalDiff) }}</td>
    </tr>
</table>