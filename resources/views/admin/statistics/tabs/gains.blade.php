<div class="row">
    <div class="col-sm-9">
        <div class="box-header bg-gray">
            <h3 class="box-title">Resumo de Ganhos e Despesas por tipo</h3>
        </div>
        <div class="box-body p-0">
            <table class="table table-hover table-condensed table-bordered m-0">
                <thead>
                    <tr>
                        <th class="bg-gray-light">Tipo</th>
                        <th class="w-90px text-right bg-gray-light">Ganho</th>
                        <th class="w-90px text-right bg-gray-light">Despesa</th>
                        <th class="w-90px text-right bg-gray-light">Saldo</th>
                        <th class="w-90px text-right bg-gray-light">Impacto {!! tip('Percentagem correspondente ao item, em relação ao valor total no periodo selecionado.') !!}</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $totalGain = $totalCost = $totalBalance = 0;
                ?>
                    @foreach($balanceDetails as $details)
                        <?php
                        $totalGain+=$details['gain'];
                        $totalCost+=$details['cost'];
                        $totalBalance+=$details['balance'];
                        ?>
                    <tr>
                        <td>
                            {{ $details['name'] }}
                            @if(!$details['module'])
                                <span class="label label-warning" data-toggle="tooltip" title="Não possui este módulo ativo no seu programa. Contacte-nos para mais informação.">
                                    Módulo Não Ativo <i class="fas fa-info-circle"></i>
                                </span>
                            @endif
                        </td>
                        <td class="text-right" style="{{ $details['gain'] == 0.00 ? 'color: #ddd' : '' }}">{{ in_array($details['sense'], ['all', 'gain']) ? money($details['gain']) : '--' }}</td>
                        <td class="text-right" style="{{ $details['cost'] == 0.00 ? 'color: #ddd' : '' }}">{{ in_array($details['sense'], ['all', 'cost']) ? money($details['cost']) : '--' }}</td>
                        @if($details['balance'] == 0.00)
                            <td class="text-right bold" style="color: #ddd">{{ money($details['balance']) }}</td>
                        @else
                            <td class="text-right bold {{ $details['balance'] > 0.00 ? 'text-green' : 'text-red' }}" style="{{ $details['balance'] == 0.00 ? 'color: #ddd' : '' }}">{{ money($details['balance']) }}</td>
                        @endif
                        <td class="text-right" style="{{ $details['impact'] == 0.00 ? 'color: #ddd' : '' }}">{{ number($details['impact'], 2) }}%</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th class="text-right bg-gray-light"><b>TOTAL</b></th>
                        <td class="text-right bg-gray-light"><b>{{ money($totalGain, Setting::get('app_currency')) }}</b></td>
                        <td class="text-right bg-gray-light"><b>{{ money($totalCost, Setting::get('app_currency')) }}</b></td>
                        <td class="text-right bg-gray-light {{ $totalBalance > 0.00 ? 'text-green' : 'text-red' }}"><b>{{ money($totalBalance, Setting::get('app_currency')) }}</b></td>
                        <td class="text-right bg-gray-light"><b>100,00%</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stats-main-panel">
            <div class="chart">
                <canvas id="balanceChart" height="190"></canvas>
            </div>
            <div class="text-center">
                <p>Resultado no período selecionado</p>
                @if($billingTotals['balance']['balance'] > 0.00)
                    <h1 class="text-green balance-total">
                        <b>{{ money($billingTotals['balance']['balance'], Setting::get('app_currency')) }}</b><br/>
                        <small class="text-green">
                            {{ money($billingTotals['balance']['balance_percent'], '', 0) }}% de lucro
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Correspondente a {{ money($billingTotals['balance']['balanceRelativePercent'], '', 0) }}% do volume total transacionado (faturação + despesas)"></i>
                        </small>
                    </h1>
                @else
                    <h1 class="text-red balance-total">
                        <b>{{ money($billingTotals['balance']['balance'], Setting::get('app_currency')) }}</b><br/>
                        <small class="text-red">
                            {{ money($billingTotals['balance']['balance_percent'], '', 0) }}% de prejuizo
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Correspondente a {{ money($billingTotals['balance']['balanceRelativePercent'], '', 0) }}% do volume total transacionado (faturação + despesas)"></i>
                        </small>
                    </h1>
                @endif
            </div>
        </div>
    </div>
