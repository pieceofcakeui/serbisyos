<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

$stmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['profile_type'] !== 'owner') {
    echo json_encode(['status' => 'error', 'message' => 'Only shop owners can update these settings']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No shop found for this user']);
    exit;
}

$shop = $result->fetch_assoc();
$shop_id = $shop['id'];

$show_book_now = isset($_POST['show_book_now']) ? 1 : 0;
$show_emergency = isset($_POST['show_emergency']) ? 1 : 0;

$update_stmt = $conn->prepare("UPDATE shop_applications SET show_book_now = ?, show_emergency = ? WHERE id = ?");
$update_stmt->bind_param("iii", $show_book_now, $show_emergency, $shop_id);

if ($update_stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Settings updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update settings: ' . $conn->error]);
}

$update_stmt->close();
$conn->close();
?>