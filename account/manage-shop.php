<?php
require_once '../functions/auth.php';
include 'backend/base-path.php';
include 'backend/security_helper.php';
include 'backend/db_connection.php';
include 'backend/manage-shop.php';
include 'backend/owner-profile.php';

$is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];

$all_services_data = [];
$categories_result = $conn->query("SELECT * FROM service_categories ORDER BY display_order, name");
while ($cat = $categories_result->fetch_assoc()) {
    $all_services_data[$cat['id']] = $cat;
    $all_services_data[$cat['id']]['subcategories'] = [];
}
$subcategories_result = $conn->query("SELECT * FROM service_subcategories ORDER BY name");
while ($sub = $subcategories_result->fetch_assoc()) {
    if (isset($all_services_data[$sub['category_id']])) {
        $all_services_data[$sub['category_id']]['subcategories'][$sub['id']] = $sub;
        $all_services_data[$sub['category_id']]['subcategories'][$sub['id']]['services'] = [];
    }
}
$services_result = $conn->query("SELECT * FROM services ORDER BY name");
while ($ser = $services_result->fetch_assoc()) {
    foreach ($all_services_data as &$category) {
        if (isset($category['subcategories'][$ser['subcategory_id']])) {
            $category['subcategories'][$ser['subcategory_id']]['services'][] = $ser;
            break;
        }
    }
}

$current_service_ids = [];
if ($shop_id > 0) {
    $current_services_stmt = $conn->prepare("SELECT service_id FROM shop_services WHERE application_id = ?");
    $current_services_stmt->bind_param("i", $shop_id);
    $current_services_stmt->execute();
    $current_services_result = $current_services_stmt->get_result();
    while ($row = $current_services_result->fetch_assoc()) {
        $current_service_ids[] = $row['service_id'];
    }
    $current_services_stmt->close();
}

$organized_services = [];
if ($shop_id > 0) {
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
}


?>

