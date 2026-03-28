<?php
include 'db_connection.php';
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID.']);
    exit;
}

$bookingId = (int) $_GET['id'];
$userId = $_SESSION['user_id'];

try {
    $query = "SELECT
            sb.id,
            sb.customer_name,
            sb.customer_phone,
            sb.customer_email,
            sb.vehicle_make,
            sb.vehicle_model,
            sb.plate_number,
            sb.vehicle_year,
            sb.transmission_type,
            sb.fuel_type,
            sb.vehicle_type,
            sb.vehicle_issues,
            sb.service_type,
            sb.preferred_datetime,
            sb.customer_notes,
            sb.created_at,
            u.fullname AS customer_fullname,
            sa.shop_name
        FROM services_booking sb
        JOIN users u ON sb.user_id = u.id
        JOIN shop_applications sa ON sb.shop_id = sa.id
        WHERE sb.id = ? AND sb.user_id = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $bookingId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Booking not found or you do not have permission to view it.']);
        exit;
    }

    $booking = $result->fetch_assoc();

    $service_types = json_decode($booking['service_type']);
    $booking['formatted_services'] = is_array($service_types) ? implode(', ', $service_types) : str_replace(['[', ']', '"', "'"], '', $booking['service_type']);
    $booking['preferred_datetime'] = str_replace(['"', "'", "[", "]"], '', $booking['preferred_datetime']);
    $booking['vehicle_make_model'] = trim(htmlspecialchars($booking['vehicle_make'] . ' ' . $booking['vehicle_model']));

    echo json_encode(['success' => true, 'booking' => $booking]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . htmlspecialchars($e->getMessage())]);
}

?>