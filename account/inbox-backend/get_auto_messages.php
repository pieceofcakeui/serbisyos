<?php
include 'backend/db_connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$auto_messages = [
    'welcome_message' => '',
    'quick_replies' => [
        'options' => [],
        'filled_options' => [],
        'total_filled' => 0
    ]
];

try {
    if (!isset($_SESSION['user_id'])) {
        return $auto_messages;
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    if (!$stmt) {
        error_log('Database prepare error: ' . $conn->error);
        return $auto_messages;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return $auto_messages;
    }

    $shop = $result->fetch_assoc();
    $shop_id = $shop['id'];

    $stmt = $conn->prepare("SELECT welcome_message, quick_replies FROM shop_auto_messages WHERE shop_id = ?");
    if (!$stmt) {
        error_log('Database prepare error: ' . $conn->error);
        return $auto_messages;
    }
    
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        $auto_messages['welcome_message'] = $data['welcome_message'] ?? '';

        if (!empty($data['quick_replies'])) {
            $quick_replies_data = json_decode($data['quick_replies'], true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($quick_replies_data)) {
                $auto_messages['quick_replies'] = $quick_replies_data;

                while (count($auto_messages['quick_replies']['options']) < 5) {
                    $option_number = count($auto_messages['quick_replies']['options']) + 1;
                    $auto_messages['quick_replies']['options'][] = [
                        'option_number' => $option_number,
                        'label' => '',
                        'response' => '',
                        'is_filled' => false
                    ];
                }
            }
        }
    }

    if (empty($auto_messages['quick_replies']['options'])) {
        for ($i = 1; $i <= 5; $i++) {
            $auto_messages['quick_replies']['options'][] = [
                'option_number' => $i,
                'label' => '',
                'response' => '',
                'is_filled' => false
            ];
        }
        $auto_messages['quick_replies']['filled_options'] = [];
        $auto_messages['quick_replies']['total_filled'] = 0;
    }
    
} catch (Exception $e) {
    error_log('Error retrieving auto messages: ' . $e->getMessage());
    return $auto_messages;
}

return $auto_messages;
?>