<?php
require_once '../functions/auth.php';
include 'backend/base-path.php';
include 'backend/db_connection.php';
include 'backend/encrypt_loc.php';

$autocomplete_data = [];
$services_sql = "SELECT name, slug FROM services ORDER BY name ASC";
$services_result = $conn->query($services_sql);
if ($services_result) {
    while ($row = $services_result->fetch_assoc()) {
        $autocomplete_data[] = [
            'label' => $row['name'],
            'slug' => $row['slug'],
            'type' => 'service'
        ];
    }
}
$shops_sql = "SELECT shop_name, shop_slug FROM shop_applications WHERE status = 'Approved' ORDER BY shop_name ASC";
$shops_result = $conn->query($shops_sql);
if ($shops_result) {
    while ($row = $shops_result->fetch_assoc()) {
        $autocomplete_data[] = [
            'label' => $row['shop_name'],
            'slug' => $row['shop_slug'],
            'type' => 'shop'
        ];
    }
}
$locations_sql = "SELECT DISTINCT shop_location FROM shop_applications WHERE status = 'Approved' AND shop_location IS NOT NULL AND shop_location != '' ORDER BY shop_location ASC";
$locations_result = $conn->query($locations_sql);
if ($locations_result) {
    while ($row = $locations_result->fetch_assoc()) {
        $autocomplete_data[] = [
            'label' => $row['shop_location'],
            'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['shop_location']), '-')),
            'type' => 'location'
        ];
    }
}

$loggedInUserAddress = '';
if (isset($_SESSION['user_id'])) {
    $stmt_addr = $conn->prepare("SELECT full_address FROM users WHERE id = ?");
    $stmt_addr->bind_param("i", $_SESSION['user_id']);
    $stmt_addr->execute();
    $result_addr = $stmt_addr->get_result();
    if ($user_addr = $result_addr->fetch_assoc()) {
        if (!empty($user_addr['full_address'])) {
            $loggedInUserAddress = decryptData($user_addr['full_address']);
        }
    }
    $stmt_addr->close();
}

$searchResults = [];
$searchQuery = "";
$filterRating = "";
$filterLocation = "";
$filterServices = "";
$showNearbyResults = false;
$nearbyShops = [];
$userAddress = "";
$serviceSlugForLookup = "";
$hasSearchCriteria = false;
$searchRadius = 10;

