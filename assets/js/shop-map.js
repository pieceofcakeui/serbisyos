document.addEventListener('DOMContentLoaded', function() {
    initMap();
});

function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error("Map element not found!");
        return;
    }
    const address = window.shopInfo.displayAddress;
    const geocoder = new google.maps.Geocoder();

    geocoder.geocode({ 'address': address }, function(results, status) {
        if (status === 'OK') {
            const location = results[0].geometry.location;
            const map = new google.maps.Map(mapElement, {
                center: location,
                zoom: 16,
                mapTypeControl: false,
                streetViewControl: false,
            });

            new google.maps.Marker({
                map: map,
                position: location,
                title: window.shopInfo.name
            });
        } else {
            console.error('Geocode was not successful for the following reason: ' + status);
            mapElement.innerHTML = '<div class="text-center p-3">Map could not be loaded. Invalid address provided.</div>';
        }
    });
}

function openDirections() {
    const destination = window.shopInfo.encodedCombinedAddress;
    if (destination) {
        const url = `https://www.google.com/maps/dir/?api=1&destination=${destination}`;
        window.open(url, '_blank');
    }
}