@section('title')
    {{ trans('account/dashboard.title') }}
@stop

@section('account-content')
    @include('account.partials.alert_unpaid_invoices')

    @if($unconfirmedRefunds > 0)
        <h4 class="text-blue m-t-10 m-b-0 pull-left">
            <i class="fas fa-info-circle"></i> {{ trans('account/dashboard.unconfirmed-refunds', ['total' => $unconfirmedRefunds]) }}<br/>
        </h4>
        <a href="{{ route('account.refunds.index') }}" class="btn btn-xs btn-info pull-left m-l-15 m-t-10">{{ trans('account/dashboard.word.see-refunds') }}</a>
        <div class="clearfix"></div>
        <hr/>
    @endif

    @if(hasModule('invoices') && Setting::get('show_customers_ballance') && $auth->show_billing)
    <div class="row">
        <div class="col-sm-12 visible-xs visible-sm">
            @include('account.dashboard.partials.current_counters')
        </div>
        <div class="col-sm-4 col-md-3 hidden-xs hidden-sm">
            @include('account.dashboard.partials.current_counters_vertical')
        </div>
        <div class="col-sm-12 col-md-9">
            @include('account.dashboard.partials.balance')
        </div>
    </div>
    @else
        @include('account.dashboard.partials.current_counters')
    @endif

    @if($auth->show_billing)
        <hr class="m-t-0"/>
        <h4>{{ trans('account/dashboard.month-chart-title') }}</h4>
        <div class="row row-10">
            <div class="col-sm-7">
                @include('account.dashboard.partials.billing_graph')
            </div>
            <div class="col-sm-5">
                <h4>{{ trans('account/dashboard.global-chart-title') }}</h4>
                <canvas id="graphic-billing" style='width: 500px; height: 290px'></canvas>
            </div>
        </div>
        <hr class="m-t-0 m-b-20"/>
        @include('account.dashboard.partials.month_totals')
    @endif
    </div>

    @if(@$messagesPopup)
        @foreach($messagesPopup as $messagePopup)
            @include('account.partials.message_modal')
        @endforeach
    @endif
@stop

@section('scripts')
{{ Html::script('vendor/chart.js/dist/Chart.min.js') }}
<script>
    $(document).ready(function () {
        /**
         * Billing chart
         */
        var billingChartData = {
            labels: [{!! @$billingGraphData['labels'] !!}],
            datasets: [{
                label: '{{ trans('account/global.word.shipments') }}',
                data: [{{ @$billingGraphData['shipments'] }}],
                backgroundColor: "#132d66",
                borderWidth: 1
            },
            {
                label: '{{ trans('account/global.word.billing') }}',
                data: [{{ @$billingGraphData['billed'] }}],
                backgroundColor: "#42608d",
                borderWidth: 1
            },
            {
                label: '{{ trans('account/global.word.volumes') }}',
                data: [{{ @$billingGraphData['volumes'] }}],
                backgroundColor: "#000000",
                borderWidth: 1
            }]
        }    

        var billingChartOptions = {
            scales: {
                xAxes: [{
                    stacked: true
                }],
                yAxes: [{
                    ticks: { beginAtZero:true}
                }]
            },
            legend: { display: true }
        }

        var billingChart = $("#billingChart");
        var billingChart = new Chart(billingChart, {
            type: 'bar',
            data: billingChartData,
            options: billingChartOptions,
            
        });



        var chartData = {
            type: 'line',
            data: {
                labels: [{!! @$graphData['labels'] !!}],
                datasets: [{
                    label: '{{ trans('account/global.word.shipments') }}',
                    data: [{{ @$graphData['shipments'] }}],
                    backgroundColor: "#ffb600",
                    borderColor: "#ffb600",
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                },
                {
                    label: '{{ trans('account/global.word.billing') }}',
                    data: [{{ @$graphData['billing'] }}],
                    backgroundColor: "#925ca2",
                    borderColor: "#925ca2",
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                },
                {
                    label: '€ {{ trans('account/dashboard.word.averaging-shipping') }}',
                    data: [{{ @$graphData['price_avg'] }}],
                    backgroundColor: "#2498a2",
                    borderColor: "#2498a2",
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0
                }]
            },
            options: {
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
                    }
                },
            }
        };

        var gBill = $("#graphic-billing");
        new Chart(gBill, chartData);



        /**
         * STATUS CHART
         */
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

        var chart = $("#statusChart");
        var chartData = {
            labels: [{!! @$statusChart['labels'] !!}],
            datasets: [{
                data: [{{ @$statusChart['values'] }}],
                backgroundColor: [{!! @$statusChart['colors'] !!}],
            }],
        }
        new Chart(chart, {
            type: 'doughnut',
            data: chartData,
            options: chartOptions
        });

        $('#modal-sync-balance form [name="auto"]').val(1);
        $('#modal-sync-balance form').submit();
    });
    
    $('[name=month], [name=year]').on('change', function(){
        var url = "{!! route('account.index', Request::all()) !!}";
        url = Url.updateParameter(url, 'month', $('[name=month]').val());
        url = Url.updateParameter(url, 'year', $('[name=year]').val());
        window.location = url;
    })


    @if(Setting::get('invoice_software') != 'EnovoTms')
    /**
     * Sync balance
     */
    $('#modal-sync-balance form').on('submit', function(e){
        e.preventDefault()

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var auto = $(this).find('[name=auto]').val();

        $('#modal-sync-balance .loading-status').show();
        $('#modal-sync-balance .loading-status').prev().hide();
        $.post($form.attr('action'), function(data){

            if(data.result) {
                if(auto != '1') {
                    /* $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000}); */
                }
                $('.balance-total-expired').html(data.totalExpired);
                $('.balance-total-unpaid').html(data.totalUnpaid);
            } else {
               /*  $.bootstrapGrowl(data.feedback, {type: 'error', align: 'center', width: 'auto', delay: 8000}); */
            }
        }).fail(function () {
            /* $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Ocorreu um erro ao tentar obter os dados do programa de faturação.",
                {type: 'error', align: 'center', width: 'auto', delay: 8000}); */
        }).always(function () {
            $('#modal-sync-balance form').find('[name=auto]').val('')
            $('#modal-sync-balance .loading-status').hide();
            $('#modal-sync-balance .loading-status').prev().show();
            $('#modal-sync-balance').modal('hide');
            $submitBtn.button('reset');
        });
    })
    @endif


    @if($messagesPopup)
    @foreach($messagesPopup as $messagePopup)
    $(document).ready(function(){
        $('#modal-message-{{ $messagePopup->id }}').modal('show');
    })
    @endforeach

    $('[name="is_read"]').on('change', function(){
        if($(this).is(':checked')) {
            var action = $(this).closest('form').attr('action');
            $.post(action, {is_read:1}, function(){});
        } else {
            var action = $(this).closest('form').attr('action');
            $.post(action, {is_read:0}, function(){});
        }
    })
    @endif
</script>
@stop