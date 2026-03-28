<?php
session_start();
include 'db_connection.php';
include '../inbox-backend/config.php';
include '../../functions/auth.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql_conversations = "SELECT 
    u.id AS user_id, 
    u.fullname, 
    u.profile_picture, 
    s.shop_name,
    s.shop_logo,
    s.id AS shop_id,
    m.message, 
    m.attachment, 
    m.created_at, 
    m.is_read, 
    m.sender_id,
    (SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND sender_id = u.id AND is_read = 0) AS unread_count
FROM messages m
JOIN users u ON u.id = IF(m.sender_id = ?, m.receiver_id, m.sender_id)
LEFT JOIN shop_applications s ON u.id = s.user_id
WHERE m.id IN (
    SELECT MAX(id) FROM messages 
    WHERE sender_id = ? OR receiver_id = ?
    GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)
)
ORDER BY m.created_at DESC";

$stmt_conv = $conn->prepare($sql_conversations);
if (!$stmt_conv) {
    echo json_encode(['error' => 'Database error']);
    exit;
}

$stmt_conv->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
if (!$stmt_conv->execute()) {
    echo json_encode(['error' => 'Database error']);
    exit;
}

$result = $stmt_conv->get_result();
if (!$result) {
    echo json_encode(['error' => 'Database error']);
    exit;
}

$conversations = [];
while ($conv = $result->fetch_assoc()) {
    $has_attachment = !empty($conv['attachment']);
    $decrypted_message = openssl_decrypt($conv['message'], ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
    
    $preview_text = $has_attachment
        ? (empty($decrypted_message) ? 'Sent an image' : htmlspecialchars($decrypted_message))
        : htmlspecialchars($decrypted_message);

    $display_name = !empty($conv['shop_name']) ? htmlspecialchars($conv['shop_name']) : htmlspecialchars($conv['fullname']);

    if (!empty($conv['shop_name']) && !empty($conv['shop_logo'])) {
        $image_path = './uploads/shop_logo/' . $conv['shop_logo'];
        if (!file_exists($image_path)) {
            $image_path = './uploads/shop_logo/logo.jpg';
        }
    } else {
        $image_path = !empty($conv['profile_picture'])
            ? '../assets/img/profile/' . $conv['profile_picture']
            : '../assets/img/profile/profile-user.png';
    }

    $conversations[] = [
        'user_id' => $conv['user_id'],
        'display_name' => $display_name,
        'avatar' => $image_path,
        'preview' => $preview_text,
        'time' => (new DateTime($conv['created_at']))->format('h:i A'),
        'unread_count' => $conv['unread_count'],
        'is_read' => $conv['is_read'],
        'sender_id' => $conv['sender_id'],
        'shop_debug' => [
            'shop_logo' => !empty($conv['shop_logo']) ? $conv['shop_logo'] : 'no logo',
            'shop_id' => !empty($conv['shop_id']) ? $conv['shop_id'] : 'no shop id',
            'shop_name' => $conv['shop_name'] ?? 'no shop name',
            'user_id' => $conv['user_id']
        ]
    ];
}

echo json_encode(['conversations' => $conversations]);
?>