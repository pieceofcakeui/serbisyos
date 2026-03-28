<?php
session_start();
include 'backend/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}

$message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($message_id > 0) {
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ? AND receiver_id = ?");
    $stmt->bind_param("ii", $message_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['status' => 'success']);
?>