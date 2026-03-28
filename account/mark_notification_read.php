<?php
session_start();
require_once 'backend/db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $query = "UPDATE notifications 
                 SET is_read = TRUE 
                 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log("Error marking notification as read: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>