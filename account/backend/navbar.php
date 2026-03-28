<?php
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("User not authenticated.");
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT 
    users.id,
    users.fullname, 
    users.email, 
    users.profile_picture,
    users.profile_type,
    users.contact_number,
    COALESCE(users.full_address, '') as full_address,
    COALESCE(users.postal_code, '') as postal_code,
    COALESCE(users.latitude, 0) as latitude,
    COALESCE(users.longitude, 0) as longitude,
    shop_applications.shop_name,
    shop_applications.shop_logo
FROM users 
LEFT JOIN shop_applications ON shop_applications.user_id = users.id
WHERE users.id = ?");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!empty($user['full_address'])) {
    $user['address'] = $user['full_address'];
} else if (!empty($user['postal_code'])) {
    $user['address'] = $user['postal_code'];
} else {
    $user['address'] = 'Address not set';
}

$stmt->close();

$other_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$unread_count = 0;
$sql_unread = "SELECT COUNT(*) AS unread_count FROM messages WHERE receiver_id = ? AND is_read = 0";
$stmt_unread = $conn->prepare($sql_unread);
$stmt_unread->bind_param("i", $user_id);
$stmt_unread->execute();
$result_unread = $stmt_unread->get_result();
if ($result_unread && $row_unread = $result_unread->fetch_assoc()) {
    $unread_count = $row_unread['unread_count'];
}
$stmt_unread->close();

if ($other_id) {
    $stmt_read = $conn->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?");
    $stmt_read->bind_param("ii", $user_id, $other_id);
    $stmt_read->execute();
    $stmt_read->close();
}

if (isset($_GET['check_unread'])) {
    $unread_count = 0;
    $sql_unread = "SELECT COUNT(*) AS unread_count FROM messages 
                   WHERE receiver_id = ? AND is_read = 0";
    $stmt_unread = $conn->prepare($sql_unread);
    $stmt_unread->bind_param("i", $user_id);
    $stmt_unread->execute();
    $result_unread = $stmt_unread->get_result();

    if ($result_unread && $row_unread = $result_unread->fetch_assoc()) {
        $unread_count = $row_unread['unread_count'];
    }
    $stmt_unread->close();

    header('Content-Type: application/json');
    echo json_encode(['unread_count' => $unread_count]);
    exit();
}

function getNotificationCounts($conn, $user_id, $profile_type)
{
    $counts = [
        'total' => 0,
        'user_notifications' => 0,
        'owner_bookings' => 0,
        'owner_emergencies' => 0,
    ];

    try {
        $shops_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE AND (delete_notification IS NULL OR delete_notification = 0)";
        $stmt = $conn->prepare($shops_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $counts['user_notifications'] += $stmt->get_result()->fetch_assoc()['count'];
        $stmt->close();

        $booking_updates_query = "SELECT COUNT(*) as count FROM services_booking WHERE user_id = ? AND is_read = 0 AND (delete_notification IS NULL OR delete_notification = 0)";
        $stmt = $conn->prepare($booking_updates_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $counts['user_notifications'] += $stmt->get_result()->fetch_assoc()['count'];
        $stmt->close();

        $emergency_updates_query = "SELECT COUNT(*) as count FROM emergency_requests WHERE user_id = ? AND is_read = 0 AND (delete_notification IS NULL OR delete_notification = 0)";
        $stmt = $conn->prepare($emergency_updates_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $counts['user_notifications'] += $stmt->get_result()->fetch_assoc()['count'];
        $stmt->close();

        $app_updates_query = "SELECT COUNT(*) as count FROM shop_applications WHERE user_id = ? AND status IN ('Pending', 'Approved', 'Rejected') AND is_read = 0 AND (delete_notification IS NULL OR delete_notification = 0)";
        $stmt = $conn->prepare($app_updates_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $counts['user_notifications'] += $stmt->get_result()->fetch_assoc()['count'];
        $stmt->close();

        $verification_updates_query = "SELECT COUNT(*) as count FROM verification_submissions WHERE user_id = ? AND status IN ('pending', 'verified', 'rejected') AND is_read = 0 AND (delete_notification IS NULL OR delete_notification = 0)";
        $stmt = $conn->prepare($verification_updates_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $counts['user_notifications'] += $stmt->get_result()->fetch_assoc()['count'];
        $stmt->close();

        if ($profile_type === 'owner') {
            $emergency_query = "SELECT COUNT(*) as count FROM emergency_requests 
                                WHERE shop_id IN (SELECT id FROM shop_applications WHERE user_id = ?) 
                                AND status = 'pending'";
            $stmt = $conn->prepare($emergency_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $counts['owner_emergencies'] = $stmt->get_result()->fetch_assoc()['count'];
            $stmt->close();

            $booking_query = "SELECT COUNT(*) as count FROM services_booking 
                              WHERE shop_id IN (SELECT id FROM shop_applications WHERE user_id = ?)
                              AND booking_status = 'Pending'";
            $stmt = $conn->prepare($booking_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $counts['owner_bookings'] = $stmt->get_result()->fetch_assoc()['count'];
            $stmt->close();
            
            $counts['total'] = $counts['user_notifications'] + $counts['owner_bookings'] + $counts['owner_emergencies'];
        } else {
            $counts['total'] = $counts['user_notifications'];
        }
    } catch (Exception $e) {
        error_log("Error getting notification counts: " . $e->getMessage());
    }

    return $counts;
}
?>