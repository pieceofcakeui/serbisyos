<?php
$shop_id = $row['id'];
$avg_stmt = $conn->prepare("SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews FROM shop_ratings WHERE shop_id = ?");
$avg_stmt->bind_param("i", $shop_id);
$avg_stmt->execute();
$avg_result = $avg_stmt->get_result();
$avg_data = $avg_result->fetch_assoc();
$average_rating = round($avg_data['average_rating'], 1);
$total_reviews = $avg_data['total_reviews'];

$shop_logo = !empty($row['shop_logo']) ? $row['shop_logo'] : 'uploads/shop_logo/logo.jpg';
if (!str_starts_with($shop_logo, 'uploads/shop_logo/')) {
    $shop_logo = 'uploads/shop_logo/' . $shop_logo;
}
if (!file_exists($shop_logo)) {
    $shop_logo = 'uploads/shop_logo/logo.jpg';
}
?>