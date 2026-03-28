<?php
require 'backend/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;
$shop_id = $_POST['shop_id'] ?? null;

if (!$receiver_id || !$shop_id) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$stmt = $conn->prepare("UPDATE messages SET is_read = TRUE WHERE receiver_id = ? AND sender_id = ? AND shop_id = ? AND is_read = FALSE");
$stmt->bind_param("iii", $sender_id, $receiver_id, $shop_id);
$stmt->execute();

$stmt = $conn->prepare("
    SELECT m.*, u.fullname, u.profile_picture 
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
    AND shop_id = ?
    ORDER BY created_at ASC
");
$stmt->bind_param("iiiii", $sender_id, $receiver_id, $receiver_id, $sender_id, $shop_id);
$stmt->execute();
$result = $stmt->get_result();

while ($message = $result->fetch_assoc()) {
    $isCurrentUser = $message['sender_id'] == $sender_id;
    $messageClass = $isCurrentUser ? 'sent' : 'received';
    $profilePic = !empty($message['profile_picture']) ? '../assets/img/profile/' . htmlspecialchars($message['profile_picture']) : '../assets/img/profile/profile-user.png';
    
    echo '<div class="message ' . $messageClass . '">';
    echo '<img src="' . $profilePic . '" alt="' . htmlspecialchars($message['fullname']) . '" class="message-avatar">';
    echo '<div class="message-content">';
    echo '<div class="message-sender">' . htmlspecialchars($message['fullname']) . '</div>';
    echo '<div class="message-text">' . nl2br(htmlspecialchars($message['message'])) . '</div>';
    echo '<div class="message-time">' . date('M j, Y g:i A', strtotime($message['created_at'])) . '</div>';
    echo '</div></div>';
}
?>