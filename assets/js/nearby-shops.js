function initializeLocationServices() {
    const locationAccuracyMessage = document.getElementById('location-accuracy-message');
    const urlParams = new URLSearchParams(window.location.search);
    const lat = parseFloat(urlParams.get('lat'));
    const lng = parseFloat(urlParams.get('lng'));

    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
        const geocoder = new google.maps.Geocoder();
        const latlng = { lat: lat, lng: lng };

        geocoder.geocode({ location: latlng }, (results, status) => {
            if (status === 'OK' && results && results.length > 0) {
                let bestResult = results[0];

                for (let result of results) {
                    if (!result.formatted_address.includes('+') && !result.formatted_address.includes('Unnamed Road')) {
                        bestResult = result;
                        break;
                    }
                }
                let fullAddress = bestResult.formatted_address;

                if (fullAddress.includes('Unnamed Road')) {
                    const addressComponents = bestResult.address_components;
                    let barangay = '', city = '', province = '';
                    for (let component of addressComponents) {
                        if (component.types.includes('sublocality_level_1') || component.types.includes('locality')) {
                            barangay = component.long_name;
                        }
                        if (component.types.includes('administrative_area_level_2')) {
                            city = component.long_name;
                        }
                        if (component.types.includes('administrative_area_level_1')) {
                            province = component.long_name;
                        }
                    }
                    if (barangay && city && province) {
                        fullAddress = `${barangay}, ${city}, ${province}, Philippines`;
                    }
                }

                locationAccuracyMessage.textContent = fullAddress;

                fetch('../account/backend/store_location.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng,
                        address: fullAddress
                    })
                })
                .then(response => response.text())
                .then(message => {
                    console.log('Location saved:', message);

                    if (sessionStorage.getItem('processingLocation')) {
                        sessionStorage.removeItem('processingLocation');

                        const countdownOverlay = document.createElement('div');
                        countdownOverlay.style.position = 'fixed';
                        countdownOverlay.style.top = '0';
                        countdownOverlay.style.left = '0';
                        countdownOverlay.style.width = '100%';
                        countdownOverlay.style.height = '100%';
                        countdownOverlay.style.backgroundColor = 'rgba(255,255,255,0.9)';
                        countdownOverlay.style.display = 'flex';
                        countdownOverlay.style.flexDirection = 'column';
                        countdownOverlay.style.justifyContent = 'center';
                        countdownOverlay.style.alignItems = 'center';
                        countdownOverlay.style.zIndex = '9999';

                        const countdownText = document.createElement('h4');
                        countdownText.textContent = 'Finding shops near you...';
                        countdownText.className = 'mb-3';

                        const countdownNumber = document.createElement('div');
                        countdownNumber.style.fontSize = '2rem';
                        countdownNumber.style.fontWeight = 'bold';
                        countdownNumber.textContent = '5';

                        countdownOverlay.appendChild(countdownText);
                        countdownOverlay.appendChild(countdownNumber);
                        document.body.appendChild(countdownOverlay);

                        let count = 5;
                        const countdownInterval = setInterval(() => {
                            count--;
                            countdownNumber.textContent = count;
                            if (count <= 0) {
                                clearInterval(countdownInterval);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error storing location:', error);
                });
            } else {
                locationAccuracyMessage.textContent = 'Location not found - Status: ' + status;
                console.error('Geocoding failed with status:', status);
            }
        });
    } else {
        locationAccuracyMessage.textContent = 'Location not provided or invalid coordinates.';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof google !== 'undefined' && google.maps) {
        initializeLocationServices();
    } else {
        window.addEventListener('load', initializeLocationServices);
    }
});