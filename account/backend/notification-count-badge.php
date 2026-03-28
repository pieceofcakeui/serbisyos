<?php
include 'db_connection.php';

if (!isset($_SESSION['notification_tracking'])) {
    $_SESSION['notification_tracking'] = [
        'last_viewed_count' => 0,
        'current_count' => 0
    ];
}

$notification_count = 0;
$show_badge = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt_notifications = $conn->prepare("
        SELECT COUNT(*) AS count 
        FROM notifications 
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt_notifications->bind_param("i", $user_id);
    $stmt_notifications->execute();
    $result_notifications = $stmt_notifications->get_result();
    $row_notifications = $result_notifications->fetch_assoc();
    $notification_count += $row_notifications['count'];

    $stmt_services = $conn->prepare("
        SELECT COUNT(*) AS count 
        FROM services_booking 
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt_services->bind_param("i", $user_id);
    $stmt_services->execute();
    $result_services = $stmt_services->get_result();
    $row_services = $result_services->fetch_assoc();
    $notification_count += $row_services['count'];

    $stmt_emergency = $conn->prepare("
        SELECT COUNT(*) AS count 
        FROM emergency_requests 
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt_emergency->bind_param("i", $user_id);
    $stmt_emergency->execute();
    $result_emergency = $stmt_emergency->get_result();
    $row_emergency = $result_emergency->fetch_assoc();
    $notification_count += $row_emergency['count'];

    $_SESSION['notification_tracking']['current_count'] = $notification_count;

    $show_badge = ($_SESSION['notification_tracking']['current_count'] > $_SESSION['notification_tracking']['last_viewed_count']);
}
?>