<?php $currencySymbol = Setting::get('app_currency') ?>
<div class="row row-10">
    <div class="col-sm-6 col-md-3 col-lg-12">
        <div class="info-box info-box-xs">
            <span class="info-box-icon bg-purple">
                @if($currencySymbol == '€')
                    <i class="fas fa-euro-sign"></i>
                @elseif($currencySymbol == '$')
                    <i class="fas fa-dollar-sign"></i>
                @else
                    <i class="fas fa-coins"></i>
                @endif
            </span>
            <div class="info-box-content">
                <span class="info-box-text">@trans('Faturação Mês')</span>
                <span class="info-box-number">
                    @if(hasModule('statistics'))
                    {{ money(@$totals['cur_month']['total_billing']) }}
                    <br>
                    @if(@$totals['balance']['total_billing'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="Mais {{ number(@$totals['balance']['total_billing'], 0) }}% em valor de faturação que no mês anterior.">
                            <i class="fas fa-caret-up"></i>
                            {{ number(@$totals['balance']['total_billing'], 0) }} @trans('este mês')
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="Menos {{ number(@$totals['balance']['total_billing'], 0) }}% em valor de faturação que no mês anterior.">
                            <i class="fas fa-caret-down"></i>
                            {{  number(-1 * @$totals['balance']['total_billing']) }}% @trans('este mês')
                        </small>
                    @endif
                    @else
                        <span data-toggle="tooltip" title="@trans('Conheça o total de faturação do mês comparado ao mês anterior. Módulo de estatisticas gerais não ativo.')">@trans('N/A')</span>
                        <br/>
                        <small class="text-green">@trans('N/A') %</small>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-12">
        <div class="info-box info-box-xs">
            <span class="info-box-icon bg-green">
                <i class="fas fa-shipping-fast"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">@trans('Envios no Mês')</span>
                <span class="info-box-number">
                    @if(hasModule('statistics'))
                    {{ @$totals['cur_month']['count_shipments'] }}
                    <br>
                    @if(@$totals['balance']['count_shipments'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="Mais {{ @$totals['balance']['count_shipments'] }} envios em relação ao mês anterior.">
                            <i class="fas fa-caret-up"></i>
                            {{ @$totals['balance']['count_shipments'] }} @trans('este mês')
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="Menos {{ @$totals['balance']['count_shipments'] }} envios em relação ao mês anterior.">
                            <i class="fas fa-caret-down"></i>
                            {{ -1 * @$totals['balance']['count_shipments'] }} @trans('este mês')
                        </small>
                    @endif
                    @else
                        <span data-toggle="tooltip" title="Conheça o total de envios do mês comparado ao mês anterior. Módulo de estatisticas gerais não ativo.">N/A</span>
                        <br/>
                        <small class="text-green">@trans('N/A') %</small>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-12">
        <div class="info-box info-box-xs">
            <span class="info-box-icon bg-yellow">
                <i class="fas fa-box-open"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">@trans('Envios por Dia')</span>
                <span class="info-box-number">
                    @if(hasModule('statistics'))
                    {{ ceil(@$totals['cur_month']['shipments_day']) }}
                    <br>
                    @if(@$totals['balance']['shipments_day'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="Mais {{ ceil(@$totals['balance']['shipments_day']) }} envios por dia em relação ao mês anterior.">
                            <i class="fas fa-caret-up"></i>
                            {{ ceil(@$totals['balance']['shipments_day']) }} @trans('este mês')
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="Menos {{ ceil(@$totals['balance']['shipments_day']) }} envios por dia em relação ao mês anterior.">
                            <i class="fas fa-caret-down"></i>
                            {{ ceil(-1 * @$totals['balance']['shipments_day']) }} @trans('este mês')
                        </small>
                    @endif
                    @else
                        <span data-toggle="tooltip" title="Conheça a média de envios por dia, comparado ao mês anterior. Módulo de estatisticas gerais não ativo.">N/A</span>
                        <br/>
                        <small class="text-green">@trans('N/A') %</small>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-12">
        <div class="info-box info-box-xs">
            <span class="info-box-icon bg-aqua">
                <i class="fas fa-user-plus"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">@trans('Novos Clientes')</span>
                <span class="info-box-number">
                    @if(hasModule('statistics'))
                    {{ @$totals['cur_month']['new_customers'] }}
                    <br>
                    @if(@$totals['balance']['shipments_day'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="{{ ceil(@$totals['balance']['new_customers']) }} clientes relação ao mês anterior.">
                            <i class="fas fa-caret-up"></i>
                            {{ ceil(@$totals['balance']['new_customers']) }} @trans('este mês')
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="{{ ceil(@$totals['balance']['new_customers']) }} clientes este mês em relação ao mês anterior.">
                            <i class="fas fa-caret-down"></i>
                            {{ ceil(-1 * @$totals['balance']['new_customers']) }} @trans('este mês')
                        </small>
                    @endif
                    @else
                        <span data-toggle="tooltip" title="Conheça o de novos clientes no mês comparado ao mês anterior. Módulo de estatisticas gerais não ativo.">N/A</span>
                        <br/>
                        <small class="text-green">@trans('N/A') %</small>
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>