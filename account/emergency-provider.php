<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/emergency-modal.php';

$emergencyShopsQuery = "SELECT * FROM shop_applications WHERE show_emergency = 1 AND status = 'Approved'";
$emergencyShopsResult = mysqli_query($conn, $emergencyShopsQuery);

$is_owner = false;
$user_profile_type = 'user';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $profile_stmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
    $profile_stmt->bind_param("i", $user_id);
    $profile_stmt->execute();
    $profile_result = $profile_stmt->get_result();
    if ($profile_row = $profile_result->fetch_assoc()) {
        $user_profile_type = $profile_row['profile_type'];
        $is_owner = ($user_profile_type === 'owner');
    }
    $profile_stmt->close();
}

$shops_with_ratings = [];

if (mysqli_num_rows($emergencyShopsResult) > 0) {
    while ($shop = mysqli_fetch_assoc($emergencyShopsResult)) {
        $topRated = false;
        $shop_id = $shop['id'];
        
        $topRatedQuery = "SELECT AVG(rating) as avg_rating FROM shop_ratings WHERE shop_id = ?";
        $stmt = $conn->prepare($topRatedQuery);
        $stmt->bind_param("i", $shop_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result && $result['avg_rating'] >= 4.0) {
            $topRated = true;
        }
        $stmt->close();
        
        $shop['topRated'] = $topRated;
        $shops_with_ratings[] = $shop;
    }
}

