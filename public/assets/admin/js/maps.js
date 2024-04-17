/**
 * Init map
 *
 * @param lat
 * @param lng
 * @param zoom
 * @param elementId
 * @constructor
 */
var lat, lng, infowindow, thisInstance;

function Gmaps (lat, lng, zoom, elementId) {

    this.allMarkers = [];
    this.geocoder;
    this.marker;

    var zoom = typeof zoom !== 'undefined' ? zoom : 8;
    var elementId = typeof elementId !== 'undefined' ? elementId : 'map';

    var proprieties = {
        center: {lat: lat, lng: lng},
        zoom: zoom,
        zoomControl: true,
        mapTypeControl: true,
        navigationControl: false,
        streetViewControl: true,
        scrollwheel: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    this.GmapsMap = new google.maps.Map(document.getElementById(elementId), proprieties);
}


/**
 * Set marker on map
 *
 * @param lat
 * @param lng
 * @param draggable
 */
Gmaps.prototype.setMarker = function(lat, lng, options) {


    var options   = typeof options !== 'undefined' ? options : {};
    var draggable = typeof options.draggable !== 'undefined' ? options.draggable : false;
    var html      = typeof options.html !== 'undefined' ? options.html : null;
    var zoom      = typeof options.zoom !== 'undefined' ? options.zoom : null;
    var centerMap = typeof options.centerMap !== 'undefined' ? options.centerMap : false;

    var positionObj = new google.maps.LatLng(lat,lng);
    var marker, infowindow;

    //set map zoom
    if(zoom) {
        this.GmapsMap.setZoom(zoom);
    }

    //center map on marker
    if(centerMap) {
        this.GmapsMap.setCenter(positionObj);
    }

    //add marker
    marker = new google.maps.Marker({
        position: positionObj,
        draggable: draggable,
        map: this.GmapsMap
    });

    //add info window
    if(html) {
        infowindow = new google.maps.InfoWindow();
        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            if (infowindow) {
                infowindow.close();
            }

            return function () {
                infowindow.setContent(html);
                infowindow.open(this.GmapsMap, marker);
            }
        })(marker));
    }

    this.allMarkers.push(marker);

    return marker;
};


/**
 * Sets the map on all markers in the array.
 *
 * @param map
 * @param markers
 */
Gmaps.prototype.setMarkers = function (markers) {
    var markers = this.allMarkers;
    var map     = this.GmapsMap;

    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

/**
 * Clear all markers from map
 */
Gmaps.prototype.clearMarkers = function() {
    var markers = this.allMarkers;
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
}

/**
 * Set marker on map by address
 * @param address
 * @param callback
 */
Gmaps.prototype.setMarkerByAddress = function(address, callback) {

    var thisInstance = this;

    thisInstance.geocoder = new google.maps.Geocoder();

    thisInstance.geocoder.geocode({'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            lat = results[0].geometry.location.lat();
            lng = results[0].geometry.location.lng();

            callback(lat, lng);
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    })
}
