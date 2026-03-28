<?php
session_start();
require_once 'db_connection.php';
require '../../vendor/autoload.php';
require_once 'utilities.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$booking_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status = isset($_POST['status']) ? trim($_POST['status']) : null;

if (!$booking_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$status = ucfirst(strtolower($status));
if ($status === 'Delete') $status = 'delete';

$allowed_statuses = ['Accept', 'Reject', 'Completed', 'Cancelled', 'delete'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $check_query = "SELECT sb.id, sb.booking_status, sb.shop_id, sb.user_id, sb.service_type, 
                           sb.vehicle_type, sb.preferred_datetime, sb.customer_name, sb.customer_email,
                           sb.plate_number,
                           sa.user_id AS shop_owner_id, sa.shop_name
                           FROM services_booking sb
                           LEFT JOIN shop_applications sa ON sb.shop_id = sa.id
                           WHERE sb.id = ? AND sb.is_deleted = 0";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $booking_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    $booking = $check_result->fetch_assoc();
    $current_status = $booking['booking_status'];
    $shop_owner_id = $booking['shop_owner_id'];
    $customer_id = $booking['user_id'];
    $current_user_id = $_SESSION['user_id'];

    $is_owner = ($shop_owner_id == $current_user_id);
    $is_customer = ($customer_id == $current_user_id);

    if (!$is_owner && !$is_customer) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $valid_transitions = [
        'Pending' => ['Accept', 'Reject', 'Cancelled'],
        'Accept' => ['Completed', 'Cancelled'],
        'Reject' => ['delete'],
        'Completed' => ['delete'],
        'Cancelled' => ['delete']
    ];

    if (!isset($valid_transitions[$current_status]) || !in_array($status, $valid_transitions[$current_status])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status transition']);
        exit;
    }

    if (in_array($status, ['Accept', 'Reject', 'Completed']) && !$is_owner) {
        echo json_encode(['success' => false, 'message' => 'Only shop owners can perform this action']);
        exit;
    }

    if ($status === 'delete') {
        $update_query = "UPDATE services_booking SET is_deleted = 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $booking_id);
    } elseif ($status === 'Completed') {
        $update_query = "UPDATE services_booking SET booking_status = ?, completed_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $status, $booking_id);
    } else {
        $update_query = "UPDATE services_booking SET booking_status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $status, $booking_id);
    }

    if ($update_stmt->execute()) {
        if (in_array($status, ['Accept', 'Reject'])) {
            try {
                $template_id = ($status === 'Accept') ? 23 : 24;
                $subject_status = ($status === 'Accept') ? 'Accepted' : 'Rejected';
                $service_type_cleaned = htmlspecialchars(str_replace(['[', ']', '"', "'"], '', $booking['service_type']));
                $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
                $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                $apiInstance = new TransactionalEmailsApi(new Client(), $config);
                $sendSmtpEmail = new SendSmtpEmail([
                    'to' => [['email' => $booking['customer_email'], 'name' => $booking['customer_name']]],
                    'templateId' => $template_id,
                    'params' => [
                        'CUSTOMER_NAME' => $booking['customer_name'],
                        'SHOP_NAME' => $booking['shop_name'],
                        'SERVICE' => $service_type_cleaned,
                        'VEHICLE_TYPE' => htmlspecialchars($booking['vehicle_type']),
                        'PLATE_NUMBER' => htmlspecialchars($booking['plate_number']),
                        'BOOKING_DATETIME' => htmlspecialchars($booking['preferred_datetime']),
                        'STATUS' => $subject_status,
                        'BASE_URL' => $_ENV['BASE_URL'] ?? 'https://www.serbisyos.com',
                        'SUPPORT_EMAIL' => $_ENV['SUPPORT_EMAIL'] ?? 'support@serbisyos.com',
                        'LOGO_URL' => $_ENV['BASE_URL'] . '/assets/img/logo.png'
                    ],
                    'sender' => ['name' => $booking['shop_name'], 'email' => $_ENV['NO_REPLY_EMAIL']],
                    'replyTo' => ['name' => $booking['shop_name'], 'email' => $_ENV['NO_REPLY_EMAIL']]
                ]);
                $apiInstance->sendTransacEmail($sendSmtpEmail);
            } catch (Exception $e) {
                error_log("Brevo Mailer Error: " . $e->getMessage());
            }
        }

        $notification_recipient_id = null;
        if ($is_owner && $customer_id) {
            $notification_recipient_id = $customer_id;
        } elseif ($is_customer && $shop_owner_id) {
            $notification_recipient_id = $shop_owner_id;
        }

       if ($notification_recipient_id && in_array($status, ['Accept', 'Reject', 'Cancelled', 'Completed'])) {
    $shop_id = $booking['shop_id'];

    $notification_type_map = [
        'Accept' => 'booking_accepted',
        'Reject' => 'booking_rejected',
        'Cancelled' => 'booking_cancelled',
        'Completed' => 'booking_completed'
    ];

    $status_map = [
        'Accept' => 'Accepted',
        'Reject' => 'Rejected',
        'Cancelled' => 'Cancelled',
        'Completed' => 'Completed'
    ];

    $notification_type = $notification_type_map[$status];
    $notification_status = $status_map[$status];

    $notification_query = "INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status, created_at) 
                           VALUES (?, ?, ?, ?, ?, NOW())";
    $notification_stmt = $conn->prepare($notification_query);

    if ($notification_stmt) {
        $notification_stmt->bind_param(
            "iisis",
            $notification_recipient_id,
            $shop_id,
            $notification_type,
            $booking_id,
            $notification_status
        );
        $notification_stmt->execute();
        $notification_stmt->close();
    }

            try {
                $push_title = '';
                $push_body = '';
                $shop_name = htmlspecialchars($booking['shop_name']);

                switch ($status) {
                    case 'Accept':
                        $push_title = 'Booking Accepted';
                        $push_body = "Your booking with {$shop_name} has been accepted.";
                        break;
                    case 'Reject':
                        $push_title = 'Booking Rejected';
                        $push_body = "Unfortunately, your booking with {$shop_name} was rejected.";
                        break;
                    case 'Cancelled':
                        $push_title = 'Booking Cancelled';
                        $push_body = "Your booking with {$shop_name} has been cancelled.";
                        break;
                    case 'Completed':
                        $push_title = 'Booking Completed';
                        $push_body = "Your service with {$shop_name} is now complete.";
                        break;
                }

                if (!empty($push_title)) {
                    $push_url = "/account/notification"; 
                    sendPushNotification($conn, $notification_recipient_id, $push_title, $push_body, $push_url);
                }
            } catch (Exception $e) {
                error_log("Web Push Notification Error: " . $e->getMessage());
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully',
            'newStatus' => $status === 'delete' ? 'deleted' : $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
} catch (Exception $e) {
    error_log("Error updating booking status: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}
?>