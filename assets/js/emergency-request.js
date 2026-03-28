$(document).ready(function () {
    $('.emergency-tab').click(function () {
        $('.emergency-tab').removeClass('active');
        $(this).addClass('active');
        const status = $(this).data('status');
        filterEmergencyRequests(status);
    });

    function filterEmergencyRequests(status) {
        $('.empty-emergency-state').hide();
        if (status === 'all') {
            $('.emergency-row').show();
            if ($('.emergency-row').length > 0) {
                $('#emergencyTableContainer').removeClass('hide').show();
            } else {
                $('#emergencyTableContainer').addClass('hide').hide();
                $('.empty-emergency-all').show();
            }
        } else {
            $('.emergency-row').hide();
            const matchingRows = $(`.emergency-row.emergency-status-${status}`);
            matchingRows.show();
            if (matchingRows.length > 0) {
                $('#emergencyTableContainer').removeClass('hide').show();
            } else {
                $('#emergencyTableContainer').addClass('hide').hide();
                $(`.empty-emergency-${status}`).show();
            }
        }
    }
    filterEmergencyRequests('all');
    if ($('.emergency-row').length === 0) {
        $('#emergencyTableContainer').addClass('hide').hide();
        $('.empty-emergency-all').show();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    initializeEmergencySystem();
});

function initializeEmergencySystem() {
    if (window.isEmergencySystemInitialized) {
        return;
    }

    window.isEmergencySystemInitialized = true;

    const isNotificationPage = window.location.pathname.includes('notification');
    let emergencyModal = null;
    let activeMap = null;

    if (document.getElementById('emergencyModal')) {
        emergencyModal = new bootstrap.Modal(document.getElementById('emergencyModal'));
    }

    if (isNotificationPage) {
        handleNotificationPageLogic();
    }

    setupEventListeners();
    setupTooltips();
    initializeActiveSection();

    function setupEventListeners() {
        document.addEventListener('click', handleDocumentClick);
        const statusModal = document.getElementById('confirmStatusModal');
        if (statusModal) {
            statusModal.addEventListener('hidden.bs.modal', function () {
                cleanupModals();
            });
        }
        const emergencyModalElement = document.getElementById('emergencyModal');
        if (emergencyModalElement) {
            emergencyModalElement.addEventListener('hidden.bs.modal', function () {
                if (activeMap) {
                    activeMap = null;
                }
                cleanupModals();
            });
        }
    }

    function handleDocumentClick(e) {
        if (e.target.closest('.view-details')) {
            handleViewDetailsClick(e.target.closest('.view-details'));
        }
        if (e.target.closest('.change-status')) {
            handleStatusChange(e.target.closest('.change-status'));
        }
        if (e.target.closest('#confirmStatusChange')) {
            handleConfirmStatusChange();
        }
        if (e.target.closest('.video-thumbnail-container') || e.target.closest('.video-overlay')) {
            const container = e.target.closest('.video-thumbnail-container');
            if (container) {
                const videoElement = container.querySelector('video');
                if (videoElement) {
                    handleVideoClick(videoElement);
                }
            }
        }
    }

    function handleViewDetailsClick(button) {
        const requestId = button.dataset.id;
        const lat = parseFloat(button.dataset.lat);
        const lng = parseFloat(button.dataset.long);
        const address = button.dataset.address;

        if (!requestId) {
            showCustomToast('Invalid request ID', 'error');
            return;
        }

        const modal = emergencyModal || new bootstrap.Modal(document.getElementById('emergencyModal'));
        const detailsContainer = document.getElementById('emergencyDetails');
        showLoadingState(detailsContainer, 'Loading emergency details...');
        modal.show();
        fetchEmergencyDetails(requestId, detailsContainer, lat, lng, address);
    }

    function handleStatusChange(button) {
        const requestId = button.dataset.id;
        const status = button.dataset.status;

        if (!requestId || !status) {
            showCustomToast('Invalid request data', 'error');
            return;
        }

        showStatusConfirmationModal(status, requestId);
    }

    function handleConfirmStatusChange() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmStatusModal'));
        const requestId = modal._element.dataset.requestId;
        const status = modal._element.dataset.status;
        const confirmButton = document.getElementById('confirmStatusChange');

        if (requestId && status) {
            setConfirmingState(confirmButton, true);
            updateRequestStatus(requestId, status).finally(() => {
                setConfirmingState(confirmButton, false);
                modal.hide();
            });
        } else {
            modal.hide();
        }
    }

    function setConfirmingState(button, isConfirming) {
        if (isConfirming) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Confirming...';
        } else {
            button.disabled = false;
            button.innerHTML = 'Confirm';
        }
    }

    function handleVideoClick(videoElement) {
        const src = videoElement.dataset.src || videoElement.src;
        if (src) {
            openVideoModal(src);
        }
    }

    function showStatusConfirmationModal(status, requestId) {
        const modal = new bootstrap.Modal(document.getElementById('confirmStatusModal'));
        const modalElement = document.getElementById('confirmStatusModal');
        const titleElement = document.getElementById('confirmStatusModalTitle');
        const bodyElement = document.getElementById('confirmStatusModalBody');

        modalElement.dataset.requestId = requestId;
        modalElement.dataset.status = status;

        const statusMessages = {
            'accepted': {
                icon: 'fas fa-check-circle text-success',
                title: 'Accept Request',
                body: 'Are you sure you want to accept this emergency request?'
            },
            'rejected': {
                icon: 'fas fa-times-circle text-danger',
                title: 'Reject Request',
                body: 'Are you sure you want to reject this emergency request?'
            },
            'completed': {
                icon: 'fas fa-flag-checkered text-info',
                title: 'Complete Request',
                body: 'Are you sure you want to mark this request as completed?'
            },
            'delete': {
                icon: 'fas fa-trash-alt text-danger',
                title: 'Delete Request',
                body: 'Are you sure you want to delete this request?'
            }
        };

        const messageData = statusMessages[status] || statusMessages['accepted'];
        bodyElement.innerHTML = `
            <div class="text-center mb-3">
                <i class="${messageData.icon}" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-center mb-3">${messageData.title}</h5>
            <p class="text-center mb-0">${messageData.body}</p>
        `;
        modal.show();
    }

    function updateRequestStatus(requestId, status) {
        const formData = new FormData();
        formData.append('request_id', requestId);
        formData.append('status', status);

        let endpoint = '../account/backend/update_emergency_request.php';
        if (status === 'delete') {
            endpoint = '../account/backend/hide_emergency_request.php';
            formData.delete('status');
        }

        return fetch(endpoint, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showCustomToast(data.message || `Request ${status} successfully`, 'success');
                    if (status === 'delete') {
                        const row = document.querySelector(`[data-id="${requestId}"]`)?.closest('.emergency-row');
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }, 300);
                        } else {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                    localStorage.setItem('emergencyActionTaken', 'true');
                    localStorage.setItem('activeNotificationSection', 'emergency-section');
                } else {
                    showCustomToast(data.message || `Failed to ${status} request`, 'error');
                }
                return data;
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomToast(`Error updating request: ${error.message}`, 'error');
                throw error;
            });
    }

    function showLoadingState(container, message = 'Loading...') {
        if (!container) return;
        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center py-5">
                <div class="spinner-border mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mb-0">${message}</p>
            </div>
        `;
    }

    function showErrorMessage(container, message) {
        if (!container) return;
        container.innerHTML = `
            <div class="alert alert-danger m-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Error Loading Details</h5>
                        <p class="mb-0">${message}</p>
                    </div>
                </div>
            </div>
        `;
    }

    function showCustomToast(message, type = 'success') {
        const toastId = 'custom-toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = type === 'success' ? 'toast-success' : 'toast-error';
        toast.innerHTML = `
            ${message}
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (document.getElementById(toastId)) {
                toast.remove();
            }
        }, type === 'error' ? 5000 : 3000);
    }

    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${getToastIcon(type)} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: type === 'danger' ? 5000 : 3000
        });
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1200';
        document.body.appendChild(container);
        return container;
    }

    function getToastIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'danger': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle',
            'primary': 'fa-info-circle'
        };
        return icons[type] || 'fa-info-circle';
    }

    function setupTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    function cleanupModals() {
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.remove();
        });
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.classList.remove('modal-open');
    }
}


