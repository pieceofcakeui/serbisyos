<?php
require_once '../functions/auth.php';
include 'backend/save-shops.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Save Shops</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/save-shops.css">

</head>

<body>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
    <div class="save-shop-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="font-size: 1.5rem;">
                <i class="fas fa-bookmark me-2 text-dark"></i> Favorites Shops
            </h2>
        </div>

        <div class="shop-list">
            <?php if (!empty($savedShops)): ?>
                <?php foreach ($savedShops as $shop): ?>
                    <div class="horizontal-shop-card">
                        <div class="shop-logo-container">
                            <?php
                            $shop_logo = !empty($shop['shop_logo']) ? $shop['shop_logo'] : 'uploads/shop_logo/logo.jpg';
                            if (!str_starts_with($shop_logo, 'uploads/shop_logo/')) {
                                $shop_logo = 'uploads/shop_logo/' . $shop_logo;
                            }
                            if (!file_exists($shop_logo)) {
                                $shop_logo = 'uploads/shop_logo/logo.jpg';
                            }
                            ?>
                            <img src="<?= htmlspecialchars($shop_logo) ?>" alt="Shop Logo" class="shop-logo">
                        </div>
                        <div class="shop-details">
                            <div>
                                <div class="d-flex justify-content-between align-items-center">
                                   <a href="shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>" class="text-decoration-none text-dark">
                                    <h5 class="shop-name mb-0"><?= htmlspecialchars($shop['shop_name']) ?></h5>
                                   </a>
                                    <div class="shop-actions">
                                        <div class="dropdown">
                                            <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item remove-shop" href="#" data-shop-id="<?= $shop['id'] ?>">
                                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <span class="shop-location">
                                    <i class="fas fa-map-marker-alt text-warning me-1"></i>
                                    <?= !empty($shop['shop_location'])
                                        ? htmlspecialchars($shop['shop_location'])
                                        : htmlspecialchars(
                                            (!empty($shop['town_city']) ? $shop['town_city'] : '') .
                                                (!empty($shop['province']) ? ', ' . $shop['province'] : '') .
                                                (!empty($shop['country']) ? ', ' . $shop['country'] : '') .
                                                (!empty($shop['postal_code']) ? ', ' . $shop['postal_code'] : '')
                                        ) ?>
                                </span>

                                <div class="shop-rating">
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <span><?= round($shop['average_rating'], 1) ?></span>
                                    </div>
                                    <div class="rating-count">(<?= $shop['rating_count'] ?> reviews)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-shops-container">
                    <div class="no-shops-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <h3 class="text-muted">No Saved Shops Yet</h3>
                    <p class="text-muted">Start exploring and save your favorite auto repair shops for quick access later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>

    <div class="modal fade" id="removeShopModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <p class="fw-bold">Are you sure you want to remove this shop from your saved list?</p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger confirm-remove"
                            style="height: 39px; padding: 0 10px;">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="notification"></div>
    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/save-shops.js"></script>
    <script src="../assets/js/navbar.js"></script>

</body>

</html>