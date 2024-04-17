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
    <div class="container mt-5">
        <h2>Laravel Google Maps Multiple Markers Example - ItSolutionStuff.com</h2>
        <div id="mappXPTO"></div>
    </div>
@stop

@section('scripts')

    <script type="text/javascript">

        function addTomap(tracking) {              
            console.log("hkjadsjghdasvhjadshbm= " + tracking);
        }

        function initMap() {
            const myLatLng = { lat: 40.6575, lng: -7.91428 };
            const map = new google.maps.Map(document.getElementById("mappXPTO"), {
                zoom: 10,
                center: myLatLng,
            });
            
            var locations = @php echo json_encode($locations); @endphp;
            console.log(locations);
            var infowindow = new google.maps.InfoWindow();
            console.log(infowindow);
            var marker, i;
              
            for (i = 0; i < locations.length; i++) {  
                  
                  const data = locations[i].split(',');
                  latitude = data[0];
                  longitude = data[1];
                  
                  marker = new google.maps.Marker({
                    position: new google.maps.LatLng(latitude, longitude),
                    map: map
                  });
                    
                  google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                      infowindow.setContent( '<b>' + data[3]  + '</b> <br/> '+  data[4] +'<br/> <a class="btn btn-primary" onclick="addTomap('+ data[2] +')" >Adicionar<a/>');
                      infowindow.open(map, marker);
                    }
                  })(marker, i));
            }
        }
  
        window.initMap = initMap;

        
    </script>
    
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key={{ getGoogleMapsApiKey() }}&callback=initMap" ></script>
    
@stop   