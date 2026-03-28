<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/security_helper.php';
include 'backend/utilities.php';
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (isset($_SESSION['notification_tracking'])) {
    $_SESSION['notification_tracking']['last_viewed_count'] = $_SESSION['notification_tracking']['current_count'];
}

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

try {
    $brevoApiKey = $_ENV['BREVO_API_KEY'];
    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $brevoApiKey);
    $apiInstance = new TransactionalEmailsApi(new Client(), $config);
    $brevoClient = $apiInstance;
} catch (Exception $e) {
    error_log("Brevo initialization failed: " . $e->getMessage());
    $brevoClient = null;
}

function createShopNotificationIfNotExists($conn, $user_id, $shop_id, $notification_type)
{
    $query = "SELECT id FROM notifications WHERE user_id = ? AND shop_id = ? AND notification_type = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $user_id, $shop_id, $notification_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return false;
    } else {
        $insert_query = "INSERT INTO notifications (user_id, shop_id, notification_type, is_read, created_at) VALUES (?, ?, ?, 0, NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iis", $user_id, $shop_id, $notification_type);
        return $insert_stmt->execute();
    }
}


function clearOldNotifications($conn, $user_id, $notification_type)
{
    $query = "DELETE FROM notifications WHERE user_id = ? AND notification_type = ? AND is_read = FALSE AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $notification_type);
    return $stmt->execute();
}

