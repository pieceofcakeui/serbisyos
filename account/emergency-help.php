<?php
require_once '../functions/auth.php';
include 'backend/base-path.php';
include 'backend/db_connection.php';
include 'backend/emergency-modal.php';

$profile_type = '';
$is_owner_profile = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $userQuery = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
    $userQuery->bind_param("i", $user_id);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    if ($userRow = $userResult->fetch_assoc()) {
        $profile_type = $userRow['profile_type'];
        $is_owner_profile = ($profile_type === 'owner');
    }
    $userQuery->close();
}

$selected_service_slug_on_load = isset($_GET['service_slug']) ? $_GET['service_slug'] : null;

$services_structure = [];
$sql_services = "
    SELECT
        ec.name AS category_name,
        esc.name AS subcategory_name,
        es.name AS service_display_name,
        es.value AS service_machine_value,
        es.slug AS service_slug
    FROM
        emergency_assistance_services AS es
    JOIN
        emergency_subcategories AS esc ON es.subcategory_id = esc.id
    JOIN
        emergency_categories AS ec ON esc.category_id = ec.id
    ORDER BY
        ec.display_order, ec.name, esc.name, es.name
";
$result_services = $conn->query($sql_services);
if ($result_services && $result_services->num_rows > 0) {
    while ($row = $result_services->fetch_assoc()) {
        $category = $row['category_name'];
        $subcategory = $row['subcategory_name'];
        $services_structure[$category][$subcategory][] = [
            'name' => $row['service_display_name'],
            'value' => $row['service_machine_value'],
            'slug' => $row['service_slug']
        ];
    }
}