function openVideoModal(src) {
    const emergencyModal = bootstrap.Modal.getInstance(document.getElementById('emergencyModal'));
    if (emergencyModal) {
        emergencyModal.hide();
    }

    const fullscreenBackdrop = document.createElement('div');
    fullscreenBackdrop.id = 'fullscreenVideoBackdrop';
    fullscreenBackdrop.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.95);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    `;

    const closeButton = document.createElement('button');
    closeButton.innerHTML = '×';
    closeButton.style.cssText = `
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 40px;
        color: white;
        background: none;
        border: none;
        cursor: pointer;
        z-index: 10001;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background-color 0.3s;
    `;

    closeButton.addEventListener('mouseenter', () => {
        closeButton.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
    });

    closeButton.addEventListener('mouseleave', () => {
        closeButton.style.backgroundColor = 'transparent';
    });

    const videoContainer = document.createElement('div');
    videoContainer.style.cssText = `
        position: relative;
        width: 90%;
        max-width: 800px;
        max-height: 90%;
        display: flex;
        justify-content: center;
        align-items: center;
    `;

    const videoElement = document.createElement('video');
    videoElement.src = src;
    videoElement.controls = true;
    videoElement.autoplay = true;
    videoElement.style.cssText = `
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    `;

    videoElement.onerror = function () {
        videoElement.innerHTML = `
            <div class="alert alert-danger">
                Video could not be loaded
            </div>
        `;
    };

    videoContainer.appendChild(videoElement);
    fullscreenBackdrop.appendChild(videoContainer);
    fullscreenBackdrop.appendChild(closeButton);
    document.body.appendChild(fullscreenBackdrop);
    document.body.style.overflow = 'hidden';

    const closeModal = () => {
        if (document.getElementById('fullscreenVideoBackdrop')) {
            document.body.removeChild(fullscreenBackdrop);
        }
        document.body.style.overflow = '';
    };

    closeButton.addEventListener('click', closeModal);
    fullscreenBackdrop.addEventListener('click', (e) => {
        if (e.target === fullscreenBackdrop) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', escHandler);
        }
    });
}

function initEmergencyMap() {
    const mapContainer = document.getElementById('emergency-map');
    if (mapContainer && mapContainer.dataset.lat && mapContainer.dataset.lng) {
        const lat = parseFloat(mapContainer.dataset.lat);
        const lng = parseFloat(mapContainer.dataset.lng);
        initializeMapWithCoordinates(lat, lng);
    }
}

function fetchEmergencyDetails(requestId, container, lat, lng, address) {
    fetch(`../account/backend/get_emergency_request.php?request_id=${requestId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const apiLat = data.request.coordinates?.latitude || lat;
                const apiLng = data.request.coordinates?.longitude || lng;
                const apiAddress = data.request.full_address || address;

                renderEmergencyDetails(data.request, container, apiLat, apiLng, apiAddress);
            } else {
                showErrorMessage(container, data.message || 'Failed to load request details');
            }
        })
        .catch(error => {
            console.error('Error fetching emergency details:', error);
            showErrorMessage(container, `Error loading details: ${error.message}`);
        });
}

