@if($grouped)
    @foreach($movements as $groupName => $groupMovements)
        <h4>{{ $groupName }}</h4>
        <table class="table table-bordered table-pdf font-size-7pt" style="margin-bottom: 10px">
            <tr>
                <th style="width: 50px">Movimento</th>
                <th style="width: 60px">Data</th>
                <th>Colaborador</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th>Cliente/Fornecedor</th>
                <th style="width: 60px">Valor</th>
                <th style="width: 100px">Método</th>
                <th class="w-60px">Estado</th>
                {{--<th class="w-120px">Registo por</th>--}}
            </tr>
            <?php $documentTotal = 0; $countTotal = 0; ?>
            @foreach($groupMovements as $movement)
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
                    <td>{{ @$movement->operator->name }}</td>
                    <td>{{ $movement->description }}</td>
                    <td>{{ @$movement->type->name }}</td>
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
                    <td>{{ @$movement->paymentMethod->name }}</td>
                    <td>
                        @if($movement->is_paid)
                            Pago
                        @else
                            Não Pago
                        @endif
                    </td>
                    {{--<td>{{ @$movement->operator->name }}</td>--}}
                </tr>
            @endforeach
        </table>
        <h4 class="text-right m-t-0">
            <small>Saldo:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
        </h4>
    @endforeach
@else
<table class="table table-bordered table-pdf font-size-7pt" style="margin-bottom: 10px">
    <tr>
        <th style="width: 50px">Movimento</th>
        <th style="width: 60px">Data</th>
        <th>Colaborador</th>
        <th>Descrição</th>
        <th>Tipo</th>
        <th>Cliente/Fornecedor</th>
        <th style="width: 60px">Valor</th>
        <th style="width: 100px">Método</th>
        <th class="w-60px">Estado</th>
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
            <td>{{ @$movement->operator->name }}</td>
            <td>{{ $movement->description }}</td>
            <td>{{ @$movement->type->name }}</td>
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
            <td>{{ @$movement->paymentMethod->name }}</td>
            <td>
                @if($movement->is_paid)
                    Pago
                @else
                    Não Pago
                @endif
            </td>
            {{--<td>{{ @$movement->operator->name }}</td>--}}
        </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    <small>Saldo:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
</h4>
@endif