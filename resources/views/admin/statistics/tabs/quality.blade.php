<div class="row">
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-8 p-r-0">
                <div class="row row-5">
                    <div class="col-sm-6 col-xs-12">
                        <div class="info-box info-box-sm">
                        <span class="info-box-icon bg-blue">
                            <i class="fas fa-shipping-fast"></i>
                        </span>
                            <div class="info-box-content" data-toggle="tooltip" title="Valor total de envios no periodo selecionado, independente do estado final do mesmo.">
                                <span class="info-box-text">Total Envios</span>
                                <span class="info-box-number">
                                {{ @$billingTotals['shipments']['count'] }}
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="info-box info-box-sm">
                        <span class="info-box-icon bg-green">
                            <i class="fas fa-check"></i>
                        </span>
                            <div class="info-box-content" data-toggle="tooltip" title="Total de envios entregues à primeira tentativa sem ocorrencia de incidências.">
                                <span class="info-box-text">Entregas S/ Incidência</span>
                                <span class="info-box-number">
                                {{ @$billingTotals['shipments']['deliveries']['count'] }}<br/>
                                <small>{{ number(@$billingTotals['shipments']['deliveries']['percent'], 2) }}% do total</small>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="info-box info-box-sm">
                        <span class="info-box-icon bg-red">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                            <div class="info-box-content" data-toggle="tooltip" title="Total de envios que tiveram uma ou mais incidências.">
                                <span class="info-box-text">Incidências</span>
                                <span class="info-box-number">
                                {{ @$billingTotals['shipments']['incidences']['count'] }}<br/>
                                <small>{{ number(@$billingTotals['shipments']['incidences']['percent'], 2) }}% do total</small>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="info-box info-box-sm">
                        <span class="info-box-icon bg-yellow">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                            <div class="info-box-content" data-toggle="tooltip" title="Total de envios devolvidos no período selecionado.">
                                <span class="info-box-text">Devoluções</span>
                                <span class="info-box-number">
                                {{ @$billingTotals['shipments']['devolutions']['count'] }}<br/>
                                <small>{{ number(@$billingTotals['shipments']['devolutions']['percent'], 2) }}% do total</small>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="chart">
                    <canvas id="qualityShipmentsChart" height="190"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-8 p-r-0">
                <div class="row row-5">
                    <div class="col-sm-6 col-xs-12">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-purple">
                                <i class="fas fa-user-plus"></i>
                            </span>
                            <div class="info-box-content" data-toggle="tooltip" title="Total de fichas de cliente abertas no período selecionado, com ou sem envios.">
                                <span class="info-box-text">Clientes Novos</span>
                                <span class="info-box-number">
                                    {{ @$customersTotals['new']['count'] }}<br/>
                                    <small>{{ @$customersTotals['total']['count'] }} clientes total</small>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xs-12">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-olive">
                                <i class="fas fa-user-check"></i>
                            </span>
                            <div class="info-box-content" data-toggle="tooltip" title="Total de clientes que realizaram um ou mais envios no perído selecionado.">
                                <span class="info-box-text">Clientes Ativos</span>
                                <span class="info-box-number">
                                    {{ @$customersTotals['active']['count'] }}<br/>
                                    <small>{{ number(@$customersTotals['active']['percent'], 2) }}% do total</small>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-orange">
                                <i class="fas fa-user-times"></i>
                            </span>
                            <div class="info-box-content" data-toggle="tooltip" title="Total de clientes que no período selecionado já não fazem envios à {{ Setting::get('alert_max_days_without_shipments') }} dias.">
                                <span class="info-box-text">Clientes Sem Atividade</span>
                                <span class="info-box-number">
                                    {{ @$customersTotals['inactive']['count'] }}<br/>
                                    <small>{{ number(@$customersTotals['inactive']['percent'], 2) }}% do total</small>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-red">
                                <i class="fas fa-thumbs-down"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Reclamações</span>
                                <span class="info-box-number">
                                    @if(hasModule('customer_support'))
                                        {{ $totalClaims }}
                                    @else
                                        Módulo Não Ativo
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="chart">
                    <canvas id="customersChart" height="170"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<hr/>

