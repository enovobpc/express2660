@section('title')
    Estatísticas Gerais
@stop

@section('content-header')
    Estatísticas Gerais
@stop

@section('breadcrumb')
    <li class="active">Faturação</li>
    <li class="active">Estatísticas Gerais</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box no-border m-b-15">
                <div class="box-body p-t-8 p-b-10">
                    <div class="row">
                        <div class="col-sm-12">
                            {{ Form::open(['route' => ['admin.statistics.index', 'tab' => Request::get('tab')], 'method' => 'GET' ]) }}
                            <ul class="form-inline list-inline pull-left m-0">
                                <li class="fltr-primary w-180px">
                                    <strong>Visualização</strong><br class="visible-xs"/>
                                    <div class="pull-left form-group-sm w-85px">
                                        {{ Form::hidden('tab', Request::has('tab') ? Request::get('tab') : 'summary') }}
                                        {{ Form::select('metrics', trans('admin/fleet.stats.metrics'), Request::has('metrics') ? Request::get('metrics') : 'daily', array('class' => 'form-control input-sm filter-datatable select2')) }}
                                    </div>
                                </li>
                                @if($metrics == 'daily')
                                    <li class="fltr-primary w-195px custom-fltr">
                                        <strong>Periodo</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-130px">
                                            {{ Form::select('period', $periodList, Request::has('period') ? Request::get('period') : '30d', array('class' => 'form-control input-sm select2')) }}
                                        </div>
                                    </li>
                                    <li class="custom-dates {{ Request::get('date_min') ? '' : 'hide' }}">
                                        <strong>Data</strong>
                                        <div class="input-group input-group-sm w-230px">
                                            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início']) }}
                                            <span class="input-group-addon">até</span>
                                            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim']) }}
                                        </div>
                                    </li>
                                @elseif($metrics == 'yearly')
                                    <li class="fltr-primary w-105px custom-fltr">
                                        <strong>Ano</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-65px">
                                            {{ Form::select('year', $years, Request::has('year') ? Request::get('year') : null, array('class' => 'form-control input-sm select2')) }}
                                        </div>
                                    </li>
                                @else
                                    <li class="fltr-primary w-150px custom-fltr">
                                        <strong>Mês</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-100px">
                                            {{ Form::select('month', trans('datetime.list-month'), Request::has('month') ? Request::get('month') : date('m'), array('class' => 'form-control input-sm select2')) }}
                                        </div>
                                    </li>
                                    <li class="fltr-primary w-105px custom-fltr">
                                        <strong>Ano</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-65px">
                                            {{ Form::select('year', $years, Request::has('year') ? Request::get('year') : null, array('class' => 'form-control input-sm select2')) }}
                                        </div>
                                    </li>
                                @endif

                                @if(!Auth::user()->isSeller())
                                <li class="fltr-primary w-180px">
                                    <strong>Vendedor</strong><br class="visible-xs"/>
                                    <div class="pull-left form-group-sm w-105px">
                                        {{ Form::select('seller', ['' => 'Todos'] + $sellers, fltr_val(Request::all(), 'seller'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                    </div>
                                </li>
                                @endif

                                <li class="fltr-primary w-190px">
                                    <strong>Motorista</strong><br class="visible-xs"/>
                                    <div class="pull-left form-group-sm w-105px">
                                        {{ Form::select('operator', ['' => 'Todos'] + $operatorsList, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                    </div>
                                </li>
                                @if(!empty($agencies) && count($agencies) > 1)
                                    <li class="fltr-primary w-190px">
                                        <strong>Agência</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-125px">
                                            {{ Form::select('agency', ['' => 'Todos'] + $agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                        </div>
                                    </li>
                                @endif
                                @if(@$vehiclesList)
                                    <li class="fltr-primary w-150px">
                                        <strong>Viatura</strong><br class="visible-xs"/>
                                        <div class="pull-left form-group-sm w-90px">
                                            {{ Form::select('vehicle', ['' => 'Todos'] + $vehiclesList, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                        </div>
                                    </li>
                                @endif
                                <li>
                                    <button type="submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i>" class="btn btn-sm btn-success">Filtrar</button>
                                </li>
                            </ul>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-tab-url">
                    <li class="{{ empty(Request::get('tab')) || Request::get('tab') == 'summary' ? 'active' : '' }}">
                        <a href="{{ route('admin.statistics.index', Request::except('tab') + ['tab' => 'summary']) }}">
                            <i class="fas fa-chart-pie"></i> Resumo
                        </a>
                    </li>
                    <li class="{{ Request::get('tab') == 'quality' ? 'active' : '' }}">
                        <a href="{{ route('admin.statistics.index', Request::except('tab') + ['tab' => 'quality']) }}">
                            <i class="fas fa-clipboard-check"></i> Qualidade
                        </a>
                    </li>
                    <li class="{{ Request::get('tab') == 'services' ? 'active' : '' }}">
                        <a href="{{ route('admin.statistics.index', Request::except('tab') + ['tab' => 'services']) }}">
                            <i class="fas fa-shipping-fast"></i> Serviços
                        </a>
                    </li>
                    <li class="{{ Request::get('tab') == 'gains' ? 'active' : '' }}">
                        <a href="{{ route('admin.statistics.index', Request::except('tab') + ['tab' => 'gains']) }}">
                            <i class="fas fa-balance-scale"></i> Ganhos e Despesas
                        </a>
                    </li>
                    <li class="{{ Request::get('tab') == 'sellers' ? 'active' : '' }}">
                        <a href="{{ route('admin.statistics.index', Request::except('tab') + ['tab' => 'sellers']) }}">
                            <i class="fas fa-user-tie"></i> Comerciais
                        </a>
                    </li>
                    <li class="{{ Request::get('tab') == 'users' ? 'active' : '' }}">
                        <a href="{{ route('admin.statistics.index', Request::except('tab') + ['tab' => 'users']) }}">
                            <i class="fas fa-user-tie"></i> Colaboradores
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    @if(empty(Request::get('tab')) || Request::get('tab') == 'summary')
                    <div class="tab-pane active">
                        @include('admin.statistics.tabs.summary')
                    </div>
                    @endif

                    @if(Request::get('tab') == 'quality')
                    <div class="tab-pane active">
                        @include('admin.statistics.tabs.quality')
                    </div>
                    @endif

                    @if(Request::get('tab') == 'services')
                        <div class="tab-pane active">
                            @include('admin.statistics.tabs.services')
                        </div>
                    @endif

                    @if(Request::get('tab') == 'gains')
                        <div class="tab-pane active">
                            @include('admin.statistics.tabs.gains')
                        </div>
                    @endif

                    @if(Request::get('tab') == 'sellers')
                        <div class="tab-pane active">
                            @include('admin.statistics.tabs.sellers')
                        </div>
                    @endif
                    @if(Request::get('tab') == 'users')
                        <div class="tab-pane active">
                            @include('admin.statistics.tabs.users')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {{ Html::script('vendor/chart.js/dist/Chart.min.js') }}
    <script>

        $('[name=billing_month], [name=billing_year]').on('change', function(){
            var url = "{!! route('admin.dashboard', Request::all()) !!}";
            url = Url.updateParameter(url, 'billing_month', $('[name=billing_month]').val());
            url = Url.updateParameter(url, 'billing_year', $('[name=billing_year]').val());
            window.location = url;
        })

        $('.nav-tab-url li a').on('click', function (e) {
            $(this).find('i').removeClass();
            $(this).find('i').addClass('fas').addClass('fa-spin').addClass('fa-circle-notch')
        })

        $('.nav-tab-url li').click(function () {
            $('.nav-tab-url li').removeClass('active');
            $(this).addClass('active');
        })

        $('[name="metrics"]').on('change', function () {
            $('.custom-fltr').hide()
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

            $(document).on('click', '[data-toggle="show-more"]',function(){
                $('.more-about').slideToggle();
            })

        });


        $('[name=billing_month], [name=billing_year]').on('change', function(){
            var url = "{!! route('admin.dashboard', Request::all()) !!}";
            url = Url.updateParameter(url, 'billing_month', $('[name=billing_month]').val());
            url = Url.updateParameter(url, 'billing_year', $('[name=billing_year]').val());
            window.location = url;
        })



        $(document).ready(function () {

            /**
             * Billing chart
             */
            var billingChartData = {
                labels: [{!! @$monthBillingChart['labels'] !!}],
                datasets: [{
                    label: 'Faturação',
                    data: [{{ @$monthBillingChart['billing'] }}],
                    borderColor: "#31b622",
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                },
                {
                    label: 'Nº Envios',
                    data: [{{ @$monthBillingChart['shipments'] }}],
                    borderColor: "#ffb41e",
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                },
                {
                    label: 'Volumes',
                    data: [{{ @$monthBillingChart['volumes'] }}],
                    borderColor: "#00adee",
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                },
                {
                    label: 'Incidências',
                    data: [{{ @$monthBillingChart['incidences'] }}],
                    borderColor: "#f1412f",
                    borderWidth: 2,
                    lineTension: 0,
                    fill: false,
                }]
            }

            var billingChartOptions = {
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero:true}
                    }]
                },
                legend: {
                    display: true,
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }}
            }

            var billingChart = $("#billingChart");
            new Chart(billingChart, {
                type: 'line',
                data: billingChartData,
                options: billingChartOptions
            });


            /**
             * Balance chart
             */
            var billingChart = $("#balanceChart");
            var balanceChartData = {
                labels: [{!! @$balanceChart['labels'] !!}],
                datasets: [{
                    data: [{{ @$balanceChart['values'] }}],
                    backgroundColor: [{!! @$balanceChart['colors'] !!}],
                }],
            }

            var balanceChartOptions = {
                legend: {
                    display: true,
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
                animation: { animateRotate: false}
            }

            new Chart(billingChart, {
                type: 'pie',
                data: balanceChartData,
                options: balanceChartOptions
            });

            /**
             * Providers chart
             */
            var chart = $("#providersChart");
            var chartData = {
                labels: [{!! @$providersChart['labels'] !!}],
                datasets: [{
                    data: [{{ @$providersChart['values'] }}],
                    backgroundColor: [{!! @$providersChart['colors'] !!}],
                }],
            }

            var chartOptions = {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
                animation: { animateRotate: false}
            }

            new Chart(chart, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });

            /**
             * Status chart
             */
            var chart = $("#statusChart");
            var chartData = {
                labels: [{!! @$statusChart['labels'] !!}],
                datasets: [{
                    data: [{{ @$statusChart['values'] }}],
                    backgroundColor: [{!! @$statusChart['colors'] !!}],
                }],
            }

            var chartOptions = {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
                animation: { animateRotate: false}
            }

            new Chart(chart, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });

            /**
             * Recipients chart
             */
            var chart = $("#recipientsChart");
            var chartData = {
                labels: [{!! @$recipientsChart['labels'] !!}],
                datasets: [{
                    data: [{{ @$recipientsChart['values'] }}],
                    backgroundColor: [{!! @$recipientsChart['colors'] !!}],
                }],
            }

            var chartOptions = {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
                animation: { animateRotate: false}
            }

            new Chart(chart, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });

            /**
             * Sellers chart
             */
            var chart = $("#sellersChart");
            var chartData = {
                labels: [{!! @$sellersChart['labels'] !!}],
                datasets: [{
                    data: [{{ @$sellersChart['values'] }}],
                    backgroundColor: [{!! @$sellersChart['colors'] !!}],
                }],
            }

            var chartOptions = {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
                animation: { animateRotate: false}
            }

            new Chart(chart, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });

        });

        /**
         * Quality Status chart
         */
        var chart = $("#qualityShipmentsChart");
        var chartData = {
            labels: [{!! @$statusChart['labels'] !!}],
            datasets: [{
                data: [{{ @$statusChart['values'] }}],
                backgroundColor: [{!! @$statusChart['colors'] !!}],
            }],
        }

        var chartOptions = {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 5
                }
            },
            animation: { animateRotate: false}
        }

        new Chart(chart, {
            type: 'pie',
            data: chartData,
            options: chartOptions
        });

        /**
         * Customers chart
         */
        var chart = $("#customersChart");
        var chartData = {
            labels: [{!! @$customersChart['labels'] !!}],
            datasets: [{
                data: [{{ @$customersChart['values'] }}],
                backgroundColor: [{!! @$customersChart['colors'] !!}],
            }],
        }

        var chartOptions = {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 5
                }
            },
            animation: { animateRotate: false}
        }

        new Chart(chart, {
            type: 'pie',
            data: chartData,
            options: chartOptions
        });

        /**
         * Billing chart
         */
        var billingChartData = {
            labels: [{!! @$operatorAvgChart['labels'] !!}],
            datasets: [{
                label: 'Total Envios',
                data: [{{ @$operatorAvgChart['shipments'] }}],
                //backgroundColor: "#00adee",
                borderColor: "#00adee",
                borderWidth: 2,
                fill: false,
                lineTension: 0
            },
            {
                label: 'Ø Peso',
                data: [{{ @$operatorAvgChart['weight'] }}],
                //backgroundColor: "#31b622",
                borderColor: "#31b622",
                borderWidth: 2,
                fill: false,
                lineTension: 0
            },
            {
                label: 'Ø Volumes',
                data: [{{ @$operatorAvgChart['volumes'] }}],
                //backgroundColor: "#ffb41e",
                borderColor: "#ffb41e",
                borderWidth: 2,
                fill: false,
                lineTension: 0
            },
            {
                label: 'Ø Incidências',
                data: [{{ @$operatorAvgChart['incidences'] }}],
                //backgroundColor: "#f1412f",
                borderColor: "#f1412f",
                borderWidth: 2,
                lineTension: 0,
                fill: false,
            }]
        }

        var billingChartOptions = {
            scales: {
                xAxes: [{
                    stacked: true,
                }],
                yAxes: [{
                    stacked: true
                }]
            },
            legend: {
                display: true,
                labels: {
                    boxWidth: 12,
                    padding: 5
                }},
            animation: { duration: 0}

        }

        var billingChart = $("#operatorAverage");
        new Chart(billingChart, {
            type: 'line',
            data: billingChartData,
            options: billingChartOptions
        });

        /**
         * Incidences by provider Status chart
         */
        var chartOptions = {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 12,
                    padding: 5
                }
            },
            animation: { animateRotate: false}
        }


        var chart = $("#incidencesByProvider");
        var chartData = {
            labels: [{!! @$incidencesByProviderChart['labels'] !!}],
            datasets: [{
                data: [{{ @$incidencesByProviderChart['values'] }}],
                backgroundColor: [{!! @$incidencesByProviderChart['colors'] !!}],
            }],
        }
        new Chart(chart, {type: 'pie',data: chartData, options: chartOptions});

        var chart = $("#incidencesByCustomer");
        var chartData = {
            labels: [{!! @$incidencesByCustomerChart['labels'] !!}],
            datasets: [{
                data: [{{ @$incidencesByCustomerChart['values'] }}],
                backgroundColor: [{!! @$incidencesByCustomerChart['colors'] !!}],
            }],
        }
        new Chart(chart, {type: 'pie',data: chartData, options: chartOptions});

        var chart = $("#incidencesByService");
        var chartData = {
            labels: [{!! @$incidencesByServiceChart['labels'] !!}],
            datasets: [{
                data: [{{ @$incidencesByServiceChart['values'] }}],
                backgroundColor: [{!! @$incidencesByServiceChart['colors'] !!}],
            }],
        }
        new Chart(chart, {type: 'pie',data: chartData, options: chartOptions});


    </script>
@stop
