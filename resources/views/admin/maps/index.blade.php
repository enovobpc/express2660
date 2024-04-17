@section('title')
    Mapa e Localização
@stop

@section('content-header')
    Mapa e Localização
@stop

@section('breadcrumb')
    <li class="active">Mapa e Localização</li>
@stop

@section('styles')
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
@stop

@section('content')
    <div class="maps-top {{ hasModule('gateway_gps') ? 'reduced' : '' }}">
        <div class="maps-left">
            <ul class="nav nav-tabs text-center" style="height: 50px">
                <li class="active">
                    <a href="#directions" data-toggle="tab" class="tab-directions" style="padding: 5px 12px;">
                        <i class="fas fa-location-arrow"></i> <br/>Rotas
                    </a>
                </li>
                <li>
                    <a href="#history" data-toggle="tab" class="tab-history" style="padding: 5px 12px;">
                        <i class="fas fa-route"></i> <br/>Trajetos
                    </a>
                </li>
                <li>
                    <a href="#deliveries" data-toggle="tab" class="tab-deliveries" style="padding: 5px 12px;">
                        <i class="fas fa-box-open"></i> <br/>Entregas
                    </a>
                </li>
                <li>
                    <a href="#customers" data-toggle="tab" class="tab-customers" style="padding: 5px 12px;">
                        <i class="fas fa-users"></i> <br/>Clientes
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="directions">
                    <div class="p-10">
                        @include('admin.maps.tabs.directions')
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="history">
                    <div class="p-10">
                        @if(hasModule('gateway_gps'))
                            @include('admin.maps.tabs.vehicle_route')
                        @else
                         @include('admin.maps.tabs.history')
                        @endif
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="deliveries">
                    <div class="p-10">
                        @include('admin.maps.tabs.deliveries')
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="customers">
                    <div class="p-10">
                        @include('admin.maps.tabs.customers')
                    </div>
                </div>
            </div>
        </div>
        <div class="maps-right">
            <div class="map-top map-route" style="display: none">
                <div class="map-route-actions">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#"
                                class="btn btn-sm btn-success"
                                data-toggle="print-manifest-url"
                                target="_blank">
                                <i class="fas fa-file-alt"></i> Manifesto Entregas
                            </a>
                            <a href="#"
                               class="btn btn-sm btn-default pull-right"
                               target="_blank">
                                <i class="fas fa-print"></i> Resumo
                            </a>
                        </div>
                    </div>
                </div>
                <div class="map-route-summary">
                    <span class="total-distance block m-t-5 w-55"><i class="fas fa-road"></i> --.- km</span>
                    <span class="total-time block m-t-5 w-45" style="border-right: none"><i class="far fa-clock"></i> --h--</span>
                </div>
                <div class="ordered-route-list">
                </div>
            </div>
            {{--<div class="map-view-mode">
                <div class="col-sm-4">
                    <button class="btn btn-sm btn-block btn-default"> Vista Mapa</button>
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-sm btn-block btn-default">Vista Satélite</button>
                </div>
            </div>--}}
            <div id="map" style="position: absolute; left: 0; right: 0; top: 0; bottom: 0;"></div>
        </div>
    </div>
    @if(hasModule('gateway_gps'))
    @include('admin.maps.partials.map_bottom')
    @endif
    @include('admin.maps.modals.shipments')
@stop

