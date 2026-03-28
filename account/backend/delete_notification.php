<?php
include 'db_connection.php';
session_start();

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in. Please log in and try again.';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['notification_id'])) {
    $response['message'] = 'Notification ID is missing.';
    echo json_encode($response);
    exit;
}

$notificationId = filter_var($_POST['notification_id'], FILTER_VALIDATE_INT);
$userId = $_SESSION['user_id'];

if ($notificationId === false) {
    $response['message'] = 'Invalid Notification ID format.';
    echo json_encode($response);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE notifications SET delete_notification = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notificationId, $userId);
    $success = $stmt->execute();

    if ($success && $stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Notification deleted successfully.';
    } else {
        $response['message'] = 'No notification found or you do not have permission to delete it.';
    }
} catch (Exception $e) {
    error_log('Delete Notification Error: ' . $e->getMessage());
    $response['message'] = 'A server error occurred while deleting the notification.';
}

echo json_encode($response);
?>