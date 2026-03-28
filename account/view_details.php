<?php
require_once '../functions/auth.php';
include 'backend/base-path.php';
include 'backend/db_connection.php';

$user_id = $_SESSION['user_id'];
$emergency = null;

$is_verified = 0;
$profile_type = '';
if (isset($_SESSION['user_id'])) {
    $userQuery = $conn->prepare("SELECT is_verified, profile_type FROM users WHERE id = ?");
    $userQuery->bind_param("i", $_SESSION['user_id']);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    if ($userRow = $userResult->fetch_assoc()) {
        $is_verified = $userRow['is_verified'];
        $profile_type = $userRow['profile_type'];
    }
    $userQuery->close();
}

if ($user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shopOwnerData = $shopResult->fetch_assoc();

    if ($shopOwnerData) {
        $shop_id_for_owner = $shopOwnerData['id'];

        $emergencyQuery = $conn->prepare("
            SELECT er.id, u.fullname, er.issue_description, er.created_at 
            FROM emergency_requests er
            JOIN users u ON er.user_id = u.id
            WHERE er.shop_id = ? AND er.seen_emergency_request = 0
            ORDER BY er.created_at DESC LIMIT 1
        ");
        $emergencyQuery->bind_param("i", $shop_id_for_owner);
        $emergencyQuery->execute();
        $emergencyResult = $emergencyQuery->get_result();
        $emergency = $emergencyResult->fetch_assoc();

        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
        }
    }
}

try {
    $shop_slug = isset($_GET['shop']) ? $_GET['shop'] : '';

    if (empty($shop_slug)) {
        header("Location: " . BASE_URL . "/home");
        exit;
    }

    $sql = "SELECT * FROM shop_applications WHERE shop_slug = ? AND status = 'Approved'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $shop_slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header("Location: " . BASE_URL . "/home");
        exit;
    }

    $shop = $result->fetch_assoc();
    $shop_id = $shop['id']; 

$is_owner_of_this_shop = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];

if (!$is_owner_of_this_shop) {
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
    $current_time = date("Y-m-d H:i:s");

    $check_sql = "SELECT id FROM shop_profile_visits WHERE shop_id = ? AND visitor_ip = ? AND visit_timestamp > NOW() - INTERVAL 5 MINUTE";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("is", $shop_id, $visitor_ip);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        $log_sql = "INSERT INTO shop_profile_visits (shop_id, user_id, visitor_ip) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);

        $visitor_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        $log_stmt->bind_param("iis", $shop_id, $visitor_user_id, $visitor_ip);
        $log_stmt->execute();
        $log_stmt->close();
    }
    $check_stmt->close();
}
    include 'backend/shops-badge.php';

    $shopname = $shop['shop_name'];
    $is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];

    $organized_services = [];
    $sql_services = "SELECT sc.name AS category_name, sc.icon AS category_icon, sc.display_order, ssc.name AS subcategory_name, s.name AS service_name FROM shop_services ss JOIN services s ON ss.service_id = s.id JOIN service_subcategories ssc ON s.subcategory_id = ssc.id JOIN service_categories sc ON ssc.category_id = sc.id WHERE ss.application_id = ? ORDER BY sc.display_order, sc.name, ssc.name, s.name";
    $stmt_services = $conn->prepare($sql_services);
    $stmt_services->bind_param("i", $shop_id);
    $stmt_services->execute();
    $result_services = $stmt_services->get_result();
    while ($service_row = $result_services->fetch_assoc()) {
        $category_name = $service_row['category_name'];
        $subcategory_name = $service_row['subcategory_name'];
        $service_name = $service_row['service_name'];
        if (!isset($organized_services[$category_name])) {
            $organized_services[$category_name] = ['icon' => $service_row['category_icon'], 'subcategories' => []];
        }
        if (!isset($organized_services[$category_name]['subcategories'][$subcategory_name])) {
            $organized_services[$category_name]['subcategories'][$subcategory_name] = [];
        }
        $organized_services[$category_name]['subcategories'][$subcategory_name][] = $service_name;
    }
    $stmt_services->close();


    $business_hours_display = '';

    $facebook = $shop['facebook'] ?? '';
    $instagram = $shop['instagram'] ?? '';
    $website = $shop['website'] ?? '';
    $google_map_link = $shop['google_map_link'] ?? '';
    $shop_location = $shop['shop_location'] ?? '';
    $brands_serviced = $shop['brands_serviced'] ?? '';
    $vehicle_types = $shop['vehicle_type'] ?? '';
    $vehicle_types_array = !empty($vehicle_types) ? explode(',', $vehicle_types) : [];

    $full_address = htmlspecialchars($shop['town_city'] . ', ' . $shop['province'] . ', ' . $shop['country'] . ', ' . $shop['postal_code']);

    $display_address = '';
    if (!empty($shop_location)) {
        $display_address = htmlspecialchars($shop_location);
    } else {
        $display_address = $full_address;
    }

    $combined_address = $shop['shop_name'] . ', ' . $display_address;
    $encoded_combined_address = urlencode($combined_address);

    if ($shop['open_24_7']) {
        $business_hours_display = "Open 24/7";
    } elseif (!empty($shop['opening_time']) && !empty($shop['closing_time'])) {
        $opening_time = date("g:i A", strtotime($shop['opening_time']));
        $closing_time = date("g:i A", strtotime($shop['closing_time']));
        $days_string = '';
        if (!empty($shop['days_open'])) {
            $days_open = explode(',', $shop['days_open']);
            $formatted_days = array_map(function ($day) {
                return ucfirst(trim($day));
            }, $days_open);
            $days_string = ' (' . implode(', ', $formatted_days) . ')';
        }
        $business_hours_display = "{$opening_time} - {$closing_time}{$days_string}";
    }

    $reviews_sql = "SELECT sr.*, u.fullname, u.profile_picture
                                        FROM shop_ratings sr
                                        JOIN users u ON sr.user_id = u.id
                                        WHERE sr.shop_id = ?
                                        ORDER BY sr.created_at DESC";
    $stmt = $conn->prepare($reviews_sql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $reviews_result = $stmt->get_result();
    $total_reviews = $reviews_result->num_rows;

    $avg_sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews
                FROM shop_ratings
                WHERE shop_id = ?";
    $stmt = $conn->prepare($avg_sql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $avg_result = $stmt->get_result();

    $average_rating = "0.0";
    if ($avg_result->num_rows > 0) {
        $rating_data = $avg_result->fetch_assoc();
        $average_rating = $rating_data['average_rating'] ? number_format($rating_data['average_rating'], 1) : "0.0";
        $total_reviews = $rating_data['total_reviews'] ? $rating_data['total_reviews'] : 0;
    }

    $rating_distribution = [];
    $rating_distribution_sql = "SELECT rating, COUNT(*) AS count
                                        FROM shop_ratings
                                        WHERE shop_id = ?
                                        GROUP BY rating
                                        ORDER BY rating DESC";
    $stmt = $conn->prepare($rating_distribution_sql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $rating_distribution_result = $stmt->get_result();
    while ($row = $rating_distribution_result->fetch_assoc()) {
        $rating_distribution[$row['rating']] = $row['count'];
    }

    $services_offered = explode(',', $shop['services_offered']);
    $brands_serviced_array = !empty($brands_serviced) ? explode(',', $brands_serviced) : [];

    $gallery_images_php = [];
    $gallery_images_json = $shop['shop_gallery_images'] ?? '[]';
    $image_filenames = json_decode($gallery_images_json, true);

 if (json_last_error() === JSON_ERROR_NONE && is_array($image_filenames)) {
        foreach ($image_filenames as $filename) {
            $gallery_images_php[] = BASE_URL . '/account/' . $filename;
        }
    
    }
} catch (Exception $e) {
    error_log("Error processing shop ID: " . $e->getMessage());
    header("Location: " . BASE_URL . "/home");
    exit;
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title> <?php echo htmlspecialchars($shop['shop_name']); ?></title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/img/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/img/favicon.png">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/view_details.css">
    <style>
        .view-shop-profile-image-wrapper {
        position: relative;
        width: 130px;
        height: 130px;
        margin-bottom: 18px;
        overflow: visible; 
    }

    .view-shop-shop-logo-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #ddd;
    }
        .verified-badge-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 30px;
            height: 30px;
            background-color: #1d9bf0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            z-index: 2;
        }
        .verified-badge-icon .fas.fa-check {
            color: #fff;
            font-size: 14px;
        }
        .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-button:not(.collapsed) {
            color: #212529;
            background-color: #ffffff;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
        }

        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }

        .services-grid .accordion,
        .services-grid .accordion-item {
            height: 100%;
        }

        .badge-container {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 5px;
            z-index: 2;
        }

        .shop-badge {
            padding: 0 5px;
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
        .shop-status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
    margin-top: 10px;
    margin-bottom: 5px;
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
    margin-right: 5px;
}
    </style>