function renderEmergencyDetails(request, container, lat, lng, address) {
    const videoHtml = request.video
        ? `
        <div class="video-thumbnail-container mb-3">
            <video class="video-thumbnail w-100" 
                   data-src="../account/uploads/emergency_videos/${request.video}">
                <source src="../account/uploads/emergency_videos/${request.video}" type="video/mp4">
            </video>
            <div class="video-overlay">
                <i class="fas fa-play-circle text-white" style="font-size: 3rem;"></i>
            </div>
        </div>
        `
        : `
        <div class="d-flex align-items-center justify-content-center p-4 text-center w-100 bg-light rounded mb-3">
            <i class="fas fa-video-slash fa-2x text-muted mb-2"></i>
            <p class="text-muted mb-0">No video provided</p>
        </div>
        `;

    container.innerHTML = `
        <div class="detail-section mb-4">
            <h4 class="section-title mb-3" style="font-size: 18px;">
                <i class="fas fa-exclamation-triangle me-2"></i>Emergency Information
            </h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Request Date:</span>
                        <span class="info-value">${formatDate(request.created_at) || 'N/A'}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value badge ${getStatusBadgeClass(request.status)}">
                            ${request.status ? request.status.charAt(0).toUpperCase() + request.status.slice(1) : 'Pending'}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-section mb-4">
            <h4 class="section-title mb-3" style="font-size: 18px;">
                <i class="fas fa-user me-2"></i>Requester Information
            </h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">${request.requester_name || 'N/A'}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">${request.contact_number || 'N/A'}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-section mb-4">
            <h4 class="section-title mb-3" style="font-size: 18px;">
                <i class="fas fa-car me-2"></i>Vehicle Information
            </h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Type:</span>
                        <span class="info-value">${request.vehicle_type || 'N/A'}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Model:</span>
                        <span class="info-value">${request.vehicle_model || 'N/A'}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-section mb-4">
            <h4 class="section-title mb-3" style="font-size: 18px;">
                <i class="fas fa-map-marker-alt me-2"></i>Location
            </h4>
            <div class="info-item mb-2">
                <span class="info-label">Address:</span>
                <span class="info-value">${address || 'Location not specified'}</span>
            </div>
            <div id="emergency-map" class="mt-3 rounded" 
                 style="height: 250px; background: #f5f5f5; border: 1px solid #ddd;"></div>
        </div>

        <div class="detail-section mb-4">
            <h4 class="section-title mb-3" style="font-size: 18px;">
                <i class="fas fa-info-circle me-2"></i>Issue Description
            </h4>
            <div class="issue-description p-3 bg-light rounded">
                ${request.issue_description || 'No description provided'}
            </div>
        </div>

        <div class="detail-section">
            <h4 class="section-title mb-3" style="font-size: 18px;">
                <i class="fas fa-video me-2"></i>Emergency Video
            </h4>
            ${videoHtml}
        </div>
    `;

    setTimeout(() => {
        initializeMapWithCoordinates(lat, lng, address);
    }, 200);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'pending': return 'bg-warning text-dark';
        case 'accepted': return 'bg-primary';
        case 'completed': return 'bg-success';
        case 'rejected': return 'bg-secondary';
        default: return 'bg-warning text-dark';
    }
}

