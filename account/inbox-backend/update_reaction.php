<?php
session_start();
require '../backend/db_connection.php';

include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$message_id = $_POST['message_id'] ?? null;
$reaction_type = $_POST['reaction_type'] ?? null;

if (!$message_id || !$reaction_type) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$message_check = "SELECT id FROM messages WHERE id = ? AND ((sender_id = ?) OR (receiver_id = ?))";
$stmt = $conn->prepare($message_check);
$stmt->bind_param("iii", $message_id, $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid message']);
    exit;
}

$check_reaction = "SELECT id, reaction_type FROM reactions WHERE message_id = ? AND user_id = ?";
$stmt = $conn->prepare($check_reaction);
$stmt->bind_param("ii", $message_id, $current_user_id);
$stmt->execute();
$reaction_result = $stmt->get_result();

if ($reaction_result->num_rows > 0) {
    $existing_reaction = $reaction_result->fetch_assoc();
    
    if ($existing_reaction['reaction_type'] === $reaction_type) {
        $delete = "DELETE FROM reactions WHERE id = ?";
        $stmt = $conn->prepare($delete);
        $stmt->bind_param("i", $existing_reaction['id']);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'action' => 'removed',
                'reaction_type' => $reaction_type
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to remove reaction'
            ]);
        }
    } else {
        $update = "UPDATE reactions SET reaction_type = ? WHERE id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("si", $reaction_type, $existing_reaction['id']);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'action' => 'updated',
                'old_reaction' => $existing_reaction['reaction_type'],
                'new_reaction' => $reaction_type
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update reaction'
            ]);
        }
    }
} else {
    $insert = "INSERT INTO reactions (message_id, user_id, reaction_type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("iis", $message_id, $current_user_id, $reaction_type);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'action' => 'added',
            'reaction_type' => $reaction_type
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add reaction'
        ]);
    }
}

$stmt->close();
$conn->close();
?>