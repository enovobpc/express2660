<div class="counter-box counter-box-sm">
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

<div class="counter-box counter-box-sm">
    <span class="counter-box-icon bg-purple">
        <i class="far fa-clock"></i>
    </span>
    <div class="counter-box-content">
        <span class="counter-box-number">
            {{ $statusTotals['pickup'] }}
        </span>
        <span class="counter-box-text">{{ trans('account/dashboard.word.pickup') }}</span>
    </div>
    <div class="clearfix"></div>
</div>

<div class="counter-box counter-box-sm">
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

<div class="counter-box counter-box-sm">
    <span class="counter-box-icon bg-green">
        <i class="fas fa-check-circle"></i>
    </span>
    <div class="counter-box-content">
        <span class="counter-box-number">
            {{ $statusTotals['delivery'] }}
        </span>
        <span class="counter-box-text">{{ trans('account/dashboard.word.delivery') }}</span>
    </div>
    <div class="clearfix"></div>
</div>


<div class="counter-box counter-box-sm">
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

@if(Setting::get('app_mode') == 'express')
<div class="counter-box counter-box-sm">
    <span class="counter-box-icon bg-orange">
        <i class="fas fa-arrow-left"></i>
    </span>
    <div class="counter-box-content">
        <span class="counter-box-number">
            {{ $statusTotals['devolved'] }}
        </span>
        <span class="counter-box-text">{{ trans('account/dashboard.word.devolutions') }}</span>
    </div>
    <div class="clearfix"></div>
</div>
@endif