var mapDiv;
var bounds;
var infowindow;
var map;

function initMap() {
    mapDiv = document.getElementById('map');
    bounds = new google.maps.LatLngBounds();
    infowindow = new google.maps.InfoWindow();

    map = new google.maps.Map(mapDiv, {
        center: {lat: 48.7910186, lng: 2.3638164},
        zoom: 12
    });
}

function addMarker(lat, lng, label, title){
    var marker = new google.maps.Marker({
        position: {lat: lat, lng: lng},
        map: map,
        title: title
    });

    var labelObject = new Label({
        map: map,
        text: label
    });
    labelObject.bindTo('position', marker, 'position');


    google.maps.event.addListener(marker, "click", function() {
        outlineMarker(marker);
    });

    map.setCenter(marker.position);
    bounds.extend(marker.position);
}

function outlineMarker(marker){
    var id = '#professional-' + marker.title;

    $('.row-search-result').removeClass('row-search-red');
    $(id).addClass('row-search-red');
}

function fitBounds(zoomOut){
    if(zoomOut){
        map.setZoom(12);
    }
    else{
        map.fitBounds(bounds);
    }
}

//Label Design
function Label(opt_options) {
    this.setValues(opt_options);

    var span = this.span_ = document.createElement('span');
    span.style.cssText = 'position: relative; left: -50%; top: -68px; ' +
        'white-space: nowrap; border-radius: 3px; border: 1px solid #ab1919; ' +
        'padding: 5px; background-color: white; color: #ab1919; font-size: 16px';

    var div = this.div_ = document.createElement('div');
    div.appendChild(span);
    div.style.cssText = 'position: absolute; display: none';
}

Label.prototype = new google.maps.OverlayView;
Label.prototype.onAdd = function() {
    var pane = this.getPanes().overlayLayer;
    pane.appendChild(this.div_);

    // Ensures the label is redrawn if the text or position is changed.
    var me = this;
    this.listeners_ = [
        google.maps.event.addListener(this, 'position_changed',
            function() {
                me.draw();
            }),
        google.maps.event.addListener(this, 'text_changed',
            function() {
                me.draw();
            })
    ];
};
Label.prototype.draw = function() {
    var projection = this.getProjection();
    var position = projection.fromLatLngToDivPixel(this.get('position'));

    var div = this.div_;
    div.style.left = position.x + 'px';
    div.style.top = position.y + 'px';
    div.style.display = 'block';

    this.span_.innerHTML = this.get('text').toString();
};