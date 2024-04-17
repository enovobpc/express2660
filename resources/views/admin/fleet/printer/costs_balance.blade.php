<div>
    @if($vehicles)
        <table class="table table-bordered table-pdf m-b-5">
            <tr>
                <th>@trans('Viatura')</th>
                <th class="w-65px">@trans('Data Início')</th>
                <th class="w-60px">@trans('Data Fim')</th>
                <th class="w-65px">@trans('Combustível')</th>
                <th class="w-65px">@trans('Manutenções')</th>
                <th class="w-65px">@trans('Portagens')</th>
                <th class="w-65px">@trans('Motoristas')</th>
                <th class="w-100px">@trans('Despesas Gerais')</th>
                <th class="w-105px">@trans('Despesas Fixas')</th>
                <th class="w-105px">@trans('Outras Despesas')</th>
                <th class="w-90px" style="border-left: 2px solid #000">@trans('Total Despesas')</th>
                <th class="w-90px">@trans('Total Ganhos')</th>
                <th class="w-75px">@trans('Saldo')</th>
            </tr>
            @foreach($vehicles as $vehicle)
            <tr>
                <td>{{ $vehicle['license_plate'] }}</td>
                <td>{{ $startDate }}</td>
                <td>{{ $endDate }}</td>
                <td class="text-right">{{ money($vehicle['fuel']) }}</td>
                <td class="text-right">{{ money($vehicle['maintenances']) }}</td>
                <td class="text-right">{{ money($vehicle['tolls']) }}</td>
                <td class="text-right">{{ money($vehicle['operators']) }}</td>
                <td class="text-right">{{ money($vehicle['expenses']) }}</td>
                <td class="text-right">{{ money($vehicle['fixed']) }}</td>
                <td class="text-right">{{ money($vehicle['others']) }}</td>
                <td class="text-right" style="border-left: 2px solid #000">{{ money($vehicle['total_expenses']) }}</td>
                <td class="text-right">{{ money($vehicle['total_gains']) }}</td>
                <td class="text-right">
                    @if($vehicle['total_balance'] >= 0.00)
                        <span style="color: #00a623">{{ money($vehicle['total_balance']) }}</span>
                    @else
                        <span style="color: red">{{ money($vehicle['total_balance']) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        <div class="clearfix"></div>
    @endif
</div>