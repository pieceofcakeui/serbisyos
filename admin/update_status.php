<?php
session_start();
include 'backend/db_connection.php';
require '../vendor/autoload.php';
require_once 'backend/utilities.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

header('Content-Type: application/json');

$admin_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if (!$admin_id) {
    echo json_encode(['success' => false, 'message' => 'Admin not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';
    $rejection_reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    $allowed_statuses = ['Pending', 'Approved', 'Rejected'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status value']);
        exit;
    }

    if ($status === 'Approved') {
        $check_loc_query = "SELECT shop_location FROM shop_applications WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_loc_query);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $application_data = mysqli_fetch_assoc($check_result);
        mysqli_stmt_close($check_stmt);

        if (!$application_data || empty(trim($application_data['shop_location']))) {
            echo json_encode(['success' => false, 'message' => 'Shop location is required before approving this application.']);
            exit;
        }

        $query = "UPDATE shop_applications SET status = ?, approved_at = NOW(), approved_by = ?, updated_at = NOW(), is_read = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sii", $status, $admin_id, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $query = "UPDATE shop_applications SET status = ?, approved_by = ?, updated_at = NOW(), is_read = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sii", $status, $admin_id, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    if ($result) {
        $app_query = "SELECT sa.user_id, sa.email, sa.owner_name, sa.shop_name, sa.shop_location,
                             sa.opening_time_am, sa.closing_time_am, sa.opening_time_pm, sa.closing_time_pm, 
                             sa.open_24_7, sa.days_open
                             FROM shop_applications sa WHERE sa.id = ?";
        $app_stmt = mysqli_prepare($conn, $app_query);

        if ($app_stmt) {
            mysqli_stmt_bind_param($app_stmt, "i", $id);
            mysqli_stmt_execute($app_stmt);
            $app_result = mysqli_stmt_get_result($app_stmt);

            if ($row = mysqli_fetch_assoc($app_result)) {
                $user_id = $row['user_id'];
                $email = $row['email'];
                $owner_name = $row['owner_name'];
                $shop_name = $row['shop_name'];
                $shop_location = $row['shop_location'];

                $sql_services = "SELECT s.name FROM shop_services ss JOIN services s ON ss.service_id = s.id WHERE ss.application_id = ?";
                $stmt_services = $conn->prepare($sql_services);
                $stmt_services->bind_param("i", $id);
                $stmt_services->execute();
                $result_services = $stmt_services->get_result();
                $services_array = [];
                while ($service_row = $result_services->fetch_assoc()) {
                    $services_array[] = $service_row['name'];
                }
                $stmt_services->close();
                $services = implode(', ', $services_array);

                $business_hours = '';
                if (!empty($row['open_24_7'])) {
                    $business_hours = "Open 24/7";
                } else {
                    $opening_time_am = !empty($row['opening_time_am']) ? date("g:i a", strtotime($row['opening_time_am'])) : '';
                    $closing_time_am = !empty($row['closing_time_am']) ? date("g:i a", strtotime($row['closing_time_am'])) : '';
                    $business_hours = "$opening_time_am - $closing_time_am";
                    if (!empty($row['opening_time_pm']) && !empty($row['closing_time_pm'])) {
                        $opening_time_pm = date("g:i a", strtotime($row['opening_time_pm']));
                        $closing_time_pm = date("g:i a", strtotime($row['closing_time_pm']));
                        $business_hours .= " / $opening_time_pm - $closing_time_pm";
                    }
                }
                $days_open = !empty($row['days_open']) ? implode(', ', explode(',', $row['days_open'])) : '';

                mysqli_stmt_close($app_stmt);

                if (!empty($user_id)) {
                    if ($status === 'Approved') {
                        $reset_flags_query = "UPDATE shop_applications SET seen_toggle_onboarding = 0, seen_rejected_notification = 0, updated_at = NOW() WHERE user_id = ?";
                        $reset_stmt = mysqli_prepare($conn, $reset_flags_query);
                        if ($reset_stmt) {
                            mysqli_stmt_bind_param($reset_stmt, "i", $user_id);
                            mysqli_stmt_execute($reset_stmt);
                            mysqli_stmt_close($reset_stmt);
                        }
                        $clear_user_id_query = "UPDATE shop_applications SET user_id = NULL, updated_at = NOW() WHERE user_id = ? AND status = 'Rejected'";
                        $clear_stmt = mysqli_prepare($conn, $clear_user_id_query);
                        if ($clear_stmt) {
                            mysqli_stmt_bind_param($clear_stmt, "i", $user_id);
                            mysqli_stmt_execute($clear_stmt);
                            mysqli_stmt_close($clear_stmt);
                        }
                    }

                    $profile_type = ($status === 'Approved') ? 'owner' : 'user';
                    $update_profile_query = "UPDATE users SET profile_type = ? WHERE id = ?";
                    $profile_stmt = mysqli_prepare($conn, $update_profile_query);

                    if ($profile_stmt) {
                        mysqli_stmt_bind_param($profile_stmt, "si", $profile_type, $user_id);
                        $update_result = mysqli_stmt_execute($profile_stmt);
                        mysqli_stmt_close($profile_stmt);

                        if ($update_result) {
                            try {
                                $apiKey = $_ENV['BREVO_API_KEY'];
                                $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                                $apiInstance = new TransactionalEmailsApi(new Client(), $config);
                                $templateId = 6;
                                $statusColor = $status === 'Approved' ? '#007b5e' : ($status === 'Rejected' ? '#dc3545' : '#ffc107');

                                if ($status === 'Rejected' && empty($rejection_reason)) {
                                    $rejection_reason = "Application requirements were not met.";
                                }

                                $sendSmtpEmail = new SendSmtpEmail([
                                    'to' => [['email' => $email, 'name' => $owner_name]],
                                    'templateId' => $templateId,
                                    'params' => [
                                        'OWNER_NAME' => $owner_name,
                                        'SHOP_NAME' => $shop_name,
                                        'SHOP_LOCATION' => $shop_location,
                                        'SERVICES' => $services,
                                        'STATUS' => $status,
                                        'STATUS_COLOR' => $statusColor,
                                        'REJECTION_REASON' => $rejection_reason,
                                        'APPLICATION_ID' => $id,
                                        'STATUS_DATE' => date('F j, Y'),
                                        'LOGIN_URL' => 'https://serbisyos.com/login',
                                        'COPYRIGHT_YEAR' => date('Y'),
                                        'BUSINESS_HOURS' => $business_hours,
                                        'OPEN_DAYS' => $days_open
                                    ],
                                    'subject' => 'Important Update: Your Application Status is Now ' . $status,
                                    'sender' => ['name' => 'Serbisyos', 'email' => 'no-reply@serbisyos.com'],
                                    'replyTo' => ['name' => 'Serbisyos Support', 'email' => 'support@serbisyos.com']
                                ]);
                                $apiInstance->sendTransacEmail($sendSmtpEmail);

                                $notification_type = ($status === 'Approved') ? 'application_approved' : 'application_rejected';
                                $shop_id = 0;
                                $distance = null;
                                $is_read = 0;
                                $delete_notification = 0;

                                $notificationStmt = $conn->prepare(
                                    "INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status, distance, is_read, delete_notification) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                                );
                                $notificationStmt->bind_param("iisisiii", $user_id, $shop_id, $notification_type, $id, $status, $distance, $is_read, $delete_notification);
                                $notificationStmt->execute();
                                $notificationStmt->close();

                                try {
                                    $push_title = '';
                                    $push_body = '';
                                    $shop_name_clean = htmlspecialchars($shop_name);

                                    if ($status === 'Approved') {
                                        $push_title = 'Application Approved!';
                                        $push_body = "Congratulations! Your shop '{$shop_name_clean}' is now active on Serbisyos.";
                                    } elseif ($status === 'Rejected') {
                                        $push_title = 'Application Update';
                                        $push_body = "There has been an update regarding your application for '{$shop_name_clean}'.";
                                    }

                                    if (!empty($push_title)) {
                                        $push_url = "/account/notification"; 
                                        sendPushNotification($conn, $user_id, $push_title, $push_body, $push_url);
                                    }
                                } catch (Exception $e) {
                                    error_log("Web Push Notification Error (Application Status): " . $e->getMessage());
                                }

                                echo json_encode(['success' => true, 'message' => 'Status and profile type updated successfully. Email sent and notification added.']);
                            } catch (Exception $e) {
                                error_log("Brevo Mailer Error: " . $e->getMessage());
                                echo json_encode(['success' => false, 'message' => 'Status and profile type updated, but email could not be sent. Error: ' . $e->getMessage()]);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Failed to update profile_type: ' . mysqli_error($conn)]);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to prepare profile_type update statement: ' . mysqli_error($conn)]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'User ID not found for this application ID']);
                }
            } else {
                mysqli_stmt_close($app_stmt);
                echo json_encode(['success' => false, 'message' => 'Application details not found for ID: ' . $id]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch application details: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>