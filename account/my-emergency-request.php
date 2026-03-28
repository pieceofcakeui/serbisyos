<?php
require_once '../functions/auth.php';
include 'backend/my-emergency-request.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Emergency Request</title>
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
    <link rel="stylesheet" href="../assets/css/users/my-emergency-request.css">
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="my-emergency-request container py-3">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                <h2 class="fw-bold mb-0" style="font-size: 1.5rem;"><i class="fas fa-exclamation-triangle me-2"></i>My Emergency Request</h2>
            </div>

            <?php if (!empty($requests)): ?>
                <div class="row">
                    <?php foreach ($requests as $request):
                        $shop_logo = !empty($request['shop_logo']) ? $request['shop_logo'] : 'uploads/shop_logo/logo.jpg';
                        if (!str_starts_with($shop_logo, 'uploads/shop_logo/')) {
                            $shop_logo = 'uploads/shop_logo/' . $shop_logo;
                        }
                        if (!file_exists($shop_logo)) {
                            $shop_logo = 'uploads/shop_logo/logo.jpg';
                        }
                    ?>
                        <div class="col-12 mb-3">
                            <div class="request-card p-3">
                                <div class="row align-items-start g-3">
                                    <div class="col-12 col-lg-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <?php if ($request['shop_name']): ?>
                                                <div class="shop-logo-container flex-shrink-0 me-3">
                                                    <img src="<?= htmlspecialchars($shop_logo); ?>" alt="Shop Logo" class="shop-logo">
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-grow-1">
                                                <h5 class="request-title mb-1 fw-bold">
                                                    <?php if ($request['shop_name']): ?>
                                                        <p class="text-decoration-none text-dark">
                                                            <?= htmlspecialchars($request['shop_name']); ?>
                                                        </p>
                                                    <?php else: ?>
                                                        <span class="text-muted">Waiting for shop...</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <div class="request-meta small text-muted">
                                                    <div><i class="fas fa-clock me-1"></i> <?= htmlspecialchars(date('M d, Y h:i A', strtotime($request['created_at']))); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="request-meta small">
                                            <div class="location-info text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <span><?= htmlspecialchars($request['decrypted_address']); ?></span>
                                            </div>
                                            <?php if ($request['urgent']): ?>
                                                <div class="text-danger mt-1"><i class="fas fa-bolt me-1"></i><strong>URGENT</strong></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-5">
                                        <p class="mb-1"><strong>Vehicle:</strong> <?= htmlspecialchars($request['vehicle_type'] . ' - ' . $request['vehicle_model']); ?></p>
                                        <p class="mb-1"><strong>Issue:</strong> <?= htmlspecialchars($request['issue_description']); ?></p>

                                        <?php if (!empty($request['video'])): ?>
                                            <div class="expandable-section">
                                                <div class="video-summary">
                                                    <a href="#" class="see-more-link small"><i class="fas fa-video me-1"></i> View Attached Video</a>
                                                </div>
                                                <div class="expanded-content mt-2" style="display: none;">
                                                    <div class="video-container mb-2">
                                                        <div class="ratio ratio-16x9">
                                                            <video controls>
                                                                <source src="<?= htmlspecialchars($request['video']); ?>" type="video/mp4">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                        </div>
                                                    </div>
                                                    <a href="#" class="see-less-link small">Hide Video</a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-12 col-lg-3">
                                        <div class="d-flex flex-row flex-lg-column align-items-center align-items-lg-end justify-content-between justify-content-lg-center h-100">
                                            <div class="request-actions">
                                                <?php if (strtolower($request['status']) == 'pending'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-warning action-btn cancel-btn" data-request-id="<?= $request['id']; ?>">
                                                        <i class="fas fa-times me-1"></i> Cancel
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (strtolower($request['status']) == 'completed' || strtolower($request['status']) == 'cancelled' || strtolower($request['status']) == 'rejected'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger action-btn delete-btn" data-request-id="<?= $request['id']; ?>">
                                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 status-tracker-container">
                                        <?php
                                        $current_status = strtolower($request['status']);

                                        if ($current_status === 'cancelled') {
                                            echo '<div class="status-cancelled-message"><i class="fas fa-times-circle me-2"></i>This request was cancelled.</div>';
                                        } elseif ($current_status === 'rejected') {
                                            echo '<div class="status-rejected-message text-danger" style="text-align: center;"><i class="fas fa-exclamation-triangle me-2"></i>Sorry, the shop rejected your request. Please try requesting another shop.</div>';
                                        } elseif ($current_status === 'pending') {
                                            echo '<div class="status-message"><i class="fas fa-hourglass-half me-2"></i>Waiting for a shop to confirm your request.</div>';
                                        } else {
                                            $status_steps = [
                                                'confirmed' => ['label' => 'Confirmed', 'icon' => 'fas fa-user-check'],
                                                'servicing' => ['label' => 'Servicing', 'icon' => 'fas fa-tools'],
                                                'completed' => ['label' => 'Completed', 'icon' => 'fas fa-flag-checkered']
                                            ];

                                            $status_order = ['confirmed', 'servicing', 'completed'];
                                            $current_index = array_search($current_status, $status_order);

                                            echo '<ul class="status-tracker">';
                                            foreach ($status_steps as $key => $step) {
                                                $step_index = array_search($key, $status_order);
                                                $class = '';
                                                if ($step_index < $current_index || $current_status === 'completed') {
                                                    $class = 'completed';
                                                } elseif ($step_index == $current_index) {
                                                    $class = 'active';
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
                    <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                    <h4 class="mb-2">No Emergency Request Found</h4>
                    <p class="text-muted mb-3">You haven't made any emergency requests yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade confirmation-modal" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-icon text-danger"> <i class="fas fa-exclamation-circle"></i> </div>
                    <h5 class="mb-3">Are you sure you want to remove this request?</h5>
                    <div class="modal-buttons">
                        <button type="button" class="btn modal-btn btn-secondary" data-bs-dismiss="modal"  style="height: 38px; padding: 0 10px;">Cancel</button>
                        <form method="post">
                            <input type="hidden" name="request_id" id="deleteRequestId">
                            <button type="submit" name="delete_request" class="btn modal-btn btn-danger"  style="height: 38px; padding: 0 10px;">Remove</button>
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
                    <div class="modal-icon text-warning"> <i class="fas fa-exclamation-triangle"></i> </div>
                    <h5 class="mb-3">Cancel this request?</h5>
                    <div class="modal-buttons">
                        <button type="button" class="btn modal-btn btn-secondary" data-bs-dismiss="modal"  style="height: 38px; padding: 0 10px;">Go Back</button>
                        <form method="post">
                            <input type="hidden" name="request_id" id="cancelRequestId">
                            <button type="submit" name="cancel_request" class="btn modal-btn btn-warning"  style="height: 38px; padding: 0 10px;">Confirm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelConfirmationModal = new bootstrap.Modal(document.getElementById('cancelConfirmationModal'));
            const deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            document.querySelectorAll('.cancel-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const requestId = this.getAttribute('data-request-id');
                    document.getElementById('cancelRequestId').value = requestId;
                    cancelConfirmationModal.show();
                });
            });
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const requestId = this.getAttribute('data-request-id');
                    document.getElementById('deleteRequestId').value = requestId;
                    deleteConfirmationModal.show();
                });
            });
            document.querySelectorAll('.see-more-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const expandableSection = this.closest('.expandable-section');
                    const expandedContent = expandableSection.querySelector('.expanded-content');
                    this.style.display = 'none';
                    expandedContent.style.display = 'block';
                });
            });
            document.querySelectorAll('.see-less-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const expandedContent = this.closest('.expanded-content');
                    const section = this.closest('.expandable-section');
                    const seeMoreLink = section.querySelector('.see-more-link');
                    expandedContent.style.display = 'none';
                    if (seeMoreLink) {
                        seeMoreLink.style.display = 'inline';
                    }
                });
            });
        });
    </script>
</body>

</html>