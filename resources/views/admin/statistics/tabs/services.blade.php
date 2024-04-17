<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box info-box-sm">
            <span class="info-box-icon bg-purple"><i class="fas fa-euro-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total no Periodo</span>
                <span class="info-box-number">
                    {{ money($billingTotals['shipments']['total'], Setting::get('app_currency')) }}<br />
                    <small class="text-muted">{{ $billingTotals['shipments']['count'] }} Envios,
                        {{ $billingTotals['shipments']['count'] }} Volumes</small>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box info-box-sm">
            <span class="info-box-icon bg-blue">
                <i class="fas fa-flag"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Nacional</span>
                <span class="info-box-number">
                    {{ money($billingTotals['nacional']['total'], Setting::get('app_currency')) }}<br />
                    <small class="text-muted">{{ $billingTotals['nacional']['count'] }} Envios,
                        {{ $billingTotals['nacional']['count'] }} Volumes</small>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box info-box-sm">
            <span class="info-box-icon bg-green">
                <i class="fas fa-sign-out-alt"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Exportações</span>
                <span class="info-box-number">
                    {{ money($billingTotals['imports']['total'], Setting::get('app_currency')) }}<br />
                    <small class="text-muted">{{ $billingTotals['imports']['count'] }} Envios,
                        {{ $billingTotals['imports']['count'] }} Volumes</small>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box info-box-sm">
            <span class="info-box-icon bg-yellow">
                <i class="fas fa-sign-in-alt"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Importações</span>
                <span class="info-box-number">
                    {{ money($billingTotals['exports']['total'], Setting::get('app_currency')) }}<br />
                    <small class="text-muted">{{ $billingTotals['exports']['count'] }} Envios,
                        {{ $billingTotals['exports']['count'] }} Volumes</small>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="m-b-15">
            <div class="box-header bg-gray">
                <h3 class="box-title">Histórico Nacional</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Localidade de Destino</th>
                            <th class="w-50px text-right bg-gray-light">Envios</th>
                            <th class="w-50px text-right bg-gray-light">Vol.</th>
                            <th class="w-80px text-right bg-gray-light">Valor</th>
                            <th class="w-60px text-right bg-gray-light">Enc.</th>
                            <th class="w-80px text-right bg-gray-light">Total</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 215px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                            <?php
                            $count = 0;
                            $volumes = 0;
                            $totalPrice = 0;
                            $totalExpenses = 0;
                            $billingTotalsum = 0;
                            ?>
                            @foreach ($nacionalShipments as $zipCode => $shipment)
                                <?php
                                $count += $shipment['count'];
                                $volumes += $shipment['volumes'];
                                $totalPrice += $shipment['price'];
                                $totalExpenses += $shipment['expenses'];
                                $billingTotalsum += $shipment['total'];
                                ?>
                                <tr>
                                    <td>{{ $zipCode }}</td>
                                    <td class="w-50px text-right">{{ $shipment['count'] }}</td>
                                    <td class="w-50px text-right">{{ $shipment['volumes'] }}</td>
                                    <td class="w-80px text-right">{{ money($shipment['price']) }}</td>
                                    <td class="w-60px text-right">{{ money($shipment['expenses']) }}</td>
                                    <td class="w-80px text-right bold">{{ money($shipment['total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Totais</th>
                            <th class="w-50px text-right bg-gray-light">{{ $count }}</th>
                            <th class="w-50px text-right bg-gray-light">{{ $volumes }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($totalPrice, Setting::get('app_currency')) }}</th>
                            <th class="w-60px text-right bg-gray-light">
                                {{ money($totalExpenses, Setting::get('app_currency')) }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($billingTotalsum, Setting::get('app_currency')) }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="m-b-15">
            <div class="box-header bg-gray">
                <h3 class="box-title">Histórico Regional</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Localidade de Destino</th>
                            <th class="w-50px text-right bg-gray-light">Envios</th>
                            <th class="w-50px text-right bg-gray-light">Vol.</th>
                            <th class="w-80px text-right bg-gray-light">Valor</th>
                            <th class="w-60px text-right bg-gray-light">Enc.</th>
                            <th class="w-80px text-right bg-gray-light">Total</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 215px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                            <?php
                            $count = 0;
                            $volumes = 0;
                            $totalPrice = 0;
                            $totalExpenses = 0;
                            $billingTotalsum = 0;
                            ?>
                            @foreach ($regionalShipments as $zipCode => $shipment)
                                <?php
                                $count += $shipment['count'];
                                $volumes += $shipment['volumes'];
                                $totalPrice += $shipment['price'];
                                $totalExpenses += $shipment['expenses'];
                                $billingTotalsum += $shipment['total'];
                                ?>
                                <tr>
                                    <td>{{ $zipCode }}</td>
                                    <td class="w-50px text-right">{{ $shipment['count'] }}</td>
                                    <td class="w-50px text-right">{{ $shipment['volumes'] }}</td>
                                    <td class="w-80px text-right">{{ money($shipment['price']) }}</td>
                                    <td class="w-60px text-right">{{ money($shipment['expenses']) }}</td>
                                    <td class="w-80px text-right bold">{{ money($shipment['total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Totais</th>
                            <th class="w-50px text-right bg-gray-light">{{ $count }}</th>
                            <th class="w-50px text-right bg-gray-light">{{ $volumes }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($totalPrice, Setting::get('app_currency')) }}</th>
                            <th class="w-60px text-right bg-gray-light">
                                {{ money($totalExpenses, Setting::get('app_currency')) }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($billingTotalsum, Setting::get('app_currency')) }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <div class="col-sm-6">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title">Origem das Importações</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">País</th>
                            <th class="w-50px text-right bg-gray-light">Envios</th>
                            <th class="w-50px text-right bg-gray-light">Vol.</th>
                            <th class="w-80px text-right bg-gray-light">Valor</th>
                            <th class="w-60px text-right bg-gray-light">Enc.</th>
                            <th class="w-80px text-right bg-gray-light">Total</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 215px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                            <?php
                            $count = 0;
                            $volumes = 0;
                            $totalPrice = 0;
                            $totalExpenses = 0;
                            $billingTotalsum = 0;
                            ?>
                            @foreach ($importShipments as $country => $shipment)
                                <?php
                                $count += $shipment['count'];
                                $volumes += $shipment['volumes'];
                                $totalPrice += $shipment['price'];
                                $totalExpenses += $shipment['expenses'];
                                $billingTotalsum += $shipment['total'];
                                ?>
                                <tr>
                                    <td style="white-space: nowrap;">
                                        @if (empty($country))
                                            <span class=" text-red"><i class="fas fa-exclamation-triangle"></i> Sem
                                                País</span>
                                        @else
                                            <i
                                                class="flag-icon flag-icon-{{ preg_replace('/[^A-Za-z ]/', '', $country) }}"></i>
                                            {{ trans('country.' . $country) }}
                                        @endif
                                    </td>
                                    <td class="w-50px text-right">{{ $shipment['count'] }}</td>
                                    <td class="w-50px text-right">{{ $shipment['volumes'] }}</td>
                                    <td class="w-80px text-right">{{ money($shipment['price']) }}</td>
                                    <td class="w-60px text-right">{{ money($shipment['expenses']) }}</td>
                                    <td class="w-80px text-right bold">{{ money($shipment['total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Totais</th>
                            <th class="w-50px text-right bg-gray-light">{{ $count }}</th>
                            <th class="w-50px text-right bg-gray-light">{{ $volumes }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($totalPrice, Setting::get('app_currency')) }}</th>
                            <th class="w-60px text-right bg-gray-light">
                                {{ money($totalExpenses, Setting::get('app_currency')) }}</th>
                            <th class="w-80px text-right bg-gray-light bold">
                                {{ money($billingTotalsum, Setting::get('app_currency')) }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title">Destino das Exportações</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">País</th>
                            <th class="w-50px text-right bg-gray-light">Envios</th>
                            <th class="w-50px text-right bg-gray-light">Vol.</th>
                            <th class="w-80px text-right bg-gray-light">Valor</th>
                            <th class="w-60px text-right bg-gray-light">Enc.</th>
                            <th class="w-80px text-right bg-gray-light">Total</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 215px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                            <?php
                            $count = 0;
                            $volumes = 0;
                            $totalPrice = 0;
                            $totalExpenses = 0;
                            $billingTotalsum = 0;
                            ?>
                            @foreach ($exportShipments as $country => $shipment)
                                <?php
                                $count += $shipment['count'];
                                $volumes += $shipment['volumes'];
                                $totalPrice += $shipment['price'];
                                $totalExpenses += $shipment['expenses'];
                                $billingTotalsum += $shipment['total'];
                                ?>
                                <tr>
                                    <td style="white-space: nowrap;">
                                        @if (empty($country))
                                            <span class=" text-red"><i class="fas fa-exclamation-triangle"></i> Sem
                                                País</span>
                                        @else
                                            <i
                                                class="flag-icon flag-icon-{{ preg_replace('/[^A-Za-z ]/', '', $country) }}"></i>
                                            {{ trans('country.' . $country) }}
                                        @endif
                                    </td>
                                    <td class="w-50px text-right">{{ $shipment['count'] }}</td>
                                    <td class="w-50px text-right">{{ $shipment['volumes'] }}</td>
                                    <td class="w-80px text-right">{{ money($shipment['price']) }}</td>
                                    <td class="w-60px text-right">{{ money($shipment['expenses']) }}</td>
                                    <td class="w-80px text-right bold">{{ money($shipment['total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Totais</th>
                            <th class="w-50px text-right bg-gray-light">{{ $count }}</th>
                            <th class="w-50px text-right bg-gray-light">{{ $volumes }}</th>
                            <th class="w-80px text-right bg-gray-light">{{ money($totalPrice) }}</th>
                            <th class="w-60px text-right bg-gray-light">{{ money($totalExpenses) }}</th>
                            <th class="w-80px text-right bg-gray-light">{{ money($billingTotalsum) }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-sm-12">
        <hr />
    </div>

    <div class="col-sm-5">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title">Recolhas por Rota</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="w-1 text-right bg-gray-light">#</th>
                            <th class="bg-gray-light">Operador</th>
                            <th class="w-50px text-right bg-gray-light">Envios</th>
                            <th class="w-50px text-right bg-gray-light">Vol.</th>
                            <th class="w-80px text-right bg-gray-light">Valor</th>
                            <th class="w-60px text-right bg-gray-light">Enc.</th>
                            <th class="w-80px text-right bg-gray-light">Total</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 300px; overflow: scroll;">
                    @if ($routesPickups)
                        <table class="table table-hover m-0">
                            <tbody>
                                <?php
                                $count = 0;
                                $volumes = 0;
                                $totalPrice = 0;
                                $totalExpenses = 0;
                                $billingTotalsum = 0;
                                $i = 0;
                                ?>
                                @foreach ($routesPickups as $route => $shipment)
                                    <?php
                                    $count += $shipment['count'];
                                    $volumes += $shipment['volumes'];
                                    $totalPrice += $shipment['price'];
                                    $totalExpenses += $shipment['expenses'];
                                    $billingTotalsum += $shipment['total'];
                                    $i++;
                                    ?>
                                    <tr>
                                        <td class="w-1">
                                            <div class="badge">{{ $i }}</div>
                                        </td>
                                        <td class="text-uppercase">{!! empty($route) ? '<i class="text-muted">Sem rota</i>' : $route !!}</td>
                                        <td class="w-50px text-right">{{ $shipment['count'] }}</td>
                                        <td class="w-50px text-right">{{ $shipment['volumes'] }}</td>
                                        <td class="w-80px text-right">{{ money($shipment['price']) }}</td>
                                        <td class="w-60px text-right">{{ money($shipment['expenses']) }}</td>
                                        <td class="w-80px text-right bold">{{ money($shipment['total']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Totais</th>
                            <th class="w-50px text-right bg-gray-light">{{ $count }}</th>
                            <th class="w-50px text-right bg-gray-light">{{ $volumes }}</th>
                            <th class="w-80px text-right bg-gray-light">{{ money($totalPrice) }}</th>
                            <th class="w-60px text-right bg-gray-light">{{ money($totalExpenses) }}</th>
                            <th class="w-80px text-right bg-gray-light">{{ money($billingTotalsum) }}</th>
                        </tr>
                    </thead>
                </table>
            @else
                <h4><i class="fas fa-info-circle"></i> Não configurou nenhuma rota local.</h4>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="box-header bg-gray">
            <h3 class="box-title">Utilização de Serviços</h3>
        </div>
        <div class="box-body p-0">
            <table class="table table-dashed table-hover table-condensed">
                <thead>
                    <tr>
                        <th rowspan="2"></th>
                        <th colspan="3" class="text-center">A cobrar IVA</th>
                        <th colspan="3" class="text-center">Isento de IVA</th>
                    </tr>
                    <tr>
                        <th class="text-center">Envios</th>
                        <th class="text-center">Volumes</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Envios</th>
                        <th class="text-center">Volumes</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($typeOfServices as $serviceName => $values)
                        <tr>
                            <td class="text-uppercase">{!! empty($serviceName) ? '<i>Sem serviço</i>' : $serviceName !!}</td>
                            <td class="text-center">{{ $values['count_vat'] }}</td>
                            <td class="text-center">{{ $values['volumes_vat'] }}</td>
                            <td class="text-center">{{ money($values['total_vat']) }}</td>
                            <td class="text-center">{{ $values['count_no_vat'] }}</td>
                            <td class="text-center">{{ $values['volumes_no_vat'] }}</td>
                            <td class="text-center">{{ money($values['total_no_vat']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<hr />
<div class="row">
    <div class="col-sm-6">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title">Entregas por Rota</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="w-1 text-right bg-gray-light">#</th>
                            <th class="bg-gray-light">Operador</th>
                            <th class="w-50px text-right bg-gray-light">Envios</th>
                            <th class="w-50px text-right bg-gray-light">Vol.</th>
                            <th class="w-80px text-right bg-gray-light">Valor</th>
                            <th class="w-60px text-right bg-gray-light">Enc.</th>
                            <th class="w-80px text-right bg-gray-light">Total</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 300px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                            <?php
                            $count = 0;
                            $volumes = 0;
                            $totalPrice = 0;
                            $totalExpenses = 0;
                            $totalSum = 0;
                            $i = 0;
                            ?>
                            @foreach ($routesShipments as $route => $shipment)
                                <?php
                                $count += $shipment['count'];
                                $volumes += $shipment['volumes'];
                                $totalPrice += $shipment['price'];
                                $totalExpenses += $shipment['expenses'];
                                $totalSum += $shipment['total'];
                                $i++;
                                ?>
                                <tr>
                                    <td class="w-1">
                                        <div class="badge">{{ $i }}</div>
                                    </td>
                                    <td>{{ $route }}</td>
                                    <td class="w-50px text-right">{{ $shipment['count'] }}</td>
                                    <td class="w-50px text-right">{{ $shipment['volumes'] }}</td>
                                    <td class="w-80px text-right">{{ money($shipment['price']) }}</td>
                                    <td class="w-60px text-right">{{ money($shipment['expenses']) }}</td>
                                    <td class="w-80px text-right bold">{{ money($shipment['total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Totais</th>
                            <th class="w-50px text-right bg-gray-light">{{ $count }}</th>
                            <th class="w-50px text-right bg-gray-light">{{ $volumes }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($totalPrice, Setting::get('app_currency')) }}</th>
                            <th class="w-60px text-right bg-gray-light">
                                {{ money($totalExpenses, Setting::get('app_currency')) }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($totalSum, Setting::get('app_currency')) }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title">Entregas por Motorista</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light w-1">#</th>
                            <th class="bg-gray-light">Operador</th>
                            <th class="w-50px text-right bg-gray-light">Envios</th>
                            <th class="w-50px text-right bg-gray-light">Vol.</th>
                            <th class="w-80px text-right bg-gray-light">Valor</th>
                            <th class="w-60px text-right bg-gray-light">Enc.</th>
                            <th class="w-80px text-right bg-gray-light">Total</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; height: 300px; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                            <?php
                            $count = 0;
                            $volumes = 0;
                            $totalPrice = 0;
                            $totalExpenses = 0;
                            $totalSum = 0;
                            $i = 0;
                            ?>
                            @foreach ($operatorShipments as $operatorName => $shipment)
                                <?php
                                $count += $shipment['count'];
                                $volumes += $shipment['volumes'];
                                $totalPrice += $shipment['price'];
                                $totalExpenses += $shipment['expenses'];
                                $totalSum += $shipment['total'];
                                $i++;
                                ?>
                                <tr>
                                    <td class="w-1">
                                        <div class="badge">{{ $i }}</div>
                                    </td>
                                    <td>{{ $operatorName }}</td>
                                    <td class="w-50px text-right">{{ $shipment['count'] }}</td>
                                    <td class="w-50px text-right">{{ $shipment['volumes'] }}</td>
                                    <td class="w-80px text-right">{{ money($shipment['price']) }}</td>
                                    <td class="w-60px text-right">{{ money($shipment['expenses']) }}</td>
                                    <td class="w-80px text-right bold">{{ money($shipment['total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Totais</th>
                            <th class="w-50px text-right bg-gray-light">{{ $count }}</th>
                            <th class="w-50px text-right bg-gray-light">{{ $volumes }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($totalPrice, Setting::get('app_currency')) }}</th>
                            <th class="w-60px text-right bg-gray-light">
                                {{ money($totalExpenses, Setting::get('app_currency')) }}</th>
                            <th class="w-80px text-right bg-gray-light">
                                {{ money($totalSum, Setting::get('app_currency')) }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<hr />
<div class="row">
    <div class="col-sm-12">
        <div class="box-header bg-gray">
            <i class="fas fa-fw fa-shipping-fast"></i>
            <h3 class="box-title">&nbsp;Serviços por Fornecedores</h3>
        </div>
        <div class="box-body p-0 table-bordered" style="overflow-x: scroll!important;">
            <table class="table table-bordered table-hover m-0">
                <thead>
                    <tr class="bg-gray-light">
                        <th style="min-width: 100px; font-weight: bold">Fornecedores</th>
                        @foreach ($allServices as $service)
                            <th style="min-width: 50px; font-weight: bold; text-align: center; white-space: nowrap;">
                                <span data-toggle="tooltip" title="{{ $service->name }}">
                                    {{ $service->display_code }}
                                </span>
                            </th>
                        @endforeach
                        <th style="min-width: 50px; font-weight: bold; text-align: center;">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $tableTotalServices = 0;
                        $tableTotalVolumes = 0;
                    @endphp
                    @foreach ($serviceProviders as $providerId => $info)
                        @php
                            $totalShipments = $totalVolumes = 0;
                            $providerName = @$allProviders[$providerId];
                            $providerColor = @$allProvidersColor[$providerId];
                        @endphp
                        <tr>
                            <td style="min-width: 50px;">
                                <i class="fas fa-square" style="color: {{ @$providerColor }}"></i>
                                {{ $providerName }}
                            </td>
                            @foreach ($allServices as $service)
                                @php
                                    $shipmentsCount = $info[$service->id]['count'] ?? 0;
                                    $totalShipments += $shipmentsCount;
                                    $totalService[$service->id] = ($totalService[$service->id] ?? 0) + $shipmentsCount;
                                @endphp
                                <td
                                    style="min-width: 50px; text-align: center; vertical-align: middle;  white-space: nowrap;">
                                    @if ($shipmentsCount)
                                        {{ $shipmentsCount }}
                                    @endif
                                </td>
                            @endforeach
                            <td style="min-width: 50px; text-align: center; vertical-align: middle;">
                                <span data-toggle="tooltip" title="{{ $providerName }}">
                                    {{ $totalShipments }}
                                </span>
                            </td>
                        </tr>
                        @php
                            $tableTotalServices += $totalShipments;
                        @endphp
                    @endforeach
                </tbody>
                <thead>
                    <tr class="bg-gray-light bold">
                        <th style="min-width: 100px; font-weight: bold">Total</th>
                        @foreach ($allServices as $service)
                            <th style="min-width: 50px; font-weight: bold; text-align: center;">
                                {{ $totalService[$service->id] }}
                            </th>
                        @endforeach
                        <th style="min-width: 50px; font-weight: bold; text-align: center;">
                            {{ $tableTotalServices }}
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<hr />
<div class="row">
    <div class="col-sm-12">
        <div class="box-header bg-gray">
            <i class="fas fa-fw fa-box-open"></i>
            <h3 class="box-title">&nbsp;Volumes por Fornecedores</h3>
        </div>
        <div class="box-body p-0 table-bordered" style="overflow-x: scroll!important;">
            <table class="table table-bordered table-hover m-0">
                <thead>
                    <tr class="bg-gray-light">
                        <th style="min-width: 100px; font-weight: bold">Fornecedores</th>
                        @foreach ($allServices as $service)
                            <th style="min-width: 50px; font-weight: bold; text-align: center; white-space: nowrap;">
                                <span data-toggle="tooltip" title="{{ $service->name }}">
                                    {{ $service->display_code }}
                                </span>
                            </th>
                        @endforeach
                        <th style="min-width: 50px; font-weight: bold; text-align: center;">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $tableTotalVolumes = 0;
                        $totalServiceVolume[] = 0;
                    @endphp
                    @foreach ($serviceProviders as $providerId => $info)
                        @php
                            $totalVolumes = 0;
                            $providerName = @$allProviders[$providerId];
                            $providerColor = @$allProvidersColor[$providerId];
                        @endphp
                        <tr>
                            <td style="min-width: 50px;">
                                <i class="fas fa-square" style="color: {{ @$providerColor }}"></i>
                                {{ $providerName }}
                            </td>
                            @foreach ($allServices as $service)
                                @php
                                    $volumesSum = $info[$service->id]['volumes'] ?? 0;
                                    $totalVolumes += $volumesSum;
                                    $totalServiceVolume[$service->id] = ($totalServiceVolume[$service->id] ?? 0) + $volumesSum;
                                @endphp
                                <td style="min-width: 50px; text-align: center; vertical-align: middle;">
                                    @if ($volumesSum)
                                        {{ $volumesSum }}
                                    @endif
                                </td>
                            @endforeach
                            <td style="min-width: 50px; text-align: center; vertical-align: middle;">
                                <span data-toggle="tooltip" title="{{ $providerName }}">
                                    {{ $totalVolumes }}
                                </span>
                            </td>
                        </tr>
                        @php
                            $tableTotalVolumes += $totalVolumes;
                        @endphp
                    @endforeach
                </tbody>
                <thead>
                    <tr class="bg-gray-light bold">
                        <th style="min-width: 100px; font-weight: bold">Total</th>
                        @foreach ($allServices as $service)
                            <th style="min-width: 50px; font-weight: bold; text-align: center;">
                                {{ $totalServiceVolume[$service->id] }}
                            </th>
                        @endforeach
                        <th style="min-width: 50px; font-weight: bold; text-align: center;">
                            {{ $tableTotalVolumes }}
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