function initializeMapWithCoordinates(lat, lng, address) {
    const mapContainer = document.getElementById('emergency-map');
    if (!mapContainer) return null;

    const latitude = parseFloat(lat);
    const longitude = parseFloat(lng);

    if (isNaN(latitude) || isNaN(longitude)) {
        if (address && address !== 'Location not specified') {
            geocodeAddress(address, mapContainer);
        } else {
            showMapError(mapContainer, 'Invalid location coordinates');
        }
        return null;
    }

    try {
        if (typeof google === 'undefined' || !google.maps) {
            showMapError(mapContainer, 'Google Maps failed to load');
            return null;
        }

        const map = new google.maps.Map(mapContainer, {
            center: { lat: latitude, lng: longitude },
            zoom: 16,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDefaultUI: true,
            zoomControl: true,
            streetViewControl: false,
            gestureHandling: 'cooperative',
            styles: [
                {
                    "featureType": "all",
                    "elementType": "labels.icon",
                    "stylers": [{ "visibility": "off" }]
                }
            ]
        });

        const marker = new google.maps.Marker({
            position: { lat: latitude, lng: longitude },
            map: map,
            icon: {
                url: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                scaledSize: new google.maps.Size(30, 30)
            }
        });


        const infoWindow = new google.maps.InfoWindow({
            content: `
        <div style="padding: 8px 12px; min-width: 200px; max-width: 260px;">
            <div style="font-weight: bold; margin-bottom: 4px; color: #333;">
                ${address || 'Location'}
            </div>
            <a href="https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}" 
               target="_blank" 
               style="display: inline-block; margin-top: 6px; color: #1a73e8; text-decoration: none;">
                <i class="fas fa-directions"></i> Get Directions
            </a>
        </div>
    `,
            maxWidth: 260
        });


        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });

        infoWindow.open(map, marker);

        mapContainer.style.overflow = 'hidden';
        const observer = new MutationObserver(() => {
            const scrollDivs = mapContainer.querySelectorAll('div');
            scrollDivs.forEach(div => {
                if (div.style.overflow === 'auto' || div.style.overflow === 'scroll') {
                    div.style.overflow = 'hidden';
                }
            });
        });
        observer.observe(mapContainer, { childList: true, subtree: true });

        activeMap = map;
        return map;

    } catch (error) {
        showMapError(mapContainer, 'Failed to initialize map');
        return null;
    }
}