</div>
<hr/>
<h4 class="bold">Detalhe de Ganhos e Despesas por Cliente</h4>
<div class="row">
    <div class="col-sm-12">
        <div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th class="w-45px bg-gray-light">#</th>
                        <th class="bg-gray-light">Cliente</th>
                        <th class="w-55px text-right bg-gray-light" style="border-left: 1px solid #ddd;">Envios</th>
                        <th class="w-55px text-right bg-gray-light" style="border-right: 1px solid #ddd;">Vol.</th>
                        <th class="w-85px text-right bg-gray-light" data-toggle="tooltip" title="Peso médio por expedição">Ø Peso</th>
                        <th class="w-50px text-right bg-gray-light" data-toggle="tooltip" title="Volumes médio por expedição">Ø Vol.</th>
                        <th class="w-70px text-right bg-gray-light" data-toggle="tooltip" title="Peso médio por expedição">Ø Preço</th>
                        <th class="w-70px text-right bg-gray-light" style="border-right: 1px solid #ddd;" data-toggle="tooltip" title="Custo estimado mensal para o cliente">Ø Custo</th>
                        <th class="w-70px text-right bg-gray-light" style="border-right: 1px solid #ddd;">Avenças</th>
                        <th class="w-70px text-right bg-gray-light" style="border-right: 1px solid #ddd;">Outros</th>
                        <th class="w-90px text-right bg-gray-light">Total</th>
                        <th class="w-85px text-right bg-gray-light" style="border-right: 1px solid #ddd;">Custo</th>
                        <th class="w-150px text-right bg-gray-light">Resultado</th>
                    </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 220px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                        <?php
                        $count = 0;
                        $volumes = 0;
                        $totalPrice = 0;
                        $totalCost = 0;
                        $totalWeightAvg = 0;
                        $totalVolumesAvg = 0;
                        $totalPriceAvg = 0;
                        $totalCostAvg = 0;
                        $covenants = 0;
                        $countCostAvg = 0;
                        $i = 0;

                        $allProducts    = $allCovenants->groupBy('customer_id');
                        $allCovenants   = $allCovenants->groupBy('customer_id');
                        $totalCovenants = $allCovenants->count();
                        ?>
                        @foreach($customerShipments as $customerName => $shipment)
                            <?php
                            $customer = $shipment['customerData'];

                            if(!@$customer) {
                                $customer = new \App\Models\Customer();
                                $customer->name = 'Sem cliente associado';
                            } else {
                                $covenantTotal = @$allCovenants[$customer->id] ? $allCovenants[$customer->id]->sum('amount') : 0;

                                $shipment['cost']+= @$customer->avg_cost;

                                $count+= $shipment['count'];
                                $volumes+= $shipment['volumes'];
                                $totalPrice+= $shipment['total'];
                                $total = $shipment['total'] + $covenantTotal;
                                $totalCost+= $shipment['cost'];
                                $profit = $shipment['total'] - $shipment['cost'];
                                $covenants+=$covenantTotal;

                                $totalWeightAvg+= $shipment['weight_avg'];
                                $totalVolumesAvg+= $shipment['volumes_avg'];
                                $totalPriceAvg+= $shipment['price_avg'];
                                if($customer->avg_cost > 0.00) {
                                    $countCostAvg++;
                                    $totalCostAvg+= @$customer->avg_cost;
                                }
                            }

                            $i++;
                            ?>
                            <tr data-vol="{{ $shipment['volumes'] }}" data-count="{{ $shipment['count'] }}" data-total="{{ $shipment['total'] }}">
                                <td class="w-45px"><span class="badge">{{ $i }}</span></td>
                                <td>{{ @$customer->name }}</td>
                                <td class="w-55px text-right" style="border-left: 1px solid #ddd;">{{ @$shipment['count'] }}</td>
                                <td class="w-55px text-right" style="border-right: 1px solid #ddd;">{{ @$shipment['volumes'] }}</td>
                                <td class="w-85px text-right">{{ @$shipment['weight_avg'] }}</td>
                                <td class="w-50px text-right">{{ @$shipment['volumes_avg'] }}</td>
                                <td class="w-70px text-right">{{ @$shipment['price_avg'] }}</td>
                                <td class="w-70px text-right" style="border-right: 1px solid #ddd; color:{{ $customer->avg_cost == 0.00 ? '#ddd' : '' }}">{{ money(@$customer->avg_cost) }}</td>
                                <td class="w-70px text-right" style="border-right: 1px solid #ddd;">{{ money($covenantTotal) }}</td>
                                <td class="w-70px text-right" style="border-right: 1px solid #ddd;">0</td>
                                <td class="w-90px text-right bold">{{ money($total) }}</td>
                                <td class="w-85px text-right" style="border-right: 1px solid #ddd;">{{ money($shipment['cost']) }}</td>
                                <td class="w-150px text-right bold">
                                    @if($profit >= 0)
                                        <span class="text-green">
                                        <i class="fas fa-caret-up"></i> <b>{{ money($profit, Setting::get('app_currency')) }}</b>
                                            @if($shipment['total'] != 0.00)
                                                ({{ money(($profit * 100) / $shipment['total'], '', 0) }}%)
                                            @else
                                            (0%)
                                            @endif
                                    </span>
                                    @else
                                        <span class="text-red">
                                        <i class="fas fa-caret-down"></i> <b>{{ money($profit, Setting::get('app_currency')) }}</b>
                                            @if($shipment['total'] != 0.00)
                                                ({{ money(100-(($shipment['total'] * 100) / $profit), '', 0) }}%)
                                            @else
                                            (0%)
                                            @endif
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th class="bg-gray-light">Totais</th>
                        <td class="w-55px text-right bg-gray-light" style="border-left: 1px solid #ddd;">{{ $count }}</td>
                        <td class="w-55px text-right bg-gray-light" style="border-right: 1px solid #ddd;">{{ $volumes }}</td>
                        <td class="w-85px text-right bg-gray-light">{{ number($i ? $totalWeightAvg/$i : 0) }}Kg</td>
                        <td class="w-50px text-right bg-gray-light">{{ number($i ? $totalVolumesAvg/$i : 0) }}</td>
                        <td class="w-70px text-right bg-gray-light">{{ money($i ? $totalPriceAvg/$i : 0, Setting::get('app_currency')) }}</td>
                        <td class="w-70px text-right bg-gray-light" style="border-right: 1px solid #ddd;">{{ money($countCostAvg ? $totalCostAvg/$countCostAvg : 0, Setting::get('app_currency')) }}</td>
                        <td class="w-70px text-right bg-gray-light" style="border-right: 1px solid #ddd;">{{ money($covenants, Setting::get('app_currency')) }}</td>
                        <td class="w-70px text-right bg-gray-light" style="border-right: 1px solid #ddd;">{{ money(0, Setting::get('app_currency')) }}</td>
                        <td class="w-90px text-right bg-gray-light bold">{{ money($totalPrice, Setting::get('app_currency')) }}</td>
                        <td class="w-85px text-right bg-gray-light" style="border-right: 1px solid #ddd;">{{ money($totalCost, Setting::get('app_currency')) }}</td>
                        <td class="w-150px text-right bg-gray-light bold">
                            <?php $profit = $totalPrice - $totalCost ?>
                            @if($profit >= 0)
                                <span class="text-green">
                                        <i class="fas fa-caret-up"></i> <b>{{ money($profit, Setting::get('app_currency')) }}</b>
                                    @if($totalPrice != 0.00)
                                        ({{ money(($profit * 100) / $totalPrice, '', 0) }}%)
                                    @else
                                    (0%)
                                    @endif
                                    </span>
                            @else
                                <span class="text-red">
                                        <i class="fas fa-caret-down"></i> <b>{{ money($profit, Setting::get('app_currency')) }}</b>
                                    @if($totalPrice != 0.00)
                                        ({{ money(100-(($totalPrice * 100) / $profit), '', 0) }}%)
                                    @else
                                    (0%)
                                    @endif
                                    </span>
                            @endif
                        </td>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<hr/>
