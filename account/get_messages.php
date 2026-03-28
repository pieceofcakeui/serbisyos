<?php
session_start();
include 'backend/db_connection.php';

header('Content-Type: application/json');

try {
    $user_id = $_SESSION['user_id'] ?? null;
    $shop_id = $_GET['shop_id'] ?? null;
    $last_message_id = $_GET['last_message_id'] ?? 0;
    $exclude_ids = isset($_GET['exclude_ids']) ? json_decode($_GET['exclude_ids']) : [];

    if (!$user_id || !$shop_id) {
        throw new Exception('Missing required parameters');
    }

    $sql = "SELECT m.*, 
                   u.fullname, 
                   u.profile_picture,
                   DATE_FORMAT(m.created_at, '%h:%i %p') AS formatted_time
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR 
                  (m.sender_id = ? AND m.receiver_id = ?))
            AND m.id > ?";

    if (!empty($exclude_ids)) {
        $placeholders = implode(',', array_fill(0, count($exclude_ids), '?'));
        $sql .= " AND m.id NOT IN ($placeholders)";
    }

    $sql .= " ORDER BY m.created_at ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $params = [$user_id, $shop_id, $shop_id, $user_id, $last_message_id];
    if (!empty($exclude_ids)) {
        $params = array_merge($params, $exclude_ids);
    }

    $types = str_repeat('i', count($params));
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception('Database execute error: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'id' => $row['id'],
            'message' => htmlspecialchars($row['message']),
            'created_at' => $row['formatted_time'],
            'is_sender' => ($row['sender_id'] == $user_id),
            'fullname' => $row['fullname'],
            'profile_picture' => $row['profile_picture'],
            'attachment_url' => $row['attachment'],
            'is_image' => !empty($row['attachment']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $row['attachment'])
        ];
    }

    $typing = false;
    $typing_stmt = $conn->prepare("SELECT is_typing FROM typing_status 
                                  WHERE user_id = ? AND receiver_id = ? 
                                  AND updated_at > DATE_SUB(NOW(), INTERVAL 3 SECOND)");
    if ($typing_stmt) {
        $typing_stmt->bind_param("ii", $shop_id, $user_id);
        $typing_stmt->execute();
        $typing_result = $typing_stmt->get_result();
        $typing = $typing_result->num_rows > 0;
        $typing_stmt->close();
    }

    echo json_encode([
        'status' => 'success',
        'messages' => $messages,
        'is_typing' => $typing
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>