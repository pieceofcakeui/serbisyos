<?php
session_start();
include 'backend/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

$booking_id = $_POST['booking_id'] ?? 0;

$query = "UPDATE services_booking 
          SET booking_status = 'Cancelled' 
          WHERE id = $booking_id AND user_id = {$_SESSION['user_id']}";
$result = $conn->query($query);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to cancel booking']);
}
?>