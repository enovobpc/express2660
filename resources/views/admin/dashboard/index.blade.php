@section('title')
Painel de Controlo
@stop

@section('content-header')
Painel de Controlo
@stop

@section('content')
@include('admin.dashboard.partials.buy_alert')
<div class="row row-10">
    @if(!Auth::user()->isGuest() && Auth::user()->ability(Config::get('permissions.role.admin'), 'statistics'))
    <div class="col-sm-12 col-lg-2">
        @include('admin.dashboard.partials.current_counters_vertical')
    </div>
    @endif
    <div class="col-sm-8 {{ !Auth::user()->isGuest() && Auth::user()->ability(Config::get('permissions.role.admin'), 'statistics') ? 'col-lg-6' : 'col-lg-8' }}">
        <div class="box box-solid box-warning box-list">
            <div class="box-header bg-navy with-border">
                <h4 class="box-title">
                    @trans('Estado atual dos serviços')
                </h4>
            </div>
            <div class="box-body p-0">
                <div class="row row-0">
                    <div class="col-sm-6" style="height: 255px; border-right: 1px solid #999;">
                        @include('admin.dashboard.partials.services_details')
                    </div>
                    <div class="col-sm-6">
                        @include('admin.dashboard.partials.pending_shipments')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(Auth::user()->ability(Config::get('permissions.role.admin'),'calendar_events'))
    <div class="col-sm-4">
        @include('admin.dashboard.partials.calendar')
    </div>
    @endif
</div>
@if(!Auth::user()->isGuest() && hasModule('statistics') && Auth::user()->ability(Config::get('permissions.role.admin'), 'statistics'))
<div class="row row-10">
    <div class="col-sm-12 col-lg-10">
        <div class="row row-10">
            <div class="col-sm-9">
                <div class="box">
                    @if(config('app.source') == 'lousaestradas')
                    <div class="box-body" style="max-height: 300px">
                        <h4 class="m-t-0 bold">@trans('Perspectiva global') - Lousada</h4>
                        <div class="chart">
                            <canvas id="billingChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="box-body" style="max-height: 300px">
                        <h4 class="m-t-0 bold">@trans('Perspectiva global') - Amarante</h4>
                        <div class="chart">
                            <canvas id="billingChart2" height="250"></canvas>
                        </div>
                    </div>
                    @else
                    <div class="box-body" style="max-height: 300px">
                        <h4 class="m-t-0 bold">@trans('Perspectiva global - Últimos meses')</h4>
                        <div class="chart">
                            <canvas id="billingChart" height="250"></canvas>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-sm-3">
                @include('admin.dashboard.partials.provider_chart')
            </div>
        </div>
    </div>
    <div class="col-sm-3 col-lg-2 visible-lg">
        @include('admin.dashboard.partials.weather')
    </div>
</div>
@endif

