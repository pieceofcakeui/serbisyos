<?php
include 'functions/base-path.php';
include 'functions/db_connection.php';
include 'account/backend/encrypt_loc.php';

function slugify($text)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
}

$autocomplete_data = [];
$services_sql = "SELECT name FROM services ORDER BY name ASC";
if ($services_result = $conn->query($services_sql)) {
    while ($row = $services_result->fetch_assoc()) {
        $autocomplete_data[] = ['label' => $row['name'], 'slug' => slugify($row['name']), 'type' => 'service'];
    }
}
$shops_sql = "SELECT shop_name, shop_slug FROM shop_applications WHERE status = 'Approved' ORDER BY shop_name ASC";
if ($shops_result = $conn->query($shops_sql)) {
    while ($row = $shops_result->fetch_assoc()) {
        $autocomplete_data[] = ['label' => $row['shop_name'], 'slug' => $row['shop_slug'], 'type' => 'shop'];
    }
}
$temp_locations = [];
$locations_sql = "SELECT DISTINCT town_city, barangay, shop_location FROM shop_applications WHERE status = 'Approved'";
if ($locations_result = $conn->query($locations_sql)) {
    while ($row = $locations_result->fetch_assoc()) {
        if (!empty($row['town_city']) && !in_array($row['town_city'], $temp_locations))
            $temp_locations[] = $row['town_city'];
        if (!empty($row['barangay']) && !in_array($row['barangay'], $temp_locations))
            $temp_locations[] = $row['barangay'];
        if (!empty($row['shop_location']) && !in_array($row['shop_location'], $temp_locations))
            $temp_locations[] = $row['shop_location'];
    }
}
sort($temp_locations);
foreach ($temp_locations as $location) {
    $autocomplete_data[] = ['label' => $location, 'slug' => slugify($location), 'type' => 'location'];
}

