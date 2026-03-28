<?php
include 'db_connection.php';

$user_id = $_SESSION['user_id'];
$emergency = null;

if ($user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shop = $shopResult->fetch_assoc();

    if ($shop) {
        $shop_id = $shop['id'];

        $emergencyQuery = $conn->prepare("
            SELECT er.id, u.fullname, er.issue_description, er.created_at 
            FROM emergency_requests er
            JOIN users u ON er.user_id = u.id
            WHERE er.shop_id = ? AND er.seen_emergency_request = 0
            ORDER BY er.created_at DESC LIMIT 1
        ");
        $emergencyQuery->bind_param("i", $shop_id);
        $emergencyQuery->execute();
        $emergencyResult = $emergencyQuery->get_result();
        $emergency = $emergencyResult->fetch_assoc();

        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
        }
    }
}

// Create a mapping between service IDs and their display names
$service_mapping = [
    'general-maintenance' => 'General Maintenance',
    'engine-diagnostics' => 'Engine Diagnostics',
    'brake-services' => 'Brake Services',
    'tire-services' => 'Tire Services',
    'air-conditioning' => 'Air Conditioning',
    'body-collision' => 'Body & Collision',
    'transmission-services' => 'Transmission Services',
    'electrical-system' => 'Electrical System',
    'suspension-steering' => 'Suspension & Steering'
];

// Get all services for the dropdown
$all_services = array_keys($service_mapping);

// Initialize shops array
$shops = [];
$selected_service = '';

if (isset($_GET['service']) && array_key_exists($_GET['service'], $service_mapping)) {
    $selected_service = $_GET['service'];
    $service_name = $service_mapping[$selected_service];
    
    $stmt = $conn->prepare("
        SELECT
            sa.id,
            sa.shop_logo,
            sa.shop_name,
            sa.shop_location,
            CONCAT(sa.town_city, ', ', sa.province, ', ', sa.country, ', ', sa.postal_code) AS full_address,
            AVG(sr.rating) AS average_rating
        FROM
            shop_applications AS sa
        LEFT JOIN
            shop_ratings AS sr ON sa.id = sr.shop_id
        WHERE
            FIND_IN_SET(?, sa.services_offered) > 0
        GROUP BY
            sa.id
        ORDER BY
            average_rating DESC
    ");

    $stmt->bind_param("s", $service_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $shops[] = $row;
        }
    }
    $stmt->close();
}
?>