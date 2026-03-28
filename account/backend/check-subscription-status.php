<?php
session_start();
require_once '../../functions/auth.php';
include 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'not_subscribed']);
    exit;
}
$user_id = $_SESSION['user_id'];
$sub = json_decode(file_get_contents('php://input'), true);

if ($sub === null || !isset($sub['endpoint'])) {
    echo json_encode(['status' => 'not_subscribed']);
    exit;
}
$endpoint = $sub['endpoint'];

try {
    $stmt = $conn->prepare("SELECT id FROM push_subscriptions WHERE endpoint = ? AND user_id = ? LIMIT 1");
    $stmt->bind_param("si", $endpoint, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'subscribed']);
    } else {
        echo json_encode(['status' => 'not_subscribed']);
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Push Subscription Check Error: " . $e->getMessage());
    echo json_encode(['status' => 'error']);
}
$conn->close();
?>