<?php
session_start();

require_once '../../functions/auth.php';
include 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$sub = json_decode(file_get_contents('php://input'), true);

if ($sub === null || !isset($sub['endpoint'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid subscription data.']);
    exit;
}

if (!isset($sub['keys']['p256dh']) || !isset($sub['keys']['auth'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete subscription keys.']);
    exit;
}

$endpoint = $sub['endpoint'];
$p256dh = $sub['keys']['p256dh'];
$auth = $sub['keys']['auth'];

try {
    $stmt = $conn->prepare("SELECT id, user_id FROM push_subscriptions WHERE endpoint = ? LIMIT 1");
    $stmt->bind_param("s", $endpoint);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $existing_sub = $result->fetch_assoc();
        if ($existing_sub['user_id'] != $user_id) {
            $update_stmt = $conn->prepare("UPDATE push_subscriptions SET user_id = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $user_id, $existing_sub['id']);
            $update_stmt->execute();
            $update_stmt->close();
        }
        echo json_encode(['status' => 'success', 'message' => 'Subscription already exists or updated.']);
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO push_subscriptions (user_id, endpoint, p256dh, auth) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("isss", $user_id, $endpoint, $p256dh, $auth);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Subscription saved.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error on insert.']);
        }
        $insert_stmt->close();
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Push Subscription Save Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A fatal error occurred.']);
}

$conn->close();
?>