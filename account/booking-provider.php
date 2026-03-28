<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/emergency-modal.php';

$bookingShopsQuery = "SELECT * FROM shop_applications WHERE show_book_now = 1 AND status = 'Approved'";
$bookingShopsResult = mysqli_query($conn, $bookingShopsQuery);

$is_verified = 0;
$profile_type = '';
$is_any_shop_owner = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $userQuery = $conn->prepare("SELECT is_verified, profile_type FROM users WHERE id = ?");
    $userQuery->bind_param("i", $user_id);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    if ($userRow = $userResult->fetch_assoc()) {
        $is_verified = $userRow['is_verified'];
        $profile_type = $userRow['profile_type'];

        if ($is_verified == 0) {
            $submissionQuery = $conn->prepare("SELECT status FROM verification_submissions WHERE user_id = ? ORDER BY submission_date DESC LIMIT 1");
            $submissionQuery->bind_param("i", $user_id);
            $submissionQuery->execute();
            $submissionResult = $submissionQuery->get_result();
            if ($submissionRow = $submissionResult->fetch_assoc()) {
                if ($submissionRow['status'] === 'pending') {
                    $is_verified = 2;
                } elseif ($submissionRow['status'] === 'rejected') {
                    $is_verified = 3;
                }
            }
            $submissionQuery->close();
        }
    }
    $userQuery->close();

    $owner_check_stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ? AND status = 'Approved'");
    $owner_check_stmt->bind_param("i", $user_id);
    $owner_check_stmt->execute();
    $owner_check_stmt->store_result();
    $is_any_shop_owner = $owner_check_stmt->num_rows > 0;
    $owner_check_stmt->close();
}

$shops_with_badges = [];

if (mysqli_num_rows($bookingShopsResult) > 0) {
    while ($shop = mysqli_fetch_assoc($bookingShopsResult)) {
        $topRated = false;
        $mostBooked = false;
        $shop_id = $shop['id'];
        $shop_status = $shop['shop_status'] ?? 'open';
        
        $topRatedQuery = "SELECT AVG(rating) as avg_rating FROM shop_ratings WHERE shop_id = ?";
        $stmt = $conn->prepare($topRatedQuery);
        $stmt->bind_param("i", $shop_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result && $result['avg_rating'] >= 4.0) {
            $topRated = true;
        }
        $stmt->close();

        $mostBookedQuery = "SELECT COUNT(*) as total_completed FROM services_booking WHERE shop_id = ? AND booking_status = 'Completed'";
        $stmt = $conn->prepare($mostBookedQuery);
        $stmt->bind_param("i", $shop_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result && $result['total_completed'] >= 10) {
            $mostBooked = true;
        }
        $stmt->close();

        $shop['topRated'] = $topRated;
        $shop['mostBooked'] = $mostBooked;
        $shop['completeAllCode'] = $topRated && $mostBooked;
        
        $shops_with_badges[] = $shop;
    }
}

