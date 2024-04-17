@section('title')
Clientes
@stop

@section('content-header')
Clientes
<small>
    @trans('Novo cliente')
</small>
@stop

@section('breadcrumb')
<li>
    <a href="{{ route('admin.customers.index') }}">
        @trans('Clientes')
    </a>
</li>
<li class="active">
    @trans('Novo cliente')
</li>
@stop

@section('plugins')
{{ HTML::script('assets/vendor/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js') }}
@stop

@section('content')
@include('admin.customers.customers.partials.info')
@include('admin.partials.modals.vat_validation')
@stop


@section('scripts')
<link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.0/mapsjs-ui.css?dp-version=1549984893" />
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-core.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-service.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-ui.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-mapevents.js"></script>
<script src="{{ asset('vendor/fuzzyset/fuzzyset.js') }}"></script>
<script>

    $("select[name=bank_code]").select2({
        minimumInputLength: 1,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.customers.search.banks') }}")
    });
    
    var EXISTING_VATS = {!! json_encode($existingVats) !!}
    $('[name="vat"]').on('change', function(){
        var value = $(this).val();
        if($.inArray(value, EXISTING_VATS) > 0) {
            $('.vat-alert').show();
        } else {
            $('.vat-alert').hide();
        }
    })

    $(document).on('click', '[data-toggle="marker-position"], .mark-on-map',function(){
        locateAddress(true);
    })

    $('[name="address"], [name="zip_code"], [name="city"]').on('change', function(){
        locateAddress(false);
    });

    function locateAddress(ignoreSimilarity) {
        var address  = $('[name="address"]').val();
        var zip_code = $('[name="zip_code"]').val();
        var city     = $('[name="city"]').val();

        if(address != '' && zip_code != '' && city != '') {
            var address = address + ' ' + zip_code + ' ' + city;

            if (ignoreSimilarity) {
                findAddressLocation(address)
            } else {
                //check similiarity
                //only update marker location if similarity inferior to 90%
                similarity = FuzzySet(["{{ $customer->address . ' ' . $customer->zip_code . ' ' . $customer->city }}"]);
                similarity = similarity.get(address);

                if (similarity != null) {
                    similarity = similarity[0][0];
                }

                if (similarity < 0.90) { //similarity inferior to 88%
                    findAddressLocation(address)
                }
            }
        }
    }


    var MAP_LAT = '{{ $customer->map_lat }}';
    var MAP_LNG = '{{ $customer->map_lng }}';

    var platform = new H.service.Platform({
        'app_id': '{{ env('HERE_MAPS_ID') }}',
        'app_code': '{{ env('HERE_MAPS_CODE') }}',
        'useHTTPS': true
    });

    var pixelRatio = window.devicePixelRatio || 1;
    var defaultLayers = platform.createDefaultLayers({
        tileSize: pixelRatio === 1 ? 256 : 512,
        ppi: pixelRatio === 1 ? undefined : 320
    });

    //initialize a map
    var map = new H.Map(document.getElementById('map'),defaultLayers.normal.map, {pixelRatio: pixelRatio});

    //dynamic map
    var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
    var ui = H.ui.UI.createDefault(map, defaultLayers);

    map.setCenter({lat: MAP_LAT, lng: MAP_LNG});
    map.setZoom(15);

    addDraggableMarker(map, behavior, MAP_LAT, MAP_LNG)

    /**
     * FUNCTIONS
     **/
    function addMarker(map, behavior) {
        var iconSVG = new H.map.Icon("{{ asset('/assets/img/default/marker.svg') }}", {size: {w: 80, h: 80}});
        var marker = new H.map.Marker({
            lat: MAP_LAT,
            lng: MAP_LNG
        }, {icon: iconSVG});
        map.addObject(marker);
    }

    function addDraggableMarker(map, behavior, lat, lng){

        var iconSVG = new H.map.Icon("{{ asset('/assets/img/default/marker.svg') }}", {size: {w: 80, h: 80}});
        var marker = new H.map.Marker({
            lat: lat,
            lng: lng
        }, {icon: iconSVG});

        // Ensure that the marker can receive drag events
        marker.draggable = true;
        map.addObject(marker);

        // disable the default draggability of the underlying map
        // when starting to drag a marker object:
        map.addEventListener('dragstart', function(ev) {
            var target = ev.target;
            if (target instanceof H.map.Marker) {
                behavior.disable();
            }
        }, false);


        // re-enable the default draggability of the underlying map
        // when dragging has completed
        map.addEventListener('dragend', function(ev) {
            var target = ev.target;
            if (target instanceof mapsjs.map.Marker) {
                behavior.enable();
            }
        }, false);

        // Listen to the drag event and move the position of the marker
        // as necessary
        map.addEventListener('drag', function(ev) {
            var target = ev.target,
                pointer = ev.currentPointer;
            if (target instanceof mapsjs.map.Marker) {
                target.setPosition(map.screenToGeo(pointer.viewportX, pointer.viewportY));

                var pos = map.screenToGeo(pointer.viewportX, pointer.viewportY);
                $('[name="map_lat"]').val(pos.lat)
                $('[name="map_lng"]').val(pos.lng)
            }
        }, false);
    }

    //check location from address
    function findAddressLocation(address) {

        //remove all markers and info bubbles
        map.removeObjects(map.getObjects())

        var geocoder = platform.getGeocodingService(),
            geocodingParameters = {
                searchText: address,
                jsonattributes : 1
            };

        geocoder.geocode(
            geocodingParameters,
            function(result) {

                if(typeof result.response.view[0] !== 'undefined') {
                    var locations = result.response.view[0].result;
                    lat = locations[0].location.displayPosition.latitude;
                    lng = locations[0].location.displayPosition.longitude;

                    addDraggableMarker(map, behavior, lat, lng);
                    map.setCenter({lat: lat, lng: lng});

                    $('[name="map_lat"]').val(lat)
                    $('[name="map_lng"]').val(lng)
                } else {
                    Growl.error('Não foi possível localizar a morada no mapa.')
                }
            },
            function(error) {
                Growl.error('Não foi possível localizar a morada no mapa.')
            }
        );
    }
</script>
{{-- GOOGLE MAPS API --}}
{{--@include('admin.customers.customers.partials.js_maps')--}}
@stop