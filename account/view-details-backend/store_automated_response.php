<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'This script only accepts POST requests.']);
    exit();
}

include '../backend/db_connection.php';
require_once 'config.php';

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

try {
    $sender_id = isset($_POST['sender_id']) ? intval($_POST['sender_id']) : 0;
    $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
    $raw_message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $is_automated = isset($_POST['is_automated']) ? intval($_POST['is_automated']) : 1;
    $client_message_id = isset($_POST['client_message_id']) ? $_POST['client_message_id'] : null;

    if ($sender_id <= 0) {
        throw new Exception('Invalid or missing Sender ID.');
    }
    if ($receiver_id <= 0) {
        throw new Exception('Invalid or missing Receiver ID.');
    }
    if (empty($raw_message)) {
        throw new Exception('Message content cannot be empty.');
    }

    $encrypted_message = openssl_encrypt($raw_message, ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
    if ($encrypted_message === false) {
        throw new Exception('Message encryption failed.');
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, is_automated, client_message_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    if ($stmt === false) {
        throw new Exception('Failed to prepare the database statement: ' . $conn->error);
    }

    $stmt->bind_param("iisis", $sender_id, $receiver_id, $encrypted_message, $is_automated, $client_message_id);

    if (!$stmt->execute()) {
        throw new Exception('Failed to save message to the database: ' . $stmt->error);
    }

    $message_id = $stmt->insert_id;
    $stmt->close();
    $conn->close();

    echo json_encode([
        'status' => 'success',
        'message_id' => $message_id,
        'client_message_id' => $client_message_id,
        'message' => 'Automated message stored successfully.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>