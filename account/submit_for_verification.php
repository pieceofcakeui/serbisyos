<?php
session_start();
include 'backend/auth.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

include "backend/db_connection.php";

define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY']);
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL']);


function encryptData($data)
{
    if (empty($data)) {
        return $data;
    }
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv) . ':' . base64_encode($encrypted);
}

function decryptData($data)
{
    if (empty($data) || strpos($data, ':') === false) {
        return $data;
    }
    $parts = explode(':', $data, 2);
    if (count($parts) !== 2) {
        return false;
    }
    $iv = base64_decode($parts[0]);
    $encrypted = base64_decode($parts[1]);
    if ($iv === false || $encrypted === false) {
        return false;
    }
    return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}

function sendVerificationEmail($userEmail, $userName)
{
    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();
    
    $submission_date = date('F j, Y g:i A');

    try {
        $sendSmtpEmail->setTemplateId(16);
        $sendSmtpEmail->setTo([['email' => $userEmail, 'name' => $userName]]);
        $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
        $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
        
        $sendSmtpEmail->setParams([
            'USER_NAME' => $userName,
            'SUBMISSION_DATE' => $submission_date,
            'LOGO_URL' => BASE_URL . '/assets/img/logo.png',
            'BASE_URL' => BASE_URL
        ]);

        $apiInstance->sendTransacEmail($sendSmtpEmail);
        return true;

    } catch (Exception $e) {
        error_log("Brevo API Error (Template 16): " . $e->getMessage());
        return false;
    }
}

function sendAdminNotificationEmail($userName)
{
    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();
    
    $submission_date = date('F j, Y g:i A');
    $review_link = 'https://www.serbisyos.com/admin/login'; 

    try {
        $sendSmtpEmail->setTemplateId(17);
        $sendSmtpEmail->setTo([['email' => ADMIN_EMAIL, 'name' => 'Serbisyos Admin']]);
        $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_ADMIN_EMAIL'], 'name' => 'Serbisyos - Verification Submission']);
        $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_ADMIN_EMAIL'], 'name' => 'Serbisyos - Verification Submission']);
        
        $sendSmtpEmail->setParams([
            'USER_NAME' => $userName,
            'SUBMISSION_DATE' => $submission_date,
            'REVIEW_LINK' => $review_link,
            'LOGO_URL' => BASE_URL . '/assets/img/logo.png',
            'BASE_URL' => BASE_URL
        ]);

        $apiInstance->sendTransacEmail($sendSmtpEmail);
        return true;

    } catch (Exception $e) {
        error_log("Brevo API Error (Template 17 - Admin): " . $e->getMessage());
        return false;
    }
}

function handleUpload($file, $uploadDir, $userId, $side)
{
    $fileExtension = strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION));
    $randomString = bin2hex(random_bytes(16));
    $newFileName = $randomString . '.' . $fileExtension;
    $targetPath = $uploadDir . $newFileName;
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileExtension, $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPG, JPEG, and PNG are allowed.'];
    }
    if ($file['size'] > 5000000) {
        return ['error' => 'File is too large. Maximum size is 5MB.'];
    }
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'path' => $targetPath];
    }
    return ['error' => 'Failed to move uploaded file. Check folder permissions.'];
}

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'DB connection failed.']));
}
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'User not logged in.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $id_type = trim($_POST['id_type'] ?? null);
    $full_name = trim($_POST['full_name'] ?? null);
    $gender = trim($_POST['gender'] ?? null);
    $birthday = trim($_POST['birthday'] ?? null);
    $address_barangay = trim($_POST['address_barangay'] ?? null);
    $address_town_city = trim($_POST['address_town_city'] ?? null);
    $address_province = trim($_POST['address_province'] ?? null);
    $address_postal_code = trim($_POST['address_postal_code'] ?? null);

    if (empty($id_type) || empty($full_name) || empty($gender) || empty($birthday) || empty($address_barangay) || empty($address_town_city) || empty($address_province) || empty($address_postal_code)) {
        die(json_encode(['status' => 'error', 'message' => 'Incomplete personal information. Please fill out all fields.']));
    }

    $encrypted_full_name = encryptData($full_name);
    $encrypted_birthday = encryptData($birthday);
    $encrypted_barangay = encryptData($address_barangay);
    $encrypted_town_city = encryptData($address_town_city);
    $encrypted_province = encryptData($address_province);
    $encrypted_postal_code = encryptData($address_postal_code);

    $uploadDir = 'uploads/v-submissions-data/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            die(json_encode(['status' => 'error', 'message' => 'Failed to create upload directory.']));
        }
    }

    if (!isset($_FILES['frontImage']) || !isset($_FILES['backImage']) || !isset($_FILES['selfieImage'])) {
        die(json_encode(['status' => 'error', 'message' => 'Incomplete file submission. Please upload all required images.']));
    }

    $frontResult = handleUpload($_FILES['frontImage'], $uploadDir, $userId, 'front');
    $backResult = handleUpload($_FILES['backImage'], $uploadDir, $userId, 'back');
    $selfieResult = handleUpload($_FILES['selfieImage'], $uploadDir, $userId, 'selfie');

    if (isset($frontResult['error'])) {
        die(json_encode(['status' => 'error', 'message' => 'Front Image: ' . $frontResult['error']]));
    }
    if (isset($backResult['error'])) {
        die(json_encode(['status' => 'error', 'message' => 'Back Image: ' . $backResult['error']]));
    }
    if (isset($selfieResult['error'])) {
        die(json_encode(['status' => 'error', 'message' => 'Selfie Image: ' . $selfieResult['error']]));
    }

    $checkStmt = $conn->prepare("SELECT id FROM verification_submissions WHERE user_id = ? AND status = 'pending'");
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        die(json_encode(['status' => 'error', 'message' => 'You already have a pending submission.']));
    }
    $checkStmt->close();

    $stmt = $conn->prepare("INSERT INTO verification_submissions (
        user_id, id_type, full_name, gender, birthday, 
        address_barangay, address_town_city, address_province, address_postal_code, 
        front_image_path, back_image_path, selfie_image_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "isssssssssss",
        $userId,
        $id_type,
        $encrypted_full_name,
        $gender,
        $encrypted_birthday,
        $encrypted_barangay,
        $encrypted_town_city,
        $encrypted_province,
        $encrypted_postal_code,
        $frontResult['path'],
        $backResult['path'],
        $selfieResult['path']
    );

    if ($stmt->execute()) {
        $lastInsertId = $stmt->insert_id;

        $notification_type = 'verification';
        $notification_status = 'pending';
        $is_read = 0;
        $delete_notification = 0;

        $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, related_id, notification_type, status, is_read, delete_notification) VALUES (?, ?, ?, ?, ?, ?)");
        
        $notifStmt->bind_param("iissii", $userId, $lastInsertId, $notification_type, $notification_status, $is_read, $delete_notification);
        
        $notifStmt->execute();
        $notifStmt->close();

        $userStmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result()->fetch_assoc();

        if ($userResult) {
            sendVerificationEmail($userResult['email'], $userResult['fullname']);
            sendAdminNotificationEmail($userResult['fullname']);
        }
        $userStmt->close();

        echo json_encode(['status' => 'success', 'message' => 'Your information has been submitted for verification!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: Failed to save submission.']);
    }

    $stmt->close();
    $conn->close();
}
?>