usort($shops_with_badges, function($a, $b) {
    if ($a['completeAllCode'] != $b['completeAllCode']) {
        return ($a['completeAllCode'] ? -1 : 1);
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
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/booking-provider.css">
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

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/booking-modal.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="booking-provider-section">
            <div class="container">
                <div class="page-header">
                    <h1>Book a Service</h1>
                    <p>Choose from our trusted partner shops that offer advance booking for your convenience.</p>
                    <div class="services-note">
                        Note: We recommend messaging the shop directly before booking to inquire about service estimated pricing.
                    </div>
                </div>
                <div class="shops-grid">
                    <?php if (!empty($shops_with_badges)): ?>
                        <?php foreach ($shops_with_badges as $shop): 
                            $topRated = $shop['topRated'];
                            $mostBooked = $shop['mostBooked'];
                            $shop_id = $shop['id'];
                            $shop_status = $shop['shop_status'] ?? 'open';
                        ?>
                            <div class="shop-card">
                                <div class="shop-card-content">
                                    <?php
                                    $logoFile = !empty($shop['shop_logo']) ? $shop['shop_logo'] : 'logo.jpg';
                                    $logoPath = 'uploads/shop_logo/' . $logoFile;
                                    if (!file_exists($logoPath)) {
                                        $logoPath = 'uploads/shop_logo/logo.jpg';
                                    }
                                    ?>
                                    <div class="shop-logo-container">
                                        <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="<?php echo htmlspecialchars($shop['shop_name']); ?>" class="shop-card-logo">
                                        <div class="verified-badge-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <div class="badge-container">
                                        <?php if ($topRated): ?>
                                            <div class="shop-badge top-rated">
                                                <i class="fas fa-star"></i> Top Rated
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($mostBooked): ?>
                                            <div class="shop-badge top-booking">
                                                <i class="fas fa-calendar-check"></i> Most Booked
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h5>
                                        <a href="shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>">
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
                                        'is_verified' => $is_verified,
                                        'profile_type' => $profile_type,
                                        'is_owner' => $is_owner_of_this_shop,
                                        'is_any_owner' => $is_any_shop_owner,
                                        'shop_slug' => $shop['shop_slug']
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
    </div>
    <div class="modal fade" id="ownerRestrictionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3" style="max-width: 400px; margin: auto;">
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle text-warning fa-2x mb-3"></i>
                    <p id="restrictionMessage" class="mb-3"></p>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generalOwnerRestrictionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3" style="max-width: 400px; margin: auto;">
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle text-warning fa-2x mb-3"></i>
                    <p>Shop owners cannot book services from other shops. This feature is for customers only.</p>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="verificationRequiredModal" tabindex="-1" aria-labelledby="verificationRequiredModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1rem; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                <div class="modal-body text-center p-4">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="mb-3">
                        <i id="verificationModalIcon" class="fas fa-user-shield" style="font-size: 4rem; color: #ffc107;"></i>
                    </div>
                    <h5 class="modal-title mb-2" id="verificationRequiredModalLabel" style="font-weight: 600;">Account Verification Required</h5>
                    <p id="verificationModalBody" class="text-muted" style="font-size: 0.9rem;">To access this feature, please complete your account verification first. This helps us maintain a secure community.</p>
                    <a id="verificationModalButton" href="verify-account.php" class="btn btn-warning text-white mt-3" style="padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: 600;">Verify My Account</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <?php include 'include/toast.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/navbar.js"></script>

    <script>
        const verificationModal = new bootstrap.Modal(document.getElementById('verificationRequiredModal'));
        const ownerModal = new bootstrap.Modal(document.getElementById('ownerRestrictionModal'));
        const generalOwnerModal = new bootstrap.Modal(document.getElementById('generalOwnerRestrictionModal'));

        function showOwnerRestrictionModal(actionType) {
            const messageEl = document.getElementById('restrictionMessage');
            if (actionType === 'book') {
                messageEl.textContent = 'You cannot book an appointment for your own shop. This feature is for customers only.';
            }
            ownerModal.show();
        }

        function showGeneralOwnerRestrictionModal() {
            generalOwnerModal.show();
        }

        function handleBookNowClick(params) {
            if (params.is_owner) {
                showOwnerRestrictionModal('book');
                return;
            }

            if (params.is_any_owner) {
                showGeneralOwnerRestrictionModal();
                return;
            }

            if (params.profile_type === 'user' && (params.is_verified == 0 || params.is_verified == 2 || params.is_verified == 3)) {
                const icon = document.getElementById('verificationModalIcon');
                const title = document.getElementById('verificationRequiredModalLabel');
                const body = document.getElementById('verificationModalBody');
                const button = document.getElementById('verificationModalButton');

                button.href = 'verify-account.php';

                if (params.is_verified == 2) {
                    icon.className = 'fas fa-clock';
                    icon.style.color = '#0dcaf0';
                    title.textContent = 'Verification Under Review';
                    body.textContent = 'Your submission is under review. This feature will be available once approved.';
                    button.textContent = 'Check Verification Status';
                } else if (params.is_verified == 3) {
                    icon.className = 'fas fa-times-circle';
                    icon.style.color = '#dc3545';
                    title.textContent = 'Verification Rejected';
                    body.textContent = 'Your previous submission was not approved. Please try again to access this feature.';
                    button.textContent = 'Re-Verify Account';
                } else {
                    icon.className = 'fas fa-user-shield';
                    icon.style.color = '#ffc107';
                    title.textContent = 'Account Verification Required';
                    body.textContent = 'To access this feature, please complete your account verification first.';
                    button.textContent = 'Verify My Account';
                }
                verificationModal.show();
                return;
            }
            window.location.href = `book-now.php?shop=${params.shop_slug}`;
        }
    </script>

</body>

</html>