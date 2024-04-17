<?php $modalHash = rand(); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">@trans('Localização de serviços')</h4>
</div>
<div class="modal-body modal-{{ $modalHash }} modal-shipments-map p-0 p-b-8">
    <div class="row row-0">
        <div class="col-sm-12">
            <div class="modal-map-deliveries-filters">
                {{ Form::open(['route' => ['admin.trips.shipments.map.show', $trip->id], 'method' => 'get', 'style' => 'margin: -10px 0;']) }}
                {{ Form::hidden('filter', 1) }}
                <ul class="list-inline">
                    <li style="float: left">
                        <strong>@trans('Data recolha')</strong><br/>
                        <div class="input-group input-group-sm w-220px">
                            {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                            <span class="input-group-addon">até</span>
                            {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                        </div>
                    </li>
                    <li>
                        <strong>@trans('Tipo Transporte')</strong><br/>
                        <div class="w-120px">
                            {{ Form::selectMultiple('transport_type[]', $transportTypes, fltr_val(Request::all(), 'transport_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li>
                        <strong>@trans('Serviço')</strong><br/>
                        <div class="w-120px">
                            {{ Form::selectMultiple('service[]', $services, fltr_val(Request::all(), 'service'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li>
                        <strong>@trans('Fornecedor')</strong><br/>
                        <div class="w-110px">
                            {{ Form::selectMultiple('provider[]', $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li>
                        <strong>@trans('Rota')</strong><br/>
                        <div class="w-110px">
                            {{ Form::selectMultiple('route[]', $routes, fltr_val(Request::all(), 'route'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li>
                        <strong>@trans('Pais Origem')</strong><br/>
                        <div class="w-110px">
                            {{ Form::selectMultiple('sender_country[]', trans('country'), fltr_val(Request::all(), 'sender_country'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li>
                        <strong>@trans('Pais Destino')</strong><br/>
                        <div class="w-110px">
                            {{ Form::selectMultiple('recipient_country[]', trans('country'), fltr_val(Request::all(), 'sender_country'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    {{-- <li>
                        <button type="button" class="btn btn-sm btn-filter-map btn-default m-t-15">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li> --}}
                </ul>
                {{ Form::close() }}
                <div class="clearfix"></div>
                {{-- <ul class="list-inline map-filters-extended" style="display: none">
                    <li style="float: left">
                        
                    </li>
                </ul>
                <div class="clearfix"></div> --}}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="map-tips">
                <div class=""><i class="fas fa-hand-pointer"></i><b>2x</b> @trans('Duplo clique no marcador para adicionar o serviço')</div>
            </div>
            <div class="map-legend">
                <a href="{{ route('admin.services.index') }}" class="pull-right"><small><i class="fas fa-cog"></i> @trans('Gerir Icones')</small></a>
                <h4>@trans('Legenda') <i class="fas fa-angle-down"></i></h4>
                <ul class="list-unstyled" style="display: none">
                @foreach ($allServices as $service)
                    @if(@$service->marker_icon) 
                    <li><img src="{{ asset($service->marker_icon) }}"> {{ @$service->name }}</li>        
                    @endif            
                @endforeach
            </ul>
            </div>
            <div id="mapModalShipments" 
            style="width: 100%;
            height: 550px;
            position: relative;
            overflow: hidden;
            border-right: 1px solid #999;"></div>
        </div>
        <div class="col-sm-3">
            <h4 class="list-shipments-title">@trans('Por atribuir') (<span class="count-markers-available">{{ $shipments->count() }}</span>)</h4>
            <div class="list-shipments shipments-markers">
                @include('admin.trips.partials.shipments_markers')
            </div>
            <h4 class="list-shipments-title">@trans('Serviços selecionados') (<span class="count-markers-selected">0</span>)</h4>
            <div class="list-shipments shipments-markers-selected">
        
                @if($shipments->isEmpty())
                    <p class="text-muted text-center m-t-10 m-b-10">
                        <i class="fas fa-info-circle"></i> @trans('Não selecionou serviços')<br/>
                        <small>@trans('Clique nos serviços que quer adicionar ao mapa de entrega.')</small>
                    </p>
                @else
                <ul class="list-unstyled">
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="margin-top: -8px; margin-bottom: -10px;">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="button" class="btn btn-primary" data-dismiss="modal">@trans('Adicionar Selecionados')</button>
</div>

<style>

    .modal-map-deliveries-filters {
        padding: 7px 15px;
        background: #f2f2f2;
        border-bottom: 1px solid #ccc;
        position: relative;
        z-index: 1;
    }

    .modal-map-deliveries-filters li {
        float: left;
    }

    .modal-map-deliveries-filters li .select2-container .select2-selection--single {
        padding: 4px 10px;
        height: 30px;
    }

    .modal-map-deliveries-filters li .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 28px;
        right: 2px;
    }

    .list-shipments-title {
        margin: 0;
        background: #e5e5e5;
        padding: 5px 10px;
        border-bottom: 1px solid #999;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .list-shipments {
        padding: 0;
        height: 249px;
        overflow: scroll;
    }

    .list-shipments li {
        border-bottom: 1px solid #ddd;
        padding: 5px 10px;
        cursor: pointer;
    }

    .list-shipments li:hover {
        background: #f2f2f2;
    }

    .empty-coords {
        background: #ffa900;
        padding: 1px 5px;
        border-radius: 3px;
        font-size: 12px;
    }
   
   .list-marker {
        float: left;
        height: 16px;
   }

   .shipments-markers .empty-coordinates {
    opacity: 0.5;
   }

   .map-tips {
    position: absolute;
    z-index: 1;
    left: 200px;
    top: 10px;
    background: #ffc700;
    padding: 12px;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.3);
   }

   .map-legend {
    position: absolute;
    z-index: 1;
    left: 10px;
    top: 60px;
    width: 173px;
    max-height: 240px;
    background: rgba(255,255,255,0.8);
    border-radius: 2px;
    padding: 7px;
    overflow-y: scroll;
   }

   .map-legend li {
    padding: 2px 0;
    width: 140px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
   }

   .map-legend img {
    height: 17px;
   }

   .map-legend h4 {
    margin: 0;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer
   }

   .map-legend ul {
    margin-top: 5px;
   }
</style>

<script type="text/javascript">
    var MODAL_HASH = ".modal-{{ $modalHash }}"
    $('.modal-shipments-map .select2').select2(Init.select2());
    $('.modal-shipments-map .select2-multiple').select2MultiCheckboxes(Init.select2Multiple());
    $('.modal-shipments-map .datepicker').datepicker(Init.datepicker());

    $(document).on('click', '.modal .list-shipments li', function(){
        var id  = $(this).data('id');
        closeAllInfoWindow();
        openInfoWindowById(id, 16);
    })

    $('.map-legend h4').on('click', function(){
        $('.map-legend ul').slideToggle();
    })

    $('.btn-filter-map').on('click', function(){
        $('.map-filters-extended').toggle();
    })


    $('.modal-map-deliveries-filters').on('change', 'input, select', function(){
        var $form = $(this).closest('form');
        var url   = $form.attr('action')
        var data  = $form.serialize();

        $('.modal .shipments-markers').html('<div style="text-align: center;padding-top: 105px;"><i class="fas fa-spin fa-circle-notch"></i> Aguarde...</div>')
        $.get(url, data, function(data){
            $('.modal .shipments-markers').html(data);
            clearModalMarkers();
            setModalMapMarkers();
        }).fail(function () {
            Growl.error500()
        })
    })

    $(document).on('click', MODAL_HASH + ' .marker-add-shipment', function(e){
        e.preventDefault();
        var markerId = $(this).data('id')
        marker = findMarkerById(markerId);
        eventClickMarker(marker);
    })

</script>

<script type="text/javascript">

    var modalMap, modalMarker, modalInfowindow;
    var modalMarkers  = [];
    var modalMarkerCluster = null;
    $(document).ready(function(){
        initModalMap();
        setModalMapMarkers();
    })

    /**
     * INIT MAP
     */
    function initModalMap() {

        modalProps = {
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

        modalMap = new google.maps.Map(document.getElementById('mapModalShipments'), modalProps);
    }


    function getModalCoordinatesFromAddress(address) {
        return new Promise(function(resolve, reject) {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'address': address }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    var latitude  = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    resolve({ latitude: latitude, longitude: longitude });
                } else {
                    reject("Geocode was not successful for the following reason: " + status);
                }
            });
        });
    }

    /**
     * Set marker
     * @param lat
     * @param lng
     * @param options
     * @returns {google.maps.Marker}
     */
    function setModalMarker(lat, lng, options) {
        var lat       = parseFloat(lat);
        var lng       = parseFloat(lng);
        var options   = typeof options !== 'undefined' ? options : {};
        var draggable = typeof options.draggable !== 'undefined' ? options.draggable : false;
        var html      = typeof options.html !== 'undefined' ? options.html : null;
        var zoom      = typeof options.zoom !== 'undefined' ? options.zoom : null;
        var centerMap = typeof options.centerMap !== 'undefined' ? options.centerMap : false;
        var assembly  = typeof options.assembly !== 'undefined' ? options.assembly : false;
        var icon      = typeof options.icon !== 'undefined' ? options.icon : "{{ asset('assets/img/default/map/marker_red.svg') }}";
        var id        = typeof options.id !== 'undefined' ? options.id : '';

        var positionObj = new google.maps.LatLng(lat,lng);
        var marker, marker2, infowindow;

        //set map zoom
        if(zoom) {
            modalMap.setZoom(zoom);
        }

        //center map on marker
        if(centerMap) {
            modalMap.setCenter(positionObj);
        }

        infowindow = new google.maps.InfoWindow();
        infowindow.setContent(html);



        //add marker
        marker = new google.maps.Marker({
            position: positionObj,
            draggable: draggable,
            icon:icon,
            map: modalMap,
            infowindow: infowindow
        });

        if(id != "") {
            marker.set("id", id);
        }

        icon = {
            url: icon,
            scaledSize: new google.maps.Size(40, 40)
        };
        marker.setIcon(icon);

        //assembly icon
        if(assembly) {

            //add marker
            marker2 = new google.maps.Marker({
                position: positionObj,
                draggable: draggable,
                icon:icon,
                map: modalMap,
                infowindow: infowindow
            });

            if(id != "") {
                marker2.set("id", id+'_assembly');
            }
            icon = {
                url: '{{ asset("assets/img/default/map/assembly.svg") }}',
                scaledSize: new google.maps.Size(40, 40)
            };
            marker2.setIcon(icon);
            modalMarkers.push(marker2);

        }


        //evento quando se faz duplo clica no marker
        marker.addListener('dblclick', function() {
            eventClickMarker(marker)
        });

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

            if(assembly) {
                google.maps.event.addListener(marker2, 'click', (function (marker, i) {
                    if (infowindow) {
                        infowindow.close();
                    }

                    return function () {
                        infowindow.open(map, marker2);
                    }
                })(marker2));
            }
        }

        return marker;
    };

    function clearModalMarkers() {
        for (var i = 0; i < modalMarkers.length; i++) {
            modalMarkers[i].setMap(null);
        }

        if(modalMarkerCluster) {
            modalMarkerCluster.setMap(null);
        }
    }

    function setModalMapMarkers(){
        clearModalMarkers();

        $(document).find('.shipments-markers ul li').each(function () {

            var $target = $(this);
            var id   = $target.data('id');
            var html = $target.data('html');
            var lat  = $target.data('lat');
            var lng  = $target.data('lng');
            var addr = $target.data('addr');
            var icon = $target.data('marker-icon');
            var hasAssembly = $target.data('assembly');

            var options = {
                'html' : html, 
                'id' : id,
                'icon': icon,
                'assembly': hasAssembly
            }

            $target.addClass('empty-coordinates');

            if(lat == '') {
                getModalCoordinatesFromAddress(addr).then(function(result) {
                    var latitude  = result.latitude;
                    var longitude = result.longitude;
                    
                    $target.data('lat', latitude);
                    $target.data('lat', longitude);

                    $target.find('[name="recipient_latitude"]').val(latitude);
                    $target.find('[name="recipient_longitude"]').val(longitude);
                    $target.find('[name="addr"]').val(id);
                    
                    if(latitude != "" && longitude != "") {
                        modalMarker = setModalMarker(latitude, longitude, options)
                        modalMarkers.push(modalMarker);
                        $target.removeClass('empty-coordinates');
                        
                        $.post("{{ route('admin.shipments.update.fields') }}", {
                            'id': id,
                            'sender_latitude': '',
                            'sender_longitude': '',
                            'recipient_latitude': latitude,
                            'recipient_longitude': longitude
                        }, function(){})
                    }
                }).catch(function(error) {});

            } else {
                lat = typeof lat != 'undefined' ? lat : '';
                lng = typeof lng != 'undefined' ? lng : '';

                if(lat != "" && lng != "") {
                    modalMarker = setModalMarker(lat, lng, options)
                    modalMarkers.push(modalMarker);
                    $target.removeClass('empty-coordinates');
                }
            }
            
            
        })
    }

    function closeAllInfoWindow() {
        zoom = typeof zoom == 'undefined' ? 16 : zoom;

        for (var i = 0; i < modalMarkers.length; i++) {
            marker = modalMarkers[i];
            marker.infowindow.close();
        }
    }

    function openInfoWindowById(id, zoom) {

        zoom = typeof zoom == 'undefined' ? 16 : zoom;

        for (var i = 0; i < modalMarkers.length; i++) {
            if (modalMarkers[i].id == id) {
                marker = modalMarkers[i];
                infowindow = marker.infowindow
                infowindow.open(map, marker);

                modalMap.setZoom(zoom);
                modalMap.setCenter({lat: marker.position.lat(), lng: marker.position.lng()});

                return;
            }
        }
    }

    function findMarkerById(id) {

        for (var i = 0; i < modalMarkers.length; i++) {
            if (modalMarkers[i].id == id) {
                marker = modalMarkers[i];
                return marker;
            }
        }
    }

    function selectShipmentFromMap(shipmentId) {

        markersAvailable = parseInt($(document).find('.modal .count-markers-available').html());
        markersSelected  = parseInt($(document).find('.modal .count-markers-selected').html());
        $(document).find('.modal .count-markers-available').html(markersAvailable-1)
        $(document).find('.modal .count-markers-selected').html(markersSelected+1);

        $targetObj = $(document).find('.shipments-markers li[data-id="'+shipmentId+'"]');
        $clone = $targetObj.clone();
        $(document).find('.shipments-markers-selected ul').append($clone);
        $targetObj.hide()


        $.post("{{ route('admin.trips.shipments.add-single', $trip->id) }}", {'shipment':shipmentId}, function(data){
            if(data.result && data.html) {
                $('.shipments-table').html(data.html)
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500()
        })
    }


    function removeShipmentFromMap(shipmentId) {

        markersAvailable = parseInt($(document).find('.modal .count-markers-available').html());
        markersSelected  = parseInt($(document).find('.modal .count-markers-selected').html());
        $(document).find('.modal .count-markers-available').html(markersAvailable+1)
        $(document).find('.modal .count-markers-selected').html(markersSelected-1);

        $targetObj = $(document).find('.shipments-markers li[data-id="'+shipmentId+'"]').show()
        $(document).find('.shipments-markers-selected li[data-id="'+shipmentId+'"]').remove();
       
        $.post("{{ route('admin.trips.shipments.remove', [$trip->id, '0']) }}", {'shipment':shipmentId}, function(data){
            if(data.result && data.html) {
                $('.shipments-table').html(data.html)
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500()
        }) 
    }

    function eventClickMarker(marker) {
        var markerId    = marker.id;
        var notSelected = $(document).find('.shipments-markers li[data-id="'+markerId+'"]:visible').length;
        var icon        = $(document).find('.shipments-markers li[data-id="'+markerId+'"]').data('marker-icon');
        var iconCheck   = icon.replace(/\.[^/.]+$/, "")+'_check.svg';

        if(notSelected) {
            icon = {
                url: iconCheck,
                scaledSize: new google.maps.Size(40, 40), // scaled size
            };

            marker.setIcon(icon);
            selectShipmentFromMap(markerId)
            
        } else {

            icon = {
                url: icon,
                scaledSize: new google.maps.Size(40, 40), // scaled size
            };

            marker.setIcon(icon);
            removeShipmentFromMap(markerId)
        }

        closeAllInfoWindow();
    }

</script>




