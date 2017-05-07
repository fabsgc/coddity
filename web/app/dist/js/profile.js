var mapDiv;
var bounds;
var infowindow;
var map;

function initMap(lat, lng) {
    mapDiv = document.getElementById('map');
    bounds = new google.maps.LatLngBounds();
    infowindow = new google.maps.InfoWindow();

    map = new google.maps.Map(mapDiv, {
        center: {lat: lat, lng: lng},
        zoom: 12
    });
}

function addMarker(lat, lng){
    var marker = new google.maps.Marker({
        position: {lat: lat, lng: lng},
        map: map
    });
}