<?php
session_start();
require_once 'backend/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['shop_id'])) {
    $shop_id = $_POST['shop_id'];

    $checkQuery = "SELECT * FROM saved_shops WHERE user_id = ? AND shop_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $user_id, $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $deleteQuery = "DELETE FROM saved_shops WHERE user_id = ? AND shop_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ii", $user_id, $shop_id);
        $stmt->execute();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'action' => 'unsaved']);
    } else {
        $insertQuery = "INSERT INTO saved_shops (user_id, shop_id, saved_date) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $user_id, $shop_id);
        $stmt->execute();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'action' => 'saved']);
    }
    
    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Shop ID is required']);
}
?>