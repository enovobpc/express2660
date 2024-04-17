<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Mapa e Localização</h4>
</div>
<div class="modal-body" style="padding: 0 0 8px 0;">
    @if(!hasModule('maps'))
    <div class="alert" style="position: absolute;
    z-index: 200;
    left: 287px;
    right: 0;
    top: 0;
    bottom: -12px;
    background: rgba(255,255,255,0.85);
    font-size: 20px;
    text-align: center;
    padding-top: 18%;
    font-weight: bold;">
        Módulo de mapa e localização não incluído no plano contratado.
    </div>
    @endif
    <div class="tabbable-line m-0">
        <ul class="nav nav-tabs">
            @if(hasModule('gateway_gps'))
            <li class="tab-location active">
                <a href="#tab-location" data-toggle="tab">
                    <i class="fas fa-map-marker-alt"></i> Viaturas
                </a>
            </li>
            @endif
            <li class="tab-operators {{ hasModule('gateway_gps') ? '' : 'active' }}">
                <a href="#tab-operators" data-toggle="tab">
                    <i class="fas fa-map-marker-alt"></i> Motoristas
                </a>
            </li>
            @if(!hasModule('gateway_gps'))
                <li>
                    <a href="#tab-location" data-toggle="tab">
                        <i class="fas fa-map-marker-alt"></i> Viaturas
                    </a>
                </li>
            @endif
            <li>
                <a href="#tab-history" data-toggle="tab">
                    <i class="fas fa-route"></i> Histórico de Trajeto
                </a>
            </li>
        </ul>
    </div>
    <div class="modal-body p-0">
        <div class="row row-0">
            <div class="col-sm-3" data-tab="#tab-location" style="border-right: 1px solid #999; {{ hasModule('gateway_gps') ? '' : 'display: none;' }}">
                @if(hasModule('gateway_gps'))
                <div class="vehicles-list">
                    <div class="filter-list" data-target=".vehicles-data-list>li">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-search"></i>
                            </div>
                            {{ Form::text('search', null, ['placeholder' => 'Procurar viatura...', 'class' => 'form-control']) }}
                        </div>
                    </div>
                    @include('admin.maps.partials.modal_vehicles_list')
                </div>
                @else
                    <div class="text-center p-t-30">
                        <i class="fas fa-map-marked-alt fs-70"></i>
                        <br/>
                        <h4 style="font-weight: bold;margin-top: 30px;">Viaturas em Tempo Real</h4>
                        <p>
                            Integre em tempo real a localização das suas viaturas,
                            níveis de combustível, trajetos percorridos, velocidades
                            entre outras funcionalidades.
                        </p>
                        <p>Contacte-nos para mais informação.</p>
                        <hr/>
                        <small>Em parceria com</small><br/>
                        <img src="{{ asset('assets/img/default/inosat.png') }}" class="h-40px"/>
                        <img src="{{ asset('assets/img/default/gesfrota.png') }}" class="h-40px"/>
                    </div>
                @endif
            </div>
            <div class="col-sm-3" data-tab="#tab-operators" style="border-right: 1px solid #999; {{ hasModule('gateway_gps') ? 'display: none;' : '' }}">
                <div class="operators-list">
                    <div class="filter-list" data-target=".operators-data-list>li">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-search"></i>
                            </div>
                            {{ Form::text('search', null, ['placeholder' => 'Procurar motorista...', 'class' => 'form-control']) }}
                        </div>
                    </div>
                    @include('admin.maps.partials.modal_operators_list')
                </div>
            </div>
            <div class="col-sm-3" data-tab="#tab-history" style="border-right: 1px solid #999; display: none">
                <div class="operators-list">
                    @if(hasModule('gateway_gps'))
                        <div class="filter-list" data-target=".locations-history-list li">
                            <div class="row row-0">
                                <div class="col-sm-8">
                                    {{ Form::select('vehicle', ['' => ''] + $vehiclesList, null, ['class' => 'form-control select2', 'data-placeholder' => 'Selecione Viatura']) }}
                                </div>
                                <div class="col-sm-4">
                                    {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'style' => 'border-bottom: 1px solid #ddd; border-top: 1px solid #ddd']) }}
                                </div>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-search"></i>
                                </div>
                                {{ Form::text('search', null, ['placeholder' => 'Procurar horário...', 'class' => 'form-control']) }}
                            </div>
                        </div>
                    @else
                        <div class="filter-list" data-target=".locations-history-list li">
                            <div class="row row-0">
                                <div class="col-sm-8">
                                    {{ Form::select('operator', ['' => ''] + $operatorsList, null, ['class' => 'form-control select2', 'data-placeholder' => 'Selecione Operador']) }}
                                </div>
                                <div class="col-sm-4">
                                    {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'style' => 'border-bottom: 1px solid #ddd; border-top: 1px solid #ddd']) }}
                                </div>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-search"></i>
                                </div>
                                {{ Form::text('search', null, ['placeholder' => 'Procurar horário...', 'class' => 'form-control']) }}
                            </div>
                        </div>
                    @endif
                    <div class="locations-history-list nicescroll">
                        @include('admin.maps.partials.history_list')
                    </div>
                </div>
            </div>
            <div class="col-sm-9">
                <div id="map" style="height: 465px; width: 100%"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="margin-top: -8px; margin-bottom: -10px;">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<style>
    .content-wrapper {
        height: 100vh !important;
    }

    .brd-right {
        border-right: 2px solid #333;
    }

    .gm-style .gm-style-iw-c {
        padding: 3px !important;
    }

    .gm-style-iw-d {
        max-height: 230px;
    }

    .vmap-local {
        padding: 10px;
        border-top: 1px solid #777;
    }

    .route-detail li {
        border:0 !important;
        padding-bottom: 0 !important;
    }
