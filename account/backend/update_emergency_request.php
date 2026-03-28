<?php
session_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

require '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

require_once 'db_connection.php';
require_once 'encrypt_loc.php';
require_once 'utilities.php';

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$request_id = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
$status = isset($_POST['status']) ? trim($_POST['status']) : null;

if (!$request_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$allowed_statuses = ['accepted', 'rejected', 'completed'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $check_query = "SELECT er.*, sa.shop_name, sa.user_id as shop_owner_id, 
                           u.email, u.fullname as customer_name
                           FROM emergency_requests er
                           JOIN shop_applications sa ON er.shop_id = sa.id
                           JOIN users u ON er.user_id = u.id
                           WHERE er.id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $request_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Request not found']);
        exit;
    }

    $request = $check_result->fetch_assoc();

    if ($_SESSION['user_id'] != $request['shop_owner_id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized - not shop owner']);
        exit;
    }

    $decryptedAddress = decryptData($request['full_address']);
    $decryptedContact = decryptData($request['contact_number']);
    $customer_name = $request['customer_name'] ?? $request['email'];
    $status_uc = strtoupper($status);
    $app_url = $_ENV['APP_URL'] ?? 'https://serbisyos.com';
    $logo_url = $app_url . '/assets/img/logo.png';

    if ($status === 'completed') {
        $update_query = "UPDATE emergency_requests SET status = ?, completed_at = NOW(), is_read = 0 WHERE id = ?";
    } else {
        $update_query = "UPDATE emergency_requests SET status = ?, is_read = 0 WHERE id = ?";
    }

    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $status, $request_id);

    if ($update_stmt->execute()) {
        $response = [
            'success' => true,
            'message' => 'Request has been ' . $status . ' successfully',
            'newStatus' => $status
        ];

        try {
            $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
            if (empty($apiKey)) throw new Exception("Brevo API Key not configured");
            if (empty($_ENV['NO_REPLY_EMAIL']) || empty($_ENV['SUPPORT_EMAIL'])) throw new Exception("Missing email environment variables");

            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
            $apiInstance = new TransactionalEmailsApi(new GuzzleClient(), $config);

            $emailParams = [
                'CUSTOMER_NAME' => $customer_name,
                'SHOP_NAME' => $request['shop_name'],
                'VEHICLE_TYPE' => $request['vehicle_type'],
                'VEHICLE_MODEL' => $request['vehicle_model'],
                'ISSUE_DESC' => $request['issue_description'],
                'FULL_ADDRESS' => $decryptedAddress,
                'CONTACT_NUMBER' => $decryptedContact,
                'STATUS_UC' => $status_uc,
                'BASE_URL' => $app_url,
                'SUPPORT_EMAIL' => $_ENV['SUPPORT_EMAIL'],
                'LOGO_URL' => $logo_url
            ];

            $sendSmtpEmail = new SendSmtpEmail([
                'to' => [['email' => $request['email'], 'name' => $customer_name]],
                'templateId' => 25,
                'params' => $emailParams,
                'subject' => "Emergency Request Update: Your Request Has Been " . $status_uc,
                'sender' => ['name' => $request['shop_name'], 'email' => $_ENV['NO_REPLY_EMAIL']],
                'replyTo' => ['name' => 'Serbisyos Support', 'email' => $_ENV['SUPPORT_EMAIL']]
            ]);

            $apiInstance->sendTransacEmail($sendSmtpEmail);
            $response['emailSent'] = true;
        } catch (Exception $e) {
            error_log("Brevo Mailer Error: " . $e->getMessage());
            $response['mailError'] = 'Email notification failed to send but status was updated';
        }

        $notification_recipient_id = $request['user_id'];
        $shop_id = $request['shop_id'];

        if ($notification_recipient_id && $shop_id) {
            $notification_type = 'emergency_' . $status;
            $notification_query = "INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $notification_stmt = $conn->prepare($notification_query);
            if ($notification_stmt) {
                $notification_stmt->bind_param("iisis", $notification_recipient_id, $shop_id, $notification_type, $request_id, $status);
                $notification_stmt->execute();
                $notification_stmt->close();
            }
        }

        try {
            $shop_name = $request['shop_name'];
            $customer_user_id = $request['user_id'];
            $push_title = '';
            $push_body = '';

            switch ($status) {
                case 'accepted':
                    $push_title = 'Request Accepted!';
                    $push_body = htmlspecialchars($shop_name) . ' is on the way to help you.';
                    break;
                case 'rejected':
                    $push_title = 'Request Rejected';
                    $push_body = htmlspecialchars($shop_name) . ' was unable to accept your request.';
                    break;
                case 'completed':
                    $push_title = 'Service Completed';
                    $push_body = 'Your emergency request with ' . htmlspecialchars($shop_name) . ' has been marked as complete.';
                    break;
            }

            if (!empty($push_title)) {
                $push_url = "/account/notification"; 
                sendPushNotification($conn, $customer_user_id, $push_title, $push_body, $push_url);
                $response['pushSent'] = true;
            }
        } catch (Exception $e) {
            error_log("Web Push Notification Error: " . $e->getMessage());
            $response['pushError'] = 'Web push notification failed to send.';
        }

        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
} catch (Exception $e) {
    error_log("Error updating emergency request: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again later.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>