<div class="row row-10">
    <div class="col-sm-12 col-md-8 hidden-xs">
        <div class="box">
            <div class="box-body">
                {{--<a href="{{ route('admin.maps.operators') }}"
                   style="padding: 5px !important; margin: -3px -3px -3px;"
                   class="btn btn-xs btn-default pull-right"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-map-marker-alt"></i> Procurar Colaborador
                </a>--}}
                <h4 class="m-t-0 m-b-3 bold">@trans('Localização dos motoristas')</h4>
            </div>
            @include('admin.dashboard.partials.map')
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="box">
            <div class="box-body">
                <h4 class="m-t-0 m-b-0 bold">@trans('Últimos clientes sem atividade')</h4>
                <p><small>@trans('Clientes sem envios há mais de') {{ Setting::get('alert_max_days_without_shipments') }} dias</small></p>
                <div class="nicescroll" style="height: 326px; overflow-y: scroll">
                    <table class="table table-condensed">
                        <tr>
                            <th>@trans('Cliente')</th>
                            <th class="w-1">@trans('Envios')</th>
                            <th class="w-85px">@trans('Últ.Env.')</th>
                        </tr>
                        @if(!Auth::user()->isGuest())
                            @foreach($inactiveCustomers as $customer)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.customers.edit', $customer->id) }}" data-toggle="tooltip" title="{{ @$customer->customer->zip_code }} - {{ @$customer->customer->city }}">
                                            {{ $customer->code }} - {{ $customer->name }}
                                        </a>
                                        <small class="visible-xs text-muted">{{ @$customer->customer->zip_code }} - {{ @$customer->customer->city }}</small>
                                    </td>
                                    <td class="text-center">{{ $customer->total_shipments }}</td>
                                    <td>{{ $customer->last_shipment }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.dashboard.modals.weather_settings')
@endsection

@section('scripts')
{{ Html::script('vendor/chart.js/dist/Chart.min.js') }}
<script>

    $.get("{{ route('admin.weather.show') }}", function(data){
        $('.weather-panel').replaceWith(data);
    }).fail(function(){
        var html = '<div class="widget-loading"><i class="fas fa-exclamation-triangle text-red"></i> Erro ao carregar dados.</div>';
        $('.weather-panel').html(html);
    })

    /**
     * SEARCH WEATHER LOCATION
     * ajax method
     */
    $("select[name=weather_setting_location]").select2({
        ajax: {
            url: "{{ route('admin.weather.search.cities') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=weather_setting_location] option').remove()

                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('select[name=weather_setting_location]').on('change', function(){
        $('[name="weather_setting_city"]').val($(this).find(':selected').text())
    })

    $(document).ready(function () {

        /**
         * Billing chart
         */
        var billingChartData = {
            labels: [{!! @$globalChartData['labels'] !!}],
            datasets: [{
                label: 'Faturação',
                data: [{{ @$globalChartData['billing'] }}],
                borderColor: "#31b622",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            },
            {
                label: 'Nº Envios',
                data: [{{ @$globalChartData['shipments'] }}],
                borderColor: "#ffb41e",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            },
            {
                label: 'Volumes',
                data: [{{ @$globalChartData['volumes'] }}],
                borderColor: "#00adee",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            },
            {
                label: 'Peso Médio',
                data: [{{ @$globalChartData['weight'] }}],
                borderColor: "#8427c2",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            },
            {
                label: 'Incidências',
                data: [{{ @$globalChartData['incidences'] }}],
                borderColor: "#f1412f",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            }]
        }
        
        @if(config('app.source') == 'lousaestradas')
        var billingChart2Data = {
            labels: [{!! @$globalChart2Data['labels'] !!}],
            datasets: [{
                label: 'Faturação',
                data: [{{ @$globalChart2Data['billing'] }}],
                borderColor: "#31b622",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            },
            {
                label: 'Nº Envios',
                data: [{{ @$globalChart2Data['shipments'] }}],
                borderColor: "#ffb41e",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            },
            {
                label: 'Volumes',
                data: [{{ @$globalChart2Data['volumes'] }}],
                borderColor: "#00adee",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            },
            {
                label: 'Peso Médio',
                data: [{{ @$globalChart2Data['weight'] }}],
                borderColor: "#8427c2",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            },
            {
                label: 'Incidências',
                data: [{{ @$globalChart2Data['incidences'] }}],
                borderColor: "#f1412f",
                borderWidth: 2,
                fill: false,
                lineTension: 0.2
            }]
        }
        @endif

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
                }
            },
            animation: { animateRotate: false}
        }

        var billingChart = $("#billingChart");
        new Chart(billingChart, {
            type: 'line',
            data: billingChartData,
            options: billingChartOptions
        });

        @if(config('app.source') == 'lousaestradas')
        var billingChart = $("#billingChart2");
        new Chart(billingChart, {
            type: 'line',
            data: billingChart2Data,
            options: billingChartOptions
        });
        @endif

        /**
         * Providers & Location chart
         */
        var chartOptions = {
            legend: {
                display: true,
                position: 'right',
                labels: {
                    boxWidth: 12,
                    padding: 5
                }
            },
            animation: { animateRotate: false}
        }

        var chart = $("#providersChart");
        var chartData = {
            labels: [{!! @$providersChart['labels'] !!}],
            datasets: [{
                data: [{{ @$providersChart['values'] }}],
                backgroundColor: [{!! @$providersChart['colors'] !!}],
            }],
        }
        new Chart(chart, {
            type: 'doughnut',
            data: chartData,
            options: chartOptions
        });

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
    });

    
    $('[name=billing_month], [name=billing_year]').on('change', function(){
        var url = "{!! route('admin.dashboard', Request::all()) !!}";
        url = Url.updateParameter(url, 'billing_month', $('[name=billing_month]').val());
        url = Url.updateParameter(url, 'billing_year', $('[name=billing_year]').val());
        window.location = url;
    })
    
    
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey() }}&callback=initAutocomplete"></script>
<script src="{{ asset('assets/admin/js/maps.js') }}"></script>
<script>
    var map, infowindow, directionsDisplay;
    var bounds = new google.maps.LatLngBounds();
    var markers  = [];

    $(document).ready(function(){
        var props = {
            center: {lat: 40.404874, lng: -7.874651},
            zoom: 12,
            zoomControl: true,
            mapTypeControl: true,
            navigationControl: false,
            streetViewControl: true,
            scrollwheel: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById('map'), props);

        $('.operators-data-list li').each(function () {
            var lat  = $(this).data('lat');
            var lng  = $(this).data('lng');
            var id   = $(this).data('id');
            var html = $(this).data('html');
            var icon = $(this).data('marker');

            if(lat != "" && lng != "") {
                options = { 'html' : html, 'id' : id, icon: icon }
                marker = setMarker(lat, lng, options)
                markers.push(marker);

                bounds.extend(marker.position); //auto center map
            }
        })

        if($('.operators-list ul li').length) {
            map.fitBounds(bounds); //auto center map
        }
    })

    $('.filter-list input').on('keyup', function(){
        var txt = removeAccents($(this).val());
        var target = $(this).closest('.filter-list').data('target');

        if(txt == '') {
            $(target).show();
        } else {
            $(target).hide();
            $('.filter-noresults').hide()
            $(target).each(function(){
                divTxt = removeAccents($(this).text());
                if(divTxt.toUpperCase().indexOf(txt.toUpperCase()) != -1){
                    $(this).show();
                }
            });

            if($(target+':visible').length == 0) {
                $('.filter-noresults').show()
            }
        }
    })

    $('.operators-list ul li').click(function(){
        var id = $(this).data('id');
        openInfoWindowById(id);
        $('.close-customers-list').trigger('click');
    })

    //set map marker
    function setMarker(lat, lng, options) {
        var options   = typeof options !== 'undefined' ? options : {};
        var draggable = typeof options.draggable !== 'undefined' ? options.draggable : false;
        var html      = typeof options.html !== 'undefined' ? options.html : null;
        var zoom      = typeof options.zoom !== 'undefined' ? options.zoom : null;
        var centerMap = typeof options.centerMap !== 'undefined' ? options.centerMap : false;
        var icon      = typeof options.icon !== 'undefined' ? options.icon : '';
        var id        = typeof options.id !== 'undefined' ? options.id : '';

        var positionObj = new google.maps.LatLng(lat,lng);
        var marker, infowindow;

        //set map zoom
        if(zoom) {
            map.setZoom(zoom);
        }

        //center map on marker
        if(centerMap) {
            map.setCenter(positionObj);
        }

        infowindow = new google.maps.InfoWindow();
        infowindow.setContent(html);

        //add marker
        marker = new google.maps.Marker({
            position: positionObj,
            draggable: draggable,
            icon: icon,
            map: map,
            infowindow: infowindow
        });

        if(id != "") {
            marker.set("id", id)
        }

        //add info window
        if(html) {

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                if (infowindow) {
                    infowindow.close();
                }

                return function () {
                    infowindow.open(map, marker);
                }
            })(marker));
        }

        return marker;
    };

    /**
     * Open infowindow by marker id
     */
    function openInfoWindowById(id) {

        for (var i = 0; i < markers.length; i++) {
            if (markers[i].id == id) {
                marker = markers[i];
                infowindow = marker.infowindow
                infowindow.open(map, marker);
                map.setZoom(16);

                map.setCenter({lat: marker.position.lat(), lng: marker.position.lng()});
                return;
            }
        }
    }

    /**
     * Clear all map routes
     */
    function clearRoutes() {
        directionsDisplay.setMap(null);
    }

    /**
     * Clear all map markers
     */
    function clearMarkers() {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }

        /*      if(markerCluster) {
                  markerCluster.setMap(null);
              }*/
    }

    /**
     * Clear Marker by Id
     * @param id
     */
    function clearMarkerById(id) {
        //Find and remove the marker from the Array
        for (var i = 0; i < markers.length; i++) {
            if (markers[i].id == id) {
                //Remove the marker from Map
                markers[i].setMap(null);

                //Remove the marker from array.
                markers.splice(i, 1);
                return;
            }
        }
    };
</script>
@stop