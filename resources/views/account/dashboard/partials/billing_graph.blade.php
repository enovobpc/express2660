<div class="box">
    <div class="panel-heading">
        <div class="row row-5 m-b-10">
            <div class="col-sm-3">
                <div class="form-group-sm">
                {{ Form::select('month', ['' => '- ' . trans('account/dashboard.word.annual-view') . ' -'] + trans('datetime.list-month'), Request::get('month') === null ? date('m') : (Request::get('month') === "" ? null :  Request::get('month')), array('class' => 'form-control select2 input-sm pull-left')) }}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group-sm">
                {{ Form::select('year', $years, empty(Request::get('year')) ? date('Y') : Request::get('year'), array('class' => 'form-control select2 input-sm pull-left')) }}
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body p-0" style="background: #fff; border: none">
        <div class="row">
            <div class="col-md-12">
                <div class="chart">
                    <canvas id="billingChart" height="135"></canvas>
                </div>
                <div class="spacer-10"></div>
            </div>
        </div>
        {{--<div class="panel-footer text-center" style="background: #fff;">
            <div class="row  p-b-5">
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <h4 class="m-b-0">{{ money(@$billingTotals['billed'] , Setting::get('app_currency')) }}</h4>
                        <span class="line-height-1p3">
                            Custo dos Envios
                            <div class="text-muted font-size-10px" style="margin-top: 0px">No período assinalado</div>
                        </span>
                    </div>
                </div>

                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <h4 class="m-b-0">{{ @$billingTotals['shipments'] }}</h4>
                        <span class="line-height-1p3">
                            Total de Envios
                            <div class="text-muted font-size-10px" style="margin-top: 0px">No período assinalado</div>
                        </span>
                    </div>
                </div>

                <div class="col-sm-3 col-xs-6">
                    <div class="border-right">
                        <h4 class="m-b-0">{{ @$billingTotals['collections'] }}</h4>
                        <span class="line-height-1p3">
                            Total de Recolhas
                            <div class="text-muted font-size-10px" style="margin-top: 0px">No período assinalado</div>
                        </span>
                    </div>
                </div>

                <div class="col-sm-3 col-xs-6">
                    <div class="">
                        <h4 class="m-b-0">{{ money(@$billingTotals['shipments_avg']) }}</h4>
                        <span class="line-height-1p3">
                            Média de Envios
                            <div class="text-muted font-size-10px" style="margin-top: 0px">No período assinalado</div>
                        </span>
                    </div>
                </div>
            </div>
        </div>--}}
    </div>
</div>