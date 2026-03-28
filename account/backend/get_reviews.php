<?php
require 'db_connection.php';

header('Content-Type: application/json');

$shop_id = isset($_GET['shop_id']) ? (int)$_GET['shop_id'] : 0;

if ($shop_id <= 0) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT sr.*, u.fullname, u.profile_picture 
        FROM shop_ratings sr
        LEFT JOIN users u ON sr.user_id = u.id
        WHERE sr.shop_id = ?
        ORDER BY sr.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);
?>