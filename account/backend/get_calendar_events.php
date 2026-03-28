<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../functions/db_connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$owner_user_id = $_SESSION['user_id'] ?? null;
$events = [];

if (!$conn || $conn->connect_error || empty($owner_user_id)) {
    echo json_encode([]);
    exit;
}

$shop_id_to_use = null;
$sql_check_owner = "SELECT profile_type FROM users WHERE id = ?";
$stmt_owner = $conn->prepare($sql_check_owner);
$stmt_owner->bind_param("i", $owner_user_id);
$stmt_owner->execute();
$result_owner = $stmt_owner->get_result();
$row_owner = $result_owner->fetch_assoc();
$is_owner = false;

if ($row_owner && $row_owner['profile_type'] === 'owner') {
    $is_owner = true;
}

if ($is_owner) {
    $sql_shop = "SELECT id FROM shop_applications WHERE user_id = ?";
    $stmt_shop = $conn->prepare($sql_shop);
    $stmt_shop->bind_param("i", $owner_user_id);
    $stmt_shop->execute();
    $result_shop = $stmt_shop->get_result();
    if ($row_shop = $result_shop->fetch_assoc()) {
        $shop_id_to_use = $row_shop['id'];
    }
    $stmt_shop->close();
}

$stmt_owner->close();

if (empty($shop_id_to_use)) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT sb.id, sb.service_type, sb.preferred_datetime, sb.booking_status, 
        u.fullname, u.id as user_id, u.profile_picture
        FROM services_booking sb
        JOIN users u ON sb.user_id = u.id
        WHERE sb.shop_id = ?
        AND sb.booking_status IN ('Pending', 'Accept', 'Completed', 'Reject', 'Cancelled')";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL preparation failed.']);
    exit;
}

$stmt->bind_param("i", $shop_id_to_use);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $datetime_string = $row['preferred_datetime'];
    $status = strtolower(trim($row['booking_status']));
    
    $service_type_clean = str_replace(['[', ']', '"'], '', $row['service_type']);
    
    $color = '#3788d8';
    $textColor = '#fff';
    
    if ($status === 'pending') {
        $color = '#ffc107';
        $textColor = '#000';
    } elseif ($status === 'accept') {
        $color = '#007bff';
    } elseif ($status === 'completed') {
        $color = '#28a745';
    } elseif ($status === 'reject' || $status === 'cancelled') {
        $color = '#dc3545';
    }
    
    $regex = '/^(\d{1,2}\/\d{1,2}\/\d{4}),\s*([^-\s]+)\s*(AM|PM)\s*-\s*([^-\s]+)\s*(AM|PM)$/i';
    
    if (preg_match($regex, $datetime_string, $matches)) {
        $date_part = $matches[1];
        $start_time_part = trim($matches[2] . ' ' . $matches[3]);
        $end_time_part = trim($matches[4] . ' ' . $matches[5]);
        
        $start_datetime_str = $date_part . ' ' . $start_time_part;
        $end_datetime_str = $date_part . ' ' . $end_time_part;
        $format = 'm/d/Y g:i A';
        
        $start_datetime = DateTime::createFromFormat($format, $start_datetime_str);
        $end_datetime = DateTime::createFromFormat($format, $end_datetime_str);
        
        if ($start_datetime && $end_datetime) {
            $events[] = [
                'id' => $row['id'],
                'title' => htmlspecialchars($row['fullname']) . ' - ' . $service_type_clean,
                'start' => $start_datetime->format('Y-m-d\TH:i:s'),
                'end' => $end_datetime->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => $textColor,
                'extendedProps' => [
                    'status' => $status,
                    'booking_id' => $row['id'],
                    'customer_name' => $row['fullname'],
                    'service_type' => $service_type_clean,
                    'user_id' => $row['user_id'],
                    'profile_picture' => $row['profile_picture']
                ]
            ];
        }
    }
}

$stmt->close();
$conn->close();

echo json_encode($events);
?>