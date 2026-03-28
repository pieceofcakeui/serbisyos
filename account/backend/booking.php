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

$user_profile = ['profile_type' => 'owner'];
$status_counts = [
    'all' => 0,
    'Pending' => 0,
    'Accept' => 0,
    'Completed' => 0,
    'Cancelled' => 0
];
$booking_result = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    if (isset($_SESSION['user_profile'])) {
        $user_profile = $_SESSION['user_profile'];
    }

    if ($user_profile['profile_type'] === 'owner') {
        try {
           $booking_query = "SELECT 
    sb.id, 
    sb.user_id,
    sb.customer_name, 
    sb.customer_phone, 
    sb.customer_email,
    sb.vehicle_type,
    sb.vehicle_make,
    sb.vehicle_model,
    sb.vehicle_year,
    sb.service_type,
    sb.preferred_datetime,
    sb.vehicle_issues,
    sb.customer_notes,
    sb.booking_status,
    sb.created_at,
    sb.completed_at,
    u.fullname AS customer_fullname,
    u.profile_picture
    FROM services_booking sb
    JOIN users u ON sb.user_id = u.id
    WHERE sb.shop_id IN (SELECT id FROM shop_applications WHERE user_id = ?)
    AND sb.is_deleted = 0
    ORDER BY 
        CASE 
            WHEN sb.booking_status = 'Completed' THEN 1
            ELSE 0
        END,
        sb.created_at DESC";

            $stmt = $conn->prepare($booking_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $booking_result = $stmt->get_result();

            if ($booking_result->num_rows > 0) {
                $status_counts['all'] = $booking_result->num_rows;
                $booking_result->data_seek(0);
                
                while ($booking = $booking_result->fetch_assoc()) {
                    $status = !empty($booking['booking_status']) ? trim($booking['booking_status']) : 'Pending';
                    if (isset($status_counts[$status])) {
                        $status_counts[$status]++;
                    }
                }
                $booking_result->data_seek(0);
            }
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            $booking_result = false;
            echo '<div class="alert alert-danger">Error loading bookings: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
?>