$searchResults = [];
$searchQuery = "";
$filterRating = "";
$filterLocation = "";
$filterServices = "";
$serviceSlugForLookup = "";
$hasSearchCriteria = false;

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $searchQuery = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
    $serviceSlugForLookup = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : '';
    $filterLocation = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '';
    $filterRating = isset($_GET['rating']) ? htmlspecialchars($_GET['rating']) : '';

    if (!empty($serviceSlugForLookup)) {
        $stmt_slug = $conn->prepare("SELECT name FROM services WHERE slug = ?");
        if ($stmt_slug) {
            $stmt_slug->bind_param("s", $serviceSlugForLookup);
            $stmt_slug->execute();
            if ($row_slug = $stmt_slug->get_result()->fetch_assoc()) {
                $filterServices = $row_slug['name'];
            }
            $stmt_slug->close();
        }
    }


    $hasSearchCriteria = !empty($searchQuery) || !empty($filterRating) || !empty($filterLocation) || !empty($filterServices);

    if ($hasSearchCriteria) {
        $sql = "SELECT sa.id, sa.shop_name, sa.shop_slug, sa.shop_location, sa.shop_logo, sa.town_city, sa.province, sa.shop_status,
                                COALESCE(AVG(sr.rating), 0) as average_rating, COUNT(DISTINCT sr.id) as rating_count
                          FROM shop_applications sa 
                          LEFT JOIN shop_ratings sr ON sa.id = sr.shop_id
                          LEFT JOIN shop_services ss ON sa.id = ss.application_id
                          LEFT JOIN services s ON ss.service_id = s.id";
        $where = [];
        $params = [];
        $types = "";
        $where[] = "sa.status = 'Approved'";

        if (!empty($searchQuery)) {
            $where[] = "(sa.shop_name LIKE ? OR s.name LIKE ? OR sa.shop_location LIKE ?)";
            $searchParam = "%" . $searchQuery . "%";
            array_push($params, $searchParam, $searchParam, $searchParam);
            $types .= "sss";
        }
        if (!empty($filterLocation)) {
            $where[] = "(sa.town_city = ? OR sa.barangay = ? OR sa.shop_location LIKE ?)";
            $locationLikeParam = "%" . $filterLocation . "%";
            array_push($params, $filterLocation, $filterLocation, $locationLikeParam);
            $types .= "sss";
        }
        if (!empty($filterServices)) {
            $where[] = "s.name = ?";
            $params[] = $filterServices;
            $types .= "s";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " GROUP BY sa.id";
        if (!empty($filterRating)) {
            $sql .= " HAVING average_rating >= ?";
            $params[] = (float) $filterRating;
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

function getIloiloLocations()
{
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
function getAllServices()
{
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
function getShopRating($shop_data)
{
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
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/account/style.css">
    <style>
        .autocomplete-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-height: 350px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
        }

        .autocomplete-suggestions div {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .autocomplete-suggestions div:hover {
            background-color: #f5f5f5;
        }

        .autocomplete-suggestions div:last-child {
            border-bottom: none;
        }
        .autocomplete-no-result { 
            padding: 12px 16px; 
            color: #6c757d; 
            cursor: default; 
        }
        .autocomplete-suggestions div.autocomplete-no-result:hover { 
            background-color: #ffffff; 
        }
    </style>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 70%, #e4e8f0 100%);
            font-family: 'Montserrat', sans-serif;
        }

        .initial-view-prompt {
            text-align: center;
            padding: 70px 40px;
            color: #888;
            border: 2px dashed #e0e0e0;
            border-radius: 15px;
            margin-top: 40px;
            background-color: #fcfcfc;
        }

        .initial-view-prompt i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #ccc;
        }

        .initial-view-prompt h4 {
            color: #555;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .initial-view-prompt p {
            font-size: 1rem;
            line-height: 1.6;
            color: #777;
        }

        .search-section {
            padding: 80px 0;
        }

        .search-header {
            text-align: center;
            margin-bottom: 40px;
            margin-top: 0;
        }

        .search-header h1 {
            font-weight: 800;
            font-size: 3rem;
            color: #1a1a1a;
        }

        .main-search-area {
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        .search-form-wrapper {
            position: relative;
            flex-grow: 1;
        }

        .search-form-wrapper .form-control {
            padding: 1rem 4.5rem 1rem 1.5rem;
            border-radius: 50px;
            border: 2px solid #e2e8f0;
            height: 58px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .search-form-wrapper .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
        }

        .search-controls {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .btn-filter-icon {
            background-color: transparent;
            border: none;
            color: #343a40;
            font-size: 1.5rem;
            padding: 0.5rem;
            border-radius: 50%;
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        .btn-filter-icon:hover {
            color: #ffc107;
            background-color: #f8f9fa;
        }

        .btn-search {
            background: #ffc107;
            color: #1a1a1a;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-search:hover {
            background: #ffca2c;
            transform: scale(1.05);
        }

        .shop-results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
        }

        .shop-card {
            background: white;
            border-radius: 15px;
            text-align: center;
            padding: 1.5rem;
            border: 1px solid #E5E7EB;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .shop-card-content {
            flex-grow: 1;
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
            color: #fff;
            font-size: 12px;
        }

        .shop-card h5 {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #1a1a1a;
        }

        .shop-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .shop-card .btn-view {
            background: #1a1a1a;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .rating-display {
            margin-bottom: 1rem;
            color: #6c757d;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 24px;
        }

        .rating-display .stars {
            color: #ffc107;
            font-size: 1.1rem;
        }

        .rating-display .rating-value {
            font-weight: 600;
            color: #343a40;
            margin-right: 0.25rem;
        }

        .rating-display .rating-count {
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }

        .no-results {
            text-align: center;
            padding: 3rem 2rem;
            background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin: 2rem 0;
        }

        .no-results i {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 1.5rem;
        }

        .no-results h4 {
            color: #1a1a1a;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .no-results p {
            color: #666;
            margin-bottom: 1.5rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-nearby {
            background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
            color: #1a1a1a;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .btn-nearby:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }

        .results-count {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            margin-top: 20px;
        }

        .results-count strong {
            color: #ffc107;
        }

        .filter-modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .filter-modal .modal-dialog {
            margin: auto;
            max-height: calc(100vh - 2rem);
            width: 100%;
            position: relative;
        }

        .filter-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .filter-modal .modal-header {
            color: #1a1a1a;
            border-radius: 20px 20px 0 0;
            border: none;
            flex-shrink: 0;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #ddd;
        }

        .filter-modal .modal-title {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .filter-modal .modal-body {
            padding: 1.5rem;
        }

        .filter-section {
            margin-bottom: 2.5rem;
        }

        .filter-section:last-child {
            margin-bottom: 1rem;
        }

        .filter-section h6 {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.2rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-section h6 i {
            color: #ffc107;
            font-size: 1rem;
        }

        .rating-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .rating-option {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            padding: 10px 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .rating-option:hover {
            border-color: #ffc107;
            background: #fffbf0;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
        }

        .rating-option.active {
            background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
            border-color: #ffc107;
            color: #1a1a1a;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .rating-option .stars {
            color: #ffc107;
            font-size: 1rem;
        }

        .rating-option.active .stars {
            color: #1a1a1a;
        }

        .location-grid,
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            max-height: 280px;
            overflow-y: auto;
            border: none;
            border-radius: 15px;
            padding: 20px;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .location-grid::-webkit-scrollbar,
        .services-grid::-webkit-scrollbar {
            width: 6px;
        }

        .location-grid::-webkit-scrollbar-thumb,
        .services-grid::-webkit-scrollbar-thumb {
            background: #ffc107;
            border-radius: 3px;
        }

        .location-option,
        .service-option {
            border-radius: 12px;
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .location-option:hover,
        .service-option:hover {
            border-color: #ffc107;
            background: #fffbf0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
        }

        .location-option.active,
        .service-option.active {
            background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
            border-color: #ffc107;
            color: #1a1a1a;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            transform: translateY(-1px);
        }

        .filter-footer {
            background: white;
            padding: 1rem;
            border-top: 1px solid #e2e8f0;
            border-radius: 0 0 20px 20px;
            z-index: 10;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            width: 100%;
        }

        .btn-apply-filter {
            background: linear-gradient(135deg, #1a1a1a 0%, #333333 100%);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(26, 26, 26, 0.25);
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 180px;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .btn-apply-filter:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-apply-filter:hover:before {
            left: 0;
        }

        .btn-apply-filter:hover {
            color: #1a1a1a;
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 193, 7, 0.4);
        }

        .btn-apply-filter i {
            font-size: 1.1rem;
        }

        .btn-clear-filter {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            color: #64748b;
            border: 2px solid #e2e8f0;
            padding: 10px 30px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 180px;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .btn-clear-filter:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-clear-filter:hover:before {
            left: 0;
        }

        .btn-clear-filter:hover {
            color: white;
            border-color: #ef4444;
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(239, 68, 68, 0.4);
        }

        .btn-clear-filter i {
            font-size: 1.1rem;
        }

        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 1rem;
        }

        .filter-tag {
            background: #f1f1f1;
            color: #1a1a1a;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .filter-tag .remove-filter {
            cursor: pointer;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .results-count {
                color: #666;
                font-size: 1rem;
                margin-bottom: 1.5rem;
                margin-top: 20px;
            }

            .results-count strong {
                font-size: 0.90rem;
            }

            .rating-filter {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: repeat(3, auto);
                gap: 10px;
            }

            .rating-option {
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 8px 12px;
                font-size: 0.85rem;
            }

            .rating-option .stars {
                display: block;
                margin-bottom: 4px;
            }

            .search-header {
                margin-top: 40px;
            }

            .search-header h1 {
                font-size: 2.2rem;
            }

            .search-form-wrapper .form-control {
                padding-right: 4.5rem;
            }

           .location-grid,
           .services-grid {
                grid-template-columns: repeat(2, 1fr);
                padding: 15px;
            }
        
            .location-option,
            .service-option {
                padding: 10px 8px;
                font-size: 0.85rem;
            }
            .no-results {
                padding: 2rem 1rem;
            }

            .no-results i {
                font-size: 3rem;
            }

            .filter-modal .modal-header,
            .filter-modal .modal-body,
            .filter-footer {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .filter-actions {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .btn-apply-filter,
            .btn-clear-filter {
                width: 100%;
                min-width: unset;
            }
        }

        .full-page-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-indicator {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #ffc107;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .badge-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            margin-bottom: 8px;
        }

        .shop-badge {
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 0.70rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            white-space: nowrap;
        }

        .shop-badge.top-rated i,
        .shop-badge.top-booking i {
            font-size: 0.70rem !important;
        }

        .shop-badge.top-rated {
            background-color: #ffc107;
            color: #212529;
        }

        .shop-badge.top-booking {
            background-color: #00A3BF;
            color: white;
        }

        @media (max-width: 992px) {
            .booking-provider-section {
                margin-top: 0;
            }
        }

        @media (max-width: 480px) {
            .search-section {
                margin-top: 0;
            }
        }

        .no-results-container {
            text-align: center;
            padding: 60px 30px;
            margin-top: 40px;
            background-color: #f8f9fa;
            border: 1px dashed #e0e0e0;
            border-radius: 12px;
        }

        .no-results-icon {
            font-size: 4.5rem;
            color: #ced4da;
            margin-bottom: 25px;
        }

        .no-results-container h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }

        .no-results-container p {
            color: #6c757d;
            max-width: 450px;
            margin: 0 auto;
            line-height: 1.6;
        }
    </style>
  <style>
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
    <?php include 'offline-handler.php'; ?>
    <div class="search-section">
        <div class="container">
            <div class="search-header">
                <h1>Find a Shop</h1>
                <p class="text-muted">Discover the best shops in your area</p>
            </div>
            <div class="main-search-area">
                <div class="d-flex align-items-center gap-2">
                    <div style="position: relative; width: 100%;" class="search-form-wrapper">
                        <form class="flex-grow-1" id="searchForm">
                            <input type="text" name="search" id="searchQuery" class="form-control"
                                placeholder="Search for services, shops, or address..."
                                value="<?php echo htmlspecialchars($searchQuery); ?>" autocomplete="off">
                            <div class="search-controls">
                                <button type="submit" class="btn-search" aria-label="Search"><i
                                        class="fas fa-search"></i></button>
                            </div>
                        </form>
                        <div class="autocomplete-suggestions"></div>
                    </div>
                    <button type="button" class="btn-filter-icon" data-bs-toggle="modal" data-bs-target="#filterModal"
                        aria-label="Open filters"><i class="fas fa-filter"></i></button>
                </div>
            </div>
            <div class="search-results" id="searchResultsContainer">
                <?php if ($hasSearchCriteria): ?>
                    <div class="results-count">
                        <?php if (!empty($searchResults)): ?>Found <strong><?php echo count($searchResults); ?></strong>
                            result(s)<?php endif; ?>
                    </div>
                    <?php if (empty($searchResults)): ?>
                        <div class="no-results-container"><i class="fas fa-search no-results-icon"></i>
                            <h4>No Shops Found</h4>
                            <p>We couldn't find any shops matching your criteria.</p>
                        </div>
                    <?php else: ?>
                        <div class="shop-results-grid">
                            <?php foreach ($searchResults as $shop):
                                $topRated = ($shop['average_rating'] >= 4.0);
                                $mostBooked = false;
                                $default_logo_url = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
                                $logoPath = $default_logo_url;
                                if (!empty($shop['shop_logo'])) {
                                    $base_directory = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
                                    $logo_file_path = $_SERVER['DOCUMENT_ROOT'] . $base_directory . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                                    if (file_exists($logo_file_path)) {
                                        $logoPath = BASE_URL . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                                    }
                                }
                                $ratingData = getShopRating($shop);
                                $shop_status = $shop['shop_status'] ?? 'open';
                                ?>
                                <div class="shop-card">
                                    <div class="shop-card-content">
                                        <div class="shop-logo-container">
                                            <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="<?php echo htmlspecialchars($shop['shop_name']); ?> Logo">
                                            <div class="verified-badge-icon">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="badge-container"><?php if ($topRated): ?><span class="shop-badge top-rated"><i
                                                        class="fas fa-star"></i> Top Rated</span><?php endif; ?></div>
                                        <h5><?php echo htmlspecialchars($shop['shop_name']); ?></h5>
                                        <p><?php echo htmlspecialchars($shop['shop_location']); ?></p>
                                        
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
                                            <span
                                                class="rating-value"><?php echo number_format($ratingData['average_rating'], 1); ?></span>
                                            <span class="stars"><?php $filled_stars = round($ratingData['average_rating']);
                                            for ($i = 1; $i <= 5; $i++): ?><i
                                                    class="<?php echo ($i <= $filled_stars) ? 'fas' : 'far'; ?> fa-star"></i><?php endfor; ?></span>
                                            <span class="rating-count">(<?php echo $ratingData['rating_count']; ?>)</span>
                                        <?php else: ?><span>No Ratings Yet</span><?php endif; ?>
                                    </div>
                                    <a href="<?php echo BASE_URL; ?>/shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>"
                                        class="btn-view">View Details</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="initial-view-prompt"><i class="fas fa-search"></i>
                        <h4>Ready to find what you need?</h4>
                        <p>Enter a service, shop name, or location in the search bar above.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="modal fade filter-modal" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter me-2"></i>Filter
                    </h5>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="filter-section">
                            <h6><i class="fas fa-star"></i>Minimum Rating</h6>
                            <div class="rating-filter">
                                <div class="rating-option" data-rating=""><span>Any Rating</span></div>
                                <div class="rating-option" data-rating="1"><span class="stars">★</span><span>1+
                                        Stars</span></div>
                                <div class="rating-option" data-rating="2"><span class="stars">★★</span><span>2+
                                        Stars</span></div>
                                <div class="rating-option" data-rating="3"><span class="stars">★★★</span><span>3+
                                        Stars</span></div>
                                <div class="rating-option" data-rating="4"><span class="stars">★★★★</span><span>4+
                                        Stars</span></div>
                                <div class="rating-option" data-rating="5"><span class="stars">★★★★★</span><span>5
                                        Stars</span></div>
                            </div>
                        </div>
                        <div class="filter-section">
                            <h6><i class="fas fa-map-marker-alt"></i>Location in Iloilo Province</h6>
                            <div class="location-grid">
                                <?php
                                $locations = getIloiloLocations();
                                foreach ($locations as $location):
                                    ?>
                                    <div class="location-option" data-location="<?php echo htmlspecialchars($location); ?>">
                                        <?php echo htmlspecialchars($location); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="filter-section">
                            <h6><i class="fas fa-tools"></i>Services</h6>
                            <div class="services-grid">
                                <?php
                                $services = getAllServices();
                                foreach ($services as $service):
                                    ?>
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
                        <button type="button" class="btn btn-clear-filter" id="clearFilters">
                            <i class="fas fa-times"></i>Clear All
                        </button>
                        <button type="button" class="btn btn-apply-filter" id="applyFilters">
                            <i class="fas fa-check"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'include/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/script.js"></script>
    <script>
        $(document).ready(function () {
            const autocompleteData = <?php echo json_encode($autocomplete_data); ?>;
            const searchInput = $('#searchQuery');
            const suggestionsContainer = $('.autocomplete-suggestions');

            searchInput.on('input', function () {
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
                            const searchUrl = '<?php echo rtrim(BASE_URL, '/'); ?>/search';
                            if (item.type === 'service') {
                                window.location.href = `${searchUrl}?service=${item.slug}`;
                            } else if (item.type === 'location') {
                                window.location.href = `${searchUrl}?location=${encodeURIComponent(item.label)}`;
                            } else if (item.type === 'shop') {
                                window.location.href = `<?php echo rtrim(BASE_URL, '/'); ?>/shop/${item.slug}`;
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

            $(document).on('click', function (e) {
                if (!$(e.target).closest('.search-form-wrapper').length) {
                    suggestionsContainer.hide();
                }
            });

            $('#searchForm').submit(function (e) {
                e.preventDefault();
                const query = $('#searchQuery').val().trim();
                if (query) {
                    window.location.href = `<?php echo rtrim(BASE_URL, '/'); ?>/search?query=${encodeURIComponent(query)}`;
                } else {
                    window.location.href = '<?php echo rtrim(BASE_URL, '/'); ?>/search';
                }
            });

            $('#applyFilters').click(function () {
                const baseUrl = '<?php echo rtrim(BASE_URL, '/'); ?>/search';
                const params = new URLSearchParams();

                const isNoResultsPage = $('.no-results-container').length > 0;
                let query = $('#searchQuery').val().trim();

                if (isNoResultsPage) {
                    query = '';
                }

                const selectedRating = $('.rating-option.active').data('rating') || '';
                const selectedLocation = $('.location-option.active').data('location') || '';
                const selectedServiceNode = $('.service-option.active');
                const selectedServiceSlug = selectedServiceNode.length > 0 ? selectedServiceNode.data('service-slug') : '';

                if (query) params.set('q', query);
                if (selectedServiceSlug) params.set('service', selectedServiceSlug);
                if (selectedLocation) params.set('location', selectedLocation);
                if (selectedRating) params.set('rating', selectedRating);

                window.location.href = `${baseUrl}?${params.toString()}`;
            });

            $('.rating-option, .location-option').click(function () {
                $(this).siblings().removeClass('active');
                $(this).toggleClass('active');
            });

            $('.service-option').click(function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                } else {
                    $('.service-option').removeClass('active');
                    $(this).addClass('active');
                }
            });

            $('#clearFilters').click(function () {
                window.location.href = '<?php echo rtrim(BASE_URL, '/'); ?>/search';
            });

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
                    $(`.location-option[data-location="${currentLocation}"]`).addClass('active');
                }

                if (currentServiceSlug) {
                    $(`.service-option[data-service-slug="${currentServiceSlug}"]`).addClass('active');
                }
            }

            initializeFilters();
        });
    </script>
</body>

</html>