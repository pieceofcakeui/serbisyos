<?php
include './backend/db_connection.php';
$user_id = $_SESSION['user_id'];
$other_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

function decrypt_data($data)
{
    if (empty($data)) {
        return '';
    }
    return openssl_decrypt($data, ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
}

$unread_count = 0;
$sql_unread = "SELECT COUNT(*) AS unread_count FROM messages 
               WHERE receiver_id = ? AND is_read = 0";
$stmt_unread = $conn->prepare($sql_unread);
$stmt_unread->bind_param("i", $user_id);
$stmt_unread->execute();
$result_unread = $stmt_unread->get_result();
if ($result_unread && $row_unread = $result_unread->fetch_assoc()) {
    $unread_count = $row_unread['unread_count'];
}
$stmt_unread->close();

$sql_conversations = "SELECT 
    u.id AS user_id, 
    u.fullname, 
    u.profile_picture, 
    s.shop_name,
    s.shop_logo,
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
$stmt_conv->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt_conv->execute();
$conversations = $stmt_conv->get_result();

$messages = null;
if ($other_id) {

    $mark_read_sql = "UPDATE messages SET is_read = 1 
                     WHERE receiver_id = ? AND sender_id = ?";
    $mark_read_stmt = $conn->prepare($mark_read_sql);
    $mark_read_stmt->bind_param("ii", $user_id, $other_id);
    $mark_read_stmt->execute();
    $mark_read_stmt->close();

    $sql_messages = "SELECT m.*, u.fullname, u.profile_picture, s.shop_name, s.shop_logo
                    FROM messages m 
                    JOIN users u ON m.sender_id = u.id
                    LEFT JOIN shop_applications s ON u.id = s.user_id
                    WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                    OR (m.sender_id = ? AND m.receiver_id = ?)
                    ORDER BY m.created_at ASC";

    $stmt_msg = $conn->prepare($sql_messages);
    $stmt_msg->bind_param("iiii", $user_id, $other_id, $other_id, $user_id);
    $stmt_msg->execute();
    $messages = $stmt_msg->get_result();

    $user_sql = "SELECT u.*, s.shop_name, s.shop_logo 
                FROM users u
                LEFT JOIN shop_applications s ON u.id = s.user_id
                WHERE u.id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $other_id);
    $user_stmt->execute();
    $other_user = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();

}
?>