@section('title')
    Previsão de Rota
@stop

@section('content-header')
    Previsão de Rota
    <br/>
    <small>Os dados apresentados são exemplificativos.</small>
@stop

@section('breadcrumb')
    <li class="active">Previsão de Rota</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <div id="map" style="width: 100%; height: 650px"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey() }}"></script>
    <script>
       $(document).ready(function(){
           initMap();
       })

        function initMap() {
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 6,
                center: {lat: 41.85, lng: -87.65}
            });
            directionsDisplay.setMap(map);

            calculateAndDisplayRoute(directionsService, directionsDisplay);

        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay) {

            var waypts = [];
            var checkboxArray = [
                'Rua Francial Nº5 Rio de Loba 3505-456 Viseu',
                'Rua Santa Isabel Lote 2 Repeses',
                'Rua da capela lages de silgueiros',
                ' R DA ESCOLA 565, 3515-775 lordosa'
                //'sdf dsf sd nº245 xada'
                ]//document.getElementById('waypoints');


            checkboxArray.forEach(function (val) {

                waypts.push({
                    location: val,
                    stopover: true
                });
            });



            directionsService.route({
                origin:  'Zona industrial de abraveses 2b, viseu',
                destination:  'Zona industrial de abraveses 2b, viseu',
                waypoints: waypts,
                optimizeWaypoints: true,
                travelMode: 'DRIVING'
                }, function(response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);


                    var totalDistance = 0;
                    var totalDuration = 0;
                    var legs = response.routes[0].legs;
                    for(var i=0; i<legs.length; ++i) {
                        totalDistance += legs[i].distance.value;
                        totalDuration += legs[i].duration.value;
                    }

                    totalDistance = totalDistance/1000

                    alert(totalDistance + 'km, ' + totalDuration)



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
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }
    </script>

@stop
