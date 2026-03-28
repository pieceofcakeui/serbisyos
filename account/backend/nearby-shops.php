<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;

if (!$lat || !$lon) {
    echo json_encode(['success' => false, 'message' => 'Invalid location coordinates']);
    exit;
}

try {
    $query = "SELECT 
                sa.id,
                sa.shop_name,
                sa.shop_location,
                sa.town_city,
                sa.province,
                sa.country,
                sa.postal_code,
                sa.latitude,
                sa.longitude,
                sa.shop_logo,
                ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * 
                  cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                  sin( radians( latitude ) ) ) AS distance,
                (SELECT GROUP_CONCAT(service_name SEPARATOR ',') FROM shop_services WHERE shop_id = sa.id) AS services,
                (SELECT AVG(rating) FROM shop_reviews WHERE shop_id = sa.id) AS average_rating,
                (SELECT COUNT(*) FROM shop_reviews WHERE shop_id = sa.id) AS total_reviews
              FROM shop_applications sa
              WHERE sa.status = 'Approved'
              HAVING distance < 20
              ORDER BY distance ASC
              LIMIT 12";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ddd', $lat, $lon, $lat);
    $stmt->execute();
    $result = $stmt->get_result();

    $shops = [];
    while ($row = $result->fetch_assoc()) {
        $row['services'] = $row['services'] ? explode(',', $row['services']) : [];

        $row['distance'] = floatval($row['distance']);
        $row['average_rating'] = $row['average_rating'] ? floatval($row['average_rating']) : 0;
        $row['total_reviews'] = intval($row['total_reviews']);
        
        $shops[] = $row;
    }

    echo json_encode(['success' => true, 'shops' => $shops]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>