$current_user_id_for_email = $_SESSION['user_id'] ?? 0;
if ($current_user_id_for_email) {

    $user_query = "SELECT id, email, full_address, latitude, longitude, status, email_notifications FROM users WHERE id = ? AND latitude IS NOT NULL AND longitude IS NOT NULL LIMIT 1";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $current_user_id_for_email);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();

    if ($user && (!isset($user['status']) || $user['status'] === 'verified')) {
        $user_town = '';
        $user_province = '';
        if (!empty($user['full_address'])) {
            $address_parts = array_map('trim', explode(',', $user['full_address']));
            if (count($address_parts) >= 3) {
                $user_town = $address_parts[1];
                $user_province = $address_parts[2];
            } elseif (count($address_parts) == 2) {
                $user_town = $address_parts[0];
                $user_province = $address_parts[1];
            }
        }

        $shops_query = "SELECT sa.id, sa.shop_name, sa.shop_logo, sa.shop_location, sa.latitude, sa.longitude, u.id as user_id FROM shop_applications sa JOIN users u ON sa.user_id = u.id WHERE sa.status = 'Approved'";
        $shops_result = $conn->query($shops_query);
        $all_shops = $shops_result->fetch_all(MYSQLI_ASSOC);
        $max_distance = 20;

        $nearby_shops = [];
        $province_shops = [];
        $shops_to_display = [];
        $notification_type = '';

        foreach ($all_shops as $shop) {
            if ($shop['user_id'] == $user['id']) continue;
            if (isset($user['latitude'], $user['longitude'], $shop['latitude'], $shop['longitude'])) {
                if (!empty($user_town) && stripos($shop['shop_location'], $user_town) !== false) {
                    $distance = calculateDistance($user['latitude'], $user['longitude'], $shop['latitude'], $shop['longitude']);
                    if ($distance <= $max_distance) {
                        $nearby_shops[] = $shop;
                    }
                }
            }
        }

        if (empty($nearby_shops)) {
            $notification_type = 'province';
            foreach ($all_shops as $shop) {
                if ($shop['user_id'] == $user['id']) continue;
                if (!empty($user_province) && isset($shop['shop_location']) && stripos($shop['shop_location'], $user_province) !== false) {
                    $province_shops[] = $shop;
                }
            }
            $shops_to_display = array_slice($province_shops, 0, 10);
        } else {
            $notification_type = 'nearby';
            $shops_to_display = $nearby_shops;
        }


        if (!empty($shops_to_display)) {
            $new_notifications_sent = false;
            foreach ($shops_to_display as $shop) {
                if (createShopNotificationIfNotExists($conn, $user['id'], $shop['id'], $notification_type)) {
                    $new_notifications_sent = true;
                }
            }

            if ($new_notifications_sent && $brevoClient !== null && isset($user['email_notifications']) && $user['email_notifications'] == 1) {
                $shops_to_email = array_filter($shops_to_display, function ($shop) use ($user) {
                    return $shop['user_id'] != $user['id'];
                });

                if (!empty($shops_to_email)) {
                    $shops_html_list = '';
                    foreach ($shops_to_email as $shop) {
                        $logo_html = '<div class="shop-placeholder">🔧</div>';
                        if (!empty($shop['shop_logo'])) {
                            $logo_html = '<img src="' . htmlspecialchars($shop['shop_logo']) . '" alt="' . htmlspecialchars($shop['shop_name']) . '" class="shop-image">';
                        }

                        $shops_html_list .= '
                                <div class="shop-card">
                                    ' . $logo_html . '
                                    <div class="shop-info">
                                        <div class="shop-name">' . htmlspecialchars($shop['shop_name']) . '</div>
                                        <div class="shop-location">' . htmlspecialchars($shop['shop_location']) . '</div>
                                    </div>
                                </div>';
                    }

                    $email_title = ($notification_type == 'nearby') ? 'Auto Shops Near You' : 'Recommended Shops in Your Province';
                    $email_heading = ($notification_type == 'nearby') ? 'Shops Near Your Location' : 'Recommended Shops in Your Province';
                    $email_intro_p = ($notification_type == 'nearby') ? 'We found these auto shops near your current location:' : 'Here are some recommended auto shops in your province:';

                    $html_content = <<<HTML
                    <!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{$email_title}</title><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333;margin:0;padding:0}.container{max-width:600px;margin:0 auto;padding:20px}.header{background-color:#e74c3c;color:white;padding:20px;text-align:center}.content{padding:20px}.shop-grid{display:grid;grid-template-columns:1fr;gap:15px;margin-top:20px}.shop-card{border:1px solid #ddd;border-radius:8px;padding:15px;display:flex;align-items:center;background-color:#f9f9f9}.shop-image{width:60px;height:60px;object-fit:cover;border-radius:8px;margin-right:15px;border:1px solid #ddd}.shop-placeholder{width:60px;height:60px;background-color:#f0f0f0;border-radius:8px;margin-right:15px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#999}.shop-info{flex:1}.shop-name{font-size:16px;font-weight:bold;margin-bottom:5px}.shop-location{font-size:14px;color:#666;margin-bottom:5px}.footer{margin-top:30px;text-align:center;font-size:14px;color:#666;padding:20px;border-top:1px solid #eee}</style></head><body><div class="container"><div class="header"><h1>{$email_title}</h1></div><div class="content"><h2>{$email_heading}</h2><p>{$email_intro_p}</p><div class="shop-grid">{$shops_html_list}</div><p style="margin-top:20px">You can view more details about these shops by logging into your account.</p></div><div class="footer"><p>© 2025 Serbisyos. All rights reserved.</p><p>If you no longer wish to receive these notifications, you can update your preferences in your account settings.</p></div></div></body></html>
HTML;
                    try {
                        $sendSmtpEmail = new SendSmtpEmail([
                            'to' => [['email' => $user['email'], 'name' => 'User']],
                            'subject' => $email_title,
                            'htmlContent' => $html_content,
                            'sender' => ['name' => 'Serbisyos', 'email' => 'sayrelle81@gmail.com']
                        ]);
                        $brevoClient->sendTransacEmail($sendSmtpEmail);
                    } catch (Exception $e) {
                        error_log("Email sending error for " . $user['email'] . ": " . $e->getMessage());
                    }
                }
            }
        }
    }
}

$user_id = $_SESSION['user_id'] ?? 0;
$all_notifications = [];