function calculateDistancePhp($lat1, $lon1, $lat2, $lon2)
{
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    }
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return ($miles * 1.609344);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    function get_coordinate($value) {
        if (empty($value)) {
            return null;
        }
        if (is_numeric($value)) {
            return $value;
        }
        $decrypted = decryptData($value);
        if (is_numeric($decrypted)) {
            return $decrypted;
        }
        return null;
    }

    $searchQuery         = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
    $filterLocation      = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '';
    $filterRating        = isset($_GET['rating']) ? htmlspecialchars($_GET['rating']) : '';
    $serviceSlugForLookup = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : '';
    $showNearbyResults   = isset($_GET['nearby']);

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base_path = rtrim(parse_url(BASE_URL, PHP_URL_PATH), '/');
    $request_path = str_replace($base_path, '', $path);
    $path_parts = explode('/', trim($request_path, '/'));

    if (isset($path_parts[0]) && $path_parts[0] === 'account' && isset($path_parts[1]) && $path_parts[1] === 'search') {
        $i = 2;
        while ($i < count($path_parts)) {
            $key = $path_parts[$i];
            $value = isset($path_parts[$i + 1]) ? $path_parts[$i + 1] : '';

            switch ($key) {
                case 'query':
                    $searchQuery = htmlspecialchars(str_replace('-', ' ', $value));
                    break;
                case 'location':
                    $filterLocation = htmlspecialchars(ucwords(str_replace('-', ' ', $value)));
                    break;
                case 'rating':
                    $filterRating = htmlspecialchars($value);
                    break;
                case 'service':
                    $serviceSlugForLookup = $value;
                    break;
                case 'nearby':
                    $showNearbyResults = true;
                    break;
            }
            $i += ($value) ? 2 : 1;
        }
    }


    if (!empty($serviceSlugForLookup)) {
        $stmt_slug = $conn->prepare("SELECT name FROM services WHERE slug = ?");
        if ($stmt_slug) {
            $stmt_slug->bind_param("s", $serviceSlugForLookup);
            $stmt_slug->execute();
            $result_slug = $stmt_slug->get_result();
            if ($row_slug = $result_slug->fetch_assoc()) {
                $filterServices = $row_slug['name'];
            }
            $stmt_slug->close();
        }
    }

    $hasSearchCriteria = !empty($searchQuery) || !empty($filterRating) || !empty($filterLocation) || !empty($filterServices) || $showNearbyResults;

    if ($showNearbyResults) {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $stmt = $conn->prepare("SELECT latitude, longitude, full_address FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($userLocation = $result->fetch_assoc()) {
                if (!empty($userLocation['latitude']) && !empty($userLocation['longitude'])) {
                    $userLat = decryptData($userLocation['latitude']);
                    $userLng = decryptData($userLocation['longitude']);
                    $userAddress = !empty($userLocation['full_address']) ? decryptData($userLocation['full_address']) : 'Address not available';

                    if (is_numeric($userLat) && is_numeric($userLng)) {
                        $sql = "SELECT sa.*, sa.shop_slug, sa.shop_status,
                                       COALESCE(AVG(sr.rating), 0) as average_rating, COUNT(sr.id) as rating_count
                                FROM shop_applications sa
                                LEFT JOIN shop_ratings sr ON sa.id = sr.shop_id
                                WHERE sa.status = 'Approved' AND sa.latitude IS NOT NULL AND sa.latitude != '' AND sa.longitude IS NOT NULL AND sa.longitude != ''
                                GROUP BY sa.id";
                        $shopResult = $conn->query($sql);

                        while ($shop = $shopResult->fetch_assoc()) {
                            $shopLat = get_coordinate($shop['latitude']);
                            $shopLng = get_coordinate($shop['longitude']);

                            if (is_numeric($shopLat) && is_numeric($shopLng)) {
                                $distance = calculateDistancePhp($userLat, $userLng, $shopLat, $shopLng);
                                if ($distance <= $searchRadius) { 
                                    $shop['distance'] = round($distance, 2);
                                    $nearbyShops[] = $shop;
                                }
                            }
                        }

                        if (!empty($nearbyShops)) {
                            usort($nearbyShops, function($a, $b) {
                                return $a['distance'] <=> $b['distance'];
                            });
                        }
                    }
                }
            }
            $stmt->close();
        }
    } else if ($hasSearchCriteria) {
        $sql = "SELECT sa.*, sa.shop_slug, sa.shop_status,
                               COALESCE(AVG(sr.rating), 0) as average_rating,
                               COUNT(DISTINCT sr.id) as rating_count
                        FROM shop_applications sa
                        LEFT JOIN shop_ratings sr ON sa.id = sr.shop_id
                        LEFT JOIN shop_services ss ON sa.id = ss.application_id
                        LEFT JOIN services s ON ss.service_id = s.id";

        $where = ["sa.status = 'Approved'"];
        $params = [];
        $types = "";

        if (!empty($searchQuery)) {
            $where[] = "(sa.shop_name LIKE ? OR s.name LIKE ? OR sa.barangay LIKE ?
                                 OR sa.town_city LIKE ? OR sa.province LIKE ? OR sa.shop_location LIKE ?)";
            $searchParam = "%" . $searchQuery . "%";
            array_push($params, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
            $types .= "ssssss";
        }
        if (!empty($filterLocation)) {
            $where[] = "(sa.town_city = ? OR sa.shop_location LIKE ? OR sa.barangay = ? OR CONCAT(sa.barangay, ', ', sa.town_city) = ? OR CONCAT(sa.town_city, ', ', sa.province) = ?)";
            $locationParam = $filterLocation;
            array_push($params, $locationParam, "%$locationParam%", $locationParam, $locationParam, $locationParam);
            $types .= "sssss";
        }
        if (!empty($filterServices)) {
            $where[] = "s.name = ?";
            $params[] = $filterServices;
            $types .= "s";
        }
        $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " GROUP BY sa.id";
        if (!empty($filterRating)) {
            $sql .= " HAVING average_rating >= ?";
            $params[] = (float)$filterRating;
            $types .= "d";
        }
        $sql .= " ORDER BY sa.id DESC";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $searchResults[] = $row;
            }
            $stmt->close();
        }
    }
}
function getIloiloLocations(){
    global $conn;
    $locations = [];
    $iloiloLocations = ['Arevalo', 'Bo. Obrero', 'Brgy. Tabuc Suba', 'City Proper', 'Jaro', 'La Paz', 'Mandurriao', 'Molo', 'Ajuy', 'Alimodian', 'Anilao', 'Badiangan', 'Balasan', 'Banate', 'Barotac Nuevo', 'Barotac Viejo', 'Batad', 'Bingawan', 'Cabatuan', 'Calinog', 'Carles', 'Concepcion', 'Dingle', 'Dueñas', 'Dumangas', 'Estancia', 'Guimbal', 'Igbaras', 'Janiuay', 'Lambunao', 'Leganes', 'Lemery', 'Leon', 'Maasin', 'Miagao', 'Mina', 'New Lucena', 'Oton', 'Passi City', 'Pavia', 'Pototan', 'San Dionisio', 'San Enrique', 'San Joaquin', 'San Miguel', 'San Rafael', 'Santa Barbara', 'Sara', 'Tigbauan', 'Tubungan', 'Zarraga'];
    $sql = "SELECT DISTINCT town_city, barangay FROM shop_applications WHERE status = 'Approved' AND province = 'Iloilo' ORDER BY town_city, barangay";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $locations[] = $row['town_city'];
            if (!empty($row['barangay']) && $row['barangay'] != $row['town_city']) {
                $locations[] = $row['barangay'];
            }
        }
    }
    $allLocations = array_unique(array_merge($locations, $iloiloLocations));
    sort($allLocations);
    return $allLocations;
}
function getAllServices(){
    global $conn;
    $services = [];
    $sql = "SELECT DISTINCT s.name, s.slug FROM services s JOIN shop_services ss ON s.id = ss.service_id JOIN shop_applications sa ON ss.application_id = sa.id WHERE sa.status = 'Approved' ORDER BY s.name ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
    return $services;
}
function getShopRating($shop_data){
    return [
        'average_rating' => isset($shop_data['average_rating']) ? round($shop_data['average_rating'], 1) : 0,
        'rating_count' => isset($shop_data['rating_count']) ? $shop_data['rating_count'] : 0
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/search.css">
    <style>
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
            color: #fff;
            font-size: 12px;
        }
        .no-results-container { text-align: center; padding: 60px 30px; margin-top: 40px; background-color: #f8f9fa; border: 1px dashed #e0e0e0; border-radius: 12px; }
        .no-results-icon { font-size: 4.5rem; color: #ced4da; margin-bottom: 25px; }
        .no-results-container h4 { font-size: 1.5rem; font-weight: 600; color: #343a40; margin-bottom: 10px; }
        .no-results-container p { color: #6c757d; max-width: 450px; margin: 0 auto; line-height: 1.6; }
        .autocomplete-suggestions { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); max-height: 350px; overflow-y: auto; z-index: 1001; display: none; }
        .autocomplete-suggestions div { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #eee; }
        .autocomplete-suggestions div:hover { background-color: #f5f5f5; }
        .autocomplete-suggestions div:last-child { border-bottom: none; }
        .autocomplete-no-result { padding: 12px 16px; color: #6c757d; cursor: default; }
        .autocomplete-suggestions div.autocomplete-no-result:hover { background-color: #ffffff; }

        .shop-status-badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 5px;
            margin-bottom: 8px;
            text-align: center;
        }
        .shop-status-badge.temporarily-closed {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .shop-status-badge.permanently-closed {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .shop-status-badge i {
            margin-right: 4px;
        }
    </style>
</head>
<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>
    <div class="modal fade filter-modal" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel"><i class="fas fa-filter me-2"></i>Filter</h5>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="filter-section">
                            <h6><i class="fas fa-star"></i>Minimum Rating</h6>
                            <div class="rating-filter">
                                <div class="rating-option" data-rating=""><span>Any Rating</span></div>
                                <div class="rating-option" data-rating="1"><span class="stars">★</span><span>1+ Stars</span></div>
                                <div class="rating-option" data-rating="2"><span class="stars">★★</span><span>2+ Stars</span></div>
                                <div class="rating-option" data-rating="3"><span class="stars">★★★</span><span>3+ Stars</span></div>
                                <div class="rating-option" data-rating="4"><span class="stars">★★★★</span><span>4+ Stars</span></div>
                                <div class="rating-option" data-rating="5"><span class="stars">★★★★★</span><span>5 Stars</span></div>
                            </div>
                        </div>
                        <div class="filter-section">
                            <h6><i class="fas fa-map-marker-alt"></i>Location in Iloilo Province</h6>
                            <div class="location-grid">
                                <?php $locations = getIloiloLocations(); foreach ($locations as $location): ?>
                                    <div class="location-option" data-location="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="filter-section">
                            <h6><i class="fas fa-tools"></i>Services</h6>
                            <div class="services-grid">
                                <?php $services = getAllServices(); foreach ($services as $service): ?>
                                    <div class="service-option"
                                         data-service-name="<?php echo htmlspecialchars($service['name']); ?>"
                                         data-service-slug="<?php echo htmlspecialchars($service['slug']); ?>">
                                        <?php echo htmlspecialchars($service['name']); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="filter-footer">
                    <div class="filter-actions">
                        <button type="button" class="btn btn-clear-filter" id="clearFilters"><i class="fas fa-times"></i>Clear All</button>
                        <button type="button" class="btn btn-apply-filter" id="applyFilters"><i class="fas fa-check"></i>Apply Filters</button>
                    </div>
                </div>
            </div>
       </div>
    </div>
    <div id="main-content" class="main-content">
        <div class="search-section">
            <div class="container">
                <div class="search-header">
                    <h1>Find a Shop</h1>
                    <p class="text-muted">Discover the best shops in your area</p>
                </div>
                <div class="main-search-area">
                    <div class="d-flex align-items-center gap-2">
                        <div style="position: relative; width: 100%;" class="search-form-wrapper">
                            <form method="GET" action="" class="flex-grow-1" id="searchForm">
                                <input type="text" name="search" id="searchQuery" class="form-control" placeholder="Search for services, shops, or address..." value="<?php echo htmlspecialchars($searchQuery); ?>" autocomplete="off">
                                <div class="search-controls">
                                    <button type="submit" class="btn-search" aria-label="Search"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                        <button type="button" class="btn-filter-icon" data-bs-toggle="modal" data-bs-target="#filterModal" aria-label="Open filters"><i class="fas fa-filter"></i></button>
                    </div>
                </div>
                <?php if (!empty($filterRating) || !empty($filterLocation) || !empty($filterServices)): ?>
                    <div class="active-filters justify-content-center">
                        <?php if (!empty($filterRating)): ?>
                            <div class="filter-tag"><i class="fas fa-star me-1"></i><?php echo htmlspecialchars($filterRating); ?>+ Stars<span class="remove-filter" onclick="removeFilter('rating')" title="Remove rating filter">×</span></div>
                        <?php endif; ?>
                        <?php if (!empty($filterLocation)): ?>
                            <div class="filter-tag"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($filterLocation); ?><span class="remove-filter" onclick="removeFilter('location')" title="Remove location filter">×</span></div>
                        <?php endif; ?>
                        <?php if (!empty($filterServices)): ?>
                            <div class="filter-tag"><i class="fas fa-tools me-1"></i><?php echo htmlspecialchars($filterServices); ?><span class="remove-filter" onclick="removeFilter('services')" title="Remove service filter">×</span></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="text-center mt-4">
                    <button class="btn btn-nearby fw-bold" id="findNearbyBtn" data-address="<?php echo htmlspecialchars($loggedInUserAddress); ?>">
                        <i class="fas fa-location-arrow me-2" style="color: #1a1a1a;"></i>Find Nearby Shops
                    </button>
                </div>
                <div id="userAddressDisplay">
                    <?php if (!empty($userAddress) && $showNearbyResults): ?>
                        <p class="text-muted text-center mt-3">Your location: <?php echo htmlspecialchars($userAddress); ?></p>
                    <?php endif; ?>
                </div>
                <div class="search-results" id="searchResultsContainer">
                    <?php if ($showNearbyResults): ?>
                        <div class="results-count" style="text-align: center;">
                            <?php if (!empty($nearbyShops)): ?>
                                Found <strong><?php echo count($nearbyShops); ?></strong> nearby shop(s) within <?php echo $searchRadius; ?>km
                            <?php else: ?>
                                <div class="no-results-container"><i class="fas fa-compass no-results-icon"></i><h4>No Nearby Shops Found</h4><p>We couldn't find any shops within a <?php echo $searchRadius; ?>km radius of your current location. Try searching for a specific service or place instead.</p></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($nearbyShops)): ?>
                            <div class="row justify-content-center">
                                <?php foreach ($nearbyShops as $shop):
                                    $shop_id = $shop['id'];
                                    $ratingData = getShopRating($shop);
                                    $topRated = false;
                                    $mostBooked = false;
                                    $topRatedQuery = "SELECT AVG(rating) as avg_rating FROM shop_ratings WHERE shop_id = ?";
                                    $stmt_badge = $conn->prepare($topRatedQuery);
                                    if ($stmt_badge) {
                                        $stmt_badge->bind_param("i", $shop_id);
                                        $stmt_badge->execute();
                                        $result_badge = $stmt_badge->get_result()->fetch_assoc();
                                        if ($result_badge && $result_badge['avg_rating'] >= 4.0) { $topRated = true; }
                                        $stmt_badge->close();
                                    }
                                    $mostBookedQuery = "SELECT COUNT(*) as total_completed FROM services_booking WHERE shop_id = ? AND booking_status = 'Completed'";
                                    $stmt_badge = $conn->prepare($mostBookedQuery);
                                    if ($stmt_badge) {
                                        $stmt_badge->bind_param("i", $shop_id);
                                        $stmt_badge->execute();
                                        $result_badge = $stmt_badge->get_result()->fetch_assoc();
                                        if ($result_badge && $result_badge['total_completed'] >= 10) { $mostBooked = true; }
                                        $stmt_badge->close();
                                    }

                                    $default_logo_url = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
                                    $logoPath = $default_logo_url;
                                    if (!empty($shop['shop_logo'])) {
                                        $base_directory = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
                                        $logo_file_path = $_SERVER['DOCUMENT_ROOT'] . $base_directory . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                                        if (file_exists($logo_file_path)) {
                                            $logoPath = BASE_URL . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                                        }
                                    }
                                    $shop_status = $shop['shop_status'] ?? 'open';
                                ?>
                                    <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
                                        <div class="shop-card">
                                            <div class="shop-card-content">
                                                <div class="shop-logo-container">
                                                    <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="<?php echo htmlspecialchars($shop['shop_name']); ?> Logo">
                                                    <div class="verified-badge-icon"><i class="fas fa-check"></i></div>
                                                </div>
                                                <div class="badge-container">
                                                    <?php if ($topRated): ?><span class="shop-badge top-rated"><i class="fas fa-star"></i> Top Rated</span><?php endif; ?>
                                                    <?php if ($mostBooked): ?><span class="shop-badge top-booking"><i class="fas fa-medal"></i> Most Booked</span><?php endif; ?>
                                                </div>
                                                <h5><?php echo htmlspecialchars($shop['shop_name']); ?></h5>
                                                <p><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($shop['shop_location']); ?></p>
                                                <p class="distance-tag"><i class="fas fa-road me-2"></i>Approx. <?php echo $shop['distance']; ?> km away</p>

                                                <?php if ($shop_status == 'temporarily_closed') : ?>
                                                    <div class="shop-status-badge temporarily-closed">
                                                        <i class="fas fa-exclamation-triangle"></i> Temporarily Closed
                                                    </div>
                                                <?php elseif ($shop_status == 'permanently_closed') : ?>
                                                    <div class="shop-status-badge permanently-closed">
                                                        <i class="fas fa-store-slash"></i> Permanently Closed
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                            <div class="rating-display">
                                                <?php if ($ratingData['rating_count'] > 0): ?>
                                                    <span class="rating-value"><?php echo number_format($ratingData['average_rating'], 1); ?></span>
                                                    <span class="stars">
                                                        <?php $filled_stars = round($ratingData['average_rating']); for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="<?php echo ($i <= $filled_stars) ? 'fas' : 'far'; ?> fa-star"></i>
                                                        <?php endfor; ?>
                                                    </span>
                                                    <span class="rating-count">(<?php echo $ratingData['rating_count']; ?>)</span>
                                                <?php else: ?>
                                                    <span>No Ratings Yet</span>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?php echo BASE_URL; ?>/account/shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>" class="btn-view">View Details</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif ($hasSearchCriteria): ?>
                        <div class="results-count" style="text-align: center;">
                            <?php if (!empty($searchResults)): ?>
                                Found <strong><?php echo count($searchResults); ?></strong> result(s)
                            <?php endif; ?>
                        </div>
                        <?php if (empty($searchResults)): ?>
                            <div class="no-results-container"><i class="fas fa-search no-results-icon"></i><h4>No Shops Found</h4><p>We couldn't find any shops matching your search and filter criteria. Try adjusting your filters or search query.</p></div>
                        <?php else: ?>
                            <div class="row justify-content-center">
                                <?php foreach ($searchResults as $shop):
                                    $shop_id = $shop['id'];
                                    $ratingData = getShopRating($shop);
                                    $topRated = false;
                                    $mostBooked = false;
                                    $topRatedQuery = "SELECT AVG(rating) as avg_rating FROM shop_ratings WHERE shop_id = ?";
                                    $stmt_badge = $conn->prepare($topRatedQuery);
                                    if ($stmt_badge) {
                                        $stmt_badge->bind_param("i", $shop_id);
                                        $stmt_badge->execute();
                                        $result_badge = $stmt_badge->get_result()->fetch_assoc();
                                        if ($result_badge && $result_badge['avg_rating'] >= 4.0) { $topRated = true; }
                                        $stmt_badge->close();
                                    }
                                    $mostBookedQuery = "SELECT COUNT(*) as total_completed FROM services_booking WHERE shop_id = ? AND booking_status = 'Completed'";
                                    $stmt_badge = $conn->prepare($mostBookedQuery);
                                    if ($stmt_badge) {
                                        $stmt_badge->bind_param("i", $shop_id);
                                        $stmt_badge->execute();
                                        $result_badge = $stmt_badge->get_result()->fetch_assoc();
                                        if ($result_badge && $result_badge['total_completed'] >= 10) { $mostBooked = true; }
                                        $stmt_badge->close();
                                    }

                                    $default_logo_url = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
                                    $logoPath = $default_logo_url;
                                    if (!empty($shop['shop_logo'])) {
                                        $base_directory = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
                                        $logo_file_path = $_SERVER['DOCUMENT_ROOT'] . $base_directory . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                                        if (file_exists($logo_file_path)) {
                                            $logoPath = BASE_URL . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                                        }
                                    }
                                    $shop_status = $shop['shop_status'] ?? 'open';
                                ?>
                                    <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
                                        <div class="shop-card">
                                            <div class="shop-card-content">
                                                <div class="shop-logo-container">
                                                    <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="<?php echo htmlspecialchars($shop['shop_name']); ?> Logo">
                                                    <div class="verified-badge-icon"><i class="fas fa-check"></i></div>
                                                </div>
                                                <div class="badge-container">
                                                    <?php if ($topRated): ?><span class="shop-badge top-rated"><i class="fas fa-star"></i> Top Rated</span><?php endif; ?>
                                                    <?php if ($mostBooked): ?><span class="shop-badge top-booking"><i class="fas fa-medal"></i> Most Booked</span><?php endif; ?>
                                                </div>
                                                <h5><?php echo htmlspecialchars($shop['shop_name']); ?></h5>
                                                <p><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($shop['shop_location']); ?></p>

                                                <?php if ($shop_status == 'temporarily_closed') : ?>
                                                    <div class="shop-status-badge temporarily-closed">
                                                        <i class="fas fa-exclamation-triangle"></i> Temporarily Closed
                                                    </div>
                                                <?php elseif ($shop_status == 'permanently_closed') : ?>
                                                    <div class="shop-status-badge permanently-closed">
                                                        <i class="fas fa-store-slash"></i> Permanently Closed
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                            <div class="rating-display">
                                                <?php if ($ratingData['rating_count'] > 0): ?>
                                                    <span class="rating-value"><?php echo number_format($ratingData['average_rating'], 1); ?></span>
                                                    <span class="stars">
                                                        <?php $filled_stars = round($ratingData['average_rating']); for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="<?php echo ($i <= $filled_stars) ? 'fas' : 'far'; ?> fa-star"></i>
                                                        <?php endfor; ?>
                                                    </span>
                                                    <span class="rating-count">(<?php echo $ratingData['rating_count']; ?>)</span>
                                                <?php else: ?>
                                                    <span>No Ratings Yet</span>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?php echo BASE_URL; ?>/account/shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>" class="btn-view">View Details</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="initial-view-prompt"><i class="fas fa-search"></i><h4>Ready to find what you need?</h4><p>Enter a service, shop name, or location in the search bar above,<br>or click the "Find Nearby Shops" button to explore options around you.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="geolocationSpinner" class="full-page-overlay" style="display: none;">
       <div class="loading-indicator">
            <div class="spinner"></div>
            <p>Finding nearby shops...</p>
        </div>
    </div>
    <?php include 'include/emergency-modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrTdXF4gDpNht4CGvQJ2GlehIQTOSrKV4&libraries=places"></script>
  
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/navbar.js"></script>
<script>
    function buildQueryUrlAndRedirect() {
        const baseUrl = '<?php echo rtrim(BASE_URL, '/'); ?>/account/search';
        const params = new URLSearchParams();

        const query = $('#searchQuery').val().trim();
        const selectedRating = $('.rating-option.active').data('rating') || '';
        const selectedLocation = $('.location-option.active').data('location') || '';
        const selectedServiceNode = $('.service-option.active');
        const selectedServiceSlug = selectedServiceNode.length > 0 ? selectedServiceNode.data('service-slug') : '';

        if (query) params.set('query', query);
        if (selectedServiceSlug) params.set('service', selectedServiceSlug);
        if (selectedLocation) params.set('location', selectedLocation);
        if (selectedRating) params.set('rating', selectedRating);

        const paramString = params.toString();
        window.location.href = paramString ? `${baseUrl}?${paramString}` : baseUrl;
    }

    function removeFilter(filterType) {
        const params = new URLSearchParams(window.location.search);

        if (filterType === 'services') {
            params.delete('service');
        } else {
            params.delete(filterType);
        }

        const baseUrl = '<?php echo rtrim(BASE_URL, '/'); ?>/account/search';
        const paramString = params.toString();
        window.location.href = paramString ? `${baseUrl}?${paramString}` : baseUrl;
    }

    function handleFindNearbyClick() {
        document.getElementById('geolocationSpinner').style.display = 'flex';

        if (!navigator.geolocation) {
            document.getElementById('geolocationSpinner').style.display = 'none';
            alert('Geolocation is not supported by your browser.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                storeUserLocation(position.coords.latitude, position.coords.longitude);
            },
            function(error) {
                document.getElementById('geolocationSpinner').style.display = 'none';
                let errorMessage = 'Unable to get your location. ';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Please allow location access to find nearby shops.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out. Please try again.';
                        break;
                    default:
                        errorMessage += 'Please ensure location services are enabled.';
                }
                alert(errorMessage);
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    }

    function storeUserLocation(latitude, longitude) {
        const formData = new FormData();
        formData.append('latitude', latitude);
        formData.append('longitude', longitude);

        fetch('<?php echo BASE_URL; ?>/account/backend/store_nearby_location.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '<?php echo rtrim(BASE_URL, '/'); ?>/account/search?nearby=true';
                } else {
                    throw new Error(data.message || 'Failed to store location');
                }
            })
            .catch(error => {
                document.getElementById('geolocationSpinner').style.display = 'none';
                console.error('Error storing location:', error);
                alert('Error storing your location: ' + error.message);
            });
    }

    function initializeFilters() {
        const params = new URLSearchParams(window.location.search);
        const currentRating = params.get('rating') || "<?php echo htmlspecialchars($filterRating); ?>";
        const currentLocation = params.get('location') || "<?php echo htmlspecialchars($filterLocation); ?>";
        const currentServiceSlug = params.get('service') || "<?php echo htmlspecialchars($serviceSlugForLookup); ?>";

        if (currentRating) {
            $(`.rating-option[data-rating="${currentRating}"]`).addClass('active');
        } else {
            $('.rating-option[data-rating=""]').addClass('active');
        }
        if (currentLocation) {
            $(`.location-option`).filter(function() {
                return $(this).data('location') === currentLocation;
            }).addClass('active');
        }
        if (currentServiceSlug) {
            $(`.service-option[data-service-slug="${currentServiceSlug}"]`).addClass('active');
        }
    }

    $(document).ready(function() {
        const autocompleteData = <?php echo json_encode($autocomplete_data); ?>;
        const searchInput = $('#searchQuery');
        const suggestionsContainer = $('.autocomplete-suggestions');

        initializeFilters();

        searchInput.on('input', function() {
            const query = $(this).val().toLowerCase();
            const originalQuery = $(this).val();
            suggestionsContainer.empty().hide();
            if (query.length < 2) return;

            const filteredItems = autocompleteData.filter(item => item.label.toLowerCase().includes(query));

            if (filteredItems.length > 0) {
                filteredItems.sort((a, b) => a.label.localeCompare(b.label));

                filteredItems.slice(0, 10).forEach(item => {
                    const regex = new RegExp(`(${query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
                    const itemHtml = `<div>${item.label.replace(regex, '<strong>$1</strong>')}</div>`;
                    const suggestionItem = $(itemHtml);

                    suggestionItem.on('click', () => {
                        const searchUrl = '<?php echo rtrim(BASE_URL, '/'); ?>/account/search';
                        if (item.type === 'service') {
                            window.location.href = `${searchUrl}?service=${item.slug}`;
                        } else if (item.type === 'shop') {
                            window.location.href = `<?php echo rtrim(BASE_URL, '/'); ?>/account/shop/${item.slug}`;
                        } else if (item.type === 'location') {
                            window.location.href = `${searchUrl}?location=${encodeURIComponent(item.label)}`;
                        }
                    });
                    suggestionsContainer.append(suggestionItem);
                });
                suggestionsContainer.show();
            } else {
                const noResultHtml = `<div class="autocomplete-no-result">No results for "<strong></strong>"</div>`;
                const noResultItem = $(noResultHtml);
                noResultItem.find('strong').text(originalQuery);
                suggestionsContainer.append(noResultItem);
                suggestionsContainer.show();
            }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-form-wrapper').length) {
                suggestionsContainer.hide();
            }
        });

        $('#applyFilters').click(buildQueryUrlAndRedirect);

        $('#searchForm').submit(function(e) {
            e.preventDefault();
            buildQueryUrlAndRedirect();
        });

        $('#findNearbyBtn').click(handleFindNearbyClick);

        $('#clearFilters').click(function() {
            window.location.href = '<?php echo rtrim(BASE_URL, '/'); ?>/account/search';
        });

        $('.rating-option').click(function() {
            $('.rating-option').removeClass('active');
            $(this).addClass('active');
        });

        $('.location-option').click(function() {
            $('.location-option').removeClass('active');
            $(this).addClass('active');
        });

        $('.service-option').click(function() {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            } else {
                $('.service-option').removeClass('active');
                $(this).addClass('active');
            }
        });
    });
</script>
</body>
</html>