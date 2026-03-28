<?php
session_start();
require '../backend/db_connection.php';
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$raw_message = isset($_POST['message']) ? trim($_POST['message']) : '';
$client_message_id = isset($_POST['client_message_id']) ? $_POST['client_message_id'] : null;

if ($receiver_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid receiver ID']);
    exit();
}

$attachment_url = '';
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    try {
        if (!is_dir(ATTACHMENT_DIR)) {
            if (!mkdir(ATTACHMENT_DIR, 0755, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }

        $file_info = $_FILES['attachment'];
        $file_name = $file_info['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($file_ext, $allowed_extensions)) {
            throw new Exception('Only JPG, PNG, GIF, and WEBP images are allowed');
        }

        if ($file_info['size'] > 10 * 1024 * 1024) {
            throw new Exception('Image too large (max 10MB)');
        }

        $unique_name = 'img_' . uniqid() . '_' . time() . '.' . $file_ext;
        $file_path = ATTACHMENT_DIR . $unique_name;

        if (move_uploaded_file($file_info['tmp_name'], $file_path)) {
            $attachment_url = $unique_name;
            if (empty($raw_message)) {
                $raw_message = 'Sent an image';
            }
        } else {
            throw new Exception('Failed to move uploaded file');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
}

if (empty($raw_message) && empty($attachment_url)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Message or attachment is required']);
    exit();
}

$raw_message = htmlspecialchars($raw_message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$encrypted_message = openssl_encrypt($raw_message, ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);

try {
    if ($client_message_id) {
        $check_stmt = $conn->prepare("SELECT id FROM messages WHERE client_message_id = ?");
        $check_stmt->bind_param("s", $client_message_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            echo json_encode([
                'status' => 'success',
                'message_id' => $row['id'],
                'client_message_id' => $client_message_id,
                'message' => $raw_message,
                'created_at' => date('h:i A'),
                'attachment_url' => $attachment_url ? ATTACHMENT_URL . $attachment_url : '',
                'is_image' => !empty($attachment_url)
            ]);
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, attachment, client_message_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $sender_id, $receiver_id, $encrypted_message, $attachment_url, $client_message_id);

    if (!$stmt->execute()) {
        throw new Exception('Database error: ' . $stmt->error);
    }

    $message_id = $stmt->insert_id;
    $created_at = date('h:i A');

    echo json_encode([
        'status' => 'success',
        'message_id' => $message_id,
        'client_message_id' => $client_message_id,
        'message' => $raw_message,
        'created_at' => $created_at,
        'attachment_url' => $attachment_url ? ATTACHMENT_URL . $attachment_url : '',
        'is_image' => !empty($attachment_url)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Message sending failed: ' . $e->getMessage(),
        'client_message_id' => $client_message_id
    ]);
}
?>