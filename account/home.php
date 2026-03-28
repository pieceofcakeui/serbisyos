<?php
require_once '../functions/auth.php';
include 'backend/security_helper.php';
include 'backend/emergency-modal.php';

function generate_stars($rating)
{
    if ($rating === null)
        return '';
    $stars_html = '';
    $rating = round($rating * 2) / 2;
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
    for ($i = 0; $i < $full_stars; $i++) {
        $stars_html .= '<i class="fas fa-star"></i>';
    }
    if ($half_star) {
        $stars_html .= '<i class="fas fa-star-half-alt"></i>';
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars_html .= '<i class="far fa-star"></i>';
    }
    return $stars_html;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serbisyos</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/home.css">
    <style>

    #page-header {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1020;
        transition: left 0.3s ease-in-out;
        background-color: var(--body-bg);
        border-bottom: 1px solid var(--border-color);
    }

    #page-header .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .main-content {
        transition: margin-left 0.3s ease-in-out;
        
    }
    #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--body-bg, #ffffff);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease;
        }

        #page-loader .spinner-container {
            text-align: center;
            color: var(--text-color, #333);
        }
</style>
</head>

<body>
<div id="page-loader">
        <div class="spinner-container">
            <i class="fas fa-spinner fa-spin fa-3x text-warning"></i>
            <p class="mt-3">Loading Serbisyos...</p>
        </div>
    </div>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="hero-home">
            <div class="container">
                <div class="hero-text">
                    <h1>Welcome to Serbisyos!</h1>
                    <p>A centralized directory for trusted auto repair. We connect you with the best local shops for all
                        your vehicle's needs.</p>
                    <div class="hero-cta-buttons d-flex justify-content-center gap-3">
                        <a href="service.php" class="btn btn-primary btn-lg hero-btn">Browse Services</a>
                        <a href="location_functions.php" class="btn btn-outline-light btn-lg hero-btn"
                            id="findShopNearMeBtn">Find a Shop Near Me</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="section section-light">
            <div class="container text-center">
                <h2 class="section-title">Quick Actions</h2>
                <p class="section-subtitle">Get started with our most popular features.</p>
                <div class="actions-grid">
                    <a href="service.php" class="action-item">
                        <i class="fas fa-tools"></i>
                        <h4>Browse Services</h4>
                    </a>
                    <a href="booking-provider.php" class="action-item">
                        <i class="fas fa-calendar-check"></i>
                        <h4>Make a Booking</h4>
                    </a>
                    <a href="emergency-provider.php" class="action-item">
                        <i class="fas fa-triangle-exclamation"></i>
                        <h4>Emergency Request Assistance</h4>
                    </a>
                </div>
            </div>
        </div>

        <div class="section section-neutral">
            <div class="container">
                <?php @include 'backend/featured-shops.php'; ?>
                <?php if (isset($result) && $result->num_rows > 0): ?>
                    <div class="text-center">
                        <h2 class="section-title">Featured Shops</h2>
                        <p class="section-subtitle">Check out some of the top-rated and most trusted shops in our network.
                        </p>
                    </div>
                    <div class="row d-flex align-items-stretch justify-content-center">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="shop-card h-100 d-flex flex-column align-items-center">
                                    <?php
                                    $default_logo_url = '../account/uploads/shop_logo/logo.jpg';
                                    $final_logo_path = $default_logo_url;

                                    if (!empty($row['shop_logo'])) {
                                        $logo_filename = $row['shop_logo'];

                                        $potential_logo_path = '../account/uploads/shop_logo/' . $logo_filename;

                                        if (file_exists($potential_logo_path)) {
                                            $final_logo_path = $potential_logo_path;
                                        }
                                    }
                                    ?>

                                    <img src="<?php echo htmlspecialchars($final_logo_path); ?>"
                                        alt="<?php echo htmlspecialchars($row['shop_name']); ?> Logo"
                                        onerror="this.onerror=null;this.src='https.placehold.co/100x100/1a1a1a/ffc107?text=S';">

                                    <?php
                                    $shop_id_for_badge = $row['id'];
                                    $topRated = false;
                                    $mostBooked = false;
                                    $average_rating = null;
                                    $rating_count = 0;
                                    if (isset($conn)) {
                                        $rating_stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(id) as rating_count FROM shop_ratings WHERE shop_id = ?");
                                        $rating_stmt->bind_param("i", $shop_id_for_badge);
                                        $rating_stmt->execute();
                                        $rating_result_data = $rating_stmt->get_result()->fetch_assoc();
                                        if ($rating_result_data && $rating_result_data['avg_rating'] !== null) {
                                            $average_rating = $rating_result_data['avg_rating'];
                                            $rating_count = $rating_result_data['rating_count'];
                                            if ($average_rating >= 4.0) {
                                                $topRated = true;
                                            }
                                        }
                                        $rating_stmt->close();
                                        $booking_stmt = $conn->prepare("SELECT COUNT(*) as total_completed FROM services_booking WHERE shop_id = ? AND booking_status = 'Completed'");
                                        $booking_stmt->bind_param("i", $shop_id_for_badge);
                                        $booking_stmt->execute();
                                        $booking_result_data = $booking_stmt->get_result()->fetch_assoc();
                                        if ($booking_result_data && $booking_result_data['total_completed'] >= 10) {
                                            $mostBooked = true;
                                        }
                                        $booking_stmt->close();
                                    }
                                    ?>

                                    <?php if ($topRated || $mostBooked): ?>
                                        <div class="badge-container">
                                            <?php if ($topRated): ?>
                                                <div class="shop-badge top-rated"><i class="fas fa-star me-1"></i> Top Rated</div>
                                            <?php endif; ?>
                                            <?php if ($mostBooked): ?>
                                                <div class="shop-badge top-booking"><i class="fas fa-calendar-check me-1"></i> Most
                                                    Booked</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <h5 class="mt-2"><?php echo htmlspecialchars($row['shop_name']); ?></h5>
                                    <p class="text-muted small flex-grow-1">
                                        <?php if (!empty($row['shop_location'])) {
                                            echo htmlspecialchars($row['shop_location']);
                                        } ?>
                                    </p>
                                    <div class="shop-rating">
                                        <?php if ($rating_count > 0): ?>
                                            <span class="rating-number"><?php echo number_format($average_rating, 1); ?></span>
                                            <span class="stars"><?php echo generate_stars($average_rating); ?></span>
                                            <span class="rating-count">(<?php echo $rating_count; ?>)</span>
                                        <?php else: ?>
                                            <span class="text-muted small">No ratings yet</span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="shop/<?php echo htmlspecialchars($row['shop_slug']); ?>" class="btn-view">View
                                        Details</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($conn))
                    $conn->close(); ?>
            </div>
        </div>

        <div class="section section-light">
            <div class="container text-center">
                <h2 class="section-title">Our Commitment to Quality</h2>
                <p class="section-subtitle">We're dedicated to providing you with a reliable and transparent car care
                    experience.</p>
                <div class="actions-grid">
                    <div class="action-item"><i class="fas fa-shield-alt"></i>
                        <h4>Verified Partners Only</h4>
                    </div>
                    <div class="action-item"><i class="fas fa-comments"></i>
                        <h4>Real Customer Reviews</h4>
                    </div>
                    <div class="action-item"><i class="far fa-calendar-check"></i>
                        <h4>Easy Booking</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="section section-neutral">
            <div class="container">
                <div class="two-col-layout">
                    <div class="tips-list">
                        <h2 class="section-title text-start">Vehicle Care Tips</h2>
                        <p class="section-subtitle text-start ms-0">Keep your car in top shape with these simple
                            maintenance tips.</p>
                        <div class="img-col d-lg-none mb-4">
                            <img src="../assets/img/partner/tip.webp" alt="Car Maintenance">
                        </div>
                        <div class="tip-item"><i class="fas fa-check-circle"></i>
                            <div>
                                <h5>Check Your Tire Pressure</h5>
                                <p>Properly inflated tires improve fuel efficiency and handling. Check them monthly.</p>
                            </div>
                        </div>
                        <div class="tip-item"><i class="fas fa-oil-can"></i>
                            <div>
                                <h5>Regular Oil Changes</h5>
                                <p>Follow your car's recommended schedule for oil changes to keep the engine healthy.
                                </p>
                            </div>
                        </div>
                        <div class="tip-item"><i class="fas fa-car-battery"></i>
                            <div>
                                <h5>Inspect Your Brakes</h5>
                                <p>Listen for any unusual noises and have your brakes checked regularly for safety.</p>
                            </div>
                        </div>
                        <div class="mt-4"><a href="more-vehicle-tips.php" class="btn-see-more">See More Tips</a></div>
                    </div>
                    <div class="img-col d-none d-lg-block"><img src="../assets/img/partner/tip.webp"
                            alt="Car Maintenance"></div>
                </div>
            </div>
        </div>

        <div class="section section-light">
            <div class="container text-center">
                <h2 class="section-title">Your Serbisyos Dashboard</h2>
                <p class="section-subtitle">Manage everything in one place. Here are some of the features you can access
                    as a registered user.</p>
                <div class="actions-grid dashboard-grid">
                    <div class="action-item">
                        <i class="fas fa-book-open"></i>
                        <h4>Manage Bookings</h4>
                    </div>
                    <div class="action-item">
                        <i class="fas fa-envelope-open-text"></i>
                        <h4>Message Shops</h4>
                    </div>
                    <div class="action-item">
                        <i class="fas fa-history"></i>
                        <h4>Track Service History</h4>
                    </div>
                    <div class="action-item">
                        <i class="fas fa-robot"></i>
                        <h4>AI ChatBot</h4>
                    </div>
                    <div class="action-item">
                        <i class="fas fa-bookmark"></i>
                        <h4>Save Favorite Shops</h4>
                    </div>
                    <div class="action-item">
                        <i class="fas fa-star"></i>
                        <h4>Write a Review</h4>
                    </div>
                </div>

            </div>
        </div>

        <div class="section section-neutral">
            <div class="container">
                <div class="two-col-layout">
                    <div class="join-network-content">
                        <h2 class="section-title text-start">Join Our Growing Network</h2>
                        <p class="section-subtitle text-start ms-0">Are you a shop owner in Iloilo? Partner with
                            Serbisyos to reach more customers, streamline your bookings, and grow your business. It's
                            free to apply!</p>
                        <div class="img-col d-lg-none mb-4">
                            <img src="../assets/img/partner/join.webp" alt="Mechanic working on a car">
                        </div>
                        <a href="become-a-partner.php" class="become-btn">Become a Partner</a>
                    </div>
                    <div class="img-col d-none d-lg-block">
                        <img src="../assets/img/partner/join.webp" alt="Mechanic working on a car">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="geolocationSpinner" class="full-page-overlay" style="display: none;">
        <div class="loading-indicator">
            <div class="spinner"></div>
            <p>Finding nearby shops...</p>
        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/modal-home.php'; ?>
    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"></script>

    <?php include 'include/toast.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/home.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const findNearbyBtn = document.getElementById('findShopNearMeBtn');
            if (findNearbyBtn) {
                findNearbyBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    handleFindNearbyClick();
                });
            }

            function handleFindNearbyClick() {
                document.getElementById('geolocationSpinner').style.display = 'flex';

                if (!navigator.geolocation) {
                    document.getElementById('geolocationSpinner').style.display = 'none';
                    toastr.error('Geolocation is not supported by your browser.');
                    return;
                }

                const geolocationOptions = {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                };

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        storeUserLocation(latitude, longitude);
                    },
                    function (error) {
                        document.getElementById('geolocationSpinner').style.display = 'none';
                        let errorMessage = 'Unable to get your location. ';

                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Please allow location access to find nearby shops. Check your browser settings.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Location information is unavailable. Please check your device settings.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Location request timed out. Please ensure a strong GPS or WiFi signal.';
                                break;
                            default:
                                errorMessage += 'An unknown error occurred.';
                        }

                        toastr.warning(errorMessage);
                        console.error('Geolocation Error:', error);
                    },
                    geolocationOptions
                );
            }

            function storeUserLocation(latitude, longitude) {
                const formData = new FormData();
                formData.append('latitude', latitude);
                formData.append('longitude', longitude);

                fetch('backend/store_nearby_location.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('geolocationSpinner').style.display = 'none';
                        if (data.success) {
                            window.location.href = 'search.php?nearby=1';
                        } else {
                            throw new Error(data.message || 'Failed to store location');
                        }
                    })
                    .catch(error => {
                        document.getElementById('geolocationSpinner').style.display = 'none';
                        console.error('Error storing location:', error);
                        toastr.error('Error: Could not save your location. Please try again.');
                    });
            }
        });
    </script>
<script>
        $(window).on('load', function () {
            $('#page-loader').fadeOut('slow', function () {
                $(this).remove();
            });
        });
    </script>
</body>

</html>