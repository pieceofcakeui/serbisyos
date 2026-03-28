<?php
session_start();
require_once 'db_connection.php';
require '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

define('BASE_URL', $_ENV['BASE_URL']);

if (!isset($_SESSION['user_id'])) {
    $_SESSION['delete-error'] = "You must be logged in to delete your account.";
    header("Location: ../../home");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['delete-error'] = "Invalid security token. Please refresh the page and try again.";
    header("Location: ../settings-and-privacy.php");
    exit();
}

if (!isset($_POST['deleteVerification']) || $_POST['deleteVerification'] !== 'DELETE MY ACCOUNT') {
    $_SESSION['delete-error'] = "Verification text did not match. Please type exactly: DELETE MY ACCOUNT";
    header("Location: ../settings-and-privacy.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_email = $user['email'];
$user_name = $user['fullname'] ?? $user_email;
$stmt->close();

$conn->begin_transaction();

try {
    $related_tables = [
        'emergency_requests'       => 'user_id',
        'shop_applications'        => 'user_id',
        'user_2fa_backup_codes'    => 'user_id',
        'user_2fa'                 => 'user_id',
        'shop_ratings'             => 'user_id',
        'services_booking'         => 'user_id',
        'save_shops'               => 'user_id',
        'review_likes'             => 'liked_by_user_id',
        'respond_reviews'          => 'user_id',
        'remember_tokens'          => 'user_id',
        'otp_verifications'        => 'user_id',
        'notifications'            => 'user_id',
        'data_download_requests'   => 'user_id',
        'activity_log'             => 'user_id',
        'active_sessions'          => 'user_id',
        'reactions'                => 'user_id',
        'chatbot'                  => 'user_id',
        'reports'                  => 'user_id',
        'shop_emergency_config'    => 'user_id',
        'shop_booking_form'        => 'user_id',
        'shop_auto_messages'       => 'user_id',
        'verification_submissions' => 'user_id'
    ];

    $stmt = $conn->prepare("DELETE FROM chatbot_messages WHERE chat_id IN (SELECT id FROM chatbot WHERE user_id = ?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM reactions WHERE message_id IN (SELECT id FROM messages WHERE sender_id = ? OR receiver_id = ?)");
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?");
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $stmt->close();

    $shop_ids = [];
    $stmt_shop = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $stmt_shop->bind_param("i", $user_id);
    $stmt_shop->execute();
    $result_shop = $stmt_shop->get_result();
    while ($row = $result_shop->fetch_assoc()) {
        $shop_ids[] = $row['id'];
    }
    $stmt_shop->close();

    if (!empty($shop_ids)) {
        $shop_related_tables = [
            'reports' => 'shop_id',
            'save_shops' => 'shop_id',
            'shop_ratings' => 'shop_id',
            'services_booking' => 'shop_id',
            'emergency_requests' => 'shop_id'
        ];
        
        foreach ($shop_related_tables as $table => $column) {
            $in_clause = implode(',', array_fill(0, count($shop_ids), '?'));
            $types = str_repeat('i', count($shop_ids));
            $stmt = $conn->prepare("DELETE FROM $table WHERE $column IN ($in_clause)");
            $stmt->bind_param($types, ...$shop_ids);
            $stmt->execute();
            $stmt->close();
        }
    }

    foreach ($related_tables as $table => $column) {
        $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$column` = ?");
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
             throw new Exception("Error deleting from $table: " . $stmt->error);
        }
        $stmt->close();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("No user found to delete.");
    }
    $stmt->close();

    $conn->commit();

    $deletion_date = date('F j, Y g:i A');

    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();

    try {
        $sendSmtpEmail->setTemplateId(20);
        $sendSmtpEmail->setTo([['email' => $user_email, 'name' => $user_name]]);
        $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
        $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
        
        $sendSmtpEmail->setParams([
            'USER_NAME' => $user_name,
            'USER_EMAIL' => $user_email,
            'DELETION_DATE' => $deletion_date,
            'LOGO_URL' => BASE_URL . '/assets/img/logo.png',
            'BASE_URL' => BASE_URL
        ]);

        $apiInstance->sendTransacEmail($sendSmtpEmail);

    } catch (Exception $e) {
        error_log("Brevo API Error (Template 20): " . $e->getMessage());
    }

    $_SESSION['delete-success'] = "Your account has been successfully deleted. We're sorry to see you go.";

    session_unset();
    session_destroy();

    header("Location: ../../home");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['delete-error'] = "Failed to delete your account. Please try again. Error: " . $e->getMessage();
    header("Location: ../settings-and-privacy.php");
    exit();
}
