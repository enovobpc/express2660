@section('title')
    Estatística de Gastos
@stop

@section('content-header')
    Estatística de Gastos
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão de Frota')</li>
<li class="active">@trans('Estatística de Gastos')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box no-border m-b-15">
                <div class="box-body p-t-8 p-b-10">
                    <div class="row">
                        <div class="col-xs-12">
                            {{ Form::open(['route' => 'admin.fleet.stats.index', 'method' => 'GET' ]) }}
                            <ul class="form-inline list-inline pull-left m-0">
                                <li class="fltr-primary w-180px">
                                    <strong>@trans('Visualização')</strong><br class="visible-xs"/>
                                    <div class="pull-left form-group-sm w-85px">
                                        {{ Form::hidden('tab', Request::has('tab') ? Request::get('tab') : 'summary') }}
                                        {{ Form::select('metrics', trans('admin/fleet.stats.metrics'), Request::has('metrics') ? Request::get('metrics') : 'daily', array('class' => 'form-control input-sm filter-datatable select2')) }}
                                    </div>
                                </li>
                                @if($metrics == 'daily')
                                    <li class="fltr-primary w-195px custom-fltr">
                                        <strong>@trans('Periodo')</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-130px">
                                            {{ Form::select('period', $periodList, Request::has('period') ? Request::get('period') : '30d', array('class' => 'form-control input-sm select2')) }}
                                        </div>
                                    </li>
                                    <li class="custom-dates {{ Request::get('date_min') ? '' : 'hide' }}">
                                        <strong>@trans('Data')</strong>
                                        <div class="input-group input-group-sm w-230px">
                                            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início')]) }}
                                            <span class="input-group-addon">@trans('até')</span>
                                            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim')]) }}
                                        </div>
                                    </li>
                                @elseif($metrics == 'yearly')
                                    <li class="fltr-primary w-115px custom-fltr">
                                        <strong>@trans('Ano')</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-75px">
                                            {{ Form::select('year', $years, Request::has('year') ? Request::get('year') : null, array('class' => 'form-control input-sm select2')) }}
                                        </div>
                                    </li>
                                @else
                                    <li class="fltr-primary w-140px custom-fltr">
                                        <strong>@trans('Mês')</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-100px">
                                            {{ Form::select('month', trans('datetime.list-month'), Request::has('month') ? Request::get('month') : date('m'), array('class' => 'form-control input-sm select2')) }}
                                        </div>
                                    </li>
                                    <li class="fltr-primary w-115px custom-fltr">
                                        <strong>@trans('Ano')</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-75px">
                                            {{ Form::select('year', $years, Request::has('year') ? Request::get('year') : null, array('class' => 'form-control input-sm select2')) }}
                                        </div>
                                    </li>
                                @endif
                                <li class="fltr-primary w-180px">
                                    <strong>@trans('Viatura')</strong><br class="visible-xs"/>
                                    <div class="pull-left form-group-sm w-120px">
                                        {{ Form::select('vehicle', ['' => __('Todas')] + $veichlesList, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                    </div>
                                </li>
                                <li>
                                    <button type="submit" class="btn btn-sm btn-success">@trans('Filtrar')</button>
                                </li>
                            </ul>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-xs-9">
            @include('admin.fleet.stats.partials.totals')
            @if(Request::has('vehicle') && !empty(Request::get('vehicle')))
                @include('admin.fleet.stats.partials.graph_history')
            @else
                @include('admin.fleet.stats.partials.graph_balance')
            @endif

        </div>
        <div class="col-xs-3">
            <div class="box no-border">
                <div class="box-header">
                    <h4 class="box-title text-total bold">@trans('Resumo Geral')</h4>
                </div>
                <div class="box-body">
                    <div class="stats-main-panel" style="border: none">
                        <div class="chart">
                            <canvas id="summaryChart" height="190"></canvas>
                        </div>
                        <div class="text-center">
                            <p>@trans('Resultado no período selecionado')</p>
                            @if(@$globalStats['balance'] >= 0.00)
                                <h1 class="text-green balance-total">
                                    <b>{{ money(@$globalStats['balance'], Setting::get('app_currency')) }}</b><br/>
                                    <small class="text-green">
                                        {{ money(@$globalStats['balance_percent'], '', 0) }}@trans('% de lucro')
                                    </small>
                                </h1>
                            @else
                                <h1 class="text-red balance-total">
                                    <b>{{ money(@$globalStats['balance'], Setting::get('app_currency')) }}</b><br/>
                                    <small class="text-red">
                                        {{ money(@$globalStats['balance_percent'], '', 0) }}@trans('% de prejuizo')
                                    </small>
                                </h1>
                            @endif
                        </div>
                        <div class="row text-center">
                            <div class="col-xs-12 col-md-offset-1 col-md-10 balance-detail">
                                <div class="row row-0">
                                    <div class="col-sm-6 col-xs-12">
                                        <h4>
                                            <small>@trans('Ganhos')</small><br/>
                                            <b>{{ money(@$globalStats['gains'], Setting::get('app_currency')) }}</b>
                                            <br/>
                                            <small>{{ money(@$globalStats['gains_percent'], '%') }}</small>
                                        </h4>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <h4>
                                            <small>@trans('Despesas')</small><br/>
                                            <b>{{ money(@$globalStats['costs'], Setting::get('app_currency')) }}</b>
                                            <br/>
                                            <small>{{ money(@$globalStats['costs_percent'], '%') }}</small>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row row-5">
        <div class="col-xs-12 col-sm-8">
            @include('admin.fleet.stats.partials.graph_veichles')
        </div>
        <div class="col-xs-12 col-sm-4">
            @include('admin.fleet.stats.partials.providers')
        </div>
    </div>
    @if(!Request::has('vehicle'))
    <div class="row">
        <div class="col-xs-12">
            @include('admin.fleet.stats.partials.graph_history')
        </div>
    </div>
    @endif
@stop

@section('scripts')
    {{ Html::script('vendor/chart.js/dist/Chart.min.js') }}
    <script>

        $('[name="metrics"]').on('change', function () {
            $(this).closest('form').submit();
        });

        $('[name="period"]').on('change', function (e) {
            e.preventDefault();
            var value = $(this).val();

            if(value == '') {
                $('.custom-dates').removeClass('hide').find('input').val('')
            } else {
                $('.custom-dates').addClass('hide').find('input').val('')
            }
        });

        $(document).ready(function () {

            var veichlesChartData = {
                labels: [{!! @$veichlesGraphData['labels'] !!}],
                datasets: [{
                    label: 'Combustível',
                    data: [{{ @$veichlesGraphData['fuel'] }}],
                    backgroundColor: "#EB1212",
                    borderWidth: 1
                },
                {
                    label: 'Manutenções',
                    data: [{{ @$veichlesGraphData['maintenances'] }}],
                    backgroundColor: "#ff851b",
                    borderWidth: 1
                },
                {
                    label: 'Despesas Gerais',
                    data: [{{ @$veichlesGraphData['expenses'] }}],
                    backgroundColor: "#ffc107",
                    borderWidth: 1
                },

                {
                    label: 'Despesas Fixas',
                    data: [{{ @$veichlesGraphData['fixed_costs'] }}],
                    backgroundColor: "#f54735",
                    borderWidth: 1
                },
                {
                    label: 'Portagens',
                    data: [{{ @$veichlesGraphData['tolls'] }}],
                    backgroundColor: "#e88d6d",
                    borderWidth: 1
                }
                ]
            }

            var veichlesChartOptions = {
                scales: {
                    xAxes: [{
                        stacked: {!! @$veichlesGraphData['stacked'] ? 'true' : 'false' !!}
                    }],
                    yAxes: [{
                        ticks: { beginAtZero:true},
                        stacked: {!! @$veichlesGraphData['stacked'] ? 'true' : 'false' !!}
                    }]
                },
                legend: {
                    display: true,
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
            }

            var veichlesChart = $("#veichlesChart");
            var veichlesChart = new Chart(veichlesChart, {
                type: 'bar',
                stacked: true,
                data: veichlesChartData,
                options: veichlesChartOptions
            });
        });


        $(document).ready(function () {

            var balanceChartData = {
                labels: [{!! @$balanceChart['labels'] !!}],
                datasets: [{
                        label: 'Total Ganhos',
                        data: [{{ @$balanceChart['gains'] }}],
                        borderColor: "#19ac2b",
                        pointBackgroundColor: '#19ac2b',
                        backgroundColor: "#19ac2b",
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2
                    },
                    {
                        label: 'Total Custos',
                        data: [{{ @$balanceChart['costs'] }}],
                        borderColor: "#EB1212",
                        pointBackgroundColor: '#EB1212',
                        backgroundColor: "#EB1212",
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2,
                    },
                    {
                        label: 'Envios/Serviços',
                        data: [{{ @$balanceChart['services'] }}],
                        borderColor: "#3876d1",
                        backgroundColor: "#3876d1",
                        pointBackgroundColor: '#3876d1',
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2,
                    }]
            }

            var balanceChartOptions = {
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero:true},
                    }]
                },
                legend: {
                    display: true,
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
            }

            var balanceChart = $("#balanceChart");
            var balanceChart = new Chart(balanceChart, {
                type: 'line',
                stacked: false,
                data: balanceChartData,
                options: balanceChartOptions
            });
        });

        $(document).ready(function () {

            var historyChartData = {
                labels: [{!! @$historyChart['labels'] !!}],
                datasets: [{
                        label: 'Ganhos',
                        data: [{{ @$historyChart['gains'] }}],
                        borderColor: "#19ac2b",
                        pointBackgroundColor: '#19ac2b',
                        backgroundColor: "#19ac2b",
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2
                    },
                    {
                        label: 'Combustível',
                        data: [{{ @$historyChart['fuel'] }}],
                        borderColor: "#EB1212",
                        pointBackgroundColor: '#EB1212',
                        backgroundColor: "#EB1212",
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2,
                    },
                    {
                        label: 'Manutenções',
                        data: [{{ @$historyChart['maintenances'] }}],
                        borderColor: "#ff851b",
                        pointBackgroundColor: '#ff851b',
                        backgroundColor: "#ff851b",
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2,
                    },
                    {
                        label: 'Despesas Gerais',
                        data: [{{ @$historyChart['expenses'] }}],
                        borderColor: "#ffc107",
                        pointBackgroundColor: '#ffc107',
                        backgroundColor: "#ffc107",
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2,
                    },
                    {
                        label: 'Despesas Fixas',
                        data: [{{ @$historyChart['fixed_costs'] }}],
                        borderColor: "#f54735",
                        pointBackgroundColor: '#f54735',
                        backgroundColor: "#f54735",
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2,
                    },
                    {
                        label: 'Portagens',
                        data: [{{ @$historyChart['tolls'] }}],
                        borderColor: "#e88d6d",
                        backgroundColor: "#e88d6d",
                        pointBackgroundColor: '#e88d6d',
                        lineTension: 0.2,
                        fill: false,
                        borderWidth: 2,
                    }]
            }

            var historyChartOptions = {
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero:true},
                    }]
                },
                legend: {
                    display: true,
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
            }

            var historyChart = $("#historyChart");
            var historyChart = new Chart(historyChart, {
                type: 'line',
                stacked: false,
                data: historyChartData,
                options: historyChartOptions
            });
        });


        $('[name=billing_month], [name=billing_year]').on('change', function(){
            var url = "{!! route('admin.dashboard', Request::all()) !!}";
            url = Url.updateParameter(url, 'billing_month', $('[name=billing_month]').val());
            url = Url.updateParameter(url, 'billing_year', $('[name=billing_year]').val());
            window.location = url;
        })


    $(document).ready(function () {

        var oTable = $('#datatable-providers').DataTable({
            dom: "<'row row-0'<'col-md-6 col-sm-6 datatable-filters-area'><'col-sm-6 col-md-6'f><'col-sm-12 datatable-filters-area-extended'>>" +
            "<'row row-0'<'col-sm-12'tr>>" +
            "<'row row-0'<'col-sm-5'l><'col-sm-7'p>>",
            serverSide: false,
            order: [[3, "desc"]],
            data: {!! json_encode(@$providerStats) !!},
            columns: [
                { data: "typeIcon" },
                { data: "name" },
                { data: "count", class: "text-center"},
                { data: "total", class: "text-right bold"},
                { data: "type", visible: false},
            ]
        });

        $('[data-target="#datatable-providers"] .filter-datatable').on('change', function (e) {
            e.preventDefault();
            var providerType = $(this).val();
            oTable.search(providerType).draw();
        });
    });



        /**
         * Summary chart
         */
        var summaryChart = $("#summaryChart");
        var summaryChartData = {
            labels: [{!! @$summaryChart['labels'] !!}],
            datasets: [{
                data: [{{ @$summaryChart['values'] }}],
                backgroundColor: [{!! @$summaryChart['colors'] !!}],
            }],
        }

        var summaryChartOptions = {
            legend: {
                display: true,
                labels: {
                    boxWidth: 12,
                    padding: 5
                }
            },
            animation: { animateRotate: false}
        }

        new Chart(summaryChart, {
            type: 'pie',
            data: summaryChartData,
            options: summaryChartOptions
        });

</script>
@stop