</style>

<script>
    $('.nicescroll').niceScroll(Init.niceScroll());
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $('.modal [href="#tab-history"]').on('click', function(){
        clearMarkers()
        $('[data-tab="#tab-history"]').show();
        $('[data-tab="#tab-location"]').hide();
        $('[data-tab="#tab-operators"]').hide();
        $('.operator-location-item').css('background', '#fff')
    })

    $('.modal [href="#tab-location"]').on('click', function(){
        clearMarkers()
        markCurrentVehiclesLocations();
        $('[data-tab="#tab-history"]').hide();
        $('[data-tab="#tab-location"]').show();
        $('[data-tab="#tab-operators"]').hide();
    })

    $('.modal [href="#tab-operators"]').on('click', function(){
        clearMarkers()
        markCurrentOperatorsLocations();
        $('[data-tab="#tab-operators"]').show();
        $('[data-tab="#tab-history"]').hide();
        $('[data-tab="#tab-location"]').hide();
    })

    $(document).on('click', '.btn-show-route-history',function(){
        var operatorId = $(this).data('id');
        $('.modal [href="#tab-history"]').trigger('click');
        $('li[data-id="'+operatorId+'"]').trigger('click');
    })

    $(document).on('change', '[data-tab="#tab-history"] [name="date"], [data-tab="#tab-history"] [name="operator"], [data-tab="#tab-history"] [name="vehicle"]',function(){
        getOperatorLocationHistory();
    })

    function getOperatorLocationHistory() {

        var operator = $('[data-tab="#tab-history"] [name="operator"]').val();
        var vehicle  = $('[data-tab="#tab-history"] [name="vehicle"]').val();
        var date     = $('[data-tab="#tab-history"] [name="date"]').val();

        $('.locations-history-list').html('<div class="m-t-20 text-center"><i class="fas fa-spin fa-circle-notch"></i> A carregar...</div>')
        clearMarkers();
        $.post("{{ route('admin.maps.load.operator.history') }}", {operator:operator, date:date, vehicle:vehicle}, function(data) {
            $('.locations-history-list').html(data);
            markHistoryLocations();
        })
    }

    var map, infowindow, directionsDisplay;
    var bounds = new google.maps.LatLngBounds();
    var markers = flightPlanCoordinates = [];

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

        @if(hasModule('gateway_gps'))
        markCurrentVehiclesLocations();
        @else
        markCurrentOperatorsLocations();
        @endif
    })



    $(document).on('keyup', '.filter-list input', function(){
        var txt = removeAccents($(this).val());
        var target  = $(this).closest('.filter-list').data('target');
        var $target = $(document).find(target);

        if(txt == '') {
            $target.show();
        } else {
            $target.hide();

            $('.filter-noresults').hide()
            $target.each(function(){
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

    $(document).on('click', '.operators-list > ul > li', function(){
        $('.operators-list > ul > li').css('background-color', '#fff');

        var id = $(this).data('id');
        openInfoWindowById(id);
        $('.close-customers-list').trigger('click');
        $(this).css('background-color', '#b0e9ff');

    })

    $(document).on('click', '.vehicles-list > ul > li', function(){
        $('.vehicles-list > ul > li').css('background-color', '#fff');

        var id = $(this).data('id');
        openInfoWindowById(id);
        $('.close-customers-list').trigger('click');
        $(this).css('background-color', '#b0e9ff');

    })

    $(document).on('click', '.locations-history-list ul li', function(){
        $(document).find('.locations-history-list ul li').css('background-color', '#fff');

        var id = $(this).data('id');
        openInfoWindowById(id);
        $(this).css('background-color', '#b0e9ff');

    })

    var flightPath;
    function markHistoryLocations() {

        var markersPath = [];

        if(flightPath) {
            flightPath.setMap(null);
        }

        $('.locations-history-list li').each(function () {
            var lat  = parseFloat($(this).data('lat'));
            var lng  = parseFloat($(this).data('lng'));
            var id   = $(this).data('id');
            var html = $(this).data('html');
            var latitude  = lat;
            var longitude = lng;

            if(lat != "" && lng != "") {

                options = { 'html' : html, 'id' : id , 'icon' : '/icon.png'}
                marker = setMarker(lat, lng, options)
                markers.push(marker);

                bounds.extend(marker.position); //auto center map

                markersPath.push({lat, lng});
            }
        })

        flightPath = new google.maps.Polyline({
            path: markersPath,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        flightPath.setMap(map);

        if($('.locations-history-list ul li').length) {
            map.fitBounds(bounds); //auto center map
        }
    }

    function markCurrentOperatorsLocations(resetZoom) {

        resetZoom = typeof resetZoom == 'undefined' ? true : resetZoom

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

        if(resetZoom) {
            if ($('.operators-list ul li').length) {
                map.fitBounds(bounds); //auto center map
            }
        }
    }

    function markCurrentVehiclesLocations(resetZoom) {

        resetZoom = typeof resetZoom == 'undefined' ? true : resetZoom

        $('.vehicles-data-list > li').each(function () {
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

        if(resetZoom) {
            if ($('.vehicles-list > ul > li').length) {
                map.fitBounds(bounds); //auto center map
            }
        }
    }

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

    var gpsChecker = setInterval(function () {
        @if(hasModule('gateway_gps'))
        if($('.tab-location').hasClass('active')) {
            $.post('{{ route("admin.maps.sync.location", ['vehicles', 'target' => 'modal']) }}', function (data) {
                if (data.result) {
                    $('.vehicles-data-list').replaceWith(data.html);
                    clearMarkers()
                    markCurrentVehiclesLocations(false);
                    return;
                }
            }).fail(function () {
            });
        }
        @endif

        if($('.tab-operators').hasClass('active')) {
            $.post('{{ route("admin.maps.sync.location", ['operators', 'target' => 'modal']) }}', function (data) {
                if (data.result) {
                    $('.operators-data-list').replaceWith(data.html);
                    clearMarkers()
                    markCurrentOperatorsLocations(false);
                    return;
                }
            }).fail(function () {
            });
        }
    }, 10000);

</script>
