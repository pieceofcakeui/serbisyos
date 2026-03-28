<?php
session_start();
if (!defined('BASE_URL')) {
    define('BASE_URL', $_ENV['BASE_URL'] ?? 'https://www.serbisyos.com'); 
}

if (isset($_SESSION['last_emergency_submission']) && (time() - $_SESSION['last_emergency_submission']) < 30) {
    sendJsonResponse(false, 'Please wait before submitting another request');
}

$_SESSION['last_emergency_submission'] = time();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

ob_start();

header('Content-Type: application/json');

if (!file_exists('db_connection.php')) {
    sendJsonResponse(false, 'Database configuration file missing');
}

require_once 'db_connection.php';
require '../../vendor/autoload.php';
require_once '../../account/backend/utilities.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

define('ENCRYPTION_KEY',  $_ENV['ENCRYPTION_KEY']);
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('ENCRYPTION_IV_LENGTH', openssl_cipher_iv_length(ENCRYPTION_METHOD));
define('UPLOAD_DIR', '../uploads/emergency_videos/');
define('NO_REPLY_EMAIL', $_ENV['NO_REPLY_EMAIL'] ?? 'noreply@serbisyos.com');


if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

function encryptData($data) {
    if (empty($data)) return '';
    $iv = openssl_random_pseudo_bytes(ENCRYPTION_IV_LENGTH);
    $encrypted = openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptData($data) {
    if (empty($data)) return '';
    $data = base64_decode($data);
    $iv = substr($data, 0, ENCRYPTION_IV_LENGTH);
    $encrypted = substr($data, ENCRYPTION_IV_LENGTH);
    return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}

function handleFileUploads($request_id) {
    $uploaded_files = [];
    
    if (!empty($_FILES['emergency_video']['tmp_name'])) {
        $file_size = $_FILES['emergency_video']['size'];
        $file_tmp = $_FILES['emergency_video']['tmp_name'];
        $file_type = $_FILES['emergency_video']['type'];
        $file_ext = strtolower(pathinfo($_FILES['emergency_video']['name'], PATHINFO_EXTENSION));
        
        if ($file_size > 100 * 1024 * 1024) {
            return null;
        }

        if ($file_ext === 'mp4' && $file_type === 'video/mp4') {
            $new_file_name = "emergency_{$request_id}_" . uniqid() . ".mp4";
            $destination = UPLOAD_DIR . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $uploaded_files[] = $new_file_name;
            }
        }
    }
    
    return !empty($uploaded_files) ? json_encode($uploaded_files) : null;
}

