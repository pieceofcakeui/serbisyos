<?php
session_start();
require_once './backend/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['total' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];
$profile_check = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
$profile_check->bind_param("i", $user_id);
$profile_check->execute();
$profile_result = $profile_check->get_result();

if ($profile_result->num_rows === 0) {
    echo json_encode(['total' => 0]);
    exit;
}

$user_profile = $profile_result->fetch_assoc();
$counts = getNotificationCounts($conn, $user_id, $user_profile['profile_type']);

echo json_encode($counts);
?>