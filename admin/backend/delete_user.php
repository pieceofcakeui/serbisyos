<?php
require_once 'db_connection.php';

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;

require '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if (!$conn || $conn->connect_error) {
    session_start();
    $_SESSION['error_message'] = "Database connection failed.";
    header('Location: user-management.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
    http_response_code(400);
    exit();
}

$userId = (int)$_POST['user_id'];
session_start();

try {
    $conn->begin_transaction();

    $userQuery = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
    $userQuery->bind_param("i", $userId);
    $userQuery->execute();
    $result = $userQuery->get_result();
    $userData = $result->fetch_assoc();
    $userQuery->close();

    if (!$userData) {
        $_SESSION['error_message'] = "User not found.";
        header('Location: user-management.php');
        exit();
    }
    
    $shopIds = [];
    $getShopsQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $getShopsQuery->bind_param("i", $userId);
    $getShopsQuery->execute();
    $shopResult = $getShopsQuery->get_result();
    while ($row = $shopResult->fetch_assoc()) {
        $shopIds[] = $row['id'];
    }
    $getShopsQuery->close();

    if (!empty($shopIds)) {
        $placeholders = implode(',', array_fill(0, count($shopIds), '?'));
        
        $shopDependentTables = [
            'emergency_requests'
        ];

        foreach ($shopDependentTables as $table) {
             $checkTable = $conn->query("SHOW TABLES LIKE '$table'");
             if ($checkTable->num_rows > 0) {
                 $deleteStmt = $conn->prepare("DELETE FROM `$table` WHERE `shop_id` IN ($placeholders)");
                 $deleteStmt->bind_param(str_repeat('i', count($shopIds)), ...$shopIds);
                 $deleteStmt->execute();
                 $deleteStmt->close();
             }
        }
    }

   $tables = [
        'shop_applications'         => 'user_id',
        'user_2fa_backup_codes'     => 'user_id',
        'user_2fa'                  => 'user_id',
        'shop_ratings'              => 'user_id',
        'services_booking'          => 'user_id',
        'save_shops'                => 'user_id',
        'review_likes'              => 'liked_by_user_id',
        'respond_reviews'           => 'user_id',
        'remember_token'            => 'user_id',
        'otp_verifications'         => 'user_id',
        'notifications'             => 'user_id',
        'emergency_requests'        => 'user_id',
        'data_download_requests'    => 'user_id',
        'activity_log'              => 'user_id',
        'active_sessions'           => 'user_id',
        'reactions'                 => 'user_id',
        'chatbot'                   => 'user_id',
        'reports'                   => 'user_id',
        'shop_emergency_config'     => 'user_id',
        'shop_booking_form'         => 'user_id',
        'shop_auto_messages'        => 'user_id',
        'verification_submissions'  => 'user_id'
    ];

    foreach ($tables as $table => $column) {
        $checkTable = $conn->query("SHOW TABLES LIKE '$table'");
        if ($checkTable->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$column` = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    $stmtMessages = $conn->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?");
    $stmtMessages->bind_param("ii", $userId, $userId);
    $stmtMessages->execute();
    $stmtMessages->close();

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $conn->commit();
        
        try {
            $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
            if (!empty($apiKey)) {
                $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                $apiInstance = new TransactionalEmailsApi(new GuzzleClient(), $config);

                $sendSmtpEmail = new SendSmtpEmail([
                    'to' => [[
                        'email' => $userData['email'], 
                        'name' => $userData['fullname']
                    ]],
                    'templateId' => 27,
                    'params' => [
                        'USER_NAME' => $userData['fullname'],
                        'DELETION_DATE' => date('F j, Y \a\t g:i A'),
                        'BASE_URL' => $_ENV['BASE_URL'],
                        'SUPPORT_EMAIL' => $_ENV['SUPPORT_EMAIL'],
                        'LOGO_URL' => $_ENV['BASE_URL'] . '/assets/img/logo.png'
                    ],
                    'subject' => "Confirmation of Account Deletion - Serbisyos",
                    'sender' => [
                        'name' => 'Serbisyos Team', 
                        'email' => $_ENV['NO_REPLY_EMAIL']
                    ],
                    'replyTo' => [
                        'name' => 'Serbisyos Support', 
                        'email' => $_ENV['SUPPORT_EMAIL']
                    ]
                ]);

                $apiInstance->sendTransacEmail($sendSmtpEmail);
            }
        } catch (Exception $e) {
            error_log("Brevo Mailer Error for account deletion: " . $e->getMessage());
        }

        $_SESSION['success_message'] = "User and all associated data have been successfully deleted.";
        header('Location: ../user-management.php');
        exit();

    } else {
        $conn->rollback();
        $_SESSION['error_message'] = "Delete failed. User not found or could not be deleted.";
        header('Location: ../user-management.php');
        exit();
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    if ($conn && $conn->thread_id) {
        $conn->rollback();
    }
    error_log("Error deleting user: " . $e->getMessage());
    $_SESSION['error_message'] = "An unexpected error occurred. Please check the server logs.";
    header('Location: ../user-management.php');
    exit();
}
?>