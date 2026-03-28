document.addEventListener("DOMContentLoaded", function () {
    const yelpApiKey = 'Bearer QhfqXgeikY4Fx2efM7NFXf_YcKNZc0T-bW-ZrJvomL1Gkl6wz2XZMnkUts-nUoj1Cw7bbk9ptIkJpohhHhqhQ6iSTaRJjY1Aqkwt5X34b-pS1llO6VXDY3f7CcFSaHYx';

    const searchInput = document.getElementById("searchInput");
    const searchBtn = document.getElementById("searchBtn");
    const mobileSearchContainer = document.getElementById("mobileSearchContainer");
    const mobileSearchIcon = document.getElementById("mobileSearchIcon");
    const searchSuggestions = document.getElementById("searchSuggestions");
    const userAddressSection = document.getElementById("userAddress");

    const customFilterPanel = document.getElementById('customFilterPanel');
    const openFilterModalBtn = document.getElementById('openFilterModal');
    const closeCustomFilterBtn = document.getElementById('closeCustomFilter');
    const applyCustomFiltersBtn = document.getElementById('applyCustomFilters');

    if (openFilterModalBtn && customFilterPanel) {
        openFilterModalBtn.addEventListener('click', function () {
            customFilterPanel.style.display = 'flex';
        });
    }

    if (closeCustomFilterBtn && customFilterPanel) {
        closeCustomFilterBtn.addEventListener('click', function () {
            customFilterPanel.style.display = 'none';
        });
    }

    if (customFilterPanel) {
        customFilterPanel.addEventListener('click', function (event) {
            if (event.target === customFilterPanel) {
                customFilterPanel.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('.clickable-location').forEach(tag => {
        tag.addEventListener('click', function() {
            const isActive = this.classList.contains('active');

            document.querySelectorAll('.clickable-location').forEach(t => {
                t.classList.remove('active');
                t.style.backgroundColor = '';
                t.style.color = '';
            });

            if (!isActive) {
                this.classList.add('active');
                this.style.backgroundColor = '#ffc107';
                this.style.color = '#fff';
                document.querySelector('#selectedLocationType').value = this.getAttribute('data-location-type');
                document.querySelector('#selectedLocationValue').value = this.getAttribute('data-location-value');
            } else {
                document.querySelector('#selectedLocationType').value = '';
                document.querySelector('#selectedLocationValue').value = '';
            }
        });
    });

    if (applyCustomFiltersBtn) {
        applyCustomFiltersBtn.addEventListener('click', applyAndRedirect);
    }

    const automotiveCategories = [
        'autorepair', 'oilchange', 'tires', 'bodyshops', 'automotive',
        'autodetailing', 'autoglass', 'transmission', 'brakes', 'mufflers',
        'autoparts', 'carwash', 'autoinspection', 'autoinsurance'
    ];

    const automotiveKeywords = [
        'auto', 'car', 'vehicle', 'repair', 'service', 'mechanic', 'garage',
        'tire', 'brake', 'oil', 'battery', 'engine', 'transmission', 'muffler',
        'body shop', 'detailing', 'wash', 'parts', 'inspection', 'towing',
        'collision', 'paint', 'glass', 'windshield', 'maintenance', 'tune',
        'alignment', 'exhaust', 'radiator', 'alternator', 'starter', 'clutch',
        'smog', 'emissions', 'diagnostic', 'rebuild', 'overhaul', 'lube'
    ];

    function isAutomotiveRelated(text) {
        const lowerText = text.toLowerCase();
        return automotiveKeywords.some(keyword => lowerText.includes(keyword));
    }

    function normalizeText(text) {
        return text.toLowerCase().replace(/[^a-z0-9]/g, '');
    }

    const autocompleteContainer = document.createElement('div');
    autocompleteContainer.className = 'autocomplete-results';
    autocompleteContainer.style.cssText = `
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: none;
    border-radius: 8px;
    box-shadow: none;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1001;
    display: none;
  `;

    const searchContainer = searchInput.closest('.search-container-modern');

    if (searchContainer) {
        searchContainer.appendChild(autocompleteContainer);
    } else {
        console.error("Autocomplete parent container '.search-container-modern' not found.");
    }

    const autocompleteService = new google.maps.places.AutocompleteService();

    searchInput.addEventListener('input', function () {
        const query = searchInput.value.trim();

        if (!query || query.length < 3) {
            autocompleteContainer.innerHTML = '';
            autocompleteContainer.style.display = 'none';
            return;
        }

        autocompleteContainer.innerHTML = '';
        autocompleteContainer.style.display = 'block';

        autocompleteService.getPlacePredictions({
            input: query,
            componentRestrictions: {
                country: 'ph'
            }
        },
            (predictions, status) => {
                autocompleteContainer.innerHTML = '';
                if (status !== google.maps.places.PlacesServiceStatus.OK || !predictions) {
                    autocompleteContainer.innerHTML = `<div style="padding: 12px 16px; color: #666; font-style: italic;">No suggestions found</div>`;
                    return;
                }

                const locationHeader = document.createElement('div');
                locationHeader.textContent = 'Locations';
                locationHeader.style.cssText = `
            padding: 8px 16px;
            background: #f8f9fa;
            font-weight: bold;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
          `;
                autocompleteContainer.appendChild(locationHeader);

                predictions.forEach(prediction => {
                    const div = document.createElement('div');
                    div.className = 'result-item';
                    div.style.cssText = `
                padding: 12px 16px;
                cursor: pointer;
                border-bottom: 1px solid #eee;
                display: flex;
                align-items: center;
              `;
                    div.innerHTML = `<i class="fas fa-map-marker-alt" style="margin-right: 12px; color: #888;"></i> <span>${prediction.description}</span>`;
                    div.addEventListener('click', () => {
                        searchInput.value = prediction.description;
                        autocompleteContainer.style.display = 'none';
                        performSearch(prediction.description);
                    });
                    autocompleteContainer.appendChild(div);
                });
            }
        );
    });

    function toggleSearchContainer() {
        if (window.innerWidth <= 768) {
            const isActive = mobileSearchContainer.classList.contains("active");

            if (!isActive) {
                mobileSearchContainer.classList.add("active");

                mobileSearchContainer.style.cssText = `
            position: fixed !important;
            top: 70px !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 999 !important;
            background: white !important;
            padding: 15px !important;
            box-shadow: none !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            transform: translateY(0) !important;
            transition: all 0.3s ease !important;
          `;

                searchInput.style.cssText = `
            flex: 1 !important;
            width: 100% !important;
            border: 1px solid #ddd !important;
            border-radius: 25px !important;
            padding: 12px 16px !important;
            font-size: 16px !important;
            outline: none !important;
            background: #f8f9fa !important;
            transition: all 0.2s ease !important;
          `;

                const searchIcon = mobileSearchContainer.querySelector('#mobileSearchIcon');
                const filterIcon = mobileSearchContainer.querySelector('#openFilterModal');

                if (searchIcon) {
                    searchIcon.style.cssText = `
                color: #333 !important;
                font-size: 18px !important;
                cursor: pointer !important;
                padding: 8px !important;
                border-radius: 50% !important;
                background: transparent !important;
                transition: all 0.2s ease !important;
              `;
                }

                if (filterIcon) {
                    filterIcon.style.cssText = `
                color: #6c757d !important;
                font-size: 18px !important;
                cursor: pointer !important;
                padding: 8px !important;
                border-radius: 50% !important;
                background: #f8f9fa !important;
                transition: all 0.2s ease !important;
              `;
                }

                setTimeout(() => {
                    searchInput.focus();
                }, 150);

            } else {
                mobileSearchContainer.classList.remove("active");

                mobileSearchContainer.style.cssText = `
            transform: translateY(-100%) !important;
            transition: all 0.3s ease !important;
          `;

                setTimeout(() => {
                    mobileSearchContainer.style.cssText = '';
                    searchInput.style.cssText = '';
                }, 300);
            }
        }
    }

    searchInput.addEventListener('focus', function () {
        if (this.value.length === 0) {
            const hasAddress = Array.from(document.querySelectorAll('.address-item'))
                .some(item => item.textContent.trim().length > 0);
            userAddressSection.style.display = hasAddress ? 'block' : 'none';
            searchSuggestions.style.display = userAddressSection.style.display === 'block' ? 'block' : 'none';
            autocompleteContainer.style.display = 'none';
        } else if (this.value.length >= 3) {
            userAddressSection.style.display = 'none';
            searchSuggestions.style.display = 'none';
            this.dispatchEvent(new Event('input'));
        }
    });

    document.addEventListener('click', function (e) {
        if (!autocompleteContainer.contains(e.target) &&
            !searchSuggestions.contains(e.target) &&
            e.target !== searchInput &&
            !searchContainer.contains(e.target)) {
            autocompleteContainer.style.display = 'none';
            searchSuggestions.style.display = 'none';
        }
    });

    function performSearch(query) {
        const searchQuery = query || searchInput.value.trim();
        if (searchQuery === "") return;

        if (window.innerWidth <= 768 && mobileSearchContainer.classList.contains("active")) {
            toggleSearchContainer();
        }

        window.location.href = `search-results.php?search=${encodeURIComponent(searchQuery)}`;
    }

    if (searchInput) {
        searchInput.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                autocompleteContainer.style.display = 'none';
                performSearch();
            }
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (window.innerWidth <= 768) {
                toggleSearchContainer();
            } else {
                performSearch();
            }
        });
    }

    if (mobileSearchIcon) {
        mobileSearchIcon.addEventListener("click", function () {
            performSearch();
        });
    }

    document.addEventListener("click", function (e) {
        if (window.innerWidth <= 768 &&
            mobileSearchContainer.classList.contains("active") &&
            !mobileSearchContainer.contains(e.target) &&
            e.target !== searchBtn &&
            !searchBtn.contains(e.target)) {
            toggleSearchContainer();
        }
    });

    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const dropdowns = document.querySelectorAll('.dropdown');

    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
            if (mobileSearchContainer.classList.contains('active')) {
                toggleSearchContainer();
            }
            closeAllDropdowns();
        });
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-content').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.icon-btn, .user-img');
        const content = dropdown.querySelector('.dropdown-content');

        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('.dropdown-content').forEach(otherContent => {
                if (otherContent !== content) {
                    otherContent.style.display = 'none';
                }
            });
            if (content.style.display === 'block') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });

    function handleResize() {
        if (window.innerWidth > 768) {
            if (menuToggle) menuToggle.classList.remove('active');
            if (navMenu) navMenu.classList.remove('active');
            if (mobileSearchContainer.classList.contains('active')) {
                mobileSearchContainer.classList.remove('active');
                mobileSearchContainer.style.cssText = '';
                searchInput.style.cssText = '';
            }
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize();

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (mobileSearchContainer.classList.contains('active')) {
                toggleSearchContainer();
            }
            if (customFilterPanel) {
                customFilterPanel.style.display = 'none';
            }
            autocompleteContainer.style.display = 'none';
            closeAllDropdowns();
        }
    });

    document.querySelectorAll('.address-item').forEach(item => {
        item.addEventListener('click', function () {
            const addressText = this.getAttribute('data-address');
            searchInput.value = addressText;
            performSearch(addressText);
        });
        item.style.cursor = 'pointer';
        item.addEventListener('mouseenter', function () {
            this.style.textDecoration = 'underline';
            this.style.color = '#0066cc';
        });
        item.addEventListener('mouseleave', function () {
            this.style.textDecoration = 'none';
            this.style.color = '';
        });
    });

    function updateUnreadCount() {
        fetch('?check_unread=1')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const badge = document.getElementById('unreadBadge');
                if (data.unread_count > 0) {
                    if (!badge) {
                        const newBadge = document.createElement('span');
                        newBadge.id = 'unreadBadge';
                        newBadge.style = 'position: absolute; top: -5px; right: -5px; background-color: red; color: white; font-size: 12px; padding: 2px 6px; border-radius: 50%; font-weight: bold;';
                        newBadge.textContent = data.unread_count;
                        document.querySelector('.message-icon-container').appendChild(newBadge);
                    } else {
                        badge.textContent = data.unread_count;
                    }
                    if (data.unread_count > parseInt(badge?.textContent || 0)) {
                        playNotificationSound();
                    }
                } else if (badge) {
                    badge.remove();
                }
            })
            .catch(error => console.error('Error fetching unread count:', error));
    }

    function playNotificationSound() {
        const audio = new Audio('../assets/sound/notification.mp3');
        audio.play().catch(e => console.log('Audio play failed:', e));
    }

    const pollInterval = setInterval(updateUnreadCount, 3000);
    updateUnreadCount();

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearInterval(pollInterval);
            setInterval(updateUnreadCount, 15000);
        } else {
            clearInterval(pollInterval);
            setInterval(updateUnreadCount, 3000);
        }
    });

    function applyAndRedirect() {
        const urlParams = new URLSearchParams(window.location.search);
        const existingSearch = urlParams.get('search') || '';
        const existingServiceType = urlParams.get('serviceType') || '';
        const existingProvince = urlParams.get('province') || '';
        const existingCity = urlParams.get('town_city') || '';
        const selectedRating = document.querySelector('input[name="rating"]:checked')?.value || 'any';
        const selectedDistance = document.querySelector('input[name="nearby"]:checked')?.value || '';
        const selectedServices = [];
        document.querySelectorAll('input[name="service"]:checked').forEach(checkbox => {
            selectedServices.push(checkbox.value);
        });
        const locationType = document.querySelector('#selectedLocationType')?.value || '';
        const locationValue = document.querySelector('#selectedLocationValue')?.value || '';
        let newUrl = 'search-results.php?';
        if (existingSearch) newUrl += `search=${encodeURIComponent(existingSearch)}&`;
        if (existingServiceType) newUrl += `serviceType=${encodeURIComponent(existingServiceType)}&`;
        if (selectedRating && selectedRating !== 'any') newUrl += `rating=${selectedRating}&`;
        if (selectedDistance) newUrl += `distance=${selectedDistance}&`;
        if (selectedServices.length > 0) {
            newUrl += `services=${encodeURIComponent(selectedServices.join(','))}&`;
        }
        if (locationType && locationValue) {
            newUrl = newUrl.replace(/province=[^&]*&?/, '');
            newUrl = newUrl.replace(/town_city=[^&]*&?/, '');
            newUrl += `${locationType}=${encodeURIComponent(locationValue)}&`;
        } else {
            if (existingProvince) newUrl += `province=${encodeURIComponent(existingProvince)}&`;
            if (existingCity) newUrl += `town_city=${encodeURIComponent(existingCity)}&`;
        }
        if (newUrl.endsWith('&')) {
            newUrl = newUrl.slice(0, -1);
        }
        window.location.href = newUrl;
    }

    const findShopsBtn = document.getElementById('findShopsBtn');
    if (findShopsBtn) {
        findShopsBtn.addEventListener('click', function (event) {
            event.preventDefault();
            openModal('nearbyShopsModal');
        });
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    const badges = document.querySelectorAll('.nav-link .badge');
    badges.forEach(badge => {
        badge.classList.add('pulse');
        setTimeout(() => {
            badge.classList.remove('pulse');
        }, 3000);
    });
});