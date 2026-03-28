<?php
session_start();
include 'functions/db_connection.php';

$emergencyShopsQuery = "SELECT id, user_id, shop_name, shop_slug, shop_location, shop_logo, phone, show_emergency, shop_status FROM shop_applications WHERE show_emergency = 1 AND status = 'Approved'";
$emergencyShopsResult = mysqli_query($conn, $emergencyShopsQuery);

$is_any_shop_owner = false;
$user_id = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $owner_check_stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $owner_check_stmt->bind_param("i", $user_id);
    $owner_check_stmt->execute();
    $owner_check_result = $owner_check_stmt->get_result();
    $is_any_shop_owner = $owner_check_result->num_rows > 0;
    $owner_check_stmt->close();
}

$shops_for_sorting = [];
if ($emergencyShopsResult) {
    while ($shop = mysqli_fetch_assoc($emergencyShopsResult)) {
        $topRated = false;
        $shop_id = $shop['id'];
        
        $topRatedQuery = "SELECT AVG(rating) as avg_rating FROM shop_ratings WHERE shop_id = ?";
        $stmt = $conn->prepare($topRatedQuery);
        $stmt->bind_param("i", $shop_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result && isset($result['avg_rating']) && $result['avg_rating'] >= 4.0) {
            $topRated = true;
        }
        $stmt->close();
        
        $shop['topRated'] = $topRated;
        $shops_for_sorting[] = $shop;
    }
}

usort($shops_for_sorting, function($a, $b) {
    if ($a['topRated'] !== $b['topRated']) {
        return ($b['topRated'] ? 1 : 0) <=> ($a['topRated'] ? 1 : 0);
    }
    
    return strcasecmp($a['shop_name'], $b['shop_name']);
});

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Shop Providers</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
   <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/emergency-provider.css">
    <link rel="stylesheet" href="assets/css/account-required.css">
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
        .btn-call.disabled, .btn-request.disabled {
            background-color: #cccccc;
            border-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
            opacity: 0.65;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>
    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>
    <div class="emergency-provider-section">
        <div class="container">
            <div class="page-header">
                <h1>Emergency Shops</h1>
                <p>Find trusted partner shops that offer immediate assistance. You can either call them directly for the fastest response or request help through their form.</p>
                <small><i>Note: Emergency assistance depends on each shop’s business hours and availability.</i></small>
            </div>
            <div class="shops-grid">
                <?php if (!empty($shops_for_sorting)): ?>
                    <?php foreach ($shops_for_sorting as $shop):
                        $topRated = $shop['topRated'];
                        $shop_id = $shop['id'];
                        $shop_status = $shop['shop_status'] ?? 'open';
                        $isDisabled = ($shop_status != 'open');
                        $disabledClass = $isDisabled ? 'disabled' : '';
                        $disabledTitle = $isDisabled ? 'Shop is currently closed' : 'Call Now';
                        $requestDisabledTitle = $isDisabled ? 'Shop is currently closed' : 'Request Help';
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

                                <div class="shop-card-actions">
                                    <a href="<?php echo !$isDisabled ? 'tel:' . htmlspecialchars($shop['phone']) : 'javascript:void(0)'; ?>" 
                                       class="btn btn-call <?php echo $disabledClass; ?>" 
                                       title="<?php echo $disabledTitle; ?>">
                                        <i class="fas fa-phone-alt me-1"></i>Call
                                    </a>
                                    <?php if ($shop['show_emergency']): ?>
                                        <a href="#" 
                                           class="btn btn-request <?php echo $disabledClass; ?>"
                                           onclick="<?php echo !$isDisabled ? "handleActionClick('emergency-provider', '" . htmlspecialchars($shop['shop_slug']) . "', '" . urlencode($shop['shop_name']) . "', '" . $shop['phone'] . "')" : "return false;"; ?>"
                                           data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>"
                                           title="<?php echo $requestDisabledTitle; ?>">
                                            Request
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-store-slash"></i>
                        <h4>No emergency service providers available at the moment.</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="loginRequiredModal" class="accountRequired-modal" style="z-index: 1150;">
        <div class="accountRequired-modal-content">
            <span class="accountRequired-close-modal">&times;</span>
            <h3 class="accountRequired-modal-title">Account Required</h3>
            <div class="accountRequired-modal-body"><p>You need an account to view shop details. Please login or signup.</p></div>
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

    <script>
        const BASE_URL = "<?php echo rtrim(BASE_URL, '/'); ?>";
    </script>
    
    <script src="js/account-required.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="js/script.js"></script>
</body>
</html>