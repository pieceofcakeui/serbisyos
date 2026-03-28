<?php
include '../backend/db_connection.php';
session_start();

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in.');
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception('Database error');
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Only shop owners can save auto messages');
    }

    $shop = $result->fetch_assoc();
    $shop_id = $shop['id'];
    
    $welcome_message = isset($_POST['welcome_message']) ? trim($_POST['welcome_message']) : '';
    $quick_replies = [];
    $filled_options = [];
    
    for ($i = 1; $i <= 5; $i++) {
        $label = isset($_POST["option{$i}_label"]) ? trim($_POST["option{$i}_label"]) : '';
        $response = isset($_POST["option{$i}_response"]) ? trim($_POST["option{$i}_response"]) : '';
        
        $quick_replies[] = [
            'option_number' => $i,
            'label' => $label,
            'response' => $response,
            'is_filled' => (!empty($label) && !empty($response))
        ];
        
        if (!empty($label) && !empty($response)) {
            $filled_options[] = $i;
        }
    }
    
    $quick_replies_data = [
        'options' => $quick_replies,
        'filled_options' => $filled_options,
        'total_filled' => count($filled_options)
    ];
    
    $quick_replies_json = json_encode($quick_replies_data);

    $stmt = $conn->prepare("SELECT id FROM shop_auto_messages WHERE shop_id = ?");
    if (!$stmt) {
        throw new Exception('Database error');
    }
    
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE shop_auto_messages SET welcome_message = ?, quick_replies = ?, updated_at = CURRENT_TIMESTAMP WHERE shop_id = ?");
        if (!$stmt) {
            throw new Exception('Database error');
        }
        $stmt->bind_param("ssi", $welcome_message, $quick_replies_json, $shop_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO shop_auto_messages (shop_id, user_id, welcome_message, quick_replies, created_at, updated_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        if (!$stmt) {
            throw new Exception('Database error');
        }
        $stmt->bind_param("iiss", $shop_id, $user_id, $welcome_message, $quick_replies_json);
    }

    if ($stmt->execute()) {
        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'Settings saved successfully!',
            'filled_count' => count($filled_options)
        ];
    } else {
        throw new Exception('Database error');
    }

} catch (Exception $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => $e->getMessage()
    ];
}

header("Location: ../inbox.php");
exit;
?>