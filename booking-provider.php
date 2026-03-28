<?php
session_start();
include 'functions/db_connection.php';

$bookingShopsQuery = "SELECT id, user_id, shop_name, shop_slug, shop_logo, shop_location, show_book_now, shop_status FROM shop_applications WHERE show_book_now = 1 AND status = 'Approved'";
$bookingShopsResult = mysqli_query($conn, $bookingShopsQuery);

$is_any_shop_owner = false;
$user_id = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $owner_check_stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ? AND status = 'Approved'");
    $owner_check_stmt->bind_param("i", $user_id);
    $owner_check_stmt->execute();
    $owner_check_stmt->store_result();
    $is_any_shop_owner = $owner_check_stmt->num_rows > 0;
    $owner_check_stmt->close();
}

$shops_for_sorting = [];
if ($bookingShopsResult) {
    while ($shop = mysqli_fetch_assoc($bookingShopsResult)) {
        $shop_id = $shop['id'];
        $topRated = false;
        $mostBooked = false;

        $topRatedQuery = "SELECT AVG(rating) as avg_rating FROM shop_ratings WHERE shop_id = ?";
        $stmt_rated = $conn->prepare($topRatedQuery);
        $stmt_rated->bind_param("i", $shop_id);
        $stmt_rated->execute();
        $result_rated = $stmt_rated->get_result()->fetch_assoc();
        if ($result_rated && isset($result_rated['avg_rating']) && $result_rated['avg_rating'] >= 4.0 && $result_rated['avg_rating'] <= 5.0) {
            $topRated = true;
        }
        $stmt_rated->close();

        $mostBookedQuery = "SELECT COUNT(*) as total_completed FROM services_booking WHERE shop_id = ? AND booking_status = 'Completed'";
        $stmt_booked = $conn->prepare($mostBookedQuery);
        $stmt_booked->bind_param("i", $shop_id);
        $stmt_booked->execute();
        $result_booked = $stmt_booked->get_result()->fetch_assoc();
        if ($result_booked && $result_booked['total_completed'] >= 10) {
            $mostBooked = true;
        }
        $stmt_booked->close();

        $shop['topRated'] = $topRated;
        $shop['mostBooked'] = $mostBooked;
        $shops_for_sorting[] = $shop;
    }
}

usort($shops_for_sorting, function($a, $b) {
    if ($a['topRated'] !== $b['topRated']) {
        return ($b['topRated'] ? 1 : 0) <=> ($a['topRated'] ? 1 : 0);
    }
    if ($a['mostBooked'] !== $b['mostBooked']) {
        return ($b['mostBooked'] ? 1 : 0) <=> ($a['mostBooked'] ? 1 : 0);
    }
    return strcasecmp($a['shop_name'], $b['shop_name']);
});

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Shops Provider</title>

<link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/account-required.css">
    <link rel="stylesheet" href="assets/css/booking-provider.css">
    <style>
        .shop-logo-container {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
        }
        
        .shop-logo-container .shop-card-logo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .verified-badge-icon {
            position: absolute;
            bottom: 3px;
            right: 3px;
            width: 26px;
            height: 26px;
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
            font-size: 13px;
        }

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
        .btn-book:disabled {
            background-color: #cccccc;
            border-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
            opacity: 0.65;
        }
    </style>
</head>

<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>
    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>
    <div class="booking-provider-section">
        <div class="container">
            <div class="page-header">
                <h1>Book a Service</h1>
                <p>Choose from our trusted partner shops that offer advance booking for your convenience.</p>
                <div class="services-note">Note: We recommend messaging the shop directly before booking to inquire about service estimated pricing.</div>
            </div>
            <div class="shops-grid">
                <?php if (!empty($shops_for_sorting)): ?>
                    <?php foreach ($shops_for_sorting as $shop):
                        $topRated = $shop['topRated'];
                        $mostBooked = $shop['mostBooked'];
                        $shop_id = $shop['id'];
                        $shop_status = $shop['shop_status'] ?? 'open';
                        ?>
                        <div class="shop-card">
                            <div class="shop-card-content">
                                <?php
                                $logoFile = !empty($shop['shop_logo']) ? $shop['shop_logo'] : 'logo.jpg';
                                $logoPath = 'account/uploads/shop_logo/' . $logoFile;
                                $defaultLogoPath = 'account/uploads/shop_logo/logo.jpg';
                                if (!empty($shop['shop_logo']) && file_exists($logoPath)) {
                                    $finalLogoPath = $logoPath;
                                } else {
                                    $finalLogoPath = $defaultLogoPath;
                                }
                                ?>
                                <div class="shop-logo-container">
                                    <img src="<?php echo htmlspecialchars($finalLogoPath); ?>" alt="<?php echo htmlspecialchars($shop['shop_name']); ?>" class="shop-card-logo">
                                    <div class="verified-badge-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                
                                <div class="badge-container">
                                    <?php if ($topRated): ?>
                                        <div class="shop-badge top-rated"><i class="fas fa-star"></i> Top Rated</div>
                                    <?php endif; ?>
                                    <?php if ($mostBooked): ?>
                                        <div class="shop-badge top-booking"><i class="fas fa-calendar-check"></i> Most Booked</div>
                                    <?php endif; ?>
                                </div>
                                <h5>
                                    <a href="<?php echo BASE_URL; ?>/shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>">
                                        <?php echo htmlspecialchars($shop['shop_name']); ?>
                                    </a>
                                </h5>
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

                                <?php
                                $is_owner_of_this_shop = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];
                                $action_params = json_encode([
                                    'is_owner' => $is_owner_of_this_shop,
                                    'is_any_owner' => $is_any_shop_owner,
                                    'shop_slug' => $shop['shop_slug'],
                                    'logged_in' => isset($_SESSION['user_id'])
                                ]);
                                $isDisabled = ($shop_status != 'open');
                                ?>
                                <button class="btn-book"
                                        onclick='<?php echo !$isDisabled ? "handleBookNowClick($action_params)" : ""; ?>'
                                        <?php echo $isDisabled ? 'disabled' : ''; ?>
                                        title="<?php echo $isDisabled ? 'Booking unavailable while shop is closed' : 'Book Now'; ?>">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-store-slash"></i>
                        <h4>No shops are currently offering advance booking.</h4>
                        <p class="text-muted">Please check back later as we continuously add more partners.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div id="loginRequiredModal" class="accountRequired-modal" style="z-index: 1150;">
        <div class="accountRequired-modal-content">
            <span class="accountRequired-close-modal">&times;</span>
            <h3 class="accountRequired-modal-title">Account Required</h3>
            <div class="accountRequired-modal-body"><p>You need an account to book a service. Please login or signup.</p></div>
            <div class="accountRequired-modal-buttons">
                <button id="loginBtn" class="accountRequired-modal-btn accountRequired-btn">Login</button>
                <button id="signupBtn" class="accountRequired-modal-btn accountRequired-btn">Sign Up</button>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        const BASE_URL = "<?php echo rtrim(BASE_URL, '/'); ?>";
    </script>
    <script src="js/account-required.js"></script>
    <?php include 'include/toast.php'; ?>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/navbar.js"></script>

    <script>
        function handleBookNowClick(params) {
            const { logged_in, is_owner, shop_slug } = params;

            if (!logged_in) {
                handleActionClick('book', shop_slug, null); 
            } else if (is_owner) {
                toastr.warning("You cannot book an appointment for your own shop.");
            } else {
                performAction('book', shop_slug, null);
            }
        }
    </script>
</body>

</html>