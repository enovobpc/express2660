<script src="{{ 'https://maps.googleapis.com/maps/api/js?key=' . getGoogleMapsApiKey() }}"></script>
<script src="{{ asset('assets/admin/js/maps.js') }}"></script>
<script src="{{ asset('vendor/fuzzyset/fuzzyset.js') }}"></script>
<script>

    @if(!$customer->exists)
    initMap()
    @endif

    $('[name="address"], [name="zip_code"], [name="city"]').on('change', function(){
        var address = $('[name="address"]').val() + ' ' +$('[name="zip_code"]').val() + ' ' +$('[name="city"]').val();
        //check similiarity
        //only update marker location if similarity inferior to 90%
        similarity = FuzzySet(["{{ $customer->address . ' ' . $customer->zip_code . ' ' . $customer->city }}"]);
        similarity = similarity.get(address);

        if(similarity != null) {
            similarity = similarity[0][0];
        }

        if(similarity < 0.90) { //similarity inferior to 88%
            if($('[name="address"]').val() != ''
                && $('[name="zip_code"]').val() != ''
                && $('[name="city"]').val() != '')
            {
                $('.mark-on-map').trigger('click');
            }
        }
    });

    $(document).on('click', '[data-toggle="marker-position"]',function(){
        initMap();
    })

    $('.mark-on-map').on('click', function(){

        if(typeof map == 'undefined') {
            initMap()
        }

        $('#map').show();
        $('.customer-map-static').hide();
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
            url =

            google.maps.event.addListener(marker,'dragend',function(event) {
                var lat = event.latLng.lat();
                var lng = event.latLng.lng()
                $('[name="map_lat"]').val(lat);
                $('[name="map_lng"]').val(lng);
            });
        });
    })

    var map;
    function initMap() {

        @if($customer->map_lat && $customer->map_lng)
        map = new Gmaps({{ $customer->map_lat }},{{ $customer->map_lng }}, 12);
        marker = map.setMarker({{ $customer->map_lat }},{{ $customer->map_lng }}, {'draggable' : true});

        google.maps.event.addListener(marker,'dragend',function(event) {
            var lat = event.latLng.lat();
            var lng = event.latLng.lng()
            $('[name="map_lat"]').val(lat);
            $('[name="map_lng"]').val(lng);
        });
        @else
            map = new Gmaps(40.404874,-7.874651, 7);
        @endif
    }
</script>