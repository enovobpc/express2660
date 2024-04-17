<h1>Resumo de Faturação e Recebimentos Anual</h1>
<table class="table table-bordered table-pdf font-size-8pt" style="border: none; margin: 0">
    <tr>
        <th>Cliente</th>
        @for($month = 1 ; $month <= 12 ; $month++)
            <th class="w-50px text-right">{{ trans('datetime.month-tiny.' . $month) }}</th>
        @endfor
        <th class="w-50px text-right">Total</th>
        {{--<th class="w-50px text-right">Docs</th>
        <th class="w-80px text-right">Faturado</th>
        <th class="w-80px text-right">Recebido</th>
        <th class="w-70px text-right">IVA</th>
        <th class="w-80px text-right">Balanço</th>--}}
    </tr>
    <?php
    $totalsMonth = [];
    ?>
    @foreach($data as $customerName => $months)
        <tr>
            <td>{{ $customerName ? $customerName : 'Sem cliente associado' }}</td>
            <?php $rowTotal = 0 ?>
            @for($month = 1 ; $month <= 12 ; $month++)
                <?php
                $key = request()->get('year', '2021') . $month;
                $totalCMonth = @$months[$key]['total'];
                $rowTotal+= $totalCMonth;
                $totalsMonth[$month] = @$totalsMonth[$month] + $totalCMonth;
                ?>
                <td class="text-right">{{ $totalCMonth ? money($totalCMonth) : '' }}</td>
                <?php unset($months[$key]['total']) ?>
            @endfor
            <td class="text-right" style="font-weight: bold">{{ money($rowTotal) }}</td>
        </tr>
    @endforeach
    <tr>
        <td class="text-right"  style="border: none">
            Total
        </td>
        <?php $rowTotal = 0 ?>
        @for($month = 1 ; $month <= 12 ; $month++)
            <?php $rowTotal+= @$totalsMonth[$month] ?>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money(@$totalsMonth[$month]) }}</td>
        @endfor
        <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($rowTotal) }}</td>
    </tr>
</table>
