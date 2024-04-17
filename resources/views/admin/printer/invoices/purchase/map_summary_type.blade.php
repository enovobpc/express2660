@if(@$data)
    <h1>Resumo de despesas por tipo</h1>
    <table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
        <tr>
            <th>Tipo Despesa</th>
            <th class="w-70px text-right">Docs</th>
            <th class="w-70px text-right">Subtotal</th>
            <th class="w-70px text-right">IVA</th>
            <th class="w-70px text-right">Total</th>
        </tr>
        <?php
            $subtotal = $vat = $total = $count = 0;
        ?>
        @foreach($data as $typeId => $row)
            <?php
            $subtotal+= @$row['subtotal'];
            $total+= @$row['total'];
            $vat+= @$row['vat'];
            $count+= @$row['count'];
            ?>
            <tr>
                <td>{{ @$row['name'] }}</td>
                <td class="text-right">{{ @$row['count'] }}</td>
                <td class="text-right">{{ money(@$row['subtotal']) }}</td>
                <td class="text-right">{{ money(@$row['vat']) }}</td>
                <td class="text-right">{{ money(@$row['total']) }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="text-right" style="border: none">
                Total
            </td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ $count }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($subtotal) }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($vat) }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($total) }}</td>
        </tr>
    </table>
@endif