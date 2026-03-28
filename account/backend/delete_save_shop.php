<?php
include 'db_connection.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$shop_id = $_POST['shop_id'] ?? null;

if (!$user_id || !$shop_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$delete_sql = "DELETE FROM save_shops WHERE user_id = ? AND shop_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ii", $user_id, $shop_id);
$delete_stmt->execute();
$affected_rows = $delete_stmt->affected_rows;
$delete_stmt->close();
$conn->close();

if ($affected_rows > 0) {
    echo json_encode(['save' => false, 'message' => '']);
} else {
    echo json_encode(['save' => false, 'message' => 'No matching record found']);
}
?>
