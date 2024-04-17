<div class="box box-solid">
    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">
            <li class="{{ Request::get('tab') == 'stats' ? '' : 'active' }}">
                <a href="#tab-shipments" data-toggle="tab">
                    <i class="fas fa-fw fa-shipping-fast"></i> Serviços realizados
                    @if($customer->total_shipments > 0.00)
                        <span class="badge pull-right fs-10 fw-500">{{ $customer->count_shipments }}</span>
                    @endif
                </a>
            </li>
            @if(hasModule('pickups'))
            <li>
                <a href="#tab-pickups" data-toggle="tab">
                    <i class="fas fa-fw fa-dolly"></i> Pedidos Recolha
                    @if($customer->count_pickups > 0.00)
                        <span class="badge pull-right fs-10 fw-500">{{ $customer->count_pickups }}</span>
                    @endif
                </a>
            </li>
            @endif
            @if($customer->count_cod)
            <li class="{{ $customer->count_cod ? : 'disabled' }}">
                <a href="#tab-cod" data-toggle="tab">
                    <i class="fas fa-fw fa-hand-holding-usd"></i> Portes Destino
                    @if($customer->count_cod)
                        <span class="badge pull-right fs-10 fw-500">{{ $customer->count_cod }}</span>
                    @endif
                </a>
            </li>
            @endif
            <li class="{{ $customer->total_expenses > 0.00 ? : 'disabled' }}">
                <a href="#tab-billing-expenses" data-toggle="tab">
                    <i class="fas fa-fw fa-euro-sign"></i> Taxas Adicionais
                    @if($customer->total_expenses > 0.00)
                        <span class="badge pull-right fs-10 fw-500">{{ $customer->count_expenses }}</span>
                    @endif
                </a>
            </li>
            @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'products,products_sales'))
                <li class="{{ !empty($customer->products) ? : 'disabled' }}">
                    <a href="#tab-products" data-toggle="tab">
                        <i class="fas fa-fw fa-wine-bottle"></i> Artigos

                        @if($customer->total_products > 0.00)
                            <span class="badge pull-right fs-10 fw-500">{{ money($customer->total_products, Setting::get('app_currency')) }}</span>
                        @endif
                    </a>
                </li>
            @endif

            @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'customer_covenants'))
                <li class="{{ !empty($customer->covenants) ? : 'disabled' }}">
                    <a href="#tab-covenants" data-toggle="tab">
                        <i class="fas fa-fw fa-handshake"></i> Avenças Mensais
                        @if($customer->total_covenants > 0.00)
                            <span class="badge pull-right fs-10 fw-500">{{ money($customer->total_covenants, Setting::get('app_currency')) }}</span>
                        @endif
                    </a>
                </li>
            @endif
            <li class="{{ Request::get('tab') == 'stats' ? 'active' : '' }}">
                {{--<a href="#tab-stats" data-toggle="tab">--}}
                <a href="{{ route('admin.billing.customers.show', [$customer->id, 'month' => $month, 'year' => $year, 'tab' => 'stats', 'period' => $period]) }}">
                    <i class="far fa-fw fa-chart-bar"></i> Estatística por Serviço
                </a>
            </li>
        </ul>
    </div>
</div>
@if($customer->total_month > 0.00)
    <div class="box box-solid">
        <div class="box-body text-center">
            <h3 class="m-0 m-t-minus-5">
                <small>Por Faturar</small>
                <br/><b class="text-blue">{{ money($customer->total_month, Setting::get('app_currency')) }}</b>
            </h3>
            <hr class="m-t-10 m-b-5"/>

            <div class="col-lg-6">
                <h4 class="m-0">
                    <small>A taxar IVA</small>
                    <br/>
                    <b class="fw-500">{{ money($customer->total_month_vat, Setting::get('app_currency')) }}</b>
                </h4>
            </div>
            <div class="col-lg-6">
                <h4 class="m-0">
                    <small>Isento IVA</small>
                    <br/>
                    <b class="fw-500">{{ money($customer->total_month - $customer->total_month_vat, Setting::get('app_currency')) }}</b>
                </h4>
            </div>
        </div>
    </div>
@endif
<div class="box box-solid">
    <div class="box-body text-center">
        <h3 class="m-0 m-t-minus-5">
            <small>Resumo Total</small>
            <p class="fs-17 m-b-3" style="line-height: 0">
                <small>{{ \App\Models\Billing::getPeriodName($year, $month, $period) }}</small>
            </p>
            <b class="text-blue">{{ money($customer->total_month_absolute, Setting::get('app_currency')) }}</b>
        </h3>

        <div class="clearfix"></div>
        <hr class="m-t-10 m-b-5"/>
        <div class="col-lg-6">
            <h4 class="m-0">
                <small>Despesas</small>
                <br/>
                @if(hasModule('statistics'))
                <b class="fw-500">{{ money($customer->total_month_cost, Setting::get('app_currency')) }}</b>
                <br/>
                <span class="fs-13">{{ money(($customer->total_month_cost * 100) / ((empty($customer->total_month_absolute) || $customer->total_month_absolute == 0.0) ? 1 : $customer->total_month_absolute), '%', 2)  }}</span>
                @else
                <span data-toggle="tooltip" title="Módulo de estatísticas gerais não ativo.">
                    <b class="fw-500">N/A</b>
                    <br/>
                    <span class="fs-13">N/A %</span>
                </span>
                @endif
            </h4>
        </div>
        <div class="col-lg-6">
            <h4 class="m-0">
                <small>Ganhos</small>
                <br/>
                @if(hasModule('statistics'))
                    @if($customer->total_month_profit >= 0)
                        <span class="text-green">
                            <b class="fw-500">{{ money($customer->total_month_profit, Setting::get('app_currency')) }}</b>
                            <br/>
                            <span class="fs-13">{{ money(($customer->total_month_profit * 100) / ((empty($customer->total_month_absolute) || $customer->total_month_absolute == 0.0) ? 1 : $customer->total_month_absolute), '%', 2)  }}</span>
                        </span>
                    @else
                        <span class="text-red">
                            <b class="fw-500">{{ money($customer->total_month_profit, Setting::get('app_currency')) }}</b>
                            <br/>
                            <span class="fs-13">{{ money(($customer->total_month_absolute * 100) / $customer->total_month_profit, '%', 2)  }}</span>
                        </span>
                    @endif
                @else
                <span data-toggle="tooltip" title="Módulo de estatísticas gerais não ativo.">
                    <b class="fw-500">N/A</b>
                    <br/>
                    <span class="fs-13">N/A %</span>
                </span>
                @endif
            </h4>
        </div>
    </div>
</div>