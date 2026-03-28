<?php
require 'db_connection.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$limit = 3;

$sql = "SELECT id, shop_name, town_city, province, country, shop_location, postal_code, services_offered, shop_logo, shop_slug
        FROM shop_applications
        WHERE status = 'Approved'
          AND user_id != ?
          AND (shop_status IS NULL OR shop_status NOT IN ('permanently_closed', 'temporarily_closed'))
        LIMIT ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $limit);
$stmt->execute();
$result = $stmt->get_result();

?>