<div class="row row-10">
    <div class="col-sm-3 col-lg-3">
        <div class="counter-box">
            <span class="counter-box-icon bg-yellow">
                <i class="far fa-clock"></i>
            </span>
            <div class="counter-box-content">
                <span class="counter-box-number">
                    {{ $statusTotals['pending'] }}
                </span>
                <span class="counter-box-text">{{ trans('account/dashboard.word.pending') }}</span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-sm-3 col-lg-3">
        <div class="counter-box counter-accepted">
            <span class="counter-box-icon bg-green">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="counter-box-content">
                <span class="counter-box-number">
                    {{ $statusTotals['accepted'] }}
                </span>
                <span class="counter-box-text">{{ trans('account/dashboard.word.accepted') }}</span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-sm-3 col-lg-3">
        <div class="counter-box counter-motion">
            <span class="counter-box-icon bg-blue">
                <i class="fas fa-truck"></i>
            </span>
            <div class="counter-box-content">
                <span class="counter-box-number">
                    {{ $statusTotals['transport'] }}
                </span>
                <span class="counter-box-text">{{ trans('account/dashboard.word.transport') }}</span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-sm-3 col-lg-3">
        <div class="counter-box counter-incidences">
            <span class="counter-box-icon bg-red">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <div class="counter-box-content">
                <span class="counter-box-number">
                    {{ $statusTotals['incidence'] }}
                </span>
                <span class="counter-box-text">{{ trans('account/dashboard.word.incidence') }}</span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="spacer-15"></div>