<div class="row">
    <div class="col-lg-9 col-md-8 col-sm-6 col-xs-12">
        <div class="row row-5">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-green"><i class="fas fa-euro-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Faturação Emitida</span>
                        <span class="info-box-number">
                            {{ money($allBillingInvoices->sum('doc_subtotal'), Setting::get('app_currency')) }}<br/>
                            <small class="text-black">IVA <b>{{ money($allBillingInvoices->sum('doc_vat'), Setting::get('app_currency')) }}</b></small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-yellow">
                        <i class="fas fa-fw fa-file-invoice"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pagam. Efetuados</span>
                        <span class="info-box-number">
                           {{ money($allPurchaseReceipts->sum('subtotal'), Setting::get('app_currency')) }}<br/>
                            <small class="text-black">IVA <b>{{ money($allPurchaseReceipts->sum('vat_total'), Setting::get('app_currency')) }}</b></small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-light-blue">
                        <i class="fas fa-fw fa-hand-holding-usd"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recebimentos</span>
                        <span class="info-box-number">
                            {{ money($allReceipts->sum('doc_subtotal'), Setting::get('app_currency')) }}<br/>
                            <small class="text-black">IVA <b>{{ money($allReceipts->sum('doc_vat'), Setting::get('app_currency')) }}</b></small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-purple">
                        <i class="fas fa-balance-scale"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Resultado Período</span>
                        <span class="info-box-number">
                            {{ money($allReceipts->sum('doc_subtotal') - $allPurchaseReceipts->sum('subtotal'), Setting::get('app_currency')) }}<br/>
                            <small class="text-black">IVA <b>{{ money($allReceipts->sum('doc_vat') - $allPurchaseReceipts->sum('vat_total'), Setting::get('app_currency')) }}</b></small>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <hr style="margin: 5px 0"/>
        <h4 class="bold text-blue text-uppercase">Resumo de serviços</h4>
        <div class="row row-5">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-orange"><i class="fas fa-user-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Novos Clientes</span>
                        <span class="info-box-number">
                            {{ $customersTotals['new']['count'] }}<br/>
                            <small class="text-muted">{{ $customersTotals['active']['count'] }} clientes ativos</small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-blue">
                        <i class="fas fa-fw fa-shipping-fast"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Envios</span>
                        <span class="info-box-number">
                            {{ $billingTotals['shipments']['count'] }}<br/>
                            <small class="text-muted">Média {{ number($billingTotals['avg']['shipments'], 0) }} envios/dia</small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-red">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Incidências</span>
                        <span class="info-box-number">
                            {{ @$billingTotals['shipments']['incidences']['count'] }}<br/>
                            <small>{{ number(@$billingTotals['shipments']['incidences']['percent'], 2) }}% do total</small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box info-box-sm">
                    <span class="info-box-icon bg-green">
                        <i class="fas fa-check"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Entrega à 1ª</span>
                        <span class="info-box-number">
                            {{ @$billingTotals['shipments']['deliveries']['count'] }}<br/>
                            <small>{{ number(@$billingTotals['shipments']['deliveries']['percent'], 2) }}% do total</small>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="chart">
            <canvas id="billingChart" height="350"></canvas>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <div class="stats-main-panel">
            <h4 class="text-center bold">
                Resultado Total<br/>
                <small style="font-weight: normal">(Inclui serviços não faturados)</small>
            </h4>
            <div class="chart">
                <canvas id="balanceChart" height="250"></canvas>
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
            <div class="row">
                <div class="col-xs-12 col-md-offset-1 col-md-10 balance-detail">
                    <div class="row row-0">
                        <div class="col-sm-6 col-xs-12">
                            <h4>
                                <small>Ganhos</small><br/>
                                <b>{{ money($billingTotals['balance']['gains'], Setting::get('app_currency')) }}</b>
                                <br/>
                                <small>{{ money($billingTotals['balance']['gains_percent'], '%') }}</small>
                            </h4>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <h4>
                                <small>Despesas</small><br/>
                                <b>{{ money($billingTotals['balance']['costs'], Setting::get('app_currency')) }}</b>
                                <br/>
                                <small>{{ money($billingTotals['balance']['costs_percent'], '%') }}</small>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-sm-3">
        <div class="text-center">
            <h4 class="text-center bold">Total envios por Fornecedor</h4>
            <div class="chart">
                <canvas id="providersChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="text-center">
            <h4 class="text-center bold">Total envios por Estado</h4>
            <div class="chart">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="text-center">
            <h4 class="text-center bold">Total envios por Destino</h4>
            <div class="chart">
                <canvas id="recipientsChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="text-center">
            <h4 class="text-center bold">Faturação por Comercial</h4>
            <div class="chart">
                <canvas id="sellersChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
