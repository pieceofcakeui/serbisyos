<?php
session_start();
require '../backend/db_connection.php';
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$other_id = $_POST['shop_owner_id'] ?? null;

if (!$other_id) {
    echo json_encode(['error' => 'Missing shop owner ID']);
    exit;
}

$last_message_id = isset($_POST['last_message_id']) ? intval($_POST['last_message_id']) : 0;

$stmt = $conn->prepare("
    SELECT m.*, 
           u.fullname,
           u.profile_picture,
           DATE_FORMAT(m.created_at, '%h:%i %p') AS formatted_time,
           DATE(m.created_at) AS message_date,
           CASE WHEN m.sender_id = ? THEN 1 ELSE 0 END AS is_sender
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
    AND m.id > ?
    AND NOT (
        (m.sender_id = ? AND m.deleted_by_sender = 1)
        OR
        (m.receiver_id = ? AND m.deleted_by_receiver = 1)
    )
    ORDER BY m.created_at ASC
");

$stmt->bind_param("iiiiiiii", 
    $current_user_id,
    $current_user_id,
    $other_id,
    $other_id,
    $current_user_id,
    $last_message_id,
    $current_user_id,
    $current_user_id
);

$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $decrypted_message = openssl_decrypt($row['message'], ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
    $decrypted_message = htmlspecialchars($decrypted_message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $attachment_url = '';
    $is_image = false;
    
    if (!empty($row['attachment'])) {
        $attachment_path = ltrim($row['attachment'], '/\\');
        $attachment_url = ATTACHMENT_URL . $attachment_path;
        $full_path = ATTACHMENT_DIR . $attachment_path;
        if (file_exists($full_path)) {
            $is_image = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $attachment_path);
        } else {
            $attachment_url = '';
        }
    }

    $reactions_query = "SELECT user_id, reaction_type FROM reactions WHERE message_id = ?";
    $reactions_stmt = $conn->prepare($reactions_query);
    $reactions_stmt->bind_param("i", $row['id']);
    $reactions_stmt->execute();
    $reactions_result = $reactions_stmt->get_result();
    
    $reactions = [];
    while ($reaction_row = $reactions_result->fetch_assoc()) {
        $reactions[] = [
            'user_id' => $reaction_row['user_id'],
            'type' => $reaction_row['reaction_type']
        ];
    }
    $reactions_stmt->close();

    $messages[] = [
        'id' => $row['id'],
        'sender_id' => $row['sender_id'],
        'message' => $decrypted_message,
        'created_at' => $row['formatted_time'],
        'date' => $row['message_date'],
        'is_sender' => $row['is_sender'],
        'attachment_url' => $attachment_url,
        'is_image' => $is_image,
        'sender_name' => $row['fullname'],
        'sender_avatar' => $row['profile_picture'],
        'reactions' => $reactions
    ];
}
$stmt->close();

$typing = false;
$typing_stmt = $conn->prepare("
    SELECT is_typing FROM typing_status
    WHERE user_id = ? AND receiver_id = ?
    AND updated_at >= DATE_SUB(NOW(), INTERVAL 3 SECOND)
");
if ($typing_stmt) {
    $typing_stmt->bind_param("ii", $other_id, $current_user_id);
    $typing_stmt->execute();
    $typing_result = $typing_stmt->get_result();
    $typing = $typing_result->num_rows > 0 ? $typing_result->fetch_assoc()['is_typing'] : false;
    $typing_stmt->close();
}

echo json_encode([
    'messages' => $messages,
    'is_typing' => $typing
]);
?>