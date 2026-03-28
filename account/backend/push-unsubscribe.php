<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

require_once '../../functions/auth.php';
include 'db_connection.php';

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['subscription']['endpoint'])) {
    $endpoint = $data['subscription']['endpoint'];
    try {
        $stmt = $conn->prepare("DELETE FROM push_subscriptions WHERE endpoint = ? AND user_id = ?");
        $stmt->bind_param("si", $endpoint, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Unsubscribed successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error on delete.']);
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Push Unsubscribe Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An exception occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No endpoint provided.']);
}
$conn->close();
?>