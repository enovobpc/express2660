 <div class="row">
    <div class="col-sm-6 col-md-5ths">
        <div class="counter-box counter-box-xs">
            <span class="counter-box-icon bg-dark-blue">
                <i class="fas fa-euro-sign"></i>
            </span>
            <div class="counter-box-content">
                <span class="counter-box-text">{{ trans('account/dashboard.word.billing-month') }}</span>
                <span class="counter-box-number">
                    {{ money(@$totals['cur_month']['total_billing'], Setting::get('app_currency')) }}
                    <br>
                    @if(@$totals['balance']['total_billing'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="{{ trans('account/dashboard.tip.plus-percent', ['value' => number(@$totals['balance']['total_billing'])]) }}">
                            <i class="fas fa-caret-up"></i>
                            {{ number(@$totals['balance']['total_billing'], 0) }}% {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="{{ trans('account/dashboard.tip.minus-percent', ['value' => number(@$totals['balance']['total_billing'])]) }}">
                            <i class="fas fa-caret-down"></i>
                            {{ number(-1 * @$totals['balance']['total_billing']) }}% {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-5ths">
        <div class="counter-box counter-box-xs">
            <span class="counter-box-icon bg-dark-blue">
                <i class="fas fa-shipping-fast"></i>
            </span>
            <div class="counter-box-content">
                <span class="counter-box-text">{{ trans('account/dashboard.word.shipments-month') }}</span>
                <span class="counter-box-number">
                    {{ @$totals['cur_month']['count_shipments'] }}
                    <br>
                    @if(@$totals['balance']['count_shipments'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="{{ trans('account/dashboard.tip.plus-total', ['value' => @$totals['balance']['count_shipments']]) }}">
                            <i class="fas fa-caret-up"></i>
                            {{ @$totals['balance']['count_shipments'] }} {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="{{ trans('account/dashboard.tip.minus-total', ['value' => @$totals['balance']['count_shipments']]) }}">
                            <i class="fas fa-caret-down"></i>
                            {{ -1 * @$totals['balance']['count_shipments'] }} {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-5ths">
        <div class="counter-box counter-box-xs">
            <span class="counter-box-icon bg-dark-blue">
                <i class="fas fa-boxes"></i>
            </span>
            <div class="counter-box-content">
                <span class="counter-box-text">{{ trans('account/dashboard.word.volumes-month') }}</span>
                <span class="counter-box-number">
                    {{ @$totals['cur_month']['sum_volumes'] }}
                    <br>
                    @if(@$totals['balance']['sum_volumes'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="{{ trans('account/dashboard.tip.plus-total', ['value' => @$totals['balance']['sum_volumes']]) }}">
                            <i class="fas fa-caret-up"></i>
                            {{ @$totals['balance']['sum_volumes'] }} {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="{{ trans('account/dashboard.tip.minus-total', ['value' => @$totals['balance']['sum_volumes']]) }}">
                            <i class="fas fa-caret-down"></i>
                            {{ -1 * @$totals['balance']['sum_volumes'] }} {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-5ths">
        <div class="counter-box counter-box-xs">
            <span class="counter-box-icon bg-dark-blue">
                <i class="fas fa-box-open"></i>
            </span>
            <div class="counter-box-content">
                <span class="counter-box-text">{{ trans('account/dashboard.word.shipments-day') }}</span>
                <span class="counter-box-number">
                    <div class="pull-left">{{ ceil(@$totals['cur_month']['shipments_day']) }}</div>
                    <div class="pull-left m-t-5 m-l-10"><small>({{ number(@$totals['cur_month']['avg_weight']) }}kg)</small></div>
                    <br>
                    @if(@$totals['balance']['shipments_day'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="{{ trans('account/dashboard.tip.plus-total', ['value' => ceil(@$totals['balance']['shipments_day'])]) }}">
                            <i class="fas fa-caret-up"></i>
                            {{ ceil(@$totals['balance']['shipments_day']) }} {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="{{ trans('account/dashboard.tip.minus-total', ['value' => ceil(@$totals['balance']['shipments_day'])]) }}">
                            <i class="fas fa-caret-down"></i>
                            {{ ceil(-1 * @$totals['balance']['shipments_day']) }} {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-5ths">
        <div class="counter-box counter-box-xs">
                <span class="counter-box-icon bg-dark-blue">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
            <div class="counter-box-content">
                <span class="counter-box-text">{{ trans('account/dashboard.word.incidence') }}</span>
                <span class="counter-box-number">
                        {{ @$totals['cur_month']['incidences'] }}
                    <br>
                    @if(@$totals['balance']['incidences'] > 0)
                        <small class="text-green" data-toggle="tooltip" title="Mais {{ @$totals['balance']['incidences'] }} incidências em relação ao mês anterior.">
                        <i class="fas fa-caret-up"></i>
                            {{ @$totals['balance']['incidences'] }} {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @else
                        <small class="text-red" data-toggle="tooltip" title="Menos {{ @$totals['balance']['incidences'] }} incidências em relação ao mês anterior.">
                            <i class="fas fa-caret-down"></i>
                            {{ -1 * @$totals['balance']['incidences'] }} {{ trans('account/dashboard.word.this-month') }}
                        </small>
                    @endif
                    </span>
            </div>
        </div>
    </div>
 </div>