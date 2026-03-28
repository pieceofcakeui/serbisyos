<?php
session_start();
require './backend/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$query = "SELECT created_at, last_activity FROM active_sessions 
          WHERE user_id = ? 
          ORDER BY last_activity DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $created_at = strtotime($row['created_at']);
    $last_activity = strtotime($row['last_activity']);
    $current_time = time();

    $since_last_activity = $current_time - $last_activity;
    $session_age = $current_time - $created_at;

    if ($since_last_activity < 120 || $session_age < 120) {
        echo json_encode([
            'status' => 'online',
            'last_active' => $last_activity
        ]);
    } 

    elseif ($since_last_activity < 300 || $session_age < 300) {
        $minutes = floor(max($since_last_activity, $session_age) / 60);
        echo json_encode([
            'status' => 'idle',
            'minutes' => $minutes
        ]);
    } 
    else {
        echo json_encode(['status' => 'offline']);
    }
} else {
    echo json_encode(['status' => 'offline']);
}
?>