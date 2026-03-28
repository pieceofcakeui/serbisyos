<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $shopCount = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $shopCount = $row['count'];
    }
    $stmt->close();

    $bookingCount = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM services_booking WHERE user_id = ? AND is_read = FALSE");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $bookingCount = $row['count'];
    }
    $stmt->close();

    $emergencyCount = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM emergency_requests WHERE user_id = ? AND is_read = FALSE");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $emergencyCount = $row['count'];
    }
    $stmt->close();

    $totalCount = $shopCount + $bookingCount + $emergencyCount;
    echo json_encode(['count' => $totalCount]);
} catch (Exception $e) {
    echo json_encode(['count' => 0]);
}