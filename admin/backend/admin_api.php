<?php
session_start();
ob_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

define('BASE_URL', $_ENV['BASE_URL']);

include 'db_connection.php';
require_once 'utilities.php';

if (!isset($conn) || $conn->connect_error) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$adminId = $_SESSION['id'] ?? null;
if (!$adminId) {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Admin not logged in.']);
    exit;
}

define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY']);
define('ENCRYPTION_METHOD', 'AES-256-CBC');

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

function sendDecisionEmail($userEmail, $userName, $status, $notes)
{
    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();

    $params = [
        'USER_NAME' => $userName,
        'LOGO_URL' => BASE_URL . '/assets/img/logo.png',
        'BASE_URL' => BASE_URL
    ];
    $templateId = 0;
    $processedNotes = nl2br(htmlspecialchars($notes));

    if ($status === 'verified') {
        $templateId = 18;
        $params['ADMIN_NOTE_TITLE'] = !empty($notes) ? "Admin's Note:" : "";
        $params['ADMIN_NOTE_BODY'] = !empty($notes) ? $processedNotes : "Welcome to Serbisyos!";
    } else {
        $templateId = 19;
        $params['REASON_TITLE'] = "Reason for Rejection:";
        $params['REASON_BODY'] = !empty($notes) ? $processedNotes : "Please review your submission details and uploaded images, and feel free to resubmit.";
    }

    try {
        $sendSmtpEmail->setTemplateId($templateId);
        $sendSmtpEmail->setTo([['email' => $userEmail, 'name' => $userName]]);
        $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
        $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos Support']);
        $sendSmtpEmail->setParams($params);
        
        $apiInstance->sendTransacEmail($sendSmtpEmail);
        return true;
    } catch (Exception $e) {
        error_log("Brevo API Error (Template $templateId): " . $e->getMessage());
        return false;
    }
}

$action = $_GET['action'] ?? '';

