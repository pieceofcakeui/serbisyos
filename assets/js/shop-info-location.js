document.addEventListener('DOMContentLoaded', function () {
    if (!shopInfo.latitude || !shopInfo.longitude) {
        geocodeShopAddress();
    }

    const locationLink = document.querySelector('.location-link');
    if (locationLink) {
        locationLink.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.fa-map-marker-alt');
            if (icon) icon.classList.add('fa-bounce');
        });

        locationLink.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.fa-map-marker-alt');
            if (icon) icon.classList.remove('fa-bounce');
        });

        locationLink.addEventListener('click', function (e) {
            e.preventDefault();
            navigateToShop();
        });
    }
});

function geocodeShopAddress() {
    const nominatimUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(shopInfo.address)}&limit=1`;

    fetch(nominatimUrl)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                shopInfo.latitude = parseFloat(data[0].lat);
                shopInfo.longitude = parseFloat(data[0].lon);
                console.log('Geocoded coordinates:', shopInfo.latitude, shopInfo.longitude);
            } else {
                console.error('Geocoding failed: No results found');
            }
        })
        .catch(error => {
            console.error('Geocoding error:', error);
        });
}

function navigateToShop() {
    const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);

    if (shopInfo.latitude && shopInfo.longitude) {
        if (isIOS) {
            window.location.href = `maps://maps.apple.com/?daddr=${shopInfo.latitude},${shopInfo.longitude}&dirflg=d`;
        } else {
            window.location.href = `https://www.google.com/maps/dir/?api=1&destination=${shopInfo.latitude},${shopInfo.longitude}`;
        }
    } else {
        window.location.href = `https://www.google.com/maps/search/?api=1&query=${shopInfo.encodedAddress}`;
    }
}