function geocodeAddress(address, mapContainer) {
    if (typeof google === 'undefined' || !google.maps) {
        showMapError(mapContainer, 'Google Maps not available for geocoding');
        return;
    }

    const geocoder = new google.maps.Geocoder();

    geocoder.geocode({ address: address }, (results, status) => {
        if (status === 'OK' && results[0]) {
            const location = results[0].geometry.location;
            const lat = location.lat();
            const lng = location.lng();

            console.log('Geocoded address to coordinates:', lat, lng);
            initializeMapWithCoordinates(lat, lng, address);
        } else {
            console.error('Geocoding failed:', status);
            showMapError(mapContainer, 'Could not find location on map');
        }
    });
}

function showMapError(mapContainer, message) {
    mapContainer.innerHTML = `
        <div class="d-flex align-items-center justify-content-center h-100 text-center p-3" 
             style="background-color: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 8px;">
            <div>
                <i class="fas fa-map-marked-alt fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">${message}</p>
                <small class="text-muted">Location details may still be available above</small>
            </div>
        </div>
    `;
}

function waitForGoogleMaps(callback) {
    if (typeof google !== 'undefined' && google.maps) {
        callback();
    } else {
        setTimeout(() => waitForGoogleMaps(callback), 100);
    }
}

function checkGoogleMapsAPI() {
    if (typeof google === 'undefined' || !google.maps) {
        console.error('Google Maps API not loaded properly');
        return false;
    }
    return true;
}

$(document).on('shown.bs.modal', '#emergencyModal', function () {
    const modalBody = $(this).find('.modal-body');
    const content = $(this).find('#emergencyDetails');

    content.focus();

    if (content.outerHeight() > modalBody.outerHeight()) {
        modalBody.css('overflow-y', 'auto');
    } else {
        modalBody.css('overflow-y', 'visible');
    }
});

$(document).on('keydown', '#emergencyDetails', function (e) {
    const scrollStep = 50;
    const modalBody = $(this).closest('.modal-body');

    if (e.key === 'ArrowUp' || e.key === 'Up') {
        modalBody.scrollTop(modalBody.scrollTop() - scrollStep);
        e.preventDefault();
    } else if (e.key === 'ArrowDown' || e.key === 'Down') {
        modalBody.scrollTop(modalBody.scrollTop() + scrollStep);
        e.preventDefault();
    } else if (e.key === 'PageUp') {
        modalBody.scrollTop(modalBody.scrollTop() - modalBody.outerHeight());
        e.preventDefault();
    } else if (e.key === 'PageDown') {
        modalBody.scrollTop(modalBody.scrollTop() + modalBody.outerHeight());
        e.preventDefault();
    } else if (e.key === 'Home') {
        modalBody.scrollTop(0);
        e.preventDefault();
    } else if (e.key === 'End') {
        modalBody.scrollTop(this.scrollHeight);
        e.preventDefault();
    }
});


document.addEventListener('DOMContentLoaded', function () {
    if (!checkGoogleMapsAPI()) {
        console.warn('Google Maps API not available - maps will not display');
    }

    const tabs = document.querySelectorAll('.emergency-tab');

    function filterEmergencyRequests(status) {
        const cards = document.querySelectorAll('.emergency-card');
        const emptyStates = document.querySelectorAll('.emergency-empty-state');
        let visibleCount = 0;

        emptyStates.forEach(state => state.style.display = 'none');

        cards.forEach(card => {
            const isVisible = status === 'all' || card.classList.contains(`emergency-status-${status}`);
            card.style.display = isVisible ? 'flex' : 'none';
            if (isVisible) {
                visibleCount++;
            }
        });

        if (visibleCount === 0) {
            const emptyState = document.querySelector(`.emergency-empty-state-${status}`);
            if (emptyState) {
                emptyState.style.display = 'block';
            }
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const status = this.getAttribute('data-status');
            filterEmergencyRequests(status);
        });
    });

    const initialStatus = document.querySelector('.emergency-tab.active')?.getAttribute('data-status') || 'all';
    filterEmergencyRequests(initialStatus);
});

window.addEventListener('load', function () {
    setTimeout(() => {
        if (!checkGoogleMapsAPI()) {
            console.error('Google Maps API failed to load after page load');
            showToast('Maps may not display properly due to loading issues', 'warning');
        }
    }, 1000);
});

window.gm_authFailure = function () {
    console.error('Google Maps API authentication failed');
    showToast('Map authentication failed. Please check API key.', 'danger');
};