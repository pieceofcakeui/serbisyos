<?php
session_start();
require_once './backend/db_connection.php';

header('Content-Type: application/json');

$response = ['count' => 0];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT COUNT(*) as count FROM notifications 
              WHERE user_id = ? AND is_read = FALSE";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    $response['count'] = $count;
}

echo json_encode($response);
?>