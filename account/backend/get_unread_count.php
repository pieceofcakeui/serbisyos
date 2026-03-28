<?php
session_start();
include 'db_connection.php';
include '../inbox-backend/config.php';
require_once '../../functions/auth.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT COUNT(*) AS unread_count FROM messages 
        WHERE receiver_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$unread_count = 0;
if ($row = $result->fetch_assoc()) {
    $unread_count = $row['unread_count'];
}

echo json_encode(['unread_count' => $unread_count]);
?>