<div class="row">
    <div class="col-sm-3">
        <h3 class="m-t-0 m-b-20">Médias no período</h3>
        <div class="row row-5">
            <div class="col-sm-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-aqua">
                        <i class="fas fa-weight"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Peso Médio/Envio</span>
                        <span class="info-box-number">
                            {{ number(@$billingTotals['shipments']['weight_avg'], 2) }}KG
                            <br/>
                            <small>{{ @money($billingTotals['shipments']['weight']) }}KG no total</small>
                        </span>
                    </div>
                </div>
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-teal">
                        <i class="fas fa-box-open"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Média Envios</span>
                        <span class="info-box-number lh-1-0">
                            {{ ceil(@$shipmentsByDay['avg']['volumes']) }} <small>volumes/dia</small>
                            <br/>
                            {{ ceil(@$shipmentsByDay['avg']['shipments']) }} <small>envios/dia</small>
                        </span>
                    </div>
                </div>
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-aqua-active">
                        <i class="fas fa-euro-sign"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Preço Médio/Envio</span>
                        <span class="info-box-number">
                        {{ money(@$billingTotals['shipments']['price_avg'], Setting::get('app_currency')) }}
                        <br/>
                        <small>{{ money(@$shipmentsByDay['avg']['price'], Setting::get('app_currency')) }} no total</small>
                    </span>
                    </div>
                </div>
                {{--<div class="info-box info-box-sm">
                    <span class="info-box-icon bg-teal-active">
                        <i class="fas fa-user"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Média/Operador</span>
                        <span class="info-box-number lh-1-0">
                            {{ @$billingTotals['operators']['volumes_avg'] }} <small>Volumes</small><br/>
                            {{ @$billingTotals['operators']['weight_avg'] }} <small>Kg</small>
                        </span>
                    </div>
                </div>--}}
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <h3 class="m-t-0 m-b-20">Dados por operador</h3>
        <div class="chart">
            <canvas id="operatorAverage" height="280"></canvas>
        </div>
    </div>
</div>
<hr class="m-t-0"/>
<div class="row row-5">
    <div class="col-sm-3">
        <div class="box-header bg-gray">
            <h4 class="box-title">Incidências por tipo</h4>
        </div>
        <div class="box-body p-0">
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light" colspan="2">Motivo Incidência</th>
                            <th class="bg-gray-light w-60px">Total</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 220px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                        <?php $count = $i = 0;?>
                        @foreach($incidencesTypes['types'] as $incidence => $total)
                            <?php $count+= $total; $i++ ?>
                            <tr>
                                <td class="w-50px"><span class="badge">{{ $i }}</span></td>
                                <td>{{ $incidence ? $incidence : 'Sem motivo especificado' }}</td>
                                <td class="w-60px text-center">{{ $total }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th class="bg-gray-light"></th>
                        <td class="text-right bg-gray-light"><b>TOTAL</b></td>
                        <td class="w-60px text-center bg-gray-light"><b>{{ $count }}</b></td>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="row">
            <div class="col-sm-4 text-center">
                <h4 class="m-t-0 bold">Incidências por Fornecedor</h4>
                <div class="chart">
                    <canvas id="incidencesByProvider" height="220"></canvas>
                </div>
                <a href="{{ route('admin.statistics.incidences.details', ['source' => 'providers', 'datemin' => $startDate, 'datemax' => $endDate]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote"
                   class="btn btn-sm btn-primary m-t-20">Ver Detalhes</a>
            </div>
            <div class="col-sm-4 text-center">
                <h4 class="m-t-0 bold">Incidências por Cliente <small>TOP 6</small></h4>
                <div class="chart">
                    <canvas id="incidencesByCustomer" height="220"></canvas>
                </div>
                <a href="{{ route('admin.statistics.incidences.details', ['source' => 'customers', 'datemin' => $startDate, 'datemax' => $endDate]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote"
                   class="btn btn-sm btn-primary m-t-20">Ver Detalhes</a>
            </div>
            <div class="col-sm-4 text-center">
                <h4 class="m-t-0 bold">Incidências por Serviço</h4>
                <div class="chart">
                    <canvas id="incidencesByService" height="220"></canvas>
                </div>
                <a href="{{ route('admin.statistics.incidences.details', ['source' => 'services', 'datemin' => $startDate, 'datemax' => $endDate]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote"
                   class="btn btn-sm btn-primary m-t-20">Ver Detalhes</a>
            </div>
        </div>
    </div>
</div>
