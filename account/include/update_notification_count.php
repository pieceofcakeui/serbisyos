<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$count = $input['count'] ?? 0;

$_SESSION['unread_notifications'] = $count;

echo json_encode(['success' => true]);
?>