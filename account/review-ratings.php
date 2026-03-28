<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/review-ratings.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($shop_name); ?> - Reviews</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/review-and-ratings.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
</head>

<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="reviews-section">
            <div class="reviews-card">
                <a href="manage-shop.php" class="custom-review-back"
                    style="text-decoration: none; background-color: transparent; outline: none;">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>

                <div class="section-header">
                    <div class="d-flex align-items-center">
                        <h1 class="section-title">Customer Reviews</h1>
                    </div>
                    <div>
                        <select class="form-select" id="sortReviews">
                            <option selected value="recent">Most Recent</option>
                            <option value="highest">Highest Rating</option>
                            <option value="lowest">Lowest Rating</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-4 g-4">
                    <div class="col-md-6">
                        <div class="card stats-card text-center h-100">
                            <div class="card-body py-4">
                                <div class="rating-circle"><?php echo number_format($average_rating, 1); ?></div>
                                <h4 class="text-center mb-3">Overall Rating</h4>
                                <div class=" text-center stars mb-2">
                                    <?php
                                    $full_stars = floor($average_rating);
                                    $half_star = ($average_rating - $full_stars) >= 0.5;
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $full_stars) {
                                            echo '<i class="fas fa-star"></i>';
                                        } elseif ($half_star && $i == $full_stars + 1) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <p class="text-center text-muted mb-0">Based on <?php echo $total_reviews; ?> customer
                                    reviews</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stats-card h-100">
                            <div class="card-body py-4">
                                <h4 class="mb-3">Rating Distribution</h4>
                                <?php
                                for ($i = 5; $i >= 1; $i--) {
                                    $count = $rating_distribution[$i] ?? 0;
                                    $percentage = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
                                    echo '
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-2" style="width: 80px;">
                                        <span class="text-muted">' . $i . '</span> <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <div class="progress flex-grow-1">
                                        <div class="progress-bar bg-warning star-progress" style="width: ' . $percentage . '%"></div>
                                    </div>
                                    <div class="ms-2 text-muted" style="width: 40px; text-align: right;">' . $count . '</div>
                                </div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reviews-list mb-4" id="reviewsList">
                    <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                        <?php while ($review = $reviews_result->fetch_assoc()): ?>
                            <div class="card review-card mb-1" data-rating="<?php echo $review['rating']; ?>"
                                data-date="<?php echo strtotime($review['created_at']); ?>"
                                data-review-id="<?php echo $review['id']; ?>">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex">
                                            <?php
                                            $profile_pic = !empty($review['profile_picture']) ? htmlspecialchars($review['profile_picture']) : 'profile-user.png';
                                            if (strpos($profile_pic, 'http') !== 0 && !file_exists('../assets/img/profile/' . $profile_pic)) {
                                                $profile_pic = 'profile-user.png';
                                            }
                                            ?>
                                            <img src="../assets/img/profile/<?php echo $profile_pic; ?>" alt="User"
                                                class="user-avatar me-2">
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($review['fullname']); ?></h6>
                                                <span
                                                    class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="stars">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $review['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <p class="mt-1 mb-1"><?php echo htmlspecialchars($review['comment']); ?></p>

                                    <?php if (!empty($review['owner_response'])): ?>
                                        <div class="response-card p-2 mt-2" style="background-color: #f0f0f0; border-radius: 5px;">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-reply me-1 text-dark"></i>
                                                <strong><?php echo htmlspecialchars($shopname); ?></strong>
                                                <span
                                                    class="ms-1 text-muted"><?php echo date('n/j/y', strtotime($review['response_date'])); ?></span>
                                                <?php if ($_SESSION['user_id'] == $shop['user_id']): ?>
                                                    <div class="ms-2 response-actions">
                                                        <button class="btn-edit-response btn-sm"
                                                            data-review-id="<?php echo $review['id']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-0 response-text"><?php echo htmlspecialchars($review['owner_response']); ?></p>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($_SESSION['user_id'] == $shop['user_id']): ?>
                                            <button class="respond-btn btn-sm mt-1 fs-7 custom-respond-btn" style="color: #333; border: 1px solid #333;"
                                                data-review-id="<?php echo $review['id']; ?>">
                                                <i class="fas fa-reply" style="margin-right: 4px; color: #333;"></i>Respond to review
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-reviews">
                            <div class="py-5 text-center">
                                <i class="far fa-comment-alt fa-3x text-muted mb-3"></i>
                                <h4>No Reviews Yet</h4>
                                <p class="text-muted"><?php echo htmlspecialchars($shopname); ?> doesn't have any reviews yet.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($total_reviews > 5): ?>
                    <div class="text-center mt-4" id="loadMoreContainer">
                        <div class="d-flex flex-column align-items-center">
                            <button id="loadMoreBtn" class="loadMore-btn btn-warning px-4 mb-2 bg-warning">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span class="btn-text">Load More Reviews</span>
                            </button>

                            <button id="seeLessBtn" class="seeLess-btn px-4 d-none"
                                style="background-color: transparent; border: none;">
                                <span class="text-dark">See Less</span>
                            </button>

                        </div>
                        <div class="mt-2 text-muted" id="reviewsCounter">
                            Showing <?php echo min(5, $total_reviews); ?> of <?php echo $total_reviews; ?> reviews
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>

        <div id="shopData" data-shop-data='<?php
                                            echo json_encode([
                                                'total_reviews' => $total_reviews,
                                                'shop_id' => $shop_id,
                                                'shop_owner_id' => $shop['user_id'],
                                                'user_id' => $_SESSION['user_id']
                                            ]);
                                            ?>'>
        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>


    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script src="../assets/js/review-and-rating.js"></script>

</body>

</html>