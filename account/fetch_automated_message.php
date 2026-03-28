<?php
include 'backend/db_connection.php';
session_start();

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in.');
    }

    $shop_owner_id = isset($_POST['shop_owner_id']) ? (int)$_POST['shop_owner_id'] : 0;
    if ($shop_owner_id <= 0) {
        throw new Exception('Invalid shop owner ID.');
    }

    $stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $stmt->bind_param("i", $shop_owner_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Shop not found.');
    }

    $shop = $result->fetch_assoc();
    $shop_id = $shop['id'];

    $stmt = $conn->prepare("SELECT welcome_message, quick_replies FROM shop_auto_messages WHERE shop_id = ?");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'welcome_message' => null,
            'quick_replies' => null
        ]);
        exit;
    }

    $auto_messages = $result->fetch_assoc();
    $quick_replies = json_decode($auto_messages['quick_replies'], true);

    echo json_encode([
        'welcome_message' => $auto_messages['welcome_message'],
        'quick_replies' => $quick_replies
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}