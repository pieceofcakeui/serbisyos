function openDirections() {
    if (!window.shopInfo?.encodedCombinedAddress) {
        console.error('No address available for directions');
        return;
    }
    const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${window.shopInfo.encodedCombinedAddress}`;
    window.open(googleMapsUrl, '_blank');
}

function initMap() {
    const mapDiv = document.getElementById('map');
    if (!mapDiv) return;

    mapDiv.style.height = '300px';
    mapDiv.style.width = '100%';

    if (!window.shopInfo?.combinedAddress) {
        mapDiv.innerHTML = '<div class="text-center p-3">No address available for this shop.</div>';
        return;
    }

    const geocoder = new google.maps.Geocoder();

    geocoder.geocode({
        'address': window.shopInfo.combinedAddress
    }, function(results, status) {
        if (status === 'OK') {
            const mapOptions = {
                center: results[0].geometry.location,
                zoom: 15,
                disableDefaultUI: true,
                zoomControl: true,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false
            };

            const map = new google.maps.Map(mapDiv, mapOptions);
            new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                title: window.shopInfo.name || 'Shop Location'
            });
        } else {
            mapDiv.innerHTML = `<div class="text-center p-3">
                <p>Map could not be loaded.</p>
                <p>Address: ${window.shopInfo.combinedAddress}</p>
            </div>`;
            console.error('Geocode was not successful for the following reason:', status);
        }
    });
}

window.addEventListener('load', function() {
    if (typeof google === 'object' && typeof google.maps === 'object') {
        initMap();
    } else {
        console.error('Google Maps API not loaded');
        const mapDiv = document.getElementById('map');
        if (mapDiv) {
            mapDiv.innerHTML = '<div class="text-center p-3">Map service unavailable</div>';
        }
    }
});