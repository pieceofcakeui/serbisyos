<?php
session_start();
require '../backend/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$other_id = $_POST['other_id'] ?? null;

if (!$other_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$query = "
    UPDATE messages 
    SET 
        deleted_by_sender = CASE WHEN sender_id = ? THEN 1 ELSE deleted_by_sender END,
        deleted_by_receiver = CASE WHEN receiver_id = ? THEN 1 ELSE deleted_by_receiver END
    WHERE 
        (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiiiii", $current_user_id, $current_user_id, $current_user_id, $other_id, $other_id, $current_user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'success', 'message' => 'No new messages to delete or already deleted.']);
}

$stmt->close();
$conn->close();
?>