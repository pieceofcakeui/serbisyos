<?php
session_start();
require '../backend/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$sender_id = isset($_POST['sender_id']) ? intval($_POST['sender_id']) : null;

if (!$sender_id) {
    echo json_encode(['status' => 'error', 'message' => 'Sender ID required']);
    exit;
}

$sql = "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL query']);
    exit;
}

$stmt->bind_param("ii", $user_id, $sender_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Messages marked as read']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update messages']);
}

$stmt->close();
?>