@section('scripts')

    <script src="{{ asset('assets/admin/js/maps.js') }}"></script>
    <script>
        var map, geocoder, infowindow, directionsService, directionsDisplay, flightPath;
        var markers  = [];
        var markerVehicles = [];
        var markerCluster = null;

        $(document).ready(function(){
            geocoder = new google.maps.Geocoder();
            directionsService = new google.maps.DirectionsService;
            directionsDisplay = new google.maps.DirectionsRenderer;
            initAutocomplete();

            @if(Request::get('address'))
            setMarkerByAddress($('[name="searchbox"]').val(), 16);
            @endif
        })

        /**
         * SET MAP MARKERS WHEN CLICK ON CUSTOMERS TAB
         */
        $(document).on('click', '.tab-customers', function(){
            clearMarkers();
            clearRoutes();
            clearVehiclesMarkers();

            $('.customers-list ul li').each(function () {
                var lat  = $(this).data('lat');
                var lng  = $(this).data('lng');
                var id   = $(this).data('id');
                var html = $(this).data('html');

                lat = typeof lat != 'undefined' ? lat : '';
                lng = typeof lng != 'undefined' ? lng : '';

                if(lat != "" && lng != "") {
                    options = { 'html' : html, 'id' : id }
                    marker = setMarker(lat, lng, options)
                    markers.push(marker);
                }
            })

            // Add a marker clusterer to manage the markers.
             markerCluster = new MarkerClusterer(map, markers, {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

        })

        /**
         * Click tab deliveries
         */
        $(document).on('click', '.tab-deliveries', function(){
            clearMarkers();
            clearRoutes();
            showDeliveryMarkers();
            clearVehiclesMarkers();
            clearFlightPath();
        });

        /**
         * Click tab history
         */
        $(document).on('click', '.tab-history', function(){
            clearMarkers();
            clearRoutes();
            clearVehiclesMarkers();
        });

        /**
         * Click tab directions
         */
        $(document).on('click', '.tab-directions', function(){
            clearMarkers();
            clearRoutes();
            @if(hasModule('gateway_gps'))
            setVehiclesMarkers();
            @else
            setOperatorsMarkers();
            @endif
        });

        $('.customers-list ul li').click(function(){
            var id = $(this).data('id');
            openInfoWindowById(id);
        })

        $(document).on('click', '.trace-operator-directions', function(){
            initDeliveryDirections();
        })

        $(document).on('click', '.calc-directions', function(){
            if($('[name="searchbox"]').val() == '') {
                $.bootstrapGrowl("Tem de selecionar uma morada ou local de partida.", {type: 'error', align: 'center', width: 'auto', delay: 8000});
            } else if($('[name="searchbox_destination"]').val() == ''){
                $.bootstrapGrowl("Tem de selecionar uma morada ou local de destino.", {type: 'error', align: 'center', width: 'auto', delay: 8000});
            } else {
                $('.map-route').show()
                initRouteDirections();
            }
        })
        
        $(document).on('click', '.add-waypoint', function () {
            var target = $('.waypoint-reference-html').html();
            $(this).closest('.input-group').prev().after(target);
        })

        $(document).on('click', '.remove-waypoint', function () {
            $(this).closest('.input-group').remove();
        })

        /**
         * GET LIST OF DELIVERIES
         *
         * @param s
         * @returns {string}
         */
        $('#deliveries [name="operator"], #deliveries [name="date"]').on('change', function(e){
            e.preventDefault();
            var $form = $(this).closest('form');
            clearMarkers();

            $('.deliveries-list').html('<div class="text-center m-t-10"><i class="fas fa-spin fa-circle-notch"></i> A carregar localizações...</div>')
            $.post($form.attr('action'), $form.serialize(), function(data){
                $('.deliveries-list').html(data.html)
            }).done(function(){
               showDeliveryMarkers();
            })
        })

        /**
         * GET LIST OF HISTORY
         */
        $('#history [name="operator"], #history [name="vehicle"], #history [name="date"]').on('change', function(e){
            e.preventDefault();
            var $form = $(this).closest('form');
            clearMarkers();

            $('.history-list').html('<div class="text-center m-t-10"><i class="fas fa-spin fa-circle-notch"></i> A carregar localizações...</div>')
            $.post($form.attr('action'), $form.serialize(), function(data){
                $('.history-list').html(data)
            }).done(function(){
                showHistoryMarkers();
            })
        })

        /*$(document).on('change', '[name="delivery_marker"]', function(e){
            e.stopPropagation();
            var id   = $(this).closest('li').data('id');
            var lat  = $(this).closest('li').data('lat');
            var lng  = $(this).closest('li').data('lng');
            var html = $(this).closest('li').data('html');
            var icon = $(this).closest('li').data('icon');


            if($(this).is(':checked')) {
                options = { 'html' : html, 'id' : id, 'icon': icon }
                marker = setMarker(lat, lng, options)
                markers.push(marker);
            } else {
                clearMarkerById(id);
            }
        });*/

        $(document).on('click', '.delivery-list-left', function (e) {
            e.stopPropagation();
        })

        $(document).on('click', '.deliveries-list ul li', function(){
            var id = $(this).data('id');
            if($(this).data('lat') != '' && $(this).data('lng') != '') {
                openInfoWindowById(id);
            }
        })

        $(document).on('click', '.history-list ul li', function(){
            var id = $(this).data('id');
            if($(this).data('lat') != '' && $(this).data('lng') != '') {
                openInfoWindowById(id);
            }
        })


        /**
         * INIT MAP
         */
        function initAutocomplete() {

            var input, input2, props, searchBox, places, bounds
            props = {
                center: {lat: 40.404874, lng: -7.874651},
                zoom: 8,
                zoomControl: true,
                mapTypeControl: true,
                navigationControl: false,
                streetViewControl: true,
                scrollwheel: true,
                mapTypeControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById('map'), props);

            // Create the search box and link it to the UI element.
            input  = document.getElementById('pac-input');
            input2 = document.getElementById('pac-input2');
           /*  searchBox = new google.maps.places.SearchBox(input);
            new google.maps.places.SearchBox(input2);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });

            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function() {
                places = searchBox.getPlaces();

                if (places.length == 0) { return; }

                // Clear out the old markers.
                clearMarkers();

                markers = [];

                // For each place, get the icon, name and location.
                bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {

                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    // Create a marker for each place.
                    options = {
                        html: '<b>' + place.name + '</b>'
                    }
                    marker = setMarker(place.geometry.location.lat(), place.geometry.location.lng(), options)
                    markers.push(marker)

                    if (place.geometry.viewport) { // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
 */
            @if(hasModule('gateway_gps'))
            setVehiclesMarkers();
            @else
            setOperatorsMarkers();
            @endif

        }

        /**
         * Set vehicles markers
         */
        function setVehiclesMarkers (autocenter) {

            autocenter = typeof autocenter == 'undefined' ? true : autocenter;

            // For each place, get the icon, name and location.
            bounds = new google.maps.LatLngBounds();

            $('.vehicles-list tbody tr').each(function () {
                var id   = $(this).data('id');
                var lat  = $(this).data('lat');
                var lng  = $(this).data('lng');
                var html = $(this).data('html');
                var isOn = $(this).data('on');

                icon = "{{ asset('assets/maps/vehicle_off.png') }}";
                if(isOn == '1') {
                    icon = "{{ asset('assets/maps/vehicle_on.png') }}";
                }

                options = { 'html' : html, 'id' : id, 'icon': icon }
                markerVehicle = setMarker(lat, lng, options)
                markerVehicles.push(markerVehicle);

                //extend the bounds to include each marker's position
                bounds.extend(markerVehicle.position);
            })


            //(optional) restore the zoom level after the map is done scaling
            if(autocenter) {
                //now fit the map to the newly inclusive bounds
                if($('.vehicles-list tbody tr').length) {
                    map.fitBounds(bounds);
                }

                var listener = google.maps.event.addListener(map, "idle", function () {
                    map.setZoom(6);
                    google.maps.event.removeListener(listener);
                });
            }
        }

        /**
         * Set vehicles markers
         */
        function setOperatorsMarkers (autocenter) {

            // For each place, get the icon, name and location.
            bounds = new google.maps.LatLngBounds();

            @foreach($operators as $operator)
                @if(@$operator->last_location->latitude)
                    options = {
                        'html' : "<b>{{ $operator->name }}</b><br/>Há {{ timeElapsedString($operator->last_location->created_at) }}<br/><button class='btn btn-xs btn-primary m-t-5 btn-show-route-history' data-id='{{ $operator->id }}'><i class='fas fa-route'></i> Ver histórico de trajeto</button>",
                        'id' : "{{ $operator->id }}",
                        'icon': "{{ $operator->getLocationMarker() }}"
                    }

                    markerVehicle = setMarker({{ $operator->last_location->latitude }},{{ $operator->last_location->longitude }}, options)
                    markerVehicles.push(markerVehicle);

                    //extend the bounds to include each marker's position
                    bounds.extend(markerVehicle.position);
                @endif
            @endforeach
        }

        $(document).on('click', '.vehicles-list tbody tr', function(){
            var id = $(this).data('id');
            openVehicleWindowById(id, 19);
        })

        /**
         * Set marker
         * @param lat
         * @param lng
         * @param options
         * @returns {google.maps.Marker}
         */
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
                icon:icon,
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
        function openInfoWindowById(id, zoom) {

            zoom = typeof zoom == 'undefined' ? 16 : zoom;

            for (var i = 0; i < markers.length; i++) {
                if (markers[i].id == id) {
                    marker = markers[i];
                    infowindow = marker.infowindow
                    infowindow.open(map, marker);
                    map.setZoom(zoom);

                    map.setCenter({lat: marker.position.lat(), lng: marker.position.lng()});
                    return;
                }
            }
        }

        /**
         * Open infowindow by marker id
         */
        function openVehicleWindowById(id, zoom) {

            zoom = typeof zoom == 'undefined' ? 16 : zoom;

            for (var i = 0; i < markerVehicles.length; i++) {
                if (markerVehicles[i].id == id) {
                    marker = markerVehicles[i];
                    infowindow = marker.infowindow
                    infowindow.open(map, marker);
                    map.setZoom(zoom);

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
         * Clear flight path
         */
        function clearFlightPath() {
            flightPath.setMap(null);
        }

        /**
         * Clear all map markers
         */
        function clearMarkers() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }

            if(markerCluster) {
                markerCluster.setMap(null);
            }
        }

        /**
         * Clear all map markers
         */
        function clearVehiclesMarkers() {
            for (var i = 0; i < markerVehicles.length; i++) {
                markerVehicles[i].setMap(null);
            }

            if(markerCluster) {
                markerCluster.setMap(null);
            }
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

        function setMarkerByAddress(address, zoom, obj) {

            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    options = {
                        zoom: zoom,
                        centerMap: true,
                        html: address
                    }
                    setMarker(results[0].geometry.location.lat(), results[0].geometry.location.lng(), options)

                } else {
                    $.bootstrapGrowl("O pedido de direções falhou ou não inclui todos os pontos. Motivo:" + status, {type: 'warning', align: 'center', width: 'auto', delay: 10000});
                }
            });
        }

        function setWaypointMarker (address, zoom, obj) {
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {

                    options = {
                        zoom: zoom,
                        centerMap: true,
                        html: $('[name="searchbox"]').val()
                    }
                    setMarker(results[0].geometry.location.lat(), results[0].geometry.location.lng(), options)
                } else {
                    obj.css('border-color', 'red')
                    $.bootstrapGrowl("Não foi possível encontrar o local indicado.", {type: 'error', align: 'center', width: 'auto', delay: 10000});
                }
            });
        }

        function showDeliveryMarkers(){

            // For each place, get the icon, name and location.
            bounds = new google.maps.LatLngBounds();

            $('.deliveries-list ul li').each(function () {
                var id   = $(this).data('id');
                var lat  = $(this).data('lat');
                var lng  = $(this).data('lng');
                var html = $(this).data('html');
                var icon = $(this).data('icon');

                lat = typeof lat != 'undefined' ? lat : '';
                lng = typeof lng != 'undefined' ? lng : '';

                if(lat != "" && lng != "") {
                    options = {'html': html, 'id': id, 'icon': icon}
                    marker = setMarker(lat, lng, options)
                    markers.push(marker);

                    //extend the bounds to include each marker's position
                    bounds.extend(marker.position);
                }
            })

            $('.deliveries-rourte-list ul li').each(function () {
                var id   = $(this).data('id');
                var lat  = $(this).data('lat');
                var lng  = $(this).data('lng');
                var html = $(this).data('html');
                var icon = $(this).data('icon');

                lat = typeof lat != 'undefined' ? lat : '';
                lng = typeof lng != 'undefined' ? lng : '';

                if(lat != "" && lng != "") {
                    options = {'html': html, 'id': id, 'icon': icon}
                    marker = setMarker(lat, lng, options)
                    markers.push(marker);

                    //extend the bounds to include each marker's position
                    bounds.extend(marker.position);
                }
            })

            showDeliveryTraject()

            //now fit the map to the newly inclusive bounds
            if($('.deliveries-list ul li').length) {
                map.fitBounds(bounds);
            }
        }

        function showDeliveryTraject(){

            if(flightPath) {
                flightPath.setMap(null);
            }

            //bounds = new google.maps.LatLngBounds();

            var markersPath = [];
            $('.delivery-traject > ul > li').each(function () {
                var id   = $(this).data('trjid');
                var lat  = parseFloat($(this).data('trjlat'));
                var lng  = parseFloat($(this).data('trjlng'));


                options = { 'id' : id , icon: '{{ asset('assets/img/maps/marker.png') }}'}
                marker = setMarker(lat, lng, options)
                markers.push(marker);

                markersPath.push({lat, lng});

                //extend the bounds to include each marker's position
                bounds.extend(marker.position);
            })

            flightPath = new google.maps.Polyline({
                path: markersPath,
                geodesic: true,
                strokeColor: '#3679ed',
                strokeOpacity: 1.0,
                strokeWeight: 3
            });

            flightPath.setMap(map);

        }

        function showHistoryMarkers(){

            if(flightPath) {
                flightPath.setMap(null);
            }

            bounds = new google.maps.LatLngBounds();

            var markersPath = [];
            $('.history-list > ul > li').each(function () {
                var id   = $(this).data('id');
                var lat  = parseFloat($(this).data('lat'));
                var lng  = parseFloat($(this).data('lng'));
                var html = $(this).data('html');
                //var icon = $(this).data('icon');

                options = { 'html' : html, 'id' : id , icon: '{{ asset('assets/img/maps/path_point.png') }}'}
                marker = setMarker(lat, lng, options)
                markers.push(marker);

                markersPath.push({lat, lng});

                //extend the bounds to include each marker's position
                bounds.extend(marker.position);
            })

            flightPath = new google.maps.Polyline({
                path: markersPath,
                geodesic: true,
                strokeColor: '#3679ed',
                strokeOpacity: 1.0,
                strokeWeight: 3
            });

            flightPath.setMap(map);

            //now fit the map to the newly inclusive bounds
            map.fitBounds(bounds);
        }

        function initDirections(){
            directionsDisplay.setMap(map);
            calculateAndDisplayRoute(directionsService, directionsDisplay);
        }

        /**
         * Trace best route to deliveries
         */
        function initDeliveryDirections(){
            //Documentation: https://developers.google.com/maps/documentation/javascript/directions
            directionsDisplay.setMap(map);
            var originAddress      = '{{ Setting::get('address_1') }}, {{ Setting::get('city_1') }}'
            var destinationAddress = '{{ Setting::get('address_1') }}, {{ Setting::get('city_1') }}'
            var avoidHighways      = $('.deliveries-route-options [name="avoid_highways"]').is(':checked');
            var avoidTolls         = $('.deliveries-route-options [name="avoid_tolls"]').is(':checked');

            var waypts    = [];
            var locations = []

            $('.deliveries-list ul li').each(function () {
                checked = $(this).find('[name="delivery_marker"]').is(':checked')
                lat = $(this).data('lat');
                lng = $(this).data('lng');

                if(checked && lat!="" && lng != "") {
                    locations.push(['title', lat,lng])
                }
            })


            for (i = 0; i < locations.length; i++) {

                if (!waypts) {
                    waypts = [];
                }

                waypts.push({
                    location: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    stopover: true
                });
            }

            directionsService.route({
                origin: originAddress,
                destination: destinationAddress,
                waypoints: waypts,
                optimizeWaypoints: true, //reorganiza para melhor rota
                avoidHighways: avoidHighways,
                avoidTolls: avoidTolls,
                travelMode: 'DRIVING'
            }, function (response, status) {

                if (status === 'OK') {
                    directionsDisplay.setDirections(response);

                    var totalDistance = 0;
                    var totalDuration = 0;
                    var deliveryList = '';
                    var legs = response.routes[0].legs;
                    for (var i = 0; i < legs.length; ++i) {
                        deliveryList+='<li>'+legs[i].end_address+'</li>';
                        totalDistance += legs[i].distance.value;
                        totalDuration += legs[i].duration.value;
                    }

                    totalDistance = totalDistance / 1000
                    $('.map-top').show();
                    $('.ordered-route-list').html('<ul>' + deliveryList + '</ul>')
                    $('.total-distance').html('<i class="fas fa-road"></i> ' + totalDistance.toFixed(2) + ' km')
                    $('.total-time').html('<i class="far fa-clock"></i> ' + secondsTimeSpanToHMS(totalDuration.toFixed(2)))
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }

        /**
         * Trace best route to deliveries
         */
        function initRouteDirections(){
            //Documentation: https://developers.google.com/maps/documentation/javascript/directions
            clearMarkers();

            directionsDisplay.setMap(map);
            var originAddress      = $('[name="searchbox"]').val();
            var destinationAddress = $('[name="searchbox_destination"]').val();
            var avoidHighways      = $('.directions-route-options [name="avoid_highways"]').is(':checked');
            var avoidTolls         = $('.directions-route-options [name="avoid_tolls"]').is(':checked');

            var waypts    = [];
            $('.route-waypoint').each(function(){
                var value = $(this).val();

                if(value != "") {
                    waypts.push({
                        location: value,
                        stopover: true
                    });
                }
            })



            directionsService.route({
                origin: originAddress,
                destination: destinationAddress,
                waypoints: waypts,
                optimizeWaypoints: true, //reorganiza para melhor rota
                avoidHighways: avoidHighways,
                avoidTolls: avoidTolls,
                travelMode: 'DRIVING'
            }, function (response, status) {

                if (status === 'OK') {
                    directionsDisplay.setDirections(response);

                    var deliveryMapUrl = "{{ route('admin.printer.shipments.delivery-map', '') }}";
                    var shipmentUrl    = "{{ route('admin.shipments.show', '') }}";
                    var letters        = ['A','B','C','D','E','F','G','H','I','J','K','L','M',
                                          'N','O','P','Q','R','S','T','U','V','X','Y','W','Z']
                    var totalDistance = 0;
                    var totalDuration = 0;
                    var deliveryList  = '';
                    var idsQuery      = '';

                    var legs     = response.routes[0].legs;
                    var order    = response.routes[0].waypoint_order;

                    for (var i = 0; i < legs.length; ++i) {

                        var shipmentRowIndex = order[i];
                        var $shipmentRow = $('.route-destination-input').eq(shipmentRowIndex + 1); //o 1º indice desta classe é o html modelo

                        if(i == 0) {
                            deliveryList+= '<li>';
                            deliveryList+= '<div class="label">A</div>';
                            deliveryList+= '<div class="pull-left" style="padding-left:22px">';
                            deliveryList+= '<div class="address">' + legs[i].start_address+'</div>';
                            deliveryList+= '</div>';
                            deliveryList+= '<div class="clearfix"></div>';
                            deliveryList+= '</li>';
                        }

                        deliveryList+= '<li>';
                        deliveryList+= '<div class="label">' +letters[i+1]+ '</div>';
                        deliveryList+= '<div class="pull-left" style="padding-left:22px">';
                        deliveryList+= '<div class="timer"><i class="fas fa-road"></i> '+ legs[i].distance.text+' | <i class="far fa-clock"></i> ' + legs[i].duration.text+'</div>';
                        if($shipmentRow.find('[name="waypoint_trk"]').val()) {
                            idsQuery+='&id[]=' + $shipmentRow.find('[name="waypoint_id"]').val();
                            deliveryList+= '<div class="trk">';
                            deliveryList+= '<a href="' + shipmentUrl + '/' + $shipmentRow.find('[name="waypoint_id"]').val()  + '" data-toggle="modal" data-target="#modal-remote-xl">TRK ' +  $shipmentRow.find('[name="waypoint_trk"]').val() + '</a>';
                            deliveryList+= '</div>';
                            deliveryList+= '<div class="name">'+  $shipmentRow.find('[name="waypoint_name"]').val() +'</div>';
                        }
                        deliveryList+= '<div class="address">' + legs[i].end_address+'</div>';
                        deliveryList+= '</div>';
                        deliveryList+= '<div class="clearfix"></div>';
                        deliveryList+= '</li>';

                        totalDistance += legs[i].distance.value;
                        totalDuration += legs[i].duration.value;
                    }

                    if(idsQuery == '') {
                        $('[data-toggle="print-manifest-url"]').attr('disabled', true);
                    } else {
                        idsQuery = idsQuery.substr(1);
                        $('[data-toggle="print-manifest-url"]').attr('disabled', false);
                        $('[data-toggle="print-manifest-url"]').attr('href', deliveryMapUrl + idsQuery + '&sortBy=picking-order');
                    }


                    totalDistance = totalDistance / 1000
                    $('.map-top').show();
                    $('.ordered-route-list').html('<ul>' + deliveryList + '</ul>')
                    $('.total-distance').html('<i class="fas fa-road"></i> ' + totalDistance.toFixed(2) + ' km')
                    $('.total-time').html('<i class="far fa-clock"></i> ' + secondsTimeSpanToHMS(totalDuration.toFixed(2)))


                    /*var route = response.routes[0];
                    var summaryPanel = document.getElementById('directions-panel');
                    summaryPanel.innerHTML = '';*/
                    /*// For each route, display summary information.
                    for (var i = 0; i < route.legs.length; i++) {
                        var routeSegment = i + 1;
                        summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                            '</b><br>';
                        summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
                        summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                        summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                    }*/
                } else {
                    $.bootstrapGrowl("O pedido de direções falhou ou não inclui todos os pontos. Motivo:" + status, {type: 'error', align: 'center', width: 'auto', delay: 10000});
                }
            });
        }

        function secondsTimeSpanToHMS(s) {
            var h = Math.floor(s/3600); //Get whole hours
            s -= h*3600;
            var m = Math.floor(s/60); //Get remaining minutes
            s -= m*60;
            return h+"h"+(m < 10 ? '0'+m : m); //zero padding on minutes and seconds
        }
    </script>
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey() }}&callback=initAutocomplete"></script>

    <script>
        $(document).on('click', '[data-target="#modal-select-shipments"]', function() {

            var $tab = $(this);

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);
                var oTable = $('#datatable-shipments').DataTable({
                    columns: [
                        {data: 'tracking_code', name: 'tracking_code', visible: false},
                        {data: 'id', name: 'id'},
                        {data: 'sender_name', name: 'sender_name'},
                        {data: 'recipient_name', name: 'recipient_name'},
                        {data: 'service_id', name: 'service_id', searchable: false},
                        {data: 'volumes', name: 'volumes', searchable: false},
                        {data: 'status_id', name: 'status_id', searchable: false, 'class': 'brd-right'},

                        {data: 'read_sender', name: 'read_sender', orderable: false, searchable: false},
                        {data: 'read_recipient', name: 'read_recipient', orderable: false, searchable: false},
                        {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
                        {data: 'sender_city', name: 'sender_city', visible: false},
                        {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                        {data: 'recipient_city', name: 'recipient_city', visible: false},
                    ],
                    ajax: {
                        url: "{{ route('admin.maps.shipments.datatable') }}",
                        type: "POST",
                        data: function (d) {
                            d.sender_agency = $('#modal-select-shipments select[name=sender_agency]').val()
                            d.recipient_agency = $('#modal-select-shipments select[name=recipient_agency]').val()
                            d.provider = $('#modal-select-shipments select[name=provider]').val()
                            d.operator = $('#modal-select-shipments select[name=operator]').val()
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });
            }

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();
            });
        });

        $(document).on('change', '.route-waypoint', function(){
            var address = $(this).val();
            setWaypointMarker(address, 8, $(this));
        })

        $(document).on('click', '.code-read', function(){

            clearVehiclesMarkers()

            var name     = $(this).data('name');
            var address  = $(this).data('address');
            var tracking = $(this).data('trk');
            var id       = $(this).data('id');

            //setMarkerByAddress(address, 8);
            $('.add-waypoint').trigger('click');

            var $lastWaypoint = $(document).find('.route-waypoint').last().closest('.route-destination-input');
            $lastWaypoint.find('[name="searchbox_waypoint"]').val(address).trigger('change')
            $lastWaypoint.find('[name="waypoint_name"]').val(name).trigger('change')
            $lastWaypoint.find('[name="waypoint_trk"]').val(tracking).trigger('change')
            $lastWaypoint.find('[name="waypoint_id"]').val(id).trigger('change')

            $(this).prop('disabled', true);
        })


        /**
         * Search customers
         */
        $(document).on('keyup', '.searchbox-customers input', function(){
            var txt = removeAccents($(this).val());
            var target = $(this).closest('.searchbox-customers').data('target');

            console.log(txt);
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

        $(document).on('click', '.maps-left .nav-tabs a', function(){
            $('.map-top').hide();
        })

        $(document).on('click', '.btn-warehouse', function () {
            var address = $(this).data('address');
            $(this).closest('.input-group').find('input').val(address);
        })

        $(document).on('click', '.btn-collapse-map-bottom', function(){
            colapseMapsBottom()
        })

        $(document).on('click', '.maps-bottom.colapsed th', function(){
            if($(this).hasClass('reduced')) {
                $('.btn-collapse-map-bottom').trigger('click')
            }
        })


        @if(hasModule('gateway_gps'))
        function gpsCheck() {
            colapsed = $('.maps-bottom').hasClass('colapsed');
            $.post('{{ route("admin.maps.sync.location", 'vehicles') }}', function(data){
                if(data.result) {
                    clearVehiclesMarkers();
                    $('.maps-bottom').replaceWith(data.html);
                    setVehiclesMarkers(false);

                    if(colapsed) {
                        colapseMapsBottom();
                    }
                    return;
                }
            }).fail(function(){
            });
        }

        var gpsChecker = setInterval(gpsCheck, {{ Setting::get('gps_gateway') == 'Cartrack' ? 20000 : 5000 }});
        gpsCheck();
        @endif


        function colapseMapsBottom() {
            if($('.maps-bottom').hasClass('colapsed')) {
                colapseBottom()
            } else {
                expandBottom()
            }
        }

        function colapseBottom() {
            $('.maps-bottom').removeClass('colapsed')
            $('.maps-top').addClass('reduced')
        }

        function expandBottom() {
            $('.maps-top').removeClass('reduced')
            $('.maps-bottom').addClass('colapsed')
        }

    </script>
@stop