<h4 class="bold">Detalhe de Ganhos e Despesas por Fornecedor</h4>
<div class="row">
    <div class="col-sm-12">
        <div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Fornecedor</th>
                            <th class="w-55px text-right bg-gray-light" style="border-left: 1px solid #ddd">Envios</th>
                            <th class="w-55px text-right bg-gray-light">Vol.</th>
                            <th class="w-85px text-right bg-gray-light" style="border-left: 1px solid #ddd" data-toggle="tooltip" title="Peso médio por expedição">Ø Peso</th>
                            <th class="w-55px text-right bg-gray-light" data-toggle="tooltip" title="Volumes médio por expedição">Ø Vol.</th>
                            <th class="w-70px text-right bg-gray-light" data-toggle="tooltip" title="Peso médio por expedição">Ø Preço</th>
                            <th class="w-85px text-right bg-gray-light" style="border-left: 1px solid #ddd">Despesa</th>
                            <th class="w-85px text-right bg-gray-light">Ganhos</th>
                            <th class="w-150px text-right bg-gray-light" style="border-left: 1px solid #ddd">Resultado</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; max-height: 220px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                        <?php
                        $count = 0;
                        $volumes = 0;
                        $totalPrice = 0;
                        $totalCost = 0;
                        $billingTotalsum = 0;

                        $totalWeightAvg = 0;
                        $totalVolumesAvg = 0;
                        $totalPriceAvg = 0;

                        $i = 0;
                        ?>
                        @foreach($billingProviders as $providerName => $shipment)
                            <?php
                            $count+= $shipment['count'];
                            $volumes+= $shipment['volumes'];
                            $totalPrice+= ($shipment['total'] + @$shipment['total_expenses']);
                            $totalCost+= $shipment['cost'];
                            $profit = $shipment['total'] - @$shipment['cost'];

                            $totalWeightAvg+= $shipment['weight_avg'];
                            $totalVolumesAvg+= $shipment['volumes_avg'];
                            $totalPriceAvg+= $shipment['cost_avg'];
                            if($customer->avg_cost > 0.00) {
                                $countCostAvg++;
                                $totalCostAvg+= @$customer->avg_cost;
                            }

                            $i++;
                            ?>
                            <tr>
                                <td style="white-space: nowrap;">
                                    <i class="fas fa-square" style="color: {{ @$shipment['color'] }}"></i>
                                    {{ $providerName  ? $providerName : 'Sem fornecedor' }}
                                </td>
                                <td class="w-55px text-right" style="border-left: 1px solid #ddd">{{ $shipment['count'] }}</td>
                                <td class="w-55px text-right">{{ $shipment['volumes'] }}</td>
                                <td class="w-85px text-right" style="border-left: 1px solid #ddd">{{ @$shipment['weight_avg'] }}</td>
                                <td class="w-55px text-right">{{ @$shipment['volumes_avg'] }}</td>
                                <td class="w-70px text-right">{{ @$shipment['cost_avg'] }}</td>
                                <td class="w-85px text-right" style="border-left: 1px solid #ddd">{{ money($shipment['cost']) }}</td>
                                <td class="w-85px text-right">{{ money($shipment['total']) }}</td>
                                <td class="w-150px text-right bold" style="border-left: 1px solid #ddd">
                                    @if($profit >= 0)
                                    <span class="text-green">
                                        <i class="fas fa-caret-up"></i> <b>{{ money($profit, Setting::get('app_currency')) }}</b>
                                        @if($shipment['total'] != 0.00)
                                            ({{ money(($profit * 100) / $shipment['total'], '', 0) }}%)
                                        @else
                                        (0%)
                                        @endif
                                    </span>
                                    @else
                                    <span class="text-red">
                                        <i class="fas fa-caret-down"></i> <b>{{ money($profit, Setting::get('app_currency')) }}</b>
                                        @if($shipment['total'] != 0.00)
                                            ({{ money(($shipment['total'] * 100) / $profit, '', 0) }}%)
                                        @else
                                        (0%)
                                        @endif
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th class="bg-gray-light">Totais</th>
                        <th class="w-55px text-right bg-gray-light" style="border-left: 1px solid #ddd">{{ $count }}</th>
                        <th class="w-55px text-right bg-gray-light">{{ $volumes }}</th>
                        <td class="w-85px text-right bg-gray-light" style="border-left: 1px solid #ddd">{{ number(empty($i) ? 0 : $totalWeightAvg/$i) }}Kg</td>
                        <td class="w-55px text-right bg-gray-light">{{ number(empty($i) ? 0 : $totalVolumesAvg/$i) }}</td>
                        <td class="w-70px text-right bg-gray-light">{{ money(empty($i) ? 0 : $totalPriceAvg/$i, Setting::get('app_currency')) }}</td>
                        <th class="w-85px text-right bg-gray-light" style="border-left: 1px solid #ddd">{{ money($totalCost, Setting::get('app_currency')) }}</th>
                        <th class="w-85px text-right bg-gray-light">{{ money($totalPrice, Setting::get('app_currency')) }}</th>
                        <th class="w-150px text-right bg-gray-light" style="border-left: 1px solid #ddd">
                            <?php $totalProfit = ($totalPrice - $totalCost); ?>
                            @if($totalProfit >= 0)
                                <span class="text-green">
                                    <i class="fas fa-caret-up"></i> <b>{{ money($totalProfit, Setting::get('app_currency')) }}</b>
                                    @if($totalPrice != 0.00)
                                        ({{ money(($totalProfit * 100) / $totalPrice, '', 0) }}%)
                                    @else
                                    (0%)
                                    @endif
                                </span>
                            @else
                                <span class="text-red">
                                    <i class="fas fa-caret-down"></i> <b>{{ money($totalProfit, Setting::get('app_currency')) }}</b>
                                    @if($totalPrice != 0.00)
                                        ({{ money(($totalPrice * 100) / $totalProfit, '', 0) }}%)
                                    @else
                                    (0%)
                                    @endif
                                </span>
                            @endif
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>