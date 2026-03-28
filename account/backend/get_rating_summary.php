<?php
require 'db_connection.php';

header('Content-Type: application/json');

$shop_id = isset($_GET['shop_id']) ? (int)$_GET['shop_id'] : 0;

if ($shop_id <= 0) {
    echo json_encode(['error' => 'Invalid shop ID']);
    exit;
}

$sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews 
        FROM shop_ratings 
        WHERE shop_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();
$summary = $result->fetch_assoc();

$rating_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$sql = "SELECT rating, COUNT(*) as count 
        FROM shop_ratings 
        WHERE shop_id = ? 
        GROUP BY rating";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $rating_counts[$row['rating']] = (int)$row['count'];
}

echo json_encode([
    'average_rating' => round($summary['average_rating'], 1),
    'total_reviews' => (int)$summary['total_reviews'],
    'rating_counts' => $rating_counts
]);
?>