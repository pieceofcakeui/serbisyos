<?php
header('Content-Type: application/json');
require_once './backend/db_connection.php';

$input = json_decode(file_get_contents("php://input"), true);

$barangay   = isset($input['barangay']) ? trim($input['barangay']) : '';
$town_city  = isset($input['town_city']) ? trim($input['town_city']) : '';
$province   = isset($input['province']) ? trim($input['province']) : '';

$response = [];

try {
    $stmt = $conn->prepare("
    SELECT 
        s.id,
        s.shop_name,
        s.barangay,
        s.town_city,
        s.province,
        s.contact_number,
        s.email,
        s.description,
        COALESCE(ROUND(AVG(r.rating), 1), 0) AS average_rating
    FROM shop_applications s
    LEFT JOIN shop_ratings r ON s.id = r.shop_id
    WHERE s.status = 'Approved'
      AND (
            (s.barangay = ? OR ? = '')
         OR (s.town_city = ? OR ? = '')
         OR (s.province = ? OR ? = '')
      )
    GROUP BY s.id
    ORDER BY average_rating DESC
    ");

    $stmt->bind_param("ssssss", $barangay, $barangay, $town_city, $town_city, $province, $province);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];
    while ($shop = $result->fetch_assoc()) {
        $response[] = $shop;
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>