usort($shops_with_ratings, function($a, $b) {
    if ($a['topRated'] != $b['topRated']) {
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
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/emergency-provider.css">
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

        .shop-card-actions .btn.disabled {
            background-color: #cccccc;
            border-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
            opacity: 0.65;
            pointer-events: none;
        }

        .btn-request:disabled {
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
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="emergency-provider-section">
            <div class="container">
                <div class="page-header">
                    <h1>Emergency Shops</h1>
                    <p>Find trusted partner shops that offer immediate assistance. You have two options: call them
                        directly for the fastest response, or request help via their form.</p>
                    <small style="display:block; text-align:center; font-style:italic; margin-top: 10px;">
                        Note: Emergency assistance depends on each shop’s business hours and availability. You can check this by clicking the request button.
                    </small>
                </div>

                <div class="shops-grid">
                    <?php if (!empty($shops_with_ratings)): ?>
                        <?php foreach ($shops_with_ratings as $shop):
                            $topRated = $shop['topRated'];
                            $shop_id = $shop['id'];
                            $shop_status = $shop['shop_status'] ?? 'open';
                            
                            $emergency_sql = "SELECT emergency_hours FROM shop_emergency_config WHERE shop_id = ?";
                            $emergency_stmt = $conn->prepare($emergency_sql);
                            $emergency_stmt->bind_param("i", $shop['id']);
                            $emergency_stmt->execute();
                            $emergency_result = $emergency_stmt->get_result();
                            $emergency_hours_data = $emergency_result->fetch_assoc();
                            $emergency_stmt->close();

                            $emergency_hours = $emergency_hours_data['emergency_hours'] ?? '[]';
                            $emergency_hours_array = json_decode($emergency_hours, true) ?: [];
                            $is_currently_open = false;

                            date_default_timezone_set('Asia/Manila');
                            $current_day = date('l');
                            $current_time = date('H:i');
                            foreach ($emergency_hours_array as $time_slot) {
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
                                                $is_currently_open = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            
                            $emergency_hours_formatted = [];
                            foreach ($emergency_hours_array as $time_slot) {
                                $parts = explode(', ', $time_slot, 2);
                                if (count($parts) === 2) {
                                    $day = trim($parts[0]);
                                    $time_range = trim($parts[1]);
                                    $times = explode(' - ', $time_range, 2);
                                    if (count($times) === 2) {
                                        $start_time_formatted = date('g:i A', strtotime(trim($times[0])));
                                        $end_time_formatted = date('g:i A', strtotime(trim($times[1])));
                                        $emergency_hours_formatted[] = $day . ', ' . $start_time_formatted . ' - ' . $end_time_formatted;
                                    } else {
                                        $emergency_hours_formatted[] = $time_slot;
                                    }
                                } else {
                                    $emergency_hours_formatted[] = $time_slot;
                                }
                            }
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
                                    </div>
                                    <h5>
                                        <a href="shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>">
                                            <?php echo htmlspecialchars($shop['shop_name']); ?>
                                        </a>
                                    </h5>
                                    <p><?php echo htmlspecialchars($shop['shop_location']); ?></p>

                                    <?php if ($shop_status == 'temporarily_closed'): ?>
                                        <div class="shop-status-badge temporarily-closed">
                                            <i class="fas fa-exclamation-triangle"></i> Temporarily Closed
                                        </div>
                                    <?php elseif ($shop_status == 'permanently_closed'): ?>
                                        <div class="shop-status-badge permanently-closed">
                                            <i class="fas fa-store-slash"></i> Permanently Closed
                                        </div>
                                    <?php endif; ?>

                                    <div class="shop-card-actions">
                                        <?php $isDisabled = ($shop_status != 'open'); ?>
                                        <a href="<?php echo !$isDisabled ? 'tel:' . htmlspecialchars($shop['phone']) : '#'; ?>"
                                            class="btn btn-call <?php echo $isDisabled ? 'disabled' : ''; ?>"
                                            title="<?php echo $isDisabled ? 'Calling unavailable while shop is closed' : 'Call Shop'; ?>">
                                            <i class="fas fa-phone-alt me-1"></i>Call
                                        </a>
                                        <?php
                                        $is_owner_of_this_shop = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];
                                        ?>

                                        <button class="btn btn-request" data-shop-id="<?php echo $shop['id']; ?>"
                                            data-is-owner-of-this="<?php echo $is_owner_of_this_shop ? 'true' : 'false'; ?>"
                                            data-is-any-owner="<?php echo $is_owner ? 'true' : 'false'; ?>"
                                            data-is-open="<?php echo $is_currently_open ? 'true' : 'false'; ?>"
                                            data-shop-slug="<?php echo htmlspecialchars($shop['shop_slug'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-emergency-hours='<?php echo htmlspecialchars(json_encode($emergency_hours_formatted), ENT_QUOTES, 'UTF-8'); ?>'
                                            onclick="handleRequestClick(this)" <?php echo $isDisabled ? 'disabled' : ''; ?>
                                            title="<?php echo $isDisabled ? 'Request unavailable while shop is closed' : 'Request Emergency Service'; ?>">
                                            <i class="fas fa-exclamation-triangle"></i> Request
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-store-slash"></i>
                            <h4>No emergency service providers available at the moment.</h4>
                            <p class="text-muted">Please check back later as we continuously add more partners.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="emergencyClosedModal" tabindex="-1" aria-hidden="true">
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
                    <div class="mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ownerRestrictionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3" style="max-width: 400px; margin: auto;">
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle text-warning fa-2x mb-3"></i>
                    <p>You cannot request emergency service for your own shop. This feature is for customers only.</p>
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
                    <p>Shop owners cannot request emergency services from other shops. This feature is for customers
                        only.</p>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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
        const ownerModal = new bootstrap.Modal(document.getElementById('ownerRestrictionModal'));
        const generalOwnerModal = new bootstrap.Modal(document.getElementById('generalOwnerRestrictionModal'));
        const closedModal = new bootstrap.Modal(document.getElementById('emergencyClosedModal'));

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
                const li = document.createElement('li');
                li.className = 'list-group-item text-center';
                li.textContent = 'Not specified.';
                listElement.appendChild(li);
            }
            closedModal.show();
        }

        function handleRequestClick(button) {
            const isOwnerOfThis = button.getAttribute('data-is-owner-of-this') === 'true';
            const isAnyOwner = button.getAttribute('data-is-any-owner') === 'true';
            const isOpen = button.getAttribute('data-is-open') === 'true';
            const shopSlug = button.getAttribute('data-shop-slug');
            const emergencyHours = JSON.parse(button.getAttribute('data-emergency-hours') || '[]');

            if (isOwnerOfThis) {
                ownerModal.show();
                return;
            }

            if (isAnyOwner) {
                generalOwnerModal.show();
                return;
            }

            if (isOpen) {
                window.location.href = 'emergency-requests.php?shop=' + shopSlug;
            } else {
                showEmergencyClosedModal(emergencyHours);
            }
        }
    </script>

</body>

</html>