<script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey() }}"></script>
<script src="{{ asset('assets/admin/js/maps.js') }}"></script>
<script>
    var map;

    @if($prospect->map_lat && $prospect->map_lng)
        map = new Gmaps({{ $prospect->map_lat }},{{ $prospect->map_lng }}, 12);
    marker = map.setMarker({{ $prospect->map_lat }},{{ $prospect->map_lng }}, {'draggable' : true});

    google.maps.event.addListener(marker,'dragend',function(event) {
        var lat = event.latLng.lat();
        var lng = event.latLng.lng()
        $('[name="map_lat"]').val(lat);
        $('[name="map_lng"]').val(lng);
    });
    @else
        map = new Gmaps(40.404874,-7.874651, 7);
    @endif

    $('[name="address"], [name="zip_code"], [name="city"]').on('change', function(){
        $('.mark-on-map').trigger('click');
    });

    $('.mark-on-map').on('click', function(){
        var address = $('[name="address"]').val() + ' ' +$('[name="zip_code"]').val() + ' ' +$('[name="city"]').val() + ', '+ $('[name="country"]').val();

        map.clearMarkers();

        options = {
            zoom: 12,
            draggable: true,
            centerMap: true
        };

        map.setMarkerByAddress(address, function(lat, lng){
            marker = map.setMarker(lat, lng, options);
            $('[name="map_lat"]').val(lat);
            $('[name="map_lng"]').val(lng);

            google.maps.event.addListener(marker,'dragend',function(event) {
                var lat = event.latLng.lat();
                var lng = event.latLng.lng()
                $('[name="map_lat"]').val(lat);
                $('[name="map_lng"]').val(lng);
            });
        });
    })
</script>