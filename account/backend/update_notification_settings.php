<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    $enable_notifications = isset($_POST['enable_notifications']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE users SET enable_notifications = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ii", $enable_notifications, $user_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Notification preferences saved!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update preferences: ' . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
