<div class="pull-left">
    <a href="{{ route('admin.trips.expenses.create', $trip->id) }}"
        class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
        <i class="fas fa-plus"></i> @trans('Novo')
    </a>
</div>
<div class="clearfix"></div>
<table class="table table-condensed table-hover table-dashed m-t-10 m-b-0">
    <thead>
        <tr>
            <th class="bg-gray w-85px">@trans('Veículo')</th>
            <th class="bg-gray w-85px">@trans('Data')</th>
            <th class="bg-gray w-160px">@trans('Tipo')</th>
            <th class="bg-gray">@trans('Descrição')</th>
            <th class="bg-gray w-125px">@trans('Operador')</th>
            <th class="bg-gray w-50px">@trans('Total')</th>
            <th class="bg-gray w-5px"></th>
        </tr>
    </thead>
    <tbody>
        @if(@$trip->shipments->sum('cost_billing_subtotal') > 0.00)
        <tr>
            <td>{{ $trip->vehicle }}</td>
            <td>{{ $trip->start_date }}</td>
            <td>@trans('Subcontratos')</td>
            <td>@trans('Subcontratação de serviços') {{ $trip->provider_id ? ' ('.@$trip->provider->name.')' : '' }}</td>
            <td></td>
            <td class="bold text-right">{{ money(@$trip->shipments->sum('cost_billing_subtotal'), Setting::get('app_currency')) }}</td>
            <td></td>
        </tr>
        @endif
        <tr>
            <td>{{ $trip->vehicle }}</td>
            <td>{{ $trip->start_date }}</td>
            <td>Consumo</td>
            <td>@trans('Consumo combustível') ({{ money($trip->kms) }}km, {{ $trip->fuel_consumption }}lt/100)</td>
            <td>{{ @$trip->operator->name }}</td>
            <td class="bold text-right">{{ money($trip->calcCostConsumption(), Setting::get('app_currency')) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $trip->vehicle }}</td>
            <td>{{ $trip->start_date }}</td>
            <td>Ordenado</td>
            <td>@trans('Custos ordenado motorista') ({{ $trip->duration_hours }}h viajadas; valor hora: {{ money(@$trip->operator->salary_value_hour, Setting::get('app_currency')) }})</td>
            <td>{{ @$trip->operator->name }}</td>
            <td class="bold text-right">{{ money(@$trip->calcCostSalary(), Setting::get('app_currency')) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $trip->vehicle }}</td>
            <td>{{ $trip->start_date }}</td>
            <td>Ordenado</td>
            <td>@trans('Ajudas de custo')</td>
            <td>{{ @$trip->operator->name }}</td>
            <td class="bold text-right">{{ money($trip->allowances_price, Setting::get('app_currency')) }}</td>
            <td></td>
        </tr>
    <?php
    $totalPrice   = @$trip->calcCostSalary() + $trip->calcCostConsumption() + @$trip->shipments->sum('cost_billing_subtotal') + $trip->allowances_price;
    $rowId        = 0;
    ?>
    @if(!$expenses->isEmpty())
    @foreach($expenses as $key => $expense)
        <?php
        $totalPrice+= $expense->total;
        $rowId++;
        ?>
        <tr data-id="{{ $expense->id }}" style="line-height: 15px;">
            <td>{{ $trip->vehicle }}</td>
            <td>{{ $expense->created_at->format('Y-m-d') }}</td>
            <td>{{ $expense->type_text }}</td>
            <td>
                {{ $expense->description ? : 'N/A' }}
                @if ($expense->purchase_invoice_id)
                    (Fatura {{ $expense->purchase_invoice->reference }})
                @endif
            </td>
            <td>{{ $expense->operator->name ?? '' }}</td>
            <td class="bold text-right">{{ money($expense->total, Setting::get('app_currency')) }}</td>
            <td class="text-center">
                @if ($expense->type == 'other' && empty($expense->purchase_invoice_id))
                <div class="btn-group btn-group-xs">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Opções Extra</span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="{{ route('admin.trips.expenses.edit', [$expense->trip_id, $expense->id]) }}"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <i class="fas fa-fw fa-pencil-alt"></i> @trans('Editar')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.trips.expenses.destroy', [$expense->trip_id, $expense->id]) }}" data-method="delete"
                               data-confirm="@trans('Confirma a remoção do registo selecionado?')" 
                               class="text-red">
                                <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
                            </a>
                        </li>
                    </ul>
                </div>
                @else
                <div></div>
                @endif
            </td>
        </tr>
    @endforeach
    @endif
    </tbody>
</table>

<div class="row">
    <div class="col-sm-12">
        <div class="shipments-totals">
            <ul class="list-inline pull-right text-right">
                <li>
                    <h4>
                        <small>@trans('Total')</small><br/>
                        {{ money($trip->cost_billing_subtotal, Setting::get('app_currency')) }}
                    </h4>
                </li>
            </ul>
        </div>
    </div>
</div>