function getAllNotificationsForUser($conn, $user_id)
{
    if (!$user_id) {
        return [];
    }

    $query = "
        SELECT * FROM (
            SELECT
                n.id, n.is_read, n.created_at AS timestamp, n.notification_type, n.status, n.shop_id, n.related_id,
                sa_info.shop_name, sa_info.shop_logo, sa_info.shop_location, sa_info.shop_slug, sa_info.user_id as shop_user_id,
                sb.id AS booking_id, sb.booking_status, sb.service_type,
                er.id AS emergency_id, er.status AS emergency_status, er.vehicle_type, er.vehicle_model,
                sapp.id AS application_id, sapp.status AS application_status, sapp.shop_name AS application_shop_name, sapp.approved_at,
                vs.id AS verification_id, vs.status AS verification_status, COALESCE(vs.verification_date, vs.submission_date) as verification_timestamp,
                ROW_NUMBER() OVER(
                    PARTITION BY n.notification_type, n.related_id 
                    ORDER BY n.created_at DESC
                ) as rn
            FROM notifications n
            LEFT JOIN shop_applications sa_info ON n.shop_id = sa_info.id
            LEFT JOIN services_booking sb ON n.related_id = sb.id AND n.notification_type LIKE 'booking%'
            LEFT JOIN emergency_requests er ON n.related_id = er.id AND n.notification_type LIKE 'emergency%'
            LEFT JOIN shop_applications sapp ON n.related_id = sapp.id AND n.notification_type LIKE 'application%'
            LEFT JOIN verification_submissions vs ON n.related_id = vs.id AND n.notification_type LIKE 'verification%'
            WHERE n.user_id = ? AND (n.delete_notification IS NULL OR n.delete_notification = 0)
        ) AS ranked_notifications
        WHERE rn = 1
        ORDER BY timestamp DESC;
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$is_shop_owner = false;
if ($user_id) {
    $all_notifications = getAllNotificationsForUser($conn, $user_id);
    $owner_check_stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ? AND status = 'Approved' LIMIT 1");
    $owner_check_stmt->bind_param("i", $user_id);
    $owner_check_stmt->execute();
    $owner_result = $owner_check_stmt->get_result();
    if ($owner_result->num_rows > 0) {
        $is_shop_owner = true;
    }
    $owner_check_stmt->close();
}

$has_any_notifications = !empty($all_notifications);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
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
    <link rel="stylesheet" href="../assets/css/users/notification.css">
</head>

<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="notification-container container py-3">
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bell me-2"></i>
                        <span class="h5 mb-0 text-nowrap">All Notifications</span>
                    </div>

                    <?php if ($has_any_notifications) : ?>
                        <div class="dropdown">
                            <button class="btn btn-link text-dark p-0" type="button" id="notificationMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationMenuButton">
                                <li>
                                    <a id="markAllAsRead" class="dropdown-item" href="#">
                                        <i class="fas fa-check-circle fa-sm me-2"></i>Mark All as Read
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <hr style="border: none; border-top: 1px solid #ccc;">

            <div class="notification-list">
                <?php if ($has_any_notifications) : ?>
                    <?php foreach ($all_notifications as $notification) : ?>
                        <?php
                        $is_read = $notification['is_read'];
                        $unread_class = !$is_read ? 'unread' : '';
                        $notification_type = $notification['notification_type'];
                        $default_logo = 'uploads/shop_logo/logo.jpg';

                        switch (true) {
                            case in_array($notification_type, ['nearby', 'province']):
                                if (isset($notification['shop_user_id']) && $notification['shop_user_id'] == $_SESSION['user_id']) continue 2;
                                $shop_logo = $notification['shop_logo'] ?? 'logo.jpg';
                                $logo_path = 'uploads/shop_logo/' . $shop_logo;
                        ?>
                                <div class="notification-item <?php echo $unread_class; ?>" data-notification-id="<?php echo $notification['id']; ?>" data-shop-id="<?php echo $notification['shop_id']; ?>" data-notification-type="shops">
                                    <div class="notification-content">
                                        <div class="notification-header">
                                            <img src="<?php echo file_exists($logo_path) ? $logo_path : $default_logo; ?>" alt="<?php echo htmlspecialchars($notification['shop_name']); ?>" class="shop-logo" onerror="this.src='<?php echo $default_logo; ?>'">
                                            <div class="notification-text">
                                                <div class="notification-message">New <?php echo ($notification['notification_type'] == 'nearby' ? 'nearby' : 'recommended'); ?> shop: <?php echo htmlspecialchars($notification['shop_name']); ?></div>
                                                <div class="notification-meta">
                                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($notification['shop_location']); ?></span>
                                                    <span class="notification-time"><i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($notification['timestamp'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ellipsis-dropdown">
                                            <button class="btn btn-menu"><i class="fas fa-ellipsis-v"></i></button>
                                            <div class="dropdown-content">
                                                <a href="#" class="dropdown-item" onclick="showDeleteModal(this, event)"><i class="fas fa-trash-alt"></i> Delete</a>
                                                <a href="#" class="dropdown-item" onclick="shareNotification(this, event)"><i class="fas fa-share"></i> Share</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        <a href="/serbisyos/shops/<?php echo htmlspecialchars($notification['shop_slug']); ?>" class="btn btn-sm btn-warning">View Details</a>
                                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($notification['shop_location']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary text-dark"><i class="fas fa-directions me-1 text-dark"></i> Directions</a>
                                    </div>
                                </div>
                            <?php
                                break;

                          case str_starts_with($notification_type, 'booking'):
    $shop_logo = $notification['shop_logo'] ?? 'logo.jpg';
    $logo_path = 'uploads/shop_logo/' . $shop_logo;
    $booking_status = trim($notification['status'] ?? '');
    $status_class = strtolower($booking_status);
    $current_user_id = $_SESSION['user_id'] ?? 0;
    $is_owner_recipient = isset($notification['shop_user_id']) && (int)$notification['shop_user_id'] === (int)$current_user_id;

    if ($notification_type === 'booking_received') {
        $message = "You have a new booking request that is <span class='fw-semibold text-uppercase {$status_class}'>" . htmlspecialchars($booking_status) . "</span>";
    } elseif ($notification_type === 'booking_cancelled') {
        if ($is_owner_recipient) {
            $message = "A customer cancelled a booking with <span class='fw-semibold'>" . htmlspecialchars($notification['shop_name']) . "</span>";
        } else {
            $message = "You cancelled your booking with <span class='fw-semibold'>" . htmlspecialchars($notification['shop_name']) . "</span>";
        }
    } elseif ($notification_type === 'booking_accepted') {
        $message = "Your booking with " . htmlspecialchars($notification['shop_name']) . " has been <span class='fw-semibold text-uppercase {$status_class}'>ACCEPTED</span>";
    } elseif ($notification_type === 'booking_rejected') {
        $message = "Your booking with " . htmlspecialchars($notification['shop_name']) . " has been <span class='fw-semibold text-uppercase {$status_class}'>REJECTED</span>";
    } elseif ($notification_type === 'booking_completed') {
        $message = "Your booking with " . htmlspecialchars($notification['shop_name']) . " has been <span class='fw-semibold text-uppercase {$status_class}'>COMPLETED</span>";
    } else {
        $message = "Your booking with " . htmlspecialchars($notification['shop_name']) . " has been <span class='fw-semibold text-uppercase {$status_class}'>" . htmlspecialchars($booking_status) . "</span>";
    }
?>
                                <div class="notification-item <?php echo $unread_class; ?>" data-notification-id="<?php echo $notification['id']; ?>" data-booking-id="<?php echo $notification['related_id']; ?>" data-notification-type="booking">
                                    <div class="notification-content">
                                        <div class="notification-header">
                                            <img src="<?php echo file_exists($logo_path) ? $logo_path : $default_logo; ?>" alt="Shop Logo" class="shop-logo-img" onerror="this.src='<?php echo $default_logo; ?>'">
                                            <div class="notification-text">
                                                <div class="notification-message d-flex flex-wrap align-items-center gap-1"><?php echo $message; ?></div>
                                                <div class="notification-meta">
                                                    <span>Service: <?php echo trim(htmlspecialchars($notification['service_type']), '[]'); ?></span>
                                                    <span class="notification-time"><i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($notification['timestamp'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ellipsis-dropdown">
                                            <button class="btn btn-menu"><i class="fas fa-ellipsis-v"></i></button>
                                            <div class="dropdown-content">
                                                <a href="#" class="dropdown-item" onclick="showDeleteModal(this, event)"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        <?php if ($is_shop_owner && $notification_type === 'booking_received') : ?>
                                            <a href="booking.php" class="btn btn-sm btn-success">View Bookings</a>
                                        <?php else : ?>
                                            <button class="btn btn-sm btn-warning view-booking-details" data-booking-id="<?php echo $notification['related_id']; ?>">View Details</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php
                                break;

                            case str_starts_with($notification_type, 'emergency'):
                                $shop_logo = $notification['shop_logo'] ?? 'logo.jpg';
                                $logo_path = 'uploads/shop_logo/' . $shop_logo;
                                $status = strtolower(trim($notification['status']));

                                if ($notification_type === 'emergency_received') {
                                    $message = "You have a new emergency request that is <span class='fw-semibold text-uppercase " . $status . "'>" . htmlspecialchars($status) . "</span>";
                                } else {
                                    $message = "Your emergency request to " . htmlspecialchars($notification['shop_name']) . " has been <span class='fw-semibold text-uppercase " . $status . "'>" . htmlspecialchars($status) . "</span>";
                                }
                            ?>
                                <div class="notification-item <?php echo $unread_class; ?>" data-notification-id="<?php echo $notification['id']; ?>" data-emergency-id="<?php echo $notification['related_id']; ?>" data-notification-type="emergency">
                                    <div class="notification-content">
                                        <div class="notification-header">
                                            <img src="<?php echo file_exists($logo_path) ? $logo_path : $default_logo; ?>" alt="Shop Logo" class="shop-logo-img" onerror="this.src='<?php echo $default_logo; ?>'">
                                            <div class="notification-text">
                                                <div class="notification-message d-flex flex-wrap align-items-center gap-1"><?php echo $message; ?></div>
                                                <div class="notification-meta">
                                                    <span><?php echo htmlspecialchars(($notification['vehicle_type']) . ' - ' . ($notification['vehicle_model'])); ?></span>
                                                    <span class="notification-time"><i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($notification['timestamp'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ellipsis-dropdown">
                                            <button class="btn btn-menu"><i class="fas fa-ellipsis-v"></i></button>
                                            <div class="dropdown-content">
                                                <a href="#" class="dropdown-item" onclick="showDeleteModal(this, event)"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        <?php if ($is_shop_owner && $notification_type === 'emergency_received') : ?>
                                            <a href="emergency-request.php" class="btn btn-sm btn-danger">View Request</a>
                                        <?php else : ?>
                                            <button class="btn btn-sm btn-primary view-emergency-details" data-emergency-id="<?php echo $notification['related_id']; ?>">View Request</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php
                                break;

                            case str_starts_with($notification_type, 'application'):
                                $status = strtolower($notification['status']);
                                $message = "Your shop application for <strong>" . htmlspecialchars($notification['application_shop_name']) . "</strong> ";
                                if ($status == 'approved') {
                                    $message .= "has been <span class='fw-semibold text-uppercase text-success'>Approved</span>.";
                                    $timestamp = !empty($notification['approved_at']) ? $notification['approved_at'] : $notification['timestamp'];
                                } elseif ($status == 'pending') {
                                    $message .= "is currently <span class='fw-semibold text-uppercase text-warning'>Pending</span> review.";
                                    $timestamp = $notification['timestamp'];
                                } else {
                                    $message .= "has been <span class='fw-semibold text-uppercase text-danger'>Rejected</span>.";
                                    $timestamp = $notification['timestamp'];
                                }
                            ?>
                                <div class="notification-item <?php echo $unread_class; ?>" data-notification-id="<?php echo $notification['id']; ?>" data-application-id="<?php echo $notification['related_id']; ?>" data-notification-type="shop_application">
                                    <div class="notification-content">
                                        <div class="notification-header">
                                            <div class="shop-logo-img d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; border-radius: 8px;">
                                                <i class="fas fa-store text-secondary fs-4"></i>
                                            </div>
                                            <div class="notification-text">
                                                <div class="notification-message"><?php echo $message; ?></div>
                                                <div class="notification-meta">
                                                    <span class="notification-time"><i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($timestamp)); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ellipsis-dropdown">
                                            <button class="btn btn-menu"><i class="fas fa-ellipsis-v"></i></button>
                                            <div class="dropdown-content">
                                                <a href="#" class="dropdown-item" onclick="showDeleteModal(this, event)"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        <?php if ($status == 'approved') : ?>
                                            <a href="shop_owner_profile.php" class="btn btn-sm btn-success">Manage Shop</a>
                                        <?php elseif ($status == 'rejected') : ?>
                                            <a href="become-a-partner.php" class="btn btn-sm btn-warning">Re-apply</a>
                                        <?php else : ?>
                                            <button class="btn btn-sm btn-secondary" disabled>Under Review</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php
                                break;

                            case str_starts_with($notification_type, 'verification'):
                                $status = strtolower($notification['status']);
                                $icon_class = "fas fa-user-clock text-secondary fs-4";
                                if ($status == 'verified') {
                                    $message = "Congratulations! Your account has been <span class='fw-semibold text-uppercase text-success'>Verified</span>.";
                                    $icon_class = "fas fa-user-check text-success fs-4";
                                } elseif ($status == 'pending') {
                                    $message = "Your account verification is currently <span class='fw-semibold text-uppercase text-warning'>Pending</span> review.";
                                    $icon_class = "fas fa-hourglass-half text-warning fs-4";
                                } else {
                                    $message = "Your account verification was <span class='fw-semibold text-uppercase text-danger'>Rejected</span>.";
                                    $icon_class = "fas fa-user-times text-danger fs-4";
                                }
                            ?>
                                <div class="notification-item <?php echo $unread_class; ?>" data-notification-id="<?php echo $notification['id']; ?>" data-verification-id="<?php echo $notification['related_id']; ?>" data-notification-type="verification">
                                    <div class="notification-content">
                                        <div class="notification-header">
                                            <div class="shop-logo-img d-flex align-items-center justify-content-center bg-light" style="width: 50px; height: 50px; border-radius: 8px;">
                                                <i class="<?php echo $icon_class; ?>"></i>
                                            </div>
                                            <div class="notification-text">
                                                <div class="notification-message"><?php echo $message; ?></div>
                                                <div class="notification-meta">
                                                    <span class="notification-time"><i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($notification['timestamp'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ellipsis-dropdown">
                                            <button class="btn btn-menu"><i class="fas fa-ellipsis-v"></i></button>
                                            <div class="dropdown-content">
                                                <a href="#" class="dropdown-item" onclick="showDeleteModal(this, event)"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        <?php if ($status == 'verified') : ?>
                                            <a href="profile.php" class="btn btn-sm btn-success">View Profile</a>
                                        <?php elseif ($status == 'pending') : ?>
                                            <button class="btn btn-sm btn-secondary" disabled>Under Review</button>
                                        <?php else : ?>
                                            <a href="verify-account.php" class="btn btn-sm btn-warning">Re-submit</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                        <?php
                                break;
                        }
                        ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <p>You don't have any notifications at this moment</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <div id="toastContainer" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1100;"></div>
    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/modal-notification.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/notification.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function(event) {
                    if (!event.target.closest('.ellipsis-dropdown, .btn, a')) {
                        this.classList.toggle('expanded');
                    }
                });
            });
        });
    </script>
</body>

</html>