<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($shop['shop_name']); ?></title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/manage-shop.css">

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
        object-fit: cover;
        border-radius: 50%;
    }
    .verified-badge-icon {
        position: absolute;
        bottom: 8px;
        right: 8px;
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
        <div class="shop-info-header">
            <div class="shop-info-name-display">
                <?php echo htmlspecialchars($shop_name); ?>
            </div>
           <nav class="shop-info-nav">
    <a href="edit-shop-profile.php?name=<?php echo urlencode($shop['shop_slug']); ?>" class="shop-info-nav-link">
        <i class="fas fa-edit"></i> Edit Shop
    </a>
    <a href="review-ratings.php?name=<?php echo urlencode($shop['shop_slug']); ?>" class="shop-info-nav-link">
        <i class="fas fa-star-half-alt"></i> See Reviews
    </a>
    <a href="insights.php?name=<?php echo urlencode($shop['shop_slug']); ?>" class="shop-info-nav-link">
        <i class="fas fa-chart-bar"></i> Insights
    </a>
</nav>
        </div>

        <main class="view-shop-container" role="main" aria-label="Listing detail and location details">
            <section class="view-shop-profile-card" aria-label="Business profile and contact details">
                <div class="view-shop-profile-image-wrapper" aria-hidden="true">
                    <img src="<?php echo htmlspecialchars($shop_logo); ?>" alt="Shop cover" class="view-shop-shop-logo-img">
                    <div class="verified-badge-icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
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

                <h1 class="view-shop-profile-title" id="profileTitle">
                    <?php echo htmlspecialchars($shop['shop_name']); ?>
                </h1>

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
                    <i class="fas fa-map-marker-alt" style="font-size: 14px; "></i>
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

                <?php if (!empty(trim($shop['description']))): ?>
                    <div class="view-shop-shop-description">
                        <p><?php echo nl2br(htmlspecialchars($shop['description'])); ?></p>
                    </div>
                <?php endif; ?>
                        
                        <?php if (($shop['shop_status'] ?? 'open') == 'open'): ?>
                            <div class="view-shop-action-buttons-container">
                                <?php
                                $is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];
                                ?>

                                <?php
                                if ($is_owner && !$shop['show_book_now'] && !$shop['show_emergency']) :
                                ?>
                                    <a href="edit-shop-profile.php?name=<?php echo urlencode($shop['shop_slug']); ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-toggle-on me-2"></i> Enable Booking / Emergency
                                    </a>

                                <?php
                                else :
                                ?>
                                    <?php
                                    if ($shop['show_book_now']) : ?>
                                        <?php if (!$is_owner) : ?>
                                            <a href="book-now.php?shop_id=<?php echo URLSecurity::encryptId($shop_id); ?>" class="btn btn-warning view-shop-book-now-btn text-white" style="font-weight: 500;">
                                                 Book Now
                                            </a>
                                        <?php else : ?>
                                            <button class="btn btn-warning view-shop-book-now-btn text-white" style="font-weight: 500;" onclick="showOwnerRestrictionModal('book')">
                                                Book Now
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php
                                    if ($shop['show_emergency']) : ?>
                                        <?php if (!$is_owner) : ?>
                                            <a href="emergency-requests.php?shop_id=<?php echo URLSecurity::encryptId($shop['id']); ?>" class="btn btn-danger view-shop-emergency-request-btn" style="font-weight: 500;">
                                                Emergency
                                            </a>
                                        <?php else : ?>
                                            <button class="btn btn-danger view-shop-emergency-request-btn" style="font-weight: 500;" onclick="showOwnerRestrictionModal('emergency')">
                                                Emergency
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

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
<div class="d-flex align-items-center flex-wrap">
    <div class="d-flex align-items-center me-2">
        <i class="fas fa-clock me-1"></i>
        <span class="badge ms-2 <?= $status_class ?>">
            <?= $current_status ?>
        </span>
    </div>
    <button class="view-schedule btn btn-sm btn-link p-0 text-decoration-none" type="button" data-bs-toggle="modal" data-bs-target="#scheduleModal" aria-label="View Full Schedule">
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
                                        $stmt = $conn->prepare("SELECT rr.*, u.fullname as shop_owner_name FROM respond_reviews rr JOIN users u ON rr.shop_owner_id = u.id WHERE rr.review_id = ?");
                                        $stmt->bind_param("i", $review['id']);
                                        $stmt->execute();
                                        $response_result = $stmt->get_result();
                                        if ($response_result->num_rows > 0) {
                                            $response_data = $response_result->fetch_assoc();
                                        }
                                    ?>
                                        <div class="view-shop-review-item" data-review-id="<?php echo $review['id']; ?>">
                                            <div class="view-shop-review-header">
                                                <img src="../assets/img/profile/<?php echo !empty($review['profile_picture']) ? htmlspecialchars($review['profile_picture']) : 'profile-user.png'; ?>" alt="<?php echo htmlspecialchars($review['fullname']); ?>" class="view-shop-reviewer-profile-pic">
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
                                                    <div><i class="fas fa-reply"></i> <strong><?php echo htmlspecialchars($response_data['shop_owner_name']); ?></strong> <span><?php echo date('F j, Y', strtotime($response_data['created_at'])); ?></span></div>
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
                                <p style="text-align: center; font-size: 14px; color: #6c757d;">No reviews yet.</p>
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

            <section class="view-shop-right-panel" aria-label="Categories and location details">

                <article class="view-shop-section" aria-labelledby="categoriesHeader">
                    <h2 class="view-shop-section-header" id="categoriesHeader">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="margin-right: 8px;">
                            <path d="M22.7 19.3l-4.2-4.2c.4-.9.6-1.9.6-3 0-4.4-3.6-8-8-8-1.1 0-2.1.2-3 .6l3.5 3.5-2.1 2.1-3.5-3.5c-.4.9-.6 1.9-.6 3 0 4.4 3.6 8 8 8 1.1 0 2.1-.2 3-.6l4.2 4.2c.4.4 1 .4 1.4 0l1.2-1.2c.3-.4.3-1 0-1.4z" />
                        </svg>
                        Services Offered
                        <?php if ($is_owner): ?>
                            <i class="fas fa-edit section-edit-icon" id="editServicesBtn" title="Edit Services" data-bs-toggle="modal" data-bs-target="#editServicesModal"></i>
                        <?php endif; ?>
                    </h2>
                    <div class="view-shop-categories-list" id="servicesList">
                        <?php
                        $categories = $organized_services ?? [];
                        $category_count = count($categories);
                        $limit = 3;
                        ?>
                        <?php if ($category_count > 0): ?>
                            <div class="services-list-wrapper">
                                <div class="services-list-container" id="services-list-container">
                                    <?php
                                    $modal_counter = 0;
                                    foreach ($categories as $name => $data):
                                        $modal_counter++;
                                        $modal_id = "categoryModal" . $modal_counter;
                                    ?>
                                        <div
                                            class="service-name-badge"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?php echo htmlspecialchars($modal_id); ?>"
                                            role="button">

                                            <span class="badge-text"><?php echo htmlspecialchars($name); ?></span>
                                            <i class="fas fa-chevron-right badge-separator-icon"></i>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($category_count > $limit): ?>
                                    <?php $remaining_count = $category_count - $limit; ?>
                                    <button class="toggle-services-btn" id="toggle-services-btn" data-more-text="See <?php echo $remaining_count; ?> More" data-less-text="See Less">
                                        See <?php echo $remaining_count; ?> More
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="view-shop-category-item">
                                No services listed.
                                <?php if ($is_owner): ?>
                                    Click the <i class="fas fa-edit"></i> icon to add your services.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toggleBtn = document.getElementById('toggle-services-btn');
                        const container = document.getElementById('services-list-container');

                        if (!toggleBtn) {
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
                        $modal_counter++;
                        $modal_id = "categoryModal" . $modal_counter;
                        $accordion_id = "accordion" . $modal_counter;
                    ?>
                        <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" aria-labelledby="<?php echo $modal_id; ?>Label" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="<?php echo $modal_id; ?>Label">
                                            <i class="fas <?php echo htmlspecialchars($category_data['icon']); ?> me-2"></i>
                                            <?php echo htmlspecialchars($category_name); ?>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
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
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button <?php echo $is_first ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapse_id; ?>" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>" aria-controls="<?php echo $collapse_id; ?>">
                                                                <?php echo htmlspecialchars($subcategory_name); ?>
                                                                <span class="badge bg-primary ms-2"><?php echo $service_count; ?></span>
                                                            </button>
                                                        </h2>
                                                        <div id="<?php echo $collapse_id; ?>" class="accordion-collapse collapse <?php echo $is_first ? 'show' : ''; ?>" data-bs-parent="#<?php echo $accordion_id; ?>">
                                                            <div class="accordion-body">
                                                                <ul class="service-list">
                                                                    <?php foreach ($services as $service_name): ?>
                                                                        <li><?php echo htmlspecialchars($service_name); ?></li>
                                                                    <?php endforeach; ?>
                                                                </ul>
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
                    <?php endforeach; ?>
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
                        <i class="fas fa-car" style="font-size: 20px; margin-right: 8px;"></i>
                        Vehicle Brands Serviced
                        <i class="fas fa-edit section-edit-icon" id="editBrandsBtn" title="Edit Brands"></i>
                    </h2>
                    <div class="view-shop-categories-list" id="brandsList" role="list">
                        <?php if (!empty($brands_serviced_array)): ?>
                            <?php foreach ($brands_serviced_array as $brand): ?>
                                <div class="view-shop-category-item" role="listitem">
                                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                                        <path d="M9 16.2l-3.5-3.5 1.41-1.41L9 13.38l7.09-7.1 1.41 1.43z"></path>
                                    </svg>
                                    <?php echo htmlspecialchars(trim($brand)); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="view-shop-category-item" role="listitem">
                                No specific brands listed.
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

                <article class="view-shop-section map" aria-labelledby="locationHeader">
                    <h2 class="view-shop-section-header" id="locationHeader">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                            <path d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" />
                        </svg>
                        Location Map
                    </h2>
                    <div class="view-shop-map-container" role="region" aria-label="Map showing business location">
                        <div id="map"></div>
                        <button onclick="openDirections()" class="map-directions-btn"><i class="fas fa-directions me-2"></i>Get Directions</button>
                    </div>
                </article>

                <article class="view-shop-section gallery" aria-labelledby="galleryHeader">
                    <h2 class="view-shop-section-header" id="galleryHeader">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                        </svg>
                        Photos
                        <i class="fas fa-plus-circle section-edit-icon" title="Add Image" data-bs-toggle="modal" data-bs-target="#uploadGalleryModal"></i>
                    </h2>
                    <div class="view-shop-gallery-grid" id="galleryGrid"></div>
                    <?php if (empty($gallery_images_php)): ?>
                        <p class="text-center text-muted">No images have been uploaded.</p>
                    <?php endif; ?>
                </article>

            </section>
        </main>
    </div>

    <?php if ($is_owner): ?>

        <div class="modal fade" id="editServicesModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Your Services</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <ul class="nav nav-tabs flex-nowrap" id="editServiceTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="select-services-tab" data-bs-toggle="tab" data-bs-target="#select-services-pane" type="button" role="tab" aria-controls="select-services-pane" aria-selected="true">
                                    Select Services
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-pane" type="button" role="tab" aria-controls="summary-pane" aria-selected="false">
                                    Summary
                                    <span class="badge bg-primary rounded-pill ms-2" id="summary-count">0</span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3" id="editServiceTabsContent">
                            <div class="tab-pane fade show active" id="select-services-pane" role="tabpanel" aria-labelledby="select-services-tab" tabindex="0">
                                <p class="text-muted small">Expand each category and sub-category to select your services.</p>

                                <div class="accordion" id="categoryAccordion">
                                    <?php foreach ($all_services_data as $category): ?>
                                        <div class="accordion-item mb-3">
                                            <h2 class="accordion-header" id="headingCategory-<?php echo $category['id']; ?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategory-<?php echo $category['id']; ?>">
                                                    <i class="fas <?php echo htmlspecialchars($category['icon']); ?> me-2"></i>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </button>
                                            </h2>
                                            <div id="collapseCategory-<?php echo $category['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#categoryAccordion">
                                                <div class="accordion-body">
                                                    <div class="accordion" id="subcategoryAccordion-<?php echo $category['id']; ?>">
                                                        <?php if (!empty($category['subcategories'])): ?>
                                                            <?php foreach ($category['subcategories'] as $sub): ?>
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header" id="headingSubcat-<?php echo $sub['id']; ?>">
                                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSubcat-<?php echo $sub['id']; ?>">
                                                                            <?php echo htmlspecialchars($sub['name']); ?>
                                                                        </button>
                                                                    </h2>
                                                                    <div id="collapseSubcat-<?php echo $sub['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#subcategoryAccordion-<?php echo $category['id']; ?>">
                                                                        <div class="accordion-body">
                                                                            <?php if (!empty($sub['services'])): ?>
                                                                                
                                                                                <div class="form-check form-check-inline border-bottom pb-2 mb-2 w-100">
                                                                                    <input class="form-check-input" type="checkbox" 
                                                                                           id="select-all-<?php echo $sub['id']; ?>" 
                                                                                           data-sub-id="<?php echo $sub['id']; ?>" 
                                                                                           onchange="toggleAllServices(this)">
                                                                                    <label class="form-check-label fw-bold" for="select-all-<?php echo $sub['id']; ?>">
                                                                                        Select All
                                                                                    </label>
                                                                                </div>

                                                                                <?php foreach ($sub['services'] as $service): ?>
                                                                                    <?php $isChecked = in_array($service['id'], $current_service_ids); ?>
                                                                                    <div class="form-check">
                                                                                        <input class="form-check-input service-checkbox-<?php echo $sub['id']; ?>" 
                                                                                               type="checkbox" 
                                                                                               value="<?php echo $service['id']; ?>" 
                                                                                               id="service-<?php echo $service['id']; ?>" 
                                                                                               <?php echo $isChecked ? 'checked' : ''; ?> 
                                                                                               onclick="handleCheckboxChange(this)">
                                                                                        <label class="form-check-label" for="service-<?php echo $service['id']; ?>">
                                                                                            <?php echo htmlspecialchars($service['name']); ?>
                                                                                        </label>
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                            <?php else: ?>
                                                                                <p class="text-muted small mb-0">No specific services listed.</p>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <p class="text-muted small mb-0">No sub-categories available.</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="summary-pane" role="tabpanel" aria-labelledby="summary-tab" tabindex="0">
                                <div id="edit-summary-container" style="max-height: 500px; overflow-y: auto;">
                                    <div class="text-center text-muted p-4 d-flex flex-column justify-content-center align-items-center" style="height: 100%;">
                                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                        <p class="mb-1 fw-bold">No Services Selected</p>
                                        <p class="small">Your chosen services will appear here.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let currentShopServices = <?php echo json_encode(array_map('strval', $current_service_ids)); ?>;
            const shopIdToEdit = <?php echo $shop_id; ?>;
            const allServicesData = <?php echo json_encode($all_services_data); ?>;

            function handleCheckboxChange(checkbox) {
                const serviceId = checkbox.value;
                const isChecked = checkbox.checked;

                if (isChecked) {
                    if (!currentShopServices.includes(serviceId)) currentShopServices.push(serviceId);
                } else {
                    currentShopServices = currentShopServices.filter(id => id !== serviceId);
                }

                updateEditSummary();
                updateSpecificSelectAll(checkbox);

                const formData = new FormData();
                formData.append('shop_id', shopIdToEdit);
                formData.append('service_id', serviceId);
                formData.append('action', isChecked ? 'add' : 'remove');

                fetch('backend/update_shop_services.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentShopServices = data.services;
                        } else {
                            toastr.error(data.message || 'An error occurred.');
                            checkbox.checked = !isChecked;
                            handleCheckboxChange(checkbox);
                        }
                    }).catch(() => {
                        toastr.error('An error occurred.');
                        checkbox.checked = !isChecked;
                        handleCheckboxChange(checkbox);
                    });
            }

            function toggleAllServices(selectAllCheckbox) {
                const subId = selectAllCheckbox.dataset.subId;
                const isChecked = selectAllCheckbox.checked;
                const serviceCheckboxes = document.querySelectorAll(`.service-checkbox-${subId}`);

                const originalToastrSuccess = toastr.success;
                const originalToastrError = toastr.error;
                toastr.success = function() {};
                toastr.error = function() {};

                let changesMade = 0;
                serviceCheckboxes.forEach(checkbox => {
                    if (checkbox.checked !== isChecked) {
                        checkbox.checked = isChecked;
                        handleCheckboxChange(checkbox);
                        changesMade++;
                    }
                });

                toastr.success = originalToastrSuccess;
                toastr.error = originalToastrError;
            }

            function updateSpecificSelectAll(serviceCheckbox) {
                const accordionBody = serviceCheckbox.closest('.accordion-body');
                if (!accordionBody) return;

                const selectAllCheckbox = accordionBody.querySelector('input[id^="select-all-"]');
                if (!selectAllCheckbox) return;

                const subId = selectAllCheckbox.dataset.subId;
                const allServiceCheckboxes = accordionBody.querySelectorAll(`.service-checkbox-${subId}`);

                if (allServiceCheckboxes.length > 0) {
                    const allChecked = Array.from(allServiceCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                }
            }

            function updateAllSelectAllStates() {
                const allSelectAllCheckboxes = document.querySelectorAll('input[id^="select-all-"]');
                allSelectAllCheckboxes.forEach(selectAllCheckbox => {
                    const subId = selectAllCheckbox.dataset.subId;
                    const serviceCheckboxes = document.querySelectorAll(`.service-checkbox-${subId}`);
                    
                    if (serviceCheckboxes.length > 0) {
                        const allChecked = Array.from(serviceCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            }

            function updateEditSummary() {
                const summaryContainer = document.getElementById('edit-summary-container');
                const summaryCountBadge = document.getElementById('summary-count');

                summaryCountBadge.textContent = currentShopServices.length;

                if (currentShopServices.length === 0) {
                    summaryContainer.innerHTML = `
                    <div class="text-center text-muted p-4 d-flex flex-column justify-content-center align-items-center" style="height: 100%;">
                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                        <p class="mb-1 fw-bold">No Services Selected</p>
                        <p class="small">Your chosen services will appear here.</p>
                    </div>`;
                    return;
                }

                let summaryHtml = '';
                const summary = {};

                currentShopServices.forEach(serviceId => {
                    for (const cat of Object.values(allServicesData)) {
                        for (const sub of Object.values(cat.subcategories)) {
                            const service = sub.services.find(s => String(s.id) === serviceId);
                            if (service) {
                                if (!summary[cat.name]) summary[cat.name] = {};
                                if (!summary[cat.name][sub.name]) summary[cat.name][sub.name] = [];
                                summary[cat.name][sub.name].push(service.name);
                                break;
                            }
                        }
                    }
                });

                for (const catName in summary) {
                    summaryHtml += `<h6 class="summary-category mt-3 mb-2">${catName}</h6>`;
                    for (const subName in summary[catName]) {
                        summaryHtml += `<div class="summary-subcategory ps-2">${subName}</div>`;
                        summaryHtml += `<ul class="summary-service-list">`;
                        summary[catName][subName].forEach(serviceName => {
                            summaryHtml += `<li>${serviceName}</li>`;
                        });
                        summaryHtml += `</ul>`;
                    }
                }
                summaryContainer.innerHTML = summaryHtml;
            }

            document.addEventListener('DOMContentLoaded', function() {
                const editServicesModalEl = document.getElementById('editServicesModal');
                if (!editServicesModalEl) return;

                editServicesModalEl.addEventListener('show.bs.modal', function () {
                    updateEditSummary();
                    updateAllSelectAllStates();
                });
                
                editServicesModalEl.addEventListener('hidden.bs.modal', () => location.reload());
            });
        </script>
    <?php endif; ?>

    <input type="file" id="editImageInput" style="display:none;" accept="image/*">

    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/view-details-modal.php'; ?>
    <?php include 'include/manage-shop-modal.php'; ?>
    <?php include 'include/toast.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"></script>
    

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/save_shop.js"></script>
    <script src="../assets/js/write-review.js"></script>
    <script src="../assets/js/emergency-request.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script src="../assets/js/brand-vehicle.js"></script>
    <script src="../assets/js/manage-shop-tab-panel.js"></script>
    <script src="../assets/js/photos.js"></script>
    <script src="../assets/js/report.js"></script>
    <script src="../assets/js/manage-shop-map.js"></script>
    <script src="../assetsjs/view-details-review-likes.js"></script>
    <script src="../assets/js/view-details-pagination.js"></script>
    <script src="../assets/js/update_shop_settings.js"></script>

    <script>
        window.shopInfo = {
            name: <?php echo json_encode($shop['shop_name']); ?>,
            displayAddress: <?php echo json_encode($display_address); ?>,
            combinedAddress: <?php echo json_encode($combined_address); ?>,
            encodedCombinedAddress: <?php echo json_encode($encoded_combined_address); ?>
        };

        window.currentUser = {
            id: <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>,
            fullname: <?php echo json_encode(htmlspecialchars($_SESSION['fullname'] ?? '')); ?>,
            isLoggedIn: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>
        };

        window.userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        window.galleryImages = <?php echo json_encode($gallery_images_php); ?>;

        function showOwnerRestrictionModal(actionType) {
            const modal = new bootstrap.Modal(document.getElementById('ownerRestrictionModal'));
            const messageEl = document.getElementById('restrictionMessage');
            if (actionType === 'book') {
                messageEl.textContent = 'You cannot book an appointment for your own shop. This feature is for customers only.';
            } else {
                messageEl.textContent = 'You cannot request emergency service for your own shop. This feature is for customers only.';
            }
            modal.show();
        }
    </script>
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

</body>

</html>