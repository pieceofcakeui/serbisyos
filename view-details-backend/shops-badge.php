<?php
include './functions/db_connection.php';
$topRated = false;
$mostBooked = false;

$topRatedQuery = "SELECT AVG(rating) as avg_rating FROM shop_ratings WHERE shop_id = ?";
$stmt = $conn->prepare($topRatedQuery);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result && $result['avg_rating'] >= 4.0 && $result['avg_rating'] <= 5.0) {
    $topRated = true;
}

$mostBookedQuery = "SELECT COUNT(*) as total_completed FROM services_booking WHERE shop_id = ? AND booking_status = 'Completed'";
$stmt = $conn->prepare($mostBookedQuery);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result && $result['total_completed'] >= 10) {
    $mostBooked = true;
}
?>