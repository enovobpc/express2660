<div class="row row-5">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fas fa-gas-pump bigger-130"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@trans('Abastecimentos')</span>
                <span class="info-box-number">
                    {{ money($globalStats['fuel'], Setting::get('app_currency')) }}<br/>
                    <small class="text-muted">{{ !empty($globalStats['global']) ? money(($globalStats['fuel'] * 100)/$globalStats['global'],'%') : '0.00%'  }}</small>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-orange"><i class="fas fa-wrench"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">@trans('Manutenções')</span>
                <span class="info-box-number">
                        {{ money($globalStats['maintenances'], Setting::get('app_currency')) }}<br/>
                        <small class="text-muted">{{ !empty($globalStats['global']) ? money((($globalStats['maintenances']) * 100) /$globalStats['global'],'%') : '0.00%' }}</small>
                    </span>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow">
                <i class="fas fa-euro-sign"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">@trans('Despesas')</span>
                <span class="info-box-number">
                        {{ money(($globalStats['expenses'] + $globalStats['fixed_costs']), Setting::get('app_currency')) }}<br/>
                        <small class="text-muted">{{ !empty($globalStats['global']) ? money((($globalStats['expenses'] + $globalStats['fixed_costs']) * 100)/$globalStats['global'],'%')  : '0.00%'  }}</small>
                    </span>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-purple"><i class="fas fa-road"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@trans('Portagens')</span>
                <span class="info-box-number">
                        {{ money($globalStats['tolls'], Setting::get('app_currency')) }}<br/>
                        <small class="text-muted">{{ !empty($globalStats['global']) ? money(($globalStats['tolls'] * 100)/$globalStats['global'],'%') : '0.00%'  }}</small>
                    </span>
            </div>
        </div>
    </div>
</div>