function sendJsonResponse($success, $message = '', $data = []) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($success ? 200 : 400);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function sendEmergencyNotification($shop_email, $shop_owner, $user_fullname, $user_email, $request_details)
{
    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();

    try {
        $sendSmtpEmail->setTemplateId(22);
        $sendSmtpEmail->setTo([['email' => $shop_email, 'name' => $shop_owner]]);
        $sendSmtpEmail->setSender(['email' => NO_REPLY_EMAIL, 'name' => 'Serbisyos Emergency']);
        $sendSmtpEmail->setReplyTo(['email' => $user_email, 'name' => $user_fullname]);
        
        $sendSmtpEmail->setParams([
            'SHOP_NAME' => $request_details['business_name'],
            'USER_NAME' => $user_fullname,
            'USER_EMAIL' => $user_email,
            'CONTACT_NUMBER' => $request_details['decrypted_contact'],
            'VEHICLE_TYPE' => $request_details['vehicle_type'],
            'VEHICLE_MODEL' => $request_details['vehicle_model'],
            'ISSUE_DESCRIPTION' => $request_details['issue_description'],
            'FULL_ADDRESS' => $request_details['decrypted_address'],
            'MAPS_LINK' => $request_details['google_maps_link'],
            'IS_URGENT' => $request_details['urgent'] ? 'Yes' : 'No',
            'URGENT_TAG' => $request_details['urgent'] ? '⚠️ URGENT ASSISTANCE REQUIRED' : 'New Request',
            'LOGO_URL' => BASE_URL . '/assets/img/logo.png',
            'BASE_URL' => BASE_URL
        ]);

        $apiInstance->sendTransacEmail($sendSmtpEmail);
        return true;

    } catch (Exception $e) {
        error_log("Brevo API Error (Template 22 - Emergency): " . $e->getMessage());
        return false;
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Invalid request method');
    }

    $has_file = !empty($_FILES['emergency_video']['tmp_name']);
    $data = $_POST;

    $requiredFields = [
        'shop_id',
        'shop_user_id',
        'vehicle_type',
        'vehicle_model',
        'issue_description',
        'full_address',
        'contact_number',
        'latitude',
        'longitude'
    ];

    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        sendJsonResponse(false, 'Missing required fields: ' . implode(', ', $missingFields));
    }

    $is_logged_in = isset($_SESSION['user_id']);
    $user_id = $is_logged_in ? $_SESSION['user_id'] : $data['shop_user_id']; 


    $shop_id = $conn->real_escape_string($data['shop_id']);
    $shop_user_id = $conn->real_escape_string($data['shop_user_id']);
    $vehicle_type = $conn->real_escape_string($data['vehicle_type']);
    $vehicle_model = $conn->real_escape_string($data['vehicle_model']);
    $issue_description = $conn->real_escape_string($data['issue_description']);
    $urgent = isset($data['urgent']) ? (int) $data['urgent'] : 0;

    $full_address = encryptData($conn->real_escape_string($data['full_address']));
    $contact_number_raw = $conn->real_escape_string($data['contact_number']);
    $contact_number_encrypted = encryptData($contact_number_raw);
    
    $latitude_raw = $conn->real_escape_string($data['latitude']);
    $longitude_raw = $conn->real_escape_string($data['longitude']);
    $latitude = encryptData($latitude_raw);
    $longitude = encryptData($longitude_raw);
    $location = "Location coordinates encrypted for security";

    $conn->begin_transaction();

    $sql = "INSERT INTO emergency_requests (
        user_id, shop_id, shop_user_id, vehicle_type, vehicle_model,
        issue_description, full_address, location, contact_number,
        urgent, latitude, longitude, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param(
        "iisssssssiss",
        $user_id,
        $shop_id,
        $shop_user_id,
        $vehicle_type,
        $vehicle_model,
        $issue_description,
        $full_address,
        $location,
        $contact_number_encrypted,
        $urgent,
        $latitude,
        $longitude
    );

    if (!$stmt->execute()) {
        throw new Exception('Database execute error: ' . $stmt->error);
    }

    $request_id = $stmt->insert_id;
    $stmt->close();

    $video_json = null;
    if ($has_file) {
        $video_json = handleFileUploads($request_id);
        
        if ($video_json) {
            $update_sql = "UPDATE emergency_requests SET video = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            if ($stmt) {
                $stmt->bind_param("si", $video_json, $request_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update video: ' . $stmt->error);
                }
                $stmt->close();
            }
        }
    }

    $shop_query = "SELECT sa.email, sa.shop_name, u.fullname as shop_owner
                   FROM shop_applications sa
                   JOIN users u ON sa.user_id = u.id
                   WHERE sa.id = ? AND sa.status = 'approved'";
    $stmt = $conn->prepare($shop_query);
    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param("i", $shop_id);
    if (!$stmt->execute()) {
        throw new Exception('Database execute error: ' . $stmt->error);
    }

    $shop_result = $stmt->get_result();

    if ($shop_result->num_rows === 0) {
        $stmt->close();
        throw new Exception('Shop not found or not approved');
    }

    $shop_data = $shop_result->fetch_assoc();
    $shop_email = $shop_data['email'];
    $business_name = $shop_data['shop_name'];
    $shop_owner = $shop_data['shop_owner'];
    $stmt->close();

    $user_query = "SELECT fullname, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($user_query);
    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception('Database execute error: ' . $stmt->error);
    }

    $user_result = $stmt->get_result();

    if ($user_result->num_rows === 0) {
        $stmt->close();
        throw new Exception("User not found with ID: $user_id");
    }

    $user_data = $user_result->fetch_assoc();
    $user_fullname = $user_data['fullname'];
    $user_email = $user_data['email'];
    $stmt->close();
    
    $google_maps_link = "https://www.google.com/maps?q={$latitude_raw},{$longitude_raw}";

    $request_details = [
        'business_name' => $business_name,
        'decrypted_contact' => $contact_number_raw,
        'vehicle_type' => $vehicle_type,
        'vehicle_model' => $vehicle_model,
        'issue_description' => $issue_description,
        'decrypted_address' => $data['full_address'],
        'google_maps_link' => $google_maps_link,
        'urgent' => $urgent,
    ];

    $mail_sent = sendEmergencyNotification($shop_email, $shop_owner, $user_fullname, $user_email, $request_details);

    $conn->commit();

    $notif_for_customer = $conn->prepare("INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status, is_read, delete_notification) VALUES (?, ?, 'emergency_sent', ?, 'pending', 0, 0)");
    if ($notif_for_customer) {
        $notif_for_customer->bind_param("iii", $user_id, $shop_id, $request_id);
        $notif_for_customer->execute();
        $notif_for_customer->close();
    }

    $notif_for_owner = $conn->prepare("INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status, is_read, delete_notification) VALUES (?, ?, 'emergency_received', ?, 'pending', 0, 0)");
    if ($notif_for_owner) {
        $notif_for_owner->bind_param("iii", $shop_user_id, $shop_id, $request_id);
        $notif_for_owner->execute();
        $notif_for_owner->close();

        try {
            $push_title = $urgent ? '⚠️ URGENT EMERGENCY REQUEST' : 'New Emergency Request';
            $push_body = 'You have received a new request from ' . htmlspecialchars($user_fullname) . '.';
            $push_url = "/account/emergency-request";
            sendPushNotification($conn, $shop_user_id, $push_title, $push_body, $push_url);
        } catch (Exception $e) {
            error_log("Web Push Notification Error (Emergency Request): " . $e->getMessage());
        }
    }

    sendJsonResponse(true, 'Emergency request submitted successfully' . ($mail_sent ? '' : ' (email notification failed)'), [
        'request_id' => $request_id,
        'shop_name' => $business_name,
        'video_uploaded' => $video_json ? 1 : 0
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }
    error_log('Emergency request error: ' . $e->getMessage());
    sendJsonResponse(false, 'An error occurred while processing your request: ' . $e->getMessage());
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>