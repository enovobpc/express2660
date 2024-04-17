<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th>Movimento</th>
        <th>Data</th>
        <th>Descrição</th>
        <th>Cliente/Fornecedor</th>
        <th>Valor</th>
        <th>Método</th>
        {{--<th class="w-120px">Registo por</th>--}}
    </tr>
    <?php $documentTotal = 0; $countTotal = 0; ?>
    @foreach($movements as $movement)
        <?php
            if($movement->sense == 'credit') {
                $documentTotal+= $movement->amount;
            } else {
                $documentTotal-= $movement->amount;
            }

            $countTotal++;
        ?>
        <tr>
            <td>
                <strong style="font-weight: bold">{{ $movement->code }}</strong>
            </td>
            <td>{{ $movement->date->format('Y-m-d') }}</td>
            <td>{{ $movement->description }}</td>
            <td>
                {{ @$movement->customer->name }}<br/>
                {{ @$movement->provider->name }}
            </td>
            <td class="text-right">
                @if($movement->sense == 'credit')
                    +{{ money($movement->amount, Setting::get('app_currency')) }}
                @else
                    -{{ money($movement->amount, Setting::get('app_currency')) }}
                @endif
            </td>
            <td>{{ trans('admin/refunds.payment-methods.'.$movement->payment_method) }}</td>
            {{--<td>{{ @$movement->operator->name }}</td>--}}
        </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    <small>Saldo:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
</h4>