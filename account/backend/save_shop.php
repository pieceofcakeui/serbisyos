<?php
include 'db_connection.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$shop_id = $_POST['shop_id'] ?? null;

header('Content-Type: application/json');

if (!$user_id || !$shop_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $check_sql = "SELECT * FROM save_shops WHERE user_id = ? AND shop_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $shop_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM save_shops WHERE user_id = ? AND shop_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $user_id, $shop_id);
        $delete_stmt->execute();
        echo json_encode(['save' => false, 'message' => 'Shop removed successfully!']);
    } else {
        $insert_sql = "INSERT INTO save_shops (user_id, shop_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $shop_id);
        $insert_stmt->execute();
        echo json_encode(['save' => true, 'message' => 'Shop saved successfully!']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>