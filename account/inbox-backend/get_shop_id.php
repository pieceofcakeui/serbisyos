<?php
include '../backend/db_connection.php';

include 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID not provided']);
    exit;
}

$user_id = intval($_GET['user_id']);

$query = "SELECT id FROM shop_applications WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['shop_id' => $row['id']]);
} else {
    echo json_encode(['error' => 'No shop found']);
}
?>