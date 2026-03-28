<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $conn->begin_transaction();

    $stmt1 = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("UPDATE services_booking SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();

    $stmt3 = $conn->prepare("UPDATE emergency_requests SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    $stmt3->bind_param("i", $user_id);
    $stmt3->execute();

    $stmt4 = $conn->prepare("UPDATE shop_applications SET is_read = 1 WHERE user_id = ? AND is_read = 0 AND status IN ('Pending', 'Approved', 'Rejected')");
    $stmt4->bind_param("i", $user_id);
    $stmt4->execute();

    $stmt4 = $conn->prepare("UPDATE verification_submissions SET is_read = 1 WHERE user_id = ? AND is_read = 0 AND status IN ('pending', 'verified', 'rejected')");
    $stmt4->bind_param("i", $user_id);
    $stmt4->execute();
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>