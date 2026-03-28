<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

$query = "
    SELECT 
        sa.id,
        sa.shop_name,
        sa.barangay,
        sa.town_city,
        sa.province,
        sa.postal_code,
        sa.latitude,
        sa.longitude,
        COALESCE(AVG(sr.rating), 0) as average_rating,
        COUNT(sr.id) as total_reviews
    FROM 
        shop_applications sa
    LEFT JOIN 
        shop_ratings sr ON sa.id = sr.shop_id
    GROUP BY 
        sa.id
    ORDER BY 
        (POW(sa.latitude - ?, 2) + POW(sa.longitude - ?, 2)) ASC
    LIMIT 5
";

$stmt = $conn->prepare($query);
$stmt->bind_param("dd", $latitude, $longitude);
$stmt->execute();
$result = $stmt->get_result();

$shops = [];
while ($row = $result->fetch_assoc()) {
    $row['average_rating'] = round($row['average_rating'], 1);
    $shops[] = $row;
}

echo json_encode($shops);
?>