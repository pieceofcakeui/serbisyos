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
$other_id = $_POST['shop_owner_id'] ?? $_POST['other_id'] ?? $_GET['user_id'] ?? null;

if (!$other_id) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$last_message_id = isset($_POST['last_message_id']) ? intval($_POST['last_message_id']) : 0;
$exclude_ids = isset($_POST['exclude_pending']) ? $_POST['exclude_pending'] : [];

$exclude_clause = '';
if (!empty($exclude_ids) && is_array($exclude_ids)) {
    $exclude_placeholders = implode(',', array_fill(0, count($exclude_ids), '?'));
    $exclude_clause = " AND m.id NOT IN ($exclude_placeholders)";
    $exclude_ids = array_map('intval', $exclude_ids);
}

$query = "
    SELECT 
        m.*,
        DATE_FORMAT(m.created_at, '%h:%i %p') AS formatted_time,
        u.fullname,
        u.profile_picture
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE 
        m.id > ? AND
      (
 (m.sender_id = ? AND m.receiver_id = ? AND m.deleted_by_sender = 0) OR
 (m.sender_id = ? AND m.receiver_id = ? AND m.deleted_by_receiver = 0)
 )
 AND m.is_deleted = 0
 $exclude_clause
    ORDER BY m.created_at ASC
";

$stmt = $conn->prepare($query);

$params = [$last_message_id, $current_user_id, $other_id, $other_id, $current_user_id];
if (!empty($exclude_ids)) {
    $params = array_merge($params, $exclude_ids);
}
$types = str_repeat('i', count($params));

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $is_sender = $row['sender_id'] == $current_user_id;
    $decrypted_message = openssl_decrypt($row['message'], ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);

    $attachment_url = '';
    $is_image = false;
    if (!empty($row['attachment'])) {
        $attachment_url = ATTACHMENT_URL . ltrim($row['attachment'], '/\\');
        $is_image = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $row['attachment']);
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

    $messages[] = [
        'id' => $row['id'],
        'message' => htmlspecialchars($decrypted_message),
        'created_at' => $row['formatted_time'],
        'is_sender' => $is_sender,
        'attachment_url' => $attachment_url,
        'is_image' => $is_image,
        'sender_name' => $row['fullname'],
        'sender_avatar' => $row['profile_picture'],
        'reactions' => $reactions,
        'is_automated' => (bool)$row['is_automated']
    ];
}

echo json_encode([
    'messages' => $messages
]);

if (isset($stmt)) $stmt->close();
if (isset($reactions_stmt)) $reactions_stmt->close();
$conn->close();
?>