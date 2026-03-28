<?php
require_once '../functions/auth.php';
include 'backend/my-booking.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/my-booking.css">

</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="my-booking container py-3">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                <h2 class="fw-bold mb-0" style="font-size: 1.5rem;"><i class="fas fa-calendar-alt me-2"></i>My Bookings</h2>
            </div>

            <?php if (!empty($bookings)): ?>
                <div class="row">
                    <?php foreach ($bookings as $booking):
                        $shop_logo = !empty($booking['shop_logo']) ? $booking['shop_logo'] : 'uploads/shop_logo/logo.jpg';
                        if (!str_starts_with($shop_logo, 'uploads/shop_logo/')) {
                            $shop_logo = 'uploads/shop_logo/' . $shop_logo;
                        }
                        if (!file_exists($shop_logo)) {
                            $shop_logo = 'uploads/shop_logo/logo.jpg';
                        }
                    ?>
                        <div class="col-12 mb-3">
                            <div class="booking-card p-3">
                                <div class="row align-items-center g-3">
                                    <div class="col-12 col-lg-4">
                                        <div class="d-flex align-items-center">
                                            <div class="shop-logo-container flex-shrink-0 me-3">
                                                <img src="<?= htmlspecialchars($shop_logo); ?>" alt="Shop Logo" class="shop-logo">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="booking-title mb-1 fw-bold">
                                                    <p class="text-decoration-none text-dark">
                                                        <?= htmlspecialchars($booking['shop_name'] ?? 'Shop not available'); ?>
                                                    </p>
                                                </h5>
                                                <div class="booking-meta small text-muted">
                                                    <i class="fas fa-calendar-day me-1"></i>
                                                    <?php
                                                    $parts = explode(' ', $booking['preferred_datetime']);
                                                    $day = $parts[0];
                                                    $startTime = $parts[1] . ' ' . $parts[2];
                                                    $endTime = $parts[4] . ' ' . $parts[5];
                                                    echo htmlspecialchars("$day, $startTime - $endTime");
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-5">
                                        <div class="row g-2">
                                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                                <p class="mb-1"><strong>Service:</strong>
                                                    <?= htmlspecialchars(trim($booking['service_type'], '[]"\'')); ?>
                                                </p>
                                                <p class="mb-1 mb-md-0"><strong>Notes:</strong>
                                                    <?= !empty($booking['customer_notes']) ? htmlspecialchars($booking['customer_notes']) : 'None'; ?>
                                                </p>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                                <p class="mb-1"><strong>Vehicle:</strong>
                                                    <?= htmlspecialchars($booking['vehicle_type'] . ' ' . $booking['vehicle_year'] . ' ' . $booking['vehicle_make'] . ' ' . $booking['vehicle_model']); ?>
                                                </p>
                                                <div class="mb-0"><strong>Issues:</strong>
                                                    <?php
                                                    $issues = !empty($booking['vehicle_issues']) ? htmlspecialchars($booking['vehicle_issues']) : 'None specified';
                                                    if (strlen($booking['vehicle_issues']) > 100): ?>
                                                        <span class="long-text collapsed"><?= $issues ?></span>
                                                        <span class="see-more-btn">See More</span>
                                                    <?php else: ?>
                                                        <?= $issues ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-3">
                                        <div class="d-flex flex-row flex-lg-column align-items-center align-items-lg-end justify-content-between justify-content-lg-center h-100">
                                            <div class="booking-actions">
                                                <?php if (strtolower($booking['booking_status']) == 'pending'): ?>
                                                    <button type="button" class="btn btn-sm btn-warning action-btn cancel-btn" data-booking-id="<?= $booking['id']; ?>">
                                                        <i class="fas fa-times me-1"></i> Cancel
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (strtolower($booking['booking_status']) == 'completed' || strtolower($booking['booking_status']) == 'cancelled' || strtolower($booking['booking_status']) == 'rejected' || strtolower($booking['booking_status']) == 'reject'): ?>
                                                    <button type="button" class="btn btn-sm btn-danger action-btn delete-btn" data-booking-id="<?= $booking['id']; ?>">
                                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 status-tracker-container">
                                        <?php
                                        $current_status = strtolower($booking['booking_status']);

                                        if ($current_status === 'cancelled') {
                                            echo '<div class="status-cancelled-message"><i class="fas fa-times-circle me-2"></i>This booking was cancelled.</div>';
                                        } elseif ($current_status === 'reject' || $current_status === 'rejected') {
                                            echo '<div class="status-rejected-message"><i class="fas fa-ban me-2"></i>Sorry, the shop rejected your booking.</div>';
                                        } elseif ($current_status === 'pending') {
                                            echo '<div class="status-message"><i class="fas fa-hourglass-half me-2"></i>Waiting for the shop to confirm this schedule.</div>';
                                        } else {
                                            $status_steps = [
                                                'confirmed' => ['label' => 'Booking Confirmed', 'icon' => 'fas fa-calendar-check'],
                                                'servicing' => ['label' => 'In Service', 'icon' => 'fas fa-cogs'],
                                                'completed' => ['label' => 'Completed', 'icon' => 'fas fa-car']
                                            ];

                                            echo '<ul class="status-tracker">';
                                            $is_completed = ($current_status === 'completed');

                                            foreach ($status_steps as $key => $step) {
                                                $class = '';
                                                if ($is_completed) {
                                                    $class = 'completed';
                                                } else {
                                                    if ($key === 'confirmed') {
                                                        $class = 'active';
                                                    }
                                                }

                                                echo '<li class="status-step ' . $class . '">';
                                                echo '<div class="step-icon"><i class="' . $step['icon'] . '"></i></div>';
                                                echo '<div class="step-label">' . $step['label'] . '</div>';
                                                echo '</li>';
                                            }
                                            echo '</ul>';
                                        }
                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 bg-light rounded-3">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h4 class="mb-2">No Bookings Found</h4>
                    <p class="text-muted mb-3">You haven't made any bookings yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <div class="modal fade confirmation-modal" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-icon text-danger">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h5 class="mb-3">Are you sure you want to remove this booking?</h5>
                    <div class="modal-buttons">
                        <button type="button" class="btn modal-btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <form method="post">
                            <input type="hidden" name="booking_id" id="deleteBookingId">
                            <button type="submit" name="delete_booking" class="btn modal-btn btn-danger"
                                style="height: 40px; padding: 0 10px;">Remove</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade confirmation-modal" id="cancelConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-icon text-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="mb-3">Cancel this booking?</h5>
                    <p class="text-muted">You can contact the shop if you need to reschedule.</p>
                    <div class="modal-buttons">
                        <button type="button" class="btn modal-btn btn-secondary" data-bs-dismiss="modal">Go
                            Back</button>
                        <form method="post">
                            <input type="hidden" name="booking_id" id="cancelBookingId">
                            <button type="submit" name="cancel_booking" class="btn modal-btn btn-warning"
                                style="height: 40px; padding: 0 10px;">Confirm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/my-booking.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script>
        $(document).on('click', '.see-more-btn', function() {
            const $textContainer = $(this).prev('.long-text');
            const isCollapsed = $textContainer.hasClass('collapsed');

            $textContainer.toggleClass('collapsed expanded');
            $(this).text(isCollapsed ? 'See Less' : 'See More');
        });
    </script>
</body>

</html>