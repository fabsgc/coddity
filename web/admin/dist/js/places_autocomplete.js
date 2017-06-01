var autocomplete;

function initAutocomplete() {

    $('.place-autocomplete-newAddress').each(function () {

        autocomplete = new google.maps.places.Autocomplete(this, {types: ['geocode']});

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }

        autocomplete.addListener('place_changed', function () {

            var place = autocomplete.getPlace();

            function getComponent(name)
            {
                for(var i in place.address_components)
                {
                    var component = place.address_components[i];
                    if(component.types[0] == name) return component.long_name;
                }
                return null;
            }
            var place = autocomplete.getPlace();
            console.log(place);

            $('.place-autocomplete-address').val(getComponent('street_number') + ' ' + getComponent('route'));
            $('.place-autocomplete-city').val(getComponent('locality'));
            $('.place-autocomplete-postalCode').val(getComponent('postal_code'));
            $('.place-autocomplete-country').val(getComponent('country'));
            $('.place-autocomplete-latitude').val(place.geometry.location.lat());
            $('.place-autocomplete-longitude').val(place.geometry.location.lng());


        });

    });

};