$shops_data = [];
// REVERTED SQL QUERY - REMOVED sa.is_verified
$sql = "SELECT sa.id, sa.shop_name, sa.shop_logo, sa.shop_location, sa.latitude, sa.longitude, sa.phone, sa.user_id, sa.show_emergency, sa.shop_slug, sa.shop_status, sec.emergency_hours, sec.offered_services
        FROM shop_applications sa
        LEFT JOIN shop_emergency_config sec ON sa.id = sec.shop_id
        WHERE sa.status = 'Approved' AND sa.show_emergency = 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $default_logo_url = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
        $logo_path = $default_logo_url;
        if (!empty($row['shop_logo'])) {
            $base_directory = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
            $logo_file_path = $_SERVER['DOCUMENT_ROOT'] . $base_directory . '/account/uploads/shop_logo/' . $row['shop_logo'];
            if (file_exists($logo_file_path)) {
                $logo_path = BASE_URL . '/account/uploads/shop_logo/' . $row['shop_logo'];
            }
        }
        $services_array = [];
        if (!empty($row['offered_services'])) {
            $decoded_services = json_decode($row['offered_services'], true);
            if (is_array($decoded_services)) {
                $services_array = $decoded_services;
            }
        }
        $shop_lat = !empty($row['latitude']) ? (float)$row['latitude'] : 0;
        $shop_lng = !empty($row['longitude']) ? (float)$row['longitude'] : 0;
        $shop_id = $row['id'];
        
        $avg_rating = 0;
        $rating_query = "SELECT AVG(rating) as avg_rating FROM shop_ratings WHERE shop_id = ?";
        $stmt_rating = $conn->prepare($rating_query);
        $stmt_rating->bind_param("i", $shop_id);
        $stmt_rating->execute();
        $rating_result = $stmt_rating->get_result()->fetch_assoc();
        if ($rating_result && $rating_result['avg_rating']) {
            $avg_rating = (float)$rating_result['avg_rating'];
        }
        $stmt_rating->close();
        
        $is_top_rated = ($avg_rating >= 4.0);
        
        $is_most_booked = false;
        $booking_query = "SELECT COUNT(*) as total_completed FROM services_booking WHERE shop_id = ? AND booking_status = 'Completed'";
        $stmt_booking = $conn->prepare($booking_query);
        $stmt_booking->bind_param("i", $shop_id);
        $stmt_booking->execute();
        $booking_result = $stmt_booking->get_result()->fetch_assoc();
        if ($booking_result && $booking_result['total_completed'] >= 10) {
            $is_most_booked = true;
        }
        $stmt_booking->close();

        $button_state = 'hidden';
        $emergency_hours_array_formatted = [];
        $shop_status = $row['shop_status'] ?? 'open';

        if ($row['show_emergency'] && $shop_status == 'open') {
            $is_owner_of_this_shop = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id'];
            
            if ($is_owner_of_this_shop) {
                $button_state = 'owner_of_this_shop';
            } elseif ($is_owner_profile) {
                $button_state = 'any_shop_owner';
            } else {
                $emergency_hours_raw = $row['emergency_hours'] ?? '[]';
                $emergency_hours_array_raw = json_decode($emergency_hours_raw, true) ?: [];
                $is_emergency_closed = true;
                date_default_timezone_set('Asia/Manila');
                $current_day = date('l');
                $current_time = date('H:i');
                if (!empty($emergency_hours_array_raw)) {
                    foreach ($emergency_hours_array_raw as $time_slot) {
                        $parts = explode(', ', $time_slot, 2);
                        if (count($parts) === 2) {
                            $day = trim($parts[0]);
                            $time_range = trim($parts[1]);
                            if (strcasecmp($day, $current_day) === 0) {
                                $times = explode(' - ', $time_range, 2);
                                if (count($times) === 2) {
                                    $start_time_24 = date('H:i', strtotime(trim($times[0])));
                                    $end_time_24 = date('H:i', strtotime(trim($times[1])));
                                    if ($current_time >= $start_time_24 && $current_time <= $end_time_24) {
                                        $is_emergency_closed = false;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                $button_state = $is_emergency_closed ? 'closed' : 'open';

                if (!empty($emergency_hours_array_raw)) {
                    foreach ($emergency_hours_array_raw as $time_slot) {
                        $parts = explode(', ', $time_slot, 2);
                        if (count($parts) === 2) {
                            $time_range = trim($parts[1]);
                            $times = explode(' - ', $time_range, 2);
                            if (count($times) === 2) {
                                $start_time_formatted = date('g:i A', strtotime(trim($times[0])));
                                $end_time_formatted = date('g:i A', strtotime(trim($times[1])));
                                $emergency_hours_array_formatted[] = trim($parts[0]) . ', ' . $start_time_formatted . ' - ' . $end_time_formatted;
                            } else { $emergency_hours_array_formatted[] = $time_slot; }
                        } else { $emergency_hours_array_formatted[] = $time_slot; }
                    }
                }
            }
        } elseif ($shop_status != 'open') {
           $button_state = 'shop_closed'; 
        }

        $shops_data[] = [
            'id' => $row['id'],
            'slug' => $row['shop_slug'],
            'name' => $row['shop_name'],
            'address' => $row['shop_location'],
            'logo' => $logo_path,
            'services' => $services_array,
            'lat' => $shop_lat,
            'lng' => $shop_lng,
            'isTopRated' => $is_top_rated,
            'isMostBooked' => $is_most_booked,
            'phone' => $row['phone'],
            'user_id' => $row['user_id'],
            'button_state' => $button_state,
            'emergency_hours' => $emergency_hours_array_formatted,
            'shop_status' => $shop_status,
            'avg_rating' => $avg_rating,
            'is_verified' => true // Set to true for all shops as requested
        ];
    }
}

usort($shops_data, function($a, $b) {
    return $b['avg_rating'] <=> $a['avg_rating'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Help</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/emergency-help.css">
    <style>
    .modal {
        z-index: 1060 !important;
    }

    .modal-backdrop {
        z-index: 1050 !important;
    }
    .sidebar {
        z-index: 1040 !important;
    }
        .shop-logo-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
        }
        .shop-logo-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .verified-badge-icon {
            position: absolute;
            bottom: 0px;
            right: 0px;
            width: 24px;
            height: 24px;
            background-color: #1d9bf0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
       .verified-badge-icon .fa-check {
            color: white !important;
            font-size: 12px;
        }
        .emergency-help-sidebar-header { position: relative; }
        #search-suggestions { position: absolute; top: 100%; left: 0; background-color: white; border: 1px solid #ddd; border-top: none; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto; display: none; border-radius: 0 0 5px 5px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .suggestion-item { padding: 10px; cursor: pointer; font-size: 0.9rem; }
        .suggestion-item:hover { background-color: #f0f0f0; }
        .emergency-help-empty-state { text-align: center; padding: 60px 20px; color: #888; width: 100%; }
        .emergency-help-empty-state i { font-size: 6rem; margin-bottom: 25px; }
        .emergency-help-empty-state p { font-size: 1.5rem; font-weight: 500; }
        .suggestion-header { padding: 8px 10px; font-size: 0.8rem; font-weight: 600; color: #555; background-color: #f8f8f8; border-bottom: 1px solid #eee; }
        .suggestion-item-google i { width: 15px; text-align: center; }
        #find-near-me-btn { font-weight: 600; }
        .shop-distance { font-size: 0.9rem; font-weight: 600; color: #0d6efd; margin-top: 5px; margin-bottom: 0; }
        .shop-distance i { margin-right: 5px; }
        .maps-marker-label-with-outline { color: black; font-size: 12px; font-weight: bold; text-shadow: -1px -1px 0 white, 1px -1px 0 white, -1px 1px 0 white, 1px 1px 0 white; }
         .shop-status-badge { padding: 3px 10px; border-radius: 15px; font-size: 0.75rem; font-weight: 600; display: inline-block; margin-top: 5px; margin-bottom: 8px; text-align: center; }
        .shop-status-badge.temporarily-closed { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .shop-status-badge.permanently-closed { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .shop-status-badge i { margin-right: 4px; }
        .shop-card-buttons .btn.disabled, .emergency-help-btn-request:disabled { background-color: #cccccc; border-color: #cccccc; color: #666666; cursor: not-allowed; opacity: 0.65; pointer-events: none; }
    </style>
</head>
<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="emergency-help-section">
            <div class="container">
                <div class="emergency-help-page-header">
                    <h1>Emergency Roadside Assistance</h1>
                    <p>Stuck on the road? Don't panic. Serbisyos connects you with nearby partner shops ready to provide immediate help.</p>
                </div>
                <div class="emergency-help-map-container">
                    <div class="emergency-help-sidebar">
                        <div class="emergency-help-sidebar-header">
                            <h2>Find Emergency Help</h2>
                            <input type="text" id="search-input" class="form-control" placeholder="Search for a shop location...">
                            <div id="search-suggestions"></div>
                            <button id="find-near-me-btn" class="btn btn-primary w-100 mt-2">
                                <i class="fas fa-location-arrow"></i> Find Near Me
                                <span id="find-me-loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            </button>
                        </div>
                        <div class="services-container">
                            <div class="services-container-accordion" id="services-accordion">
                                <button class="emergency-help-service-btn emergency-help-active" data-service="all" data-slug="all">All Services</button>
                            </div>
                            <div class="service-selectors" id="service-selectors">
                                <select id="category-select"><option value="">Select Category</option></select>
                                <select id="subcategory-select" disabled><option value="">Select Subcategory</option></select>
                                <select id="service-select" disabled><option value="">Select Service</option></select>
                            </div>
                        </div>
                    </div>
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <div class="emergency-help-shops-display-section">
            <div class="container">
                <h2 class="emergency-help-section-title">Available Shops</h2>
                <p style="text-align: center; font-size: 1.2rem; color: #666; max-width: 700px; margin: 1rem auto 0;">Available partner shops that offer immediate assistance. </br> You have two options: call them directly for the fastest response, or request help via their form.</p>
                <small style="display:block; text-align:center; font-style:italic; margin-top: 10px;">Note: Emergency assistance depends on each shop's business hours and availability.</small>
                <div class="row" id="shops-grid-main"></div>
            </div>
        </div>
        <div class="emergency-help-safety-tips-section">
            <div class="container">
                <h2 class="emergency-help-section-title">Safety Tips While You Wait</h2>
                <div class="emergency-help-safety-grid">
                    <div>
                        <div class="emergency-help-safety-tip"><i class="fas fa-exclamation-triangle"></i><div><h5>Stay Visible</h5><p>Turn on your hazard lights. If you have an early warning device, place it at a safe distance behind your vehicle.</p></div></div>
                        <div class="emergency-help-safety-tip"><i class="fas fa-map-pin"></i><div><h5>Know Your Location</h5><p>Be ready to provide your exact location. Use your phone's GPS or look for nearby landmarks or street signs.</p></div></div>
                        <div class="emergency-help-safety-tip"><i class="fas fa-car"></i><div><h5>Stay in Your Vehicle</h5><p>In most cases, it's safest to remain in your vehicle with the doors locked, especially if you are on a busy road.</p></div></div>
                    </div>
                    <div class="emergency-help-safety-img"><img src="<?php echo BASE_URL; ?>/assets/img/partner/waiting.webp" alt="Roadside safety"></div>
                </div>
            </div>
        </div>
    </div>

   <?php include 'include/emergency-modal.php'; ?>

    <div class="modal fade" id="emergencyClosedModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <h5 class="text-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Shop Closed</h5>
                    <p>We're sorry, but this shop is currently closed for emergency services.</p>
                    <p>Please try another shop that offers emergency assistance 24/7.</p>
                    <div class="mt-4 text-start">
                        <h6>Emergency Service Hours:</h6>
                        <ul class="list-group" id="emergencyHoursList"></ul>
                    </div>
                    <div class="mt-4 d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="scrollToShops()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div> <div class="modal fade" id="ownerRestrictionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3" style="max-width: 400px; margin: auto;">
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle text-warning fa-2x mb-3"></i>
                    <p>You cannot request emergency service for your own shop. This feature is for customers only.</p>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="scrollToShops()">Close</button>
                </div>
            </div>
        </div>
    </div> <div class="modal fade" id="generalOwnerRestrictionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3" style="max-width: 400px; margin: auto;">
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle text-warning fa-2x mb-3"></i>
                    <p>Shop owners cannot request emergency services from other shops. This feature is for customers only.</p>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="scrollToShops()">Close</button>
                </div>
            </div>
        </div>
    </div> <?php include 'include/help-toggle.php'; ?>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places,directions&callback=initMap" async defer></script>

    <?php include 'include/toast.php'; ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/navbar.js"></script>

    <script>
        let map;
        let infoWindow;
        let userLocationInfoWindow = null;
        let autocompleteService;
        let placesService;
        let directionsService;
        let directionsRenderer;
        const markers = [];
        let userMarker = null;
        let userCurrentLocation = null;
        const shops = <?php echo json_encode($shops_data); ?>;
        const servicesData = <?php echo json_encode($services_structure); ?>;
        const currentUserId = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
        const userProfileType = <?php echo json_encode($profile_type); ?>;
        const initialServiceSlug = <?php echo json_encode($selected_service_slug_on_load); ?>;
        const BASE_URL = "<?php echo BASE_URL; ?>";

        document.addEventListener('DOMContentLoaded', () => {
            buildServicesUI();
            setupEventListeners();
            if (initialServiceSlug) {
                const serviceBtn = document.querySelector(`.emergency-help-service-btn[data-slug="${initialServiceSlug}"]`);
                if (serviceBtn) {
                    setTimeout(() => serviceBtn.click(), 100);
                }
            }
        });

        function buildServicesUI() {
            const accordionContainer = document.getElementById('services-accordion');
            const categorySelect = document.getElementById('category-select');
            for (const categoryName in servicesData) {
                const categoryDiv = document.createElement('div');
                categoryDiv.className = 'service-category';
                categoryDiv.innerHTML = `<div class="service-category-header">${categoryName}</div>`;
                for (const subcategoryName in servicesData[categoryName]) {
                    const subcategoryDiv = document.createElement('div');
                    subcategoryDiv.className = 'service-subcategory';
                    subcategoryDiv.innerHTML = `<div class="service-subcategory-header">${subcategoryName}</div>`;
                    const serviceList = document.createElement('div');
                    serviceList.className = 'service-list';
                    servicesData[categoryName][subcategoryName].forEach(service => {
                        const serviceBtn = document.createElement('button');
                        serviceBtn.className = 'emergency-help-service-btn';
                        serviceBtn.dataset.service = service.value;
                        serviceBtn.dataset.slug = service.slug;
                        serviceBtn.textContent = service.name;
                        serviceList.appendChild(serviceBtn);
                    });
                    subcategoryDiv.appendChild(serviceList);
                    categoryDiv.appendChild(subcategoryDiv);
                }
                accordionContainer.appendChild(categoryDiv);
                categorySelect.add(new Option(categoryName, categoryName));
            }
        }

        function setupEventListeners() {
            const searchInput = document.getElementById('search-input');
            const suggestionsContainer = document.getElementById('search-suggestions');

            let debounceTimer;
            const debounce = (callback, time) => {
                window.clearTimeout(debounceTimer);
                debounceTimer = window.setTimeout(callback, time);
            };

            searchInput.addEventListener('input', () => {
                debounce(() => {
                    const query = searchInput.value;
                    if (query.length < 3) {
                        suggestionsContainer.innerHTML = '';
                        suggestionsContainer.style.display = 'none';
                        return;
                    }

                    suggestionsContainer.innerHTML = '';
                    let hasSuggestions = false;
                    const localQuery = query.toLowerCase();

                    const filteredShops = shops.filter(shop => shop.address.toLowerCase().includes(localQuery));
                    if (filteredShops.length > 0) {
                        const header = document.createElement('div');
                        header.className = 'suggestion-header';
                        header.textContent = 'Partners Shop Address';
                        suggestionsContainer.appendChild(header);

                        filteredShops.forEach(shop => {
                            const item = document.createElement('div');
                            item.className = 'suggestion-item suggestion-item-local';
                            item.textContent = shop.address;
                            item.onclick = () => {
                                searchInput.value = shop.address;
                                suggestionsContainer.innerHTML = '';
                                suggestionsContainer.style.display = 'none';
                                const shopPosition = { lat: shop.lat, lng: shop.lng };
                                map.panTo(shopPosition);
                                map.setZoom(15);
                                const marker = markers.find(m => m.getTitle() === shop.name);
                                if (marker) {
                                    infoWindow.setContent(`<b>${shop.name}</b><br>${shop.address}<br><a href="${BASE_URL}/account/shop/${shop.slug}">View Details</a>`);
                                    infoWindow.open(map, marker);
                                }
                            };
                            suggestionsContainer.appendChild(item);
                        });
                        hasSuggestions = true;
                    }

                    if (autocompleteService) {
                        autocompleteService.getPlacePredictions({
                            input: query,
                            bounds: map.getBounds(),
                            componentRestrictions: { 'country': 'ph' }
                        }, (predictions, status) => {
                            if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                                if (predictions.length > 0) {
                                    const header = document.createElement('div');
                                    header.className = 'suggestion-header';
                                    header.textContent = 'Address Suggestions';
                                    suggestionsContainer.appendChild(header);
                                }

                                predictions.forEach(prediction => {
                                    const item = document.createElement('div');
                                    item.className = 'suggestion-item suggestion-item-google';
                                    item.innerHTML = `<i class="fas fa-map-marker-alt me-2 text-muted"></i> ${prediction.description}`;
                                    item.onclick = () => {
                                        searchInput.value = prediction.description;
                                        suggestionsContainer.innerHTML = '';
                                        suggestionsContainer.style.display = 'none';

                                        placesService.getDetails({
                                            placeId: prediction.place_id,
                                            fields: ['geometry']
                                        }, (place, status) => {
                                            if (status === google.maps.places.PlacesServiceStatus.OK && place && place.geometry && place.geometry.location) {
                                                if (place.geometry.viewport) {
                                                    map.fitBounds(place.geometry.viewport);
                                                } else {
                                                    map.setCenter(place.geometry.location);
                                                    map.setZoom(17);
                                                }
                                            }
                                        });
                                    };
                                    suggestionsContainer.appendChild(item);
                                });
                                hasSuggestions = true;
                            }

                            if (hasSuggestions) {
                                suggestionsContainer.style.display = 'block';
                            } else {
                                suggestionsContainer.style.display = 'none';
                            }
                        });
                    } else {
                        if (hasSuggestions) {
                            suggestionsContainer.style.display = 'block';
                        } else {
                            suggestionsContainer.style.display = 'none';
                        }
                    }
                }, 300);
            });

            document.addEventListener('click', (event) => {
                if (!searchInput.contains(event.target)) suggestionsContainer.style.display = 'none';
            });

            document.getElementById('find-near-me-btn').addEventListener('click', () => {
                const btn = document.getElementById('find-near-me-btn');
                const loader = document.getElementById('find-me-loading');

                btn.disabled = true;
                loader.style.display = 'inline-block';

                if (!navigator.geolocation) {
                    toastr.error("Geolocation is not supported by your browser.");
                    btn.disabled = false;
                    loader.style.display = 'none';
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        userCurrentLocation = { lat: lat, lng: lng };

                        if (userMarker) {
                            userMarker.setMap(null);
                            userMarker = null;
                        }
                        if (directionsRenderer) {
                            directionsRenderer.setDirections({ routes: [] });
                        }

                        const activeServiceBtn = document.querySelector('.emergency-help-service-btn.emergency-help-active');
                        const serviceValue = activeServiceBtn ? activeServiceBtn.dataset.service : 'all';

                        displayShops(serviceValue, userCurrentLocation);
                        scrollToShops();

                        btn.disabled = false;
                        loader.style.display = 'none';
                    },
                    (error) => {
                        let errorMsg = "An unknown error occurred.";
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg = "Please allow location access in your browser settings.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg = "Location information is unavailable.";
                                break;
                            case error.TIMEOUT:
                                errorMsg = "The request to get user location timed out.";
                                break;
                        }
                        toastr.error(errorMsg);
                        btn.disabled = false;
                        loader.style.display = 'none';
                    }
                );
            });

            const accordionContainer = document.getElementById('services-accordion');
            accordionContainer.addEventListener('click', (e) => {
                const target = e.target;
                if (target.classList.contains('service-category-header')) target.parentElement.classList.toggle('open');
                if (target.classList.contains('service-subcategory-header')) target.parentElement.classList.toggle('open');
                if (target.classList.contains('emergency-help-service-btn')) {
                    document.querySelector('.emergency-help-service-btn.emergency-help-active')?.classList.remove('emergency-help-active');
                    target.classList.add('emergency-help-active');
                    const serviceValue = target.dataset.service;
                    const serviceSlug = target.dataset.slug;
                    displayShops(serviceValue, userCurrentLocation);
                    updateDropdownsFromAccordion(serviceValue);
                    const newPath = serviceSlug === 'all' ? `${BASE_URL}/emergency-help` : `${BASE_URL}/emergency-help/${serviceSlug}`;
                    history.pushState({service: serviceValue}, '', newPath);
                    
                    scrollToShops();
                }
            });

            const categorySelect = document.getElementById('category-select');
            const subcategorySelect = document.getElementById('subcategory-select');
            const serviceSelect = document.getElementById('service-select');

            categorySelect.addEventListener('change', () => {
                const selectedCategory = categorySelect.value;
                subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                serviceSelect.innerHTML = '<option value="">Select Service</option>';
                subcategorySelect.disabled = true;
                serviceSelect.disabled = true;
                if (selectedCategory && servicesData[selectedCategory]) {
                    for (const subcategoryName in servicesData[selectedCategory]) {
                        subcategorySelect.add(new Option(subcategoryName, subcategoryName));
                    }
                    subcategorySelect.disabled = false;
                }
                displayShops('all', userCurrentLocation);
                document.querySelector('.emergency-help-service-btn.emergency-help-active')?.classList.remove('emergency-help-active');
                document.querySelector('.emergency-help-service-btn[data-service="all"]')?.classList.add('emergency-help-active');
                history.pushState({service: 'all'}, '', `${BASE_URL}/emergency-help`);
            });

            subcategorySelect.addEventListener('change', () => {
                const selectedCategory = categorySelect.value;
                const selectedSubcategory = subcategorySelect.value;
                serviceSelect.innerHTML = '<option value="">Select Service</option>';
                serviceSelect.disabled = true;
                if (selectedCategory && selectedSubcategory && servicesData[selectedCategory][selectedSubcategory]) {
                    servicesData[selectedCategory][selectedSubcategory].forEach(service => {
                        serviceSelect.add(new Option(service.name, service.value));
                    });
                    serviceSelect.disabled = false;
                }
                 const currentService = serviceSelect.value || 'all';
                 displayShops(currentService, userCurrentLocation);
            });

            serviceSelect.addEventListener('change', () => {
                const serviceValue = serviceSelect.value;
                if (serviceValue) {
                    displayShops(serviceValue, userCurrentLocation);
                    document.querySelector('.emergency-help-service-btn.emergency-help-active')?.classList.remove('emergency-help-active');
                    const activeBtn = document.querySelector(`.emergency-help-service-btn[data-service="${serviceValue}"]`);
                    if (activeBtn) {
                        activeBtn.classList.add('emergency-help-active');
                        const serviceSlug = activeBtn.dataset.slug;
                        const newPath = `${BASE_URL}/emergency-help/${serviceSlug}`;
                        history.pushState({service: serviceValue}, '', newPath);
                    }

                    scrollToShops();
                } else {
                     displayShops('all', userCurrentLocation);
                }
            });
        }

        function updateDropdownsFromAccordion(serviceValue) {
            const catSelect = document.getElementById('category-select');
            const subcatSelect = document.getElementById('subcategory-select');
            const servSelect = document.getElementById('service-select');

            if (!serviceValue || serviceValue === 'all') {
                catSelect.value = "";
                subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';
                servSelect.innerHTML = '<option value="">Select Service</option>';
                subcatSelect.disabled = true;
                servSelect.disabled = true;
                return;
            }
            for (const catName in servicesData) {
                for (const subcatName in servicesData[catName]) {
                    const service = servicesData[catName][subcatName].find(s => s.value === serviceValue);
                    if (service) {
                        catSelect.value = catName;
                        subcatSelect.innerHTML = '';
                        for (const sub in servicesData[catName]) {
                            subcatSelect.add(new Option(sub, sub));
                        }
                        subcatSelect.value = subcatName;
                        subcatSelect.disabled = false;

                        servSelect.innerHTML = '';
                        servicesData[catName][subcatName].forEach(s => servSelect.add(new Option(s.name, s.value)));
                        servSelect.value = serviceValue;
                        servSelect.disabled = false;
                        return;
                    }
                }
            }
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const d = R * c;
            return d;
        }

        function initMap() {
            const iloilo = { lat: 10.7202, lng: 122.5650 };
            map = new google.maps.Map(document.getElementById("map"), {
                center: iloilo, zoom: 12, disableDefaultUI: true, zoomControl: true
            });
            infoWindow = new google.maps.InfoWindow();
            userLocationInfoWindow = new google.maps.InfoWindow();

            autocompleteService = new google.maps.places.AutocompleteService();
            placesService = new google.maps.places.PlacesService(map);

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            directionsRenderer.setOptions({ preserveViewport: false, suppressMarkers: true });

            displayShops('all', null);
        }

        function displayShops(serviceType, userLocation = null) {
            const shopGrid = document.getElementById("shops-grid-main");
            shopGrid.innerHTML = "";

            markers.forEach(marker => marker.setMap(null));
            markers.length = 0;

            if (!userLocation && userMarker) {
                 userMarker.setMap(null);
                 userMarker = null;
            }
            if (!userLocation && directionsRenderer) {
                 directionsRenderer.setDirections({ routes: [] });
            }

            let filteredShops = serviceType === 'all' ?
                [...shops] :
                shops.filter(shop => shop.services.includes(serviceType));

            if (userLocation) {
                filteredShops.forEach(shop => {
                    shop.distance = calculateDistance(userLocation.lat, userLocation.lng, shop.lat, shop.lng);
                });
                filteredShops.sort((a, b) => a.distance - b.distance);
            } else {
                filteredShops.forEach(shop => shop.distance = undefined);
            }

            if (filteredShops.length === 0) {
                shopGrid.innerHTML = `<div class="col-12"><div class="emergency-help-empty-state"><i class="fas fa-store-slash"></i><p>No shops found for this service.</p></div></div>`;
                if (directionsRenderer) directionsRenderer.setDirections({ routes: [] });
                if (userMarker) {
                    userMarker.setMap(null); userMarker = null;
                }
                return;
            }

            if(userLocation) {
                 drawDirectionsToNearestShop(userLocation, filteredShops);
            }

            filteredShops.forEach((shop) => {
                let markerOptions = {
                    position: { lat: shop.lat, lng: shop.lng },
                    map: map,
                    title: shop.name,
                };

                if (shop.distance && shop.distance > 10) {
                    markerOptions.label = {
                        text: `${shop.distance.toFixed(1)} km`,
                        color: 'black',
                        fontWeight: 'bold',
                        fontSize: '12px',
                        className: 'maps-marker-label-with-outline'
                    };
                } else {
                    markerOptions.animation = google.maps.Animation.DROP;
                }

                const marker = new google.maps.Marker(markerOptions);
                markers.push(marker);

                marker.addListener("click", () => {
                    infoWindow.setContent(`<b>${shop.name}</b><br>${shop.address}<br><a href="${BASE_URL}/account/shop/${shop.slug}">View Details</a>`);
                    infoWindow.open(map, marker);
                });

                const shopCard = document.createElement("div");
                shopCard.className = "col-12 col-md-6 col-lg-4 mb-4";
                const topRatedBadge = shop.isTopRated ? `<div class="top-rated-badge"><i class="fas fa-star"></i> Top Rated</div>` : '';
                const mostBookedBadge = shop.isMostBooked ? `<div class="most-booked-badge"><i class="fas fa-calendar-check"></i> Most Booked</div>` : '';
                const distanceHtml = shop.distance ? `<p class="shop-distance"><i class="fas fa-route"></i> ${shop.distance.toFixed(1)} km away</p>` : '';

                let statusBadgeHtml = '';
                if (shop.shop_status === 'temporarily_closed') {
                    statusBadgeHtml = `<div class="shop-status-badge temporarily-closed"><i class="fas fa-exclamation-triangle"></i> Temporarily Closed</div>`;
                } else if (shop.shop_status === 'permanently_closed') {
                    statusBadgeHtml = `<div class="shop-status-badge permanently-closed"><i class="fas fa-store-slash"></i> Permanently Closed</div>`;
                }

                const isDisabled = shop.shop_status !== 'open';
                const callButtonClass = isDisabled ? 'disabled' : '';
                const callButtonTitle = isDisabled ? 'Calling unavailable while shop is closed' : 'Call Now';
                const callButtonHref = isDisabled ? '#' : `tel:${shop.phone}`;

                const requestButtonDisabledAttr = isDisabled ? 'disabled' : '';
                const requestButtonTitle = isDisabled ? 'Request unavailable while shop is closed' : 'Request Emergency Service';
                const shopJsonString = JSON.stringify(shop).replace(/'/g, "&apos;");
                const requestButtonOnclick = isDisabled ? '' : `onclick='handleEmergencyRequestClick(event, ${shopJsonString})'`;

                let requestButtonHtml = '';
                 switch (shop.button_state) {
                     case 'owner_of_this_shop': 
                         requestButtonHtml = `<button class="emergency-help-btn-request" onclick="showOwnerRestrictionModal()" ${requestButtonDisabledAttr} style="height: 40px; padding: 0 10px;">Request</button>`; 
                         break;
                     case 'any_shop_owner': 
                         requestButtonHtml = `<button class="emergency-help-btn-request" onclick="showGeneralOwnerRestrictionModal()" ${requestButtonDisabledAttr} style="height: 40px; padding: 0 10px;">Request</button>`; 
                         break;
                     case 'shop_closed': 
                         requestButtonHtml = `<button class="emergency-help-btn-request" ${requestButtonDisabledAttr} title="${requestButtonTitle}" style="height: 40px; padding: 0 10px;">Request</button>`; 
                         break;
                     default: 
                         requestButtonHtml = `<button class="emergency-help-btn-request" ${requestButtonOnclick} ${requestButtonDisabledAttr} title="${requestButtonTitle}" style="height: 40px; padding: 0 10px;">Request</button>`; 
                         break;
                 }
                
                const verifiedBadgeHtml = shop.is_verified ? `<div class="verified-badge-icon"><i class="fas fa-check"></i></div>` : '';

                shopCard.innerHTML = `
                <div class="emergency-help-shop-card">
                    <div class="shop-card-content">
                        <div class="shop-logo-container">
                           <img src="${shop.logo}" alt="${shop.name} Logo" onerror="this.onerror=null;this.src='${BASE_URL}/uploads/shop_logo/logo.jpg';">
                           ${verifiedBadgeHtml}
                        </div>
                        <div class="badge-container">${topRatedBadge}${mostBookedBadge}</div>
                        <a href="${BASE_URL}/account/shop/${shop.slug}" class="shop-name-link"><h5>${shop.name}</h5></a>
                        <p>${shop.address}</p>
                        ${distanceHtml}
                        ${statusBadgeHtml}
                    </div>
                    <div class="shop-card-buttons">
                        <a href="${callButtonHref}" class="emergency-help-btn-call btn ${callButtonClass}" title="${callButtonTitle}"><i class="fas fa-phone"></i> Call Now</a>
                        ${requestButtonHtml}
                    </div>
                </div>`;
                shopGrid.appendChild(shopCard);
            });
        }

        function drawDirectionsToNearestShop(userLocation, shops) {
            if (userMarker) {
                userMarker.setMap(null);
                userMarker = null;
            }

            if (!userLocation || !directionsRenderer || shops.length === 0) {
                if (directionsRenderer) directionsRenderer.setDirections({ routes: [] });
                return;
            }

            let targetShop = null;
            const openShops = shops.filter(s => s.shop_status === 'open');
            const openShopsWithin10km = openShops.filter(s => s.distance <= 10);

             if (openShopsWithin10km.length > 0) {
                 targetShop = openShopsWithin10km[0];
             } else if (openShops.length > 0) {
                  targetShop = openShops[0];
               } else {
                   if (directionsRenderer) directionsRenderer.setDirections({ routes: [] });
                    userMarker = new google.maps.Marker({
                        position: userLocation, map: map, title: "Your Location",
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 10,
                            fillColor: "#4285F4",
                            fillOpacity: 1,
                            strokeColor: "white",
                            strokeWeight: 3
                        },
                        zIndex: 1000
                    });
                    userMarker.addListener("click", () => {
                        userLocationInfoWindow.setContent("Your Location");
                        userLocationInfoWindow.open(map, userMarker);
                    });
                    map.setCenter(userLocation);
                    map.setZoom(10); 
               return;
             }

            directionsService.route({
                origin: userLocation,
                destination: { lat: targetShop.lat, lng: targetShop.lng },
                travelMode: google.maps.TravelMode.DRIVING
            }, (response, status) => {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(response);
                    directionsRenderer.setOptions({
                        polylineOptions: {
                            strokeColor: '#4285F4',
                            strokeWeight: 5,
                            strokeOpacity: 0.8
                        },
                        suppressMarkers: true
                    });

                    userMarker = new google.maps.Marker({
                        position: userLocation,
                        map: map,
                        title: "Your Location",
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 10,
                            fillColor: "#4285F4",
                            fillOpacity: 1,
                            strokeColor: "white",
                            strokeWeight: 3
                        },
                        zIndex: 1000
                    });

                    const destinationMarker = new google.maps.Marker({
                        position: { lat: targetShop.lat, lng: targetShop.lng },
                        map: map,
                        title: targetShop.name,
                        zIndex: 999
                    });
                    markers.push(destinationMarker);

                    userMarker.addListener("click", () => {
                        userLocationInfoWindow.setContent("Your Location");
                        userLocationInfoWindow.open(map, userMarker);
                    });
                     destinationMarker.addListener("click", () => {
                         infoWindow.setContent(`<b>${targetShop.name}</b><br>${targetShop.address}<br><a href="${BASE_URL}/account/shop/${targetShop.slug}">View Details</a>`);
                         infoWindow.open(map, destinationMarker);
                     });

                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend(userLocation);
                    bounds.extend({ lat: targetShop.lat, lng: targetShop.lng });

                    const padding = { top: 100, right: 100, bottom: 100, left: 450 };
                    map.fitBounds(bounds, padding);

                } else {
                    console.warn('Directions request failed due to ' + status);
                    directionsRenderer.setDirections({ routes: [] });

                     userMarker = new google.maps.Marker({
                         position: userLocation, map: map, title: "Your Location",
                         icon: {
                             path: google.maps.SymbolPath.CIRCLE,
                             scale: 10,
                             fillColor: "#4285F4",
                             fillOpacity: 1,
                             strokeColor: "white",
                             strokeWeight: 3
                         },
                         zIndex: 1000
                     });
                     userMarker.addListener("click", () => {
                         userLocationInfoWindow.setContent("Your Location");
                         userLocationInfoWindow.open(map, userMarker);
                     });

                     map.setCenter(userLocation);
                     map.setZoom(10);
                }
            });
        }

        function handleEmergencyRequestClick(event, shop) {
            event.preventDefault();
            
            if (shop.shop_status !== 'open') {
                return;
            }

            if (shop.button_state === 'open') {
                window.location.href = `emergency-requests?shop=${shop.slug}`;
            } else if (shop.button_state === 'closed') {
                showEmergencyClosedModal(shop.emergency_hours);
            } else {
                showEmergencyClosedModal(shop.emergency_hours);
            }
        }
        
        function showEmergencyClosedModal(hoursArray) {
            const listElement = document.getElementById('emergencyHoursList');
            listElement.innerHTML = '';
            if (hoursArray && hoursArray.length > 0) {
                hoursArray.forEach(timeSlot => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item text-center';
                    li.textContent = timeSlot;
                    listElement.appendChild(li);
                });
            } else {
                listElement.innerHTML = '<li class="list-group-item text-center">Not specified.</li>';
            }
            new bootstrap.Modal(document.getElementById('emergencyClosedModal')).show();
        }
        
        function showOwnerRestrictionModal() {
            new bootstrap.Modal(document.getElementById('ownerRestrictionModal')).show();
        }
        
        function showGeneralOwnerRestrictionModal() {
            new bootstrap.Modal(document.getElementById('generalOwnerRestrictionModal')).show();
        }
        
        function scrollToShops() {
            setTimeout(() => {
                const shopsSection = document.querySelector('.emergency-help-shops-display-section');
                if (shopsSection) {
                    shopsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 300);
        }
    </script>
</body>
</html>