</head>

<body>

    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <main class="view-shop-container" role="main" aria-label="Listing detail and location details">
            <section class="view-shop-profile-card" aria-label="Business profile and contact details">
                <div class="view-shop-profile-image-wrapper" aria-hidden="true">
                    <?php
                    $default_logo_url = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
                    $final_logo_url = $default_logo_url;
                    if (!empty($shop['shop_logo'])) {
                        $base_directory = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
                        $logo_file_path = $_SERVER['DOCUMENT_ROOT'] . $base_directory . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                        if (file_exists($logo_file_path)) {
                            $final_logo_url = BASE_URL . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                        }
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($final_logo_url); ?>" alt="Shop cover" class="view-shop-shop-logo-img">
                    <div class="verified-badge-icon">
                       <i class="fas fa-check"></i>
                    </div>
                </div>
                <?php
                $user_id = $_SESSION['user_id'] ?? null;
                ?>

                <?php if ($topRated || $mostBooked): ?>
                    <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                        <?php if ($topRated): ?>
                            <div class="shop-badge top-rated" data-bs-toggle="tooltip" title="Top Rated">
                                <i class="fas fa-star" style="font-size: 1rem;"></i> Top Rated
                            </div>
                        <?php endif; ?>
                        <?php if ($mostBooked): ?>
                            <div class="shop-badge top-booking" data-bs-toggle="tooltip" title="Most Booked">
                                <i class="fas fa-calendar-check" style="font-size: 1rem;"></i> Most Booked
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

              <h1 class="view-shop-profile-title" id="profileTitle"><?php echo htmlspecialchars($shop['shop_name']); ?></h1>

                <?php
                $shop_status = $shop['shop_status'] ?? 'open';

                if ($shop_status == 'temporarily_closed') :
                ?>
                    <div class="shop-status-badge temporarily-closed">
                        <i class="fas fa-exclamation-triangle"></i> Temporarily Closed
                    </div>
                <?php elseif ($shop_status == 'permanently_closed') : ?>
                    <div class="shop-status-badge permanently-closed">
                        <i class="fas fa-store-slash"></i> Permanently Closed
                    </div>
                <?php endif; ?>

                <p class="view-shop-profile-subtitle" id="profileAddress" aria-describedby="profileTitle">
                    <i class="fas fa-map-marker-alt" style=" font-size: 14px;"></i>
                    <?php echo $display_address; ?>
                </p>

                <?php if (!empty($facebook) || !empty($instagram) || !empty($website)): ?>
                    <div class="view-shop-social-media-links text-center">
                        <?php if (!empty($instagram)): ?>
                            <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank" class="social-link">
                                <i class="fab fa-instagram" style="color: #E1306C;"></i> Instagram
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($facebook)): ?>
                            <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank" class="social-link">
                                <i class="fab fa-facebook" style="color: #1877F2;"></i> Facebook
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($website)): ?>
                            <a href="<?php echo htmlspecialchars($website); ?>" target="_blank" class="social-link">
                                <i class="fas fa-globe" style="color: #0066CC;"></i> Website
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty(trim($shop['description'] ?? ''))) { ?>
                    <div class="view-shop-shop-description">
                        <p><?php echo nl2br(htmlspecialchars($shop['description'])); ?></p>
                    </div>
                <?php } ?>

                <div class="view-shop-action-container">
                    <div class="view-shop-action-icons-container">
                        <?php

                        $is_saved = false;
                        if (isset($_SESSION['user_id'])) {
                            $user_id = $_SESSION['user_id'];
                            $current_shop_id = $shop['id'];

                            $check_sql = "SELECT id FROM save_shops WHERE user_id = ? AND shop_id = ?";
                            $check_stmt = $conn->prepare($check_sql);
                            $check_stmt->bind_param("ii", $user_id, $current_shop_id);
                            $check_stmt->execute();
                            $result = $check_stmt->get_result();

                            $is_saved = $result->num_rows > 0;
                        }
                        ?>

                        <div class="view-shop-save-shop-container" title="Favorites" onclick="toggleSaveShop(<?php echo $shop['id']; ?>)" data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>" data-saved="<?php echo $is_saved ? 'true' : 'false'; ?>">
                            <div class="view-shop-save-icon-wrapper <?php echo $is_saved ? 'saved' : ''; ?>">
                                <i class="bi bi-bookmark" id="save-icon-<?php echo $shop['id']; ?>" style="color:#666;"></i>
                            </div>

                        </div>
                        <div class="view-shop-message-shop-icon" title="Message" onclick="toggleChat(<?php echo $shop['user_id']; ?>)">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                         <?php if ($shop_status == 'open'): ?>
                        <div class="view-shop-report-shop-icon" title="Report" onclick="showReportModal(<?php echo $shop['id']; ?>)">
                            <i class="bi bi-flag"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php
                    $is_any_shop_owner = false;
                    if (isset($_SESSION['user_id'])) {
                        $profileCheckStmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ? LIMIT 1");
                        if ($profileCheckStmt) {
                            $profileCheckStmt->bind_param("i", $_SESSION['user_id']);
                            $profileCheckStmt->execute();
                            $profileResult = $profileCheckStmt->get_result();
                            if ($profileResult->num_rows > 0) {
                                $user = $profileResult->fetch_assoc();
                                $is_any_shop_owner = ($user['profile_type'] === 'owner');
                            }
                            $profileCheckStmt->close();
                        }
                    }
                    ?>
                    
                   <?php if (($shop['show_book_now'] || $shop['show_emergency']) && ($shop['shop_status'] ?? 'open') == 'open'): ?>
                    <div class="view-shop-action-buttons-container">
                        <?php if ($shop['show_book_now']): ?>
                            <?php
                            $is_owner_of_this_shop = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];
                            ?>
                            <?php if ($is_owner_of_this_shop): ?>
                                <button class="btn btn-warning view-shop-book-now-btn text-white" style="font-weight: 500;" onclick="showOwnerRestrictionModal('book')">
                                     Book Now
                                </button>
                            <?php elseif ($is_any_shop_owner): ?>
                                <button class="btn btn-warning view-shop-book-now-btn text-white" style="font-weight: 500;" onclick="showGeneralOwnerRestrictionModal('book')">
                                   Book Now
                                </button>
                            <?php else: ?>
                                <a id="userBookNowBtn" href="<?php echo BASE_URL; ?>/account/book-now?shop=<?php echo htmlspecialchars($shop['shop_slug']); ?>" class="btn btn-warning view-shop-book-now-btn text-white" style="font-weight: 500;">
                                    Book Now
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>


                        <?php if ($shop['show_emergency']): ?>
                            <?php
                            $is_owner_of_this_shop = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];
                            ?>
                            <?php if ($is_owner_of_this_shop): ?>
                                <button class="btn btn-danger view-shop-emergency-request-btn" style="font-weight: 500;" onclick="showOwnerRestrictionModal('emergency')">
                                    Emergency
                                </button>
                            <?php elseif ($is_any_shop_owner): ?>
                                <button class="btn btn-danger view-shop-emergency-request-btn" style="font-weight: 500;" onclick="showGeneralOwnerRestrictionModal('emergency')">
                                   Emergency
                                </button>
                            <?php else: ?>
                                <?php
                                $emergency_sql = "SELECT emergency_hours FROM shop_emergency_config WHERE shop_id = ?";
                                $emergency_stmt = $conn->prepare($emergency_sql);
                                $emergency_stmt->bind_param("i", $shop['id']);
                                $emergency_stmt->execute();
                                $emergency_result = $emergency_stmt->get_result();
                                $emergency_hours_data = $emergency_result->fetch_assoc();
                                $emergency_hours = $emergency_hours_data['emergency_hours'] ?? '[]';
                                $emergency_hours_array = json_decode($emergency_hours, true);
                                $is_emergency_closed = true;

                                date_default_timezone_set('Asia/Manila');
                                $current_day = date('l');
                                $current_time = date('H:i');

                                if (!empty($emergency_hours_array)) {
                                    foreach ($emergency_hours_array as $time_slot) {
                                        $parts = explode(', ', $time_slot, 2);
                                        if (count($parts) === 2) {
                                            $day = trim($parts[0]);
                                            $time_range = trim($parts[1]);
                                            if (strcasecmp($day, $current_day) === 0) {
                                                $times = explode(' - ', $time_range, 2);
                                                if (count($times) === 2) {
                                                    $start_time = trim($times[0]);
                                                    $end_time = trim($times[1]);
                                                    $start_time_24 = date('H:i', strtotime($start_time));
                                                    $end_time_24 = date('H:i', strtotime($end_time));
                                                    if ($current_time >= $start_time_24 && $current_time <= $end_time_24) {
                                                        $is_emergency_closed = false;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                                <?php if ($is_emergency_closed): ?>
                                    <button id="userEmergencyBtn" style="font-weight: 500;" class="btn btn-danger view-shop-emergency-request-btn" onclick="showEmergencyClosedModal()">
                                        Emergency
                                    </button>
                                <?php else: ?>
                                    <a id="userEmergencyBtn" href="<?php echo BASE_URL; ?>/account/emergency-requests?shop=<?php echo htmlspecialchars($shop['shop_slug']); ?>" class="btn btn-danger view-shop-emergency-request-btn" style="font-weight: 500;">
                                       Emergency
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>


                    </div>
                        <?php endif; ?>
                </div>
                <nav role="tablist" class="view-shop-tabs" aria-label="Profile tabs navigation">
                    <button class="view-shop-tab-button active" aria-selected="true" aria-controls="contactDetails" id="tabContact" role="tab" tabindex="0">Overview</button>
                    <button class="view-shop-tab-button" aria-selected="false" aria-controls="reviewsTab" id="tabReviews" role="tab" tabindex="-1">Reviews (<?php echo $total_reviews; ?>)</button>
                </nav>
                <div class="view-shop-tab-panels">
                    <section id="contactAndBusinessInfo" role="tabpanel" aria-labelledby="tabContact">
                        <div class="view-shop-contact-details-group">

                            <h5 class="mb-2 fw-semibold">Contact Information</h5>
                            <p><i class="fas fa-phone" style="color: #000000;"></i> <?= htmlspecialchars($shop['phone']); ?></p>
                            <p><i class="fas fa-envelope" style="color: #000000;"></i> <?= htmlspecialchars($shop['email']); ?></p>

                            <hr class="my-3" style="border-top: 1px solid #ccc;">

                            <h5 class="mb-2 fw-semibold" style="margin-top: -20px;">Business Information</h5>
                            <p><i class="fas fa-certificate"></i> <?= htmlspecialchars($shop['years_operation']); ?> Years in Operation</p>

                            <?php
                            if (!empty($shop['opening_time_am']) && !empty($shop['closing_time_am']) && !empty($shop['days_open'])) {
                                date_default_timezone_set('Asia/Manila');

                                $days_list = array_map('trim', explode(',', $shop['days_open']));
                                $schedule = [];

                                foreach ($days_list as $day) {
                                    $normalized_day = ucfirst(strtolower($day));
                                    if (!empty($normalized_day)) {
                                        $schedule[$normalized_day] = [
                                            'open_am'  => $shop['opening_time_am'],
                                            'close_am' => $shop['closing_time_am'],
                                            'open_pm'  => $shop['opening_time_pm'] ?? null,
                                            'close_pm' => $shop['closing_time_pm'] ?? null,
                                        ];
                                    }
                                }

                                $current_day = date('l');
                                $current_time_str = date('H:i');
                                $is_open = false;

                                if (isset($schedule[$current_day])) {
                                    $todays_schedule = $schedule[$current_day];
                                    $current_timestamp = strtotime($current_time_str);

                                    $open_am_ts = strtotime($todays_schedule['open_am']);
                                    $close_am_ts = strtotime($todays_schedule['close_am']);
                                    if ($current_timestamp >= $open_am_ts && $current_timestamp <= $close_am_ts) {
                                        $is_open = true;
                                    }

                                    if (!$is_open && !empty($todays_schedule['open_pm'])) {
                                        $open_pm_ts = strtotime($todays_schedule['open_pm']);
                                        $close_pm_ts = strtotime($todays_schedule['close_pm']);
                                        if ($current_timestamp >= $open_pm_ts && $current_timestamp <= $close_pm_ts) {
                                            $is_open = true;
                                        }
                                    }
                                }

                                $current_status = $is_open ? 'Open' : 'Closed';
                                $status_class = $is_open ? 'bg-success' : 'bg-danger';
                            ?>
                            <?php if ($shop_status == 'open'): ?>
                            <div class="d-flex align-items-baseline flex-nowrap">
                            <i class="fas fa-clock me-1"></i>
                            <span class="badge ms-2 <?= $status_class ?>">
                            <?= $current_status ?>
                            </span>
                            <button class="view-schedule btn btn-sm btn-link p-0 text-decoration-none ms-2" type="button" data-bs-toggle="modal" data-bs-target="#scheduleModal" aria-label="View Full Schedule">
                            View Schedule
                            </button>
                            </div>
                            <?php endif; ?>
                            
                            <?php
                            }
                            ?>
                        </div>
                    </section>

                    <section id="reviewsTab" role="tabpanel" aria-labelledby="tabReviews" tabindex="0" aria-hidden="true" hidden>
                        <?php if ($shop_status == 'open'): ?>
                    <div class="view-shop-write-review-btn-container">
                        <button id="writeReviewBtn" type="button" class="btn view-shop-write-review-btn" data-shop-name="<?php echo htmlspecialchars($shop['shop_name']); ?>" data-shop-id="<?php echo htmlspecialchars($shop['id']); ?>">
                            <i class="fas fa-plus"></i>
                            <span style="color: #000000;">WRITE A REVIEW</span>
                        </button>
                    </div>
                                <?php endif; ?>
                        <div class="view-shop-overall-rating-section-card">
                            <div class="view-shop-overall-rating-summary-and-breakdown">
                                <div class="view-shop-overall-rating-main">
                                    <div class="view-shop-overall-rating-score"><?php echo $average_rating; ?></div>
                                    <div class="view-shop-overall-rating-details">
                                        <div class="view-shop-star-rating">
                                            <?php include 'view-details-backend/rating-number.php'; ?>
                                        </div>
                                        <div class="view-shop-overall-rating-text">Based on <?php echo $total_reviews; ?> reviews</div>
                                    </div>
                                </div>
                                <div class="view-shop-rating-breakdown">
                                    <?php include 'view-details-backend/rating-bar.php'; ?>
                                </div>
                            </div>
                        </div>
                        <div id="reviewsContainer">
                            <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                                <div class="view-shop-review-list">
                                    <?php
                                    $reviews_result->data_seek(0);
                                    while ($review = $reviews_result->fetch_assoc()):
                                        $stmt = $conn->prepare("SELECT COUNT(*) as like_count FROM review_likes WHERE review_id = ?");
                                        $stmt->bind_param("i", $review['id']);
                                        $stmt->execute();
                                        $like_count = $stmt->get_result()->fetch_assoc()['like_count'];
                                        $user_liked = false;
                                        if (isset($_SESSION['user_id'])) {
                                            $stmt = $conn->prepare("SELECT id FROM review_likes WHERE review_id = ? AND liked_by_user_id = ?");
                                            $stmt->bind_param("ii", $review['id'], $_SESSION['user_id']);
                                            $stmt->execute();
                                            $user_liked = $stmt->get_result()->num_rows > 0;
                                        }
                                        $response_data = null;
                                        $stmt = $conn->prepare("SELECT rr.* FROM respond_reviews rr WHERE rr.review_id = ?");
                                        $stmt->bind_param("i", $review['id']);
                                        $stmt->execute();
                                        $response_result = $stmt->get_result();
                                        if ($response_result->num_rows > 0) {
                                            $response_data = $response_result->fetch_assoc();
                                        }
                                    ?>
                                        <div class="view-shop-review-item" data-review-id="<?php echo $review['id']; ?>">
                                            <div class="view-shop-review-header">
                                                <img src="<?php echo BASE_URL; ?>/assets/img/profile/<?php echo !empty($review['profile_picture']) ? htmlspecialchars($review['profile_picture']) : 'profile-user.png'; ?>" alt="<?php echo htmlspecialchars($review['fullname']); ?>" class="view-shop-reviewer-profile-pic">
                                                <div class="view-shop-reviewer-text-info">
                                                    <div class="view-shop-reviewer-name"><?php echo htmlspecialchars($review['fullname']); ?></div>
                                                    <div class="view-shop-star-rating">
                                                        <?php for ($i = 1; $i <= 5; $i++) {
                                                            echo ($i <= $review['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                                        } ?>
                                                    </div>
                                                    <div class="view-shop-review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
                                                </div>
                                            </div>
                                            <div class="view-shop-review-comment">
                                                <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                            </div>
                                            <?php if ($response_data): ?>
                                                <div class="view-shop-shop-response">
                                                    <div><i class="fas fa-reply"></i> <strong><?php echo htmlspecialchars($shopname); ?></strong> <span><?php echo date('F j, Y', strtotime($response_data['created_at'])); ?></span></div>
                                                    <p><?php echo htmlspecialchars($response_data['response']); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="view-shop-review-actions">
                                                <button class="view-shop-like-button" data-review-id="<?php echo $review['id']; ?>" data-review-owner-id="<?php echo $review['user_id']; ?>">
                                                    <i class="<?php echo $user_liked ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                                                    <span class="like-count"><?php echo $like_count; ?></span> <?php echo $like_count != 1 ? 'Likes' : 'Like'; ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p style="text-align: center; font-size: 14px; color: #6c757d;">No reviews yet. Be the first to leave a review!</p>
                            <?php endif; ?>
                        </div>
                        <?php if ($total_reviews > 5): ?>
                            <div class="view-shop-pagination-controls">
                                <button id="prevBtn" class="view-shop-pagination-nav-btn" disabled><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="15,18 9,12 15,6"></polyline>
                                    </svg></button>
                                <button class="view-shop-pagination-current"><span id="currentPageNum">1</span></button>
                                <span id="view-shop-remainingCount"></span>
                                <button id="nextBtn" class="view-shop-pagination-nav-btn"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9,18 15,12 9,6"></polyline>
                                    </svg></button>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            </section>

            <section class="view-shop-right-panel">

                <article class="view-shop-section services" aria-labelledby="categoriesHeader">
                    <h2 class="view-shop-section-header" id="categoriesHeader">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="margin-right: 8px;">
                            <path d="M22.7 19.3l-4.2-4.2c.4-.9.6-1.9.6-3 0-4.4-3.6-8-8-8-1.1 0-2.1.2-3 .6l3.5 3.5-2.1 2.1-3.5-3.5c-.4.9-.6 1.9-.6 3 0 4.4 3.6 8 8 8 1.1 0 2.1-.2 3-.6l4.2 4.2c.4.4 1 .4 1.4 0l1.2-1.2c.3-.4.3-1 0-1.4z" />
                        </svg>
                        Services Offered
                    </h2>
                    <div class="view-shop-categories-list" role="list">
                        <?php
                        $categories = $organized_services ?? [];
                        $category_count = count($categories);
                        $limit = 3;
                        ?>
                        <?php if (!empty($categories)): ?>
                            <div class="services-list-wrapper">
                                <div class="services-list-container" id="services-list-container">
                                    <?php
                                    $accordion_counter = 0;
                                    foreach ($categories as $category_name => $category_data):
                                        $modal_id = 'serviceModal' . $accordion_counter;
                                    ?>
                                        <div
                                            class="service-name-badge"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?php echo htmlspecialchars($modal_id); ?>"
                                            role="button">

                                            <span class="badge-text"><?php echo htmlspecialchars($category_name); ?></span>
                                            <i class="fas fa-chevron-right badge-separator-icon"></i>
                                        </div>
                                    <?php
                                        $accordion_counter++;
                                    endforeach;
                                    ?>
                                </div>

                                <?php if ($category_count > $limit): ?>
                                    <?php $remaining_count = $category_count - $limit; ?>
                                    <button class="toggle-services-btn" id="toggle-services-btn" data-more-text="See <?php echo $remaining_count; ?> More" data-less-text="See Less">
                                        See <?php echo $remaining_count; ?> More
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-items" role="listitem">
                                <span class="text-muted">No services listed.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const container = document.getElementById('services-list-container');
                        const toggleBtn = document.getElementById('toggle-services-btn');

                        if (!toggleBtn || !container) {
                            return;
                        }

                        toggleBtn.addEventListener('click', function() {
                            container.classList.toggle('is-expanded');
                            const isExpanded = container.classList.contains('is-expanded');
                            if (isExpanded) {
                                toggleBtn.textContent = toggleBtn.dataset.lessText;
                            } else {
                                toggleBtn.textContent = toggleBtn.dataset.moreText;
                            }
                        });
                    });
                </script>

                <?php if (!empty($organized_services)): ?>
                    <?php
                    $modal_counter = 0;
                    foreach ($organized_services as $category_name => $category_data):
                        $modal_id = 'serviceModal' . $modal_counter;
                        $accordion_id = 'modalAccordion' . $modal_counter;
                    ?>
                        <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" aria-labelledby="<?php echo $modal_id; ?>Label" aria-hidden="true">
                            <div class="modal-dialog modal-lg" style="display: flex; align-items: center; min-height: 100vh;">
                                <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                                    <div class="modal-header" style="border-bottom: 1px solid #eee; padding: 1rem 1.5rem;">
                                        <h5 class="modal-title" id="<?php echo $modal_id; ?>Label" style="display: flex; align-items: center; font-weight: 600;">
                                            <i class="fas <?php echo htmlspecialchars($category_data['icon']); ?> me-2"></i>
                                            <?php echo htmlspecialchars($category_name); ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="box-shadow: none; outline: none;"></button>
                                    </div>
                                    <div class="modal-body" style="padding: 1.5rem;">
                                        <?php if (empty($category_data['subcategories'])): ?>
                                            <div class="no-services-message">
                                                <p class="text-muted">No specific services listed for this category.</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="accordion" id="<?php echo $accordion_id; ?>">
                                                <?php
                                                $subcategory_counter = 0;
                                                foreach ($category_data['subcategories'] as $subcategory_name => $services):
                                                    $subcategory_counter++;
                                                    $service_count = count($services);
                                                    $collapse_id = $accordion_id . "_collapse" . $subcategory_counter;
                                                    $is_first = $subcategory_counter === 1;
                                                ?>
                                                    <div class="accordion-item" style="border: none; border-bottom: 1px solid #eee;">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button <?php echo $is_first ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapse_id; ?>" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>" aria-controls="<?php echo $collapse_id; ?>" style="background-color: #f8f9fa; font-weight: 500;">
                                                                <?php echo htmlspecialchars($subcategory_name); ?>
                                                                <span class="badge bg-primary ms-2"><?php echo $service_count; ?></span>
                                                            </button>
                                                        </h2>
                                                        <div id="<?php echo $collapse_id; ?>" class="accordion-collapse collapse <?php echo $is_first ? 'show' : ''; ?>" data-bs-parent="#<?php echo $accordion_id; ?>">
                                                            <div class="accordion-body" style="padding-left: 1.5rem;">
                                                                <ol class="service-list ps-4">
                                                                    <?php foreach ($services as $service_name): ?>
                                                                        <li><small><?php echo htmlspecialchars($service_name); ?></small></li>
                                                                    <?php endforeach; ?>
                                                                </ol>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        $modal_counter++;
                    endforeach;
                    ?>
                <?php endif; ?>


                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const categoryCards = document.querySelectorAll('.service-category-card');

                        categoryCards.forEach(card => {
                            card.addEventListener('mouseenter', function() {
                                const arrow = this.querySelector('.service-category-arrow');
                                if (arrow) {
                                    arrow.style.transform = 'translateX(5px)';
                                }
                            });

                            card.addEventListener('mouseleave', function() {
                                const arrow = this.querySelector('.service-category-arrow');
                                if (arrow) {
                                    arrow.style.transform = 'translateX(0)';
                                }
                            });
                        });

                        const accordions = document.querySelectorAll('.accordion');
                        accordions.forEach(accordion => {
                            accordion.addEventListener('shown.bs.collapse', function(e) {
                                e.target.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'nearest'
                                });
                            });
                        });
                    });
                </script>


                <article class="view-shop-section brand" aria-labelledby="brandsHeader">
                    <h2 class="view-shop-section-header" id="brandsHeader">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="margin-right: 8px;">
                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11C5.84 5 5.28 5.42 5.08 6.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
                        </svg>
                        Vehicle Brands Serviced
                    </h2>
                    <div class="view-shop-categories-list" role="list">
                        <?php if (!empty($brands_serviced_array)): ?>
                            <?php foreach ($brands_serviced_array as $brand): ?>
                                <div class="view-shop-category-item" role="listitem" tabindex="0" aria-label="<?php echo htmlspecialchars($brand); ?>">
                                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                                        <path d="M9 16.2l-3.5-3.5 1.41-1.41L9 13.38l7.09-7.1 1.41 1.43z"></path>
                                    </svg>
                                    <?php echo htmlspecialchars($brand); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="view-shop-category-item no-items" role="listitem">
                                <span class="text-muted">No specific brands listed.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
                <article class="view-shop-section map" aria-labelledby="locationHeader">
                    <h2 class="view-shop-section-header" id="locationHeader">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                            <path d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" />
                        </svg>
                        Location
                    </h2>
                    <div class="view-shop-map-container" role="region" aria-label="Map showing business location">
                        <div id="map"></div>
                        <button onclick="openDirections()" class="map-directions-btn"><i class="fas fa-directions me-2"></i>Get Directions</button>
                    </div>
                </article>

                <article class="view-shop-section" aria-labelledby="galleryHeader">
                    <h2 class="view-shop-section-header" id="galleryHeader">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                        </svg>
                        Photos
                    </h2>
                    <div class="view-shop-gallery-grid" id="galleryGrid"></div>
                    <?php if (empty($gallery_images_php)): ?>
                        <p class="text-center text-muted">No images have been uploaded.</p>
                    <?php endif; ?>
                </article>

                <div id="chatContainer" class="chat-container" data-user-id="<?php echo $_SESSION['user_id']; ?>">
                    <div class="chat-header">
                        <div class="user-info">
                            <img src="<?php echo BASE_URL; ?>/account/uploads/shop_logo/<?php echo $shop['profile_picture'] ?? 'logo.jpg'; ?>" alt="User Avatar" class="user-avatar">
                            <h3><?php echo htmlspecialchars($shop['shop_name'] ?? 'User'); ?></h3>
                        </div>
                        <div class="close-chat" onclick="toggleChat()"><i class="fas fa-times" style="color: #000000;"></i></div>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                    </div>
                    <div class="chat-input-area">
                        <div id="attachmentPreview" class="attachment-preview-container"></div>
                        <div class="chat-input">
                            <div class="chat-input-wrapper">
                                <input type="text" id="messageInput" placeholder="Type a message..." style="outline: none; border: none; padding: 8px; border-radius: 4px;" onkeypress="handleKeyPress(event, <?php echo $_SESSION['user_id']; ?>, <?php echo $shop['user_id']; ?>)">
                                <label for="file-upload" class="attachment-btn"><i class="fas fa-paperclip"></i><input id="file-upload" type="file" name="attachment" style="display:none;" accept="image/*" onchange="previewAttachment(this)"></label>
                                <button type="button" class="send-btn" onclick="sendMessage(<?php echo $_SESSION['user_id']; ?>, <?php echo $shop['user_id']; ?>)"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="imageModal" class="modal fade" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content position-relative border-0 bg-transparent shadow-none text-center">

                            <div class="position-relative d-inline-block">
                                <img class="img-fluid rounded" id="view-shop-modalImage" src="" alt="Full size image" style="border-radius: 16px;">
                                <button type="button" class="position-absolute"
                                    style="
    top: -15px;
    right: -15px;
    background-color: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    border-radius: 50%;
    padding: 6px 12px;
    font-size: 20px;
    z-index: 10;
    line-height: 1;
"
                                    data-bs-dismiss="modal" aria-label="Close">
                                    &times;
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                </div>
            </section>
        </main>
    </div>

    <div id="messageContainer" class="message-container" style="display: none;"><span id="messageText"></span></div>

    <div class="modal fade" id="verificationRequiredModal" tabindex="-1" aria-labelledby="verificationRequiredModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1rem; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                <div class="modal-body text-center p-4">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="mb-3">
                        <i class="fas fa-user-shield" style="font-size: 4rem; color: #ffc107;"></i>
                    </div>
                    <h5 class="modal-title mb-2" id="verificationRequiredModalLabel" style="font-weight: 600;">Account Verification Required</h5>
                    <p class="text-muted" style="font-size: 0.9rem;">To access this feature, please complete your account verification first. This helps us maintain a secure community.</p>
                    <a href="<?php echo BASE_URL; ?>/account/verify-account" class="btn btn-warning text-white mt-3" style="padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: 600;">Verify My Account</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/view-details-modal.php'; ?>
    <?php include 'include/toast.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"></script>

   <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/save_shop.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/view-details-message.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/emergency-request.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/navbar.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/report.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/view-details-tab-panel.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/shop-map.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/view-details-review-likes.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/view-details-photos.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/view-details-pagination.js"></script>

    

    <script>
        window.currentUser = {
            id: <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>,
            fullname: <?php echo json_encode(htmlspecialchars($_SESSION['fullname'] ?? '')); ?>,
            isLoggedIn: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>
        };

        window.shopInfo = {
            name: <?php echo json_encode($shop['shop_name'] ?? ''); ?>,
            displayAddress: <?php echo json_encode($display_address ?? ''); ?>,
            combinedAddress: <?php echo json_encode($combined_address ?? ''); ?>,
            encodedCombinedAddress: <?php echo json_encode($encoded_combined_address ?? ''); ?>
        };

        window.userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        window.galleryData = {
            images: <?php echo json_encode($gallery_images_php ?? []); ?>
        };
    </script>

    <script>
        const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let scrollPosition = 0;
            const body = document.body;

            document.addEventListener('show.bs.modal', function() {
                if (body.classList.contains('modal-open-freeze')) {
                    return;
                }
                scrollPosition = window.pageYOffset || document.documentElement.scrollTop;

                body.style.top = `-${scrollPosition}px`;

                body.classList.add('modal-open-freeze');
            });

            document.addEventListener('hidden.bs.modal', function() {
                if (!body.classList.contains('modal-open')) {
                    body.classList.remove('modal-open-freeze');
                    body.style.top = '';
                    window.scrollTo(0, scrollPosition);
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isUserLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            const isUserVerified = <?php echo json_encode($is_verified == 1); ?>;
            const userProfileType = <?php echo json_encode($profile_type); ?>;
            const isOwnerOfThisShop = <?php echo json_encode($is_owner_of_this_shop); ?>;

            const verificationModalEl = document.getElementById('verificationRequiredModal');
            const reviewModalEl = document.getElementById('writeReview');
            const writeReviewBtn = document.getElementById('writeReviewBtn');
            const bookNowBtn = document.getElementById('userBookNowBtn');

            if (!verificationModalEl || !reviewModalEl || !writeReviewBtn) {
                console.error("A required modal or button element is missing.");
                return;
            }

            const verificationModal = new bootstrap.Modal(verificationModalEl);
            const reviewModal = new bootstrap.Modal(reviewModalEl);

            writeReviewBtn.addEventListener('click', function() {
                if (!isUserLoggedIn) {
                    toastr.info('Please log in to write a review.');
                    return;
                }
                if (isOwnerOfThisShop) {
                    toastr.error("You cannot post a review on your own shop.");
                    return;
                }
                if (userProfileType === 'user' && isUserVerified) {
                    reviewModal.show();
                } else {
                    verificationModal.show();
                }
            });

            if (bookNowBtn && userProfileType === 'user' && !isUserVerified) {
                bookNowBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    verificationModal.show();
                });
            }

            reviewModalEl.addEventListener('show.bs.modal', function() {
                const shopName = writeReviewBtn.getAttribute('data-shop-name');
                const shopId = writeReviewBtn.getAttribute('data-shop-id');
                document.getElementById('modal_shop_name').value = shopName;
                document.getElementById('shop_id').value = shopId;
                document.getElementById('reviewer_name').value = window.currentUser?.fullname || '';
                resetReviewForm();
            });

            initStarRating();
            initReviewFormSubmission();

            function initStarRating() {
                const ratingStars = document.querySelectorAll('.rating-star');
                ratingStars.forEach(star => {
                    star.addEventListener('click', function() {
                        const ratingValue = parseInt(this.dataset.rating);
                        document.getElementById('rating').value = ratingValue;
                        updateStarDisplay(ratingValue);
                    });
                    star.addEventListener('mouseover', function() {
                        updateStarDisplay(parseInt(this.dataset.rating), true);
                    });
                    star.addEventListener('mouseout', function() {
                        updateStarDisplay(parseInt(document.getElementById('rating').value));
                    });
                });
            }

            function updateStarDisplay(rating, isHover = false) {
                const ratingStars = document.querySelectorAll('.rating-star');
                ratingStars.forEach(star => {
                    const starValue = parseInt(star.dataset.rating);
                    star.classList.toggle('fas', starValue <= rating);
                    star.classList.toggle('far', starValue > rating);
                });
                if (!isHover) {
                    document.getElementById('current-rating').textContent = rating || '0';
                }
            }

            function initReviewFormSubmission() {
                document.getElementById('submitReview').addEventListener('click', function() {
                    const form = document.getElementById('writeReviewForm');
                    const ratingInput = document.getElementById('rating');
                    const commentInput = document.getElementById('comment');

                    if (ratingInput.value == '0') {
                        toastr.warning('Please select a star rating.');
                        return;
                    }
                    if (commentInput.value.trim() === '') {
                        toastr.warning('Please share your experience in the comment box.');
                        commentInput.classList.add('is-invalid');
                        return;
                    }
                    commentInput.classList.remove('is-invalid');

                    const formData = new FormData(form);
                    fetch(`${BASE_URL}/account/backend/submit_review.php`, {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success('Review submitted successfully!');
                                reviewModal.hide();
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                toastr.error(data.message || 'An error occurred.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('A network error occurred. Please try again.');
                        });
                });
            }

            function resetReviewForm() {
                const form = document.getElementById('writeReviewForm');
                form.reset();
                form.classList.remove('was-validated');
                document.getElementById('rating').value = '0';
                document.getElementById('comment').classList.remove('is-invalid');
                updateStarDisplay(0);
            }
        });
    </script>
</body>
</html>