try {
    if ($action === 'get_status_counts') {
        $sql = "SELECT status, COUNT(*) as count FROM verification_submissions GROUP BY status";
        $result = $conn->query($sql);

        $counts = [
            'pending' => 0,
            'verified' => 0,
            'rejected' => 0
        ];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if (isset($counts[$row['status']])) {
                    $counts[$row['status']] = (int)$row['count'];
                }
            }
        }

        ob_end_clean();
        echo json_encode(['status' => 'success', 'counts' => $counts]);
    } elseif ($action === 'get_submissions') {
        $status = $_GET['status'] ?? 'pending';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $count_sql = "SELECT COUNT(*) as total FROM verification_submissions WHERE status = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("s", $status);
        $count_stmt->execute();
        $totalCount = $count_stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $count_stmt->close();

        $sql = "SELECT vs.id, vs.status, u.fullname, u.email, vs.verification_date, a.full_name as admin_username
                FROM verification_submissions vs
                JOIN users u ON vs.user_id = u.id 
                LEFT JOIN admins a ON vs.verified_by = a.id
                WHERE vs.status = ? ORDER BY vs.id DESC LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $status, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $submissions = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        ob_end_clean();
        echo json_encode(['status' => 'success', 'total' => $totalCount, 'submissions' => $submissions]);
    } elseif ($action === 'get_submission_details') {
        $submissionId = $_GET['id'] ?? 0;
        if (!$submissionId) {
            throw new Exception("Invalid submission ID", 400);
        }

        $sql = "SELECT vs.*, u.email FROM verification_submissions vs JOIN users u ON vs.user_id = u.id WHERE vs.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $submissionId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $row['full_name']           = decryptData($row['full_name']);
            $row['birthday']            = decryptData($row['birthday']);
            $row['address_barangay']    = decryptData($row['address_barangay']);
            $row['address_town_city']   = decryptData($row['address_town_city']);
            $row['address_province']    = decryptData($row['address_province']);
            $row['address_postal_code'] = decryptData($row['address_postal_code']);

            array_walk_recursive($row, function (&$item) {
                if (is_string($item)) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                }
            });

            ob_end_clean();
            echo json_encode(['status' => 'success', 'data' => $row]);
        } else {
            throw new Exception("Submission not found", 404);
        }
        $stmt->close();
    } elseif ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $submissionId = $_POST['submission_id'] ?? 0;
        $newStatus = $_POST['status'] ?? '';
        $notes = trim($_POST['notes'] ?? '');
        $userEmail = $_POST['user_email'] ?? '';
        $userName = $_POST['user_name'] ?? 'Valued User';

        if (!in_array($newStatus, ['verified', 'rejected'])) {
            throw new Exception("Invalid status provided", 400);
        }

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("UPDATE verification_submissions SET status = ?, verified_by = ?, notes = ?, verification_date = NOW(), is_read = 0 WHERE id = ?");
            $stmt->bind_param("sisi", $newStatus, $adminId, $notes, $submissionId);
            $stmt->execute();
            $stmt->close();

            $submission_data_stmt = $conn->prepare(
                "SELECT user_id, full_name, gender, birthday, address_barangay, address_town_city, address_province, address_postal_code FROM verification_submissions WHERE id = ?"
            );
            $submission_data_stmt->bind_param("i", $submissionId);
            $submission_data_stmt->execute();
            $submission_data = $submission_data_stmt->get_result()->fetch_assoc();
            $submission_data_stmt->close();

            if (!$submission_data) {
                throw new Exception("Could not retrieve submission data.", 404);
            }
            $userId = $submission_data['user_id'];
            $notification_type = '';

            if ($newStatus === 'verified') {
                $decryptedFullName = decryptData($submission_data['full_name']);
                $decryptedPostalCode = decryptData($submission_data['address_postal_code']);

                $updateUserStmt = $conn->prepare(
                    "UPDATE users SET fullname = ?, is_verified = 1 WHERE id = ?"
                );

                $updateUserStmt->bind_param(
                    "si",
                    $decryptedFullName,
                    $userId
                );

                $updateUserStmt->execute();
                $updateUserStmt->close();

                $notification_type = 'verification_verified';
            } elseif ($newStatus === 'rejected') {
                $notification_type = 'verification_rejected';
            }

            if (!empty($notification_type)) {
                $shop_id = 0;
                $distance = null;
                $is_read = 0;
                $delete_notification = 0;

                $notificationStmt = $conn->prepare(
                    "INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status, distance, is_read, delete_notification) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $notificationStmt->bind_param("iisisdii", $userId, $shop_id, $notification_type, $submissionId, $newStatus, $distance, $is_read, $delete_notification);
                $notificationStmt->execute();
                $notificationStmt->close();
            }

            $conn->commit();

            if (!empty($userEmail)) {
                sendDecisionEmail($userEmail, $userName, $newStatus, $notes);
            }

            try {
                $push_title = '';
                $push_body = '';

                if ($newStatus === 'verified') {
                    $push_title = 'Account Verified!';
                    $push_body = 'Congratulations, ' . htmlspecialchars($userName) . '! Your account is now verified.';
                } elseif ($newStatus === 'rejected') {
                    $push_title = 'Account Verification Update';
                    $push_body = 'There was an update on your account verification. Please check your email or notifications for details.';
                }

                if (!empty($push_title)) {
                    $push_url = "/account/notification"; 
                    sendPushNotification($conn, $userId, $push_title, $push_body, $push_url);
                }
            } catch (Exception $e) {
                error_log("Web Push Notification Error (Verification): " . $e->getMessage());
            }

            ob_end_clean();
            echo json_encode(['status' => 'success', 'message' => 'Status updated successfully.']);
        } catch (Exception $tx_e) {
            $conn->rollback();
            throw new Exception("Database transaction failed: " . $tx_e->getMessage(), 500);
        }
    } else {
        throw new Exception("No valid action specified", 400);
    }
} catch (Exception $e) {
    $errorCode = $e->getCode() >= 400 ? $e->getCode() : 500;
    error_log("API Error: " . $e->getMessage());
    if (ob_get_length()) {
        ob_end_clean();
    }
    http_response_code($errorCode);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
exit;