<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

require 'db_connection.php';
include 'config.php'; 

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

function createSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => "User not authenticated. Please log in."]);
    exit();
}

$user_id = $_SESSION['user_id'];

$check_query = "SELECT COUNT(*) AS total FROM shop_applications WHERE user_id = ? AND (status = 'Pending' OR status = 'Approved')";
$stmt_check = $conn->prepare($check_query);
if (!$stmt_check) {
    echo json_encode(['success' => false, 'error' => "Database error: " . $conn->error]);
    exit();
}
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$row_check = $stmt_check->get_result()->fetch_assoc();
$stmt_check->close();

if ($row_check['total'] > 0) {
    echo json_encode(['success' => false, 'error' => "You already have an active application. You can only submit one application at a time."]);
    exit();
}

$required_fields = ['shop_name', 'owner_name', 'years_operation', 'email', 'phone', 'opening_time_am', 'closing_time_am', 'valid_id_type', 'shop_location', 'latitude', 'longitude'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(['success' => false, 'error' => "Required field '$field' is missing or empty."]);
        exit();
    }
}

if (!isset($_POST['services_offered']) || !is_array($_POST['services_offered']) || empty($_POST['services_offered'])) {
    echo json_encode(['success' => false, 'error' => "Please select at least one service offered."]);
    exit();
}

$required_files = ['business_permit_file', 'tax_id_file', 'valid_id_front'];
foreach ($required_files as $file) {
    if (!isset($_FILES[$file]) || $_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => ucfirst(str_replace('_', ' ', $file)) . " is required."]);
        exit();
    }
}

$shop_name = html_entity_decode(trim($_POST['shop_name']), ENT_QUOTES, 'UTF-8');
$owner_name = html_entity_decode(trim($_POST['owner_name']), ENT_QUOTES, 'UTF-8');
$years_operation = intval($_POST['years_operation']);
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = trim($_POST['phone']);
$town_city = html_entity_decode(trim($_POST['town_city'] ?? ''), ENT_QUOTES, 'UTF-8');
$province = html_entity_decode(trim($_POST['province'] ?? ''), ENT_QUOTES, 'UTF-8');
$country = html_entity_decode(trim($_POST['country'] ?? ''), ENT_QUOTES, 'UTF-8');
$postal_code = trim($_POST['postal_code'] ?? '');
$shop_location = html_entity_decode(trim($_POST['shop_location'] ?? ''), ENT_QUOTES, 'UTF-8');
$latitude = filter_var(trim($_POST['latitude']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$longitude = filter_var(trim($_POST['longitude']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$brands_serviced = html_entity_decode(trim($_POST['brands_serviced'] ?? ''), ENT_QUOTES, 'UTF-8');
$opening_time_am = $_POST['opening_time_am'];
$closing_time_am = $_POST['closing_time_am'];
$opening_time_pm = !empty($_POST['opening_time_pm']) ? $_POST['opening_time_pm'] : NULL;
$closing_time_pm = !empty($_POST['closing_time_pm']) ? $_POST['closing_time_pm'] : NULL;
$days_open = isset($_POST['days_open']) && is_array($_POST['days_open']) ? implode(',', $_POST['days_open']) : '';
$status = "pending";
$service_ids_array = array_map('intval', $_POST['services_offered']);
$valid_id_type = html_entity_decode(trim($_POST['valid_id_type']), ENT_QUOTES, 'UTF-8');

$business_permit_text = isset($_POST['business_permit']) ? encryptData(html_entity_decode(trim($_POST['business_permit']), ENT_QUOTES, 'UTF-8')) : '';
$tax_id_text = isset($_POST['tax_id']) ? encryptData(html_entity_decode(trim($_POST['tax_id']), ENT_QUOTES, 'UTF-8')) : '';
$dti_sec_number_text = isset($_POST['dti_sec_number']) ? encryptData(html_entity_decode(trim($_POST['dti_sec_number']), ENT_QUOTES, 'UTF-8')) : '';


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => "Invalid email format."]);
    exit();
}

if (!is_numeric($latitude) || !is_numeric($longitude)) {
    echo json_encode(['success' => false, 'error' => "Invalid location. Please pin your location on the map."]);
    exit();
}

function uploadFile($file, $target_dir) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error code: " . $file['error']);
    }
    $allowed_types = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
    $max_size = 5 * 1024 * 1024;
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        throw new Exception("Invalid file type. Only JPG, JPEG, PNG, WEBP, and PDF are allowed.");
    }
    if ($file['size'] > $max_size) {
        throw new Exception("File size must be less than 5MB.");
    }
    if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
        throw new Exception("Failed to create upload directory.");
    }
    $new_filename = uniqid('', true) . '.' . $file_ext;
    $target_file = $target_dir . $new_filename;
    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        throw new Exception("Error moving uploaded file.");
    }
    return $target_file;
}

try {
    $business_permit_file = uploadFile($_FILES['business_permit_file'], "../uploads/business_permit/");
    $tax_id_file = uploadFile($_FILES['tax_id_file'], "../uploads/tax_id/");
    $valid_id_front = uploadFile($_FILES['valid_id_front'], "../uploads/valid_id_front/");
    $dti_sec_file = (isset($_FILES['dti_sec_file']) && $_FILES['dti_sec_file']['error'] === UPLOAD_ERR_OK) ? uploadFile($_FILES['dti_sec_file'], "../uploads/dti_sec/") : '';
    $valid_id_back = (isset($_FILES['valid_id_back']) && $_FILES['valid_id_back']['error'] === UPLOAD_ERR_OK) ? uploadFile($_FILES['valid_id_back'], "../uploads/valid_id_back/") : '';
} catch (Exception $e) {
    error_log("File upload error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => "File upload failed: " . $e->getMessage()]);
    exit();
}

$base_slug = createSlug($shop_name);

$conn->begin_transaction();
try {
    $shop_slug = $base_slug;
    $counter = 1;
    $is_unique = false;
     
    while (!$is_unique) {
        $slug_check_query = "SELECT COUNT(*) AS count FROM shop_applications WHERE shop_slug = ?";
        $stmt_slug_check = $conn->prepare($slug_check_query);
        if (!$stmt_slug_check) {
            throw new Exception("Slug check prepare failed: " . $conn->error);
        }
        $stmt_slug_check->bind_param("s", $shop_slug);
        $stmt_slug_check->execute();
        $result = $stmt_slug_check->get_result()->fetch_assoc();
        $stmt_slug_check->close();

        if ($result['count'] == 0) {
            $is_unique = true;
        } else {
            $shop_slug = $base_slug . '-' . $counter;
            $counter++;
        }
    }

    $sql_app = "INSERT INTO shop_applications (user_id, shop_name, shop_slug, owner_name, years_operation, email, phone, business_permit, tax_id, dti_sec_number, business_permit_file, tax_id_file, dti_sec_file, valid_id_type, valid_id_front, valid_id_back, country, province, town_city, postal_code, shop_location, latitude, longitude, brands_serviced, status, opening_time_am, closing_time_am, opening_time_pm, closing_time_pm, days_open) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
     
    $stmt_app = $conn->prepare($sql_app);
    if (!$stmt_app) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }

    $stmt_app->bind_param("isssissssssssssssssssddsssssss",  
        $user_id, $shop_name, $shop_slug, $owner_name, $years_operation, $email, $phone, 
        $business_permit_text, $tax_id_text, $dti_sec_number_text, 
        $business_permit_file, 
        $tax_id_file, $dti_sec_file, $valid_id_type, $valid_id_front, $valid_id_back, 
        $country, $province, $town_city, $postal_code, 
        $shop_location, $latitude, $longitude,
        $brands_serviced, $status, 
        $opening_time_am, $closing_time_am, $opening_time_pm, $closing_time_pm, $days_open
    );

    if (!$stmt_app->execute()) {
        throw new Exception("Database execute failed: " . $stmt_app->error);
    }
     
    $application_id = $conn->insert_id;
    $stmt_app->close();

    if (!empty($service_ids_array)) {
        $sql_services = "INSERT INTO shop_services (application_id, service_id) VALUES (?, ?)";
        $stmt_services = $conn->prepare($sql_services);
        if (!$stmt_services) {
            throw new Exception("Services prepare failed: " . $conn->error);
        }
         
        foreach ($service_ids_array as $service_id) {
            $stmt_services->bind_param("ii", $application_id, $service_id);
            if (!$stmt_services->execute()) {
                throw new Exception("Failed to insert service ID: " . $service_id . " - " . $stmt_services->error);
            }
        }
        $stmt_services->close();
    }

    $conn->commit();

    $related_id = $application_id;
    $notif_status = $status;  

    $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, notification_type, related_id, status, is_read, delete_notification) VALUES (?, 'application', ?, ?, 0, 0)");
    if ($notif_stmt) {
        $notif_stmt->bind_param("iis", $user_id, $related_id, $notif_status);
        $notif_stmt->execute();
        $notif_stmt->close();
    }

    try {
        $service_names_string = 'N/A';
        if (!empty($service_ids_array)) {
            $placeholders = implode(',', array_fill(0, count($service_ids_array), '?'));
            $types = str_repeat('i', count($service_ids_array));
             
            $sql_get_names = "SELECT name FROM services WHERE id IN ($placeholders)";
            $stmt_get_names = $conn->prepare($sql_get_names);
            if ($stmt_get_names) {
                $stmt_get_names->bind_param($types, ...$service_ids_array);
                $stmt_get_names->execute();
                $result_names = $stmt_get_names->get_result();
                $names_array = [];
                while ($row_name = $result_names->fetch_assoc()) {
                    $names_array[] = $row_name['name'];
                }
                $service_names_string = implode(', ', $names_array);
                $stmt_get_names->close();
            }
        }
         
        $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        if (!empty($apiKey)) {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
            $apiInstance = new TransactionalEmailsApi(new Client(), $config);
             
            $google_maps_url = "https://www.google.com/maps?q={$latitude},{$longitude}";

            $business_hours = date("g:i a", strtotime($opening_time_am)) . " - " . date("g:i a", strtotime($closing_time_am));
            if ($opening_time_pm && $closing_time_pm) {
                $business_hours .= " / " . date("g:i a", strtotime($opening_time_pm)) . " - " . date("g:i a", strtotime($closing_time_pm));
            }
            $open_days = isset($_POST['days_open']) ? implode(", ", array_map('ucfirst', $_POST['days_open'])) : '';

            $sendSmtpEmail = new SendSmtpEmail([
                'to' => [['email' => $email, 'name' => $owner_name]],
                'templateId' => 5,
                'params' => [
                    'SHOP_NAME' => $shop_name,
                    'OWNER_NAME' => $owner_name,
                    'YEARS_OPERATION' => $years_operation,
                    'EMAIL' => $email,
                    'PHONE' => $phone,
                    'SHOP_LOCATION' => $shop_location,
                    'GOOGLE_MAPS_URL' => $google_maps_url,
                    'SERVICES' => $service_names_string,
                    'BUSINESS_PERMIT' => decryptData($business_permit_text),
                    'TAX_ID' => decryptData($tax_id_text),
                    'DTI_SEC_NUMBER' => decryptData($dti_sec_number_text),
                    'VALID_ID_TYPE' => $valid_id_type,
                    'BUSINESS_HOURS' => $business_hours,
                    'OPEN_DAYS' => $open_days
                ],
                'subject' => "Your Shop Application - $shop_name",
                'sender' => ['name' => 'Serbisyos', 'email' => 'no-reply@serbisyos.com'],
                'replyTo' => ['name' => 'Serbisyos Support', 'email' => 'support@serbisyos.com']
            ]);
            $apiInstance->sendTransacEmail($sendSmtpEmail);

            $adminEmailAddress = 'admin@serbisyos.com'; 

            $sendSmtpEmailAdmin = new SendSmtpEmail([
                'to' => [['email' => $adminEmailAddress, 'name' => 'System Admin']], 
                'templateId' => 29, 
                'params' => [
                    'SHOP_NAME' => $shop_name,
                    'OWNER_NAME' => $owner_name,
                    'APPLICANT_EMAIL' => $email, 
                    'PHONE' => $phone,
                    'SHOP_LOCATION' => $shop_location,
                    'GOOGLE_MAPS_URL' => $google_maps_url,
                    'SERVICES' => $service_names_string,
                    'YEARS_OPERATION' => $years_operation,
                    'SUBMISSION_DATE' => date("Y-m-d H:i:s")
                ],
                'subject' => "New Shop Application Received: " . $shop_name,
                'sender' => ['name' => 'Serbisyos System', 'email' => 'no-reply@serbisyos.com']
            ]);
            
            $apiInstance->sendTransacEmail($sendSmtpEmailAdmin);
        }
    } catch (Exception $e) {
        error_log("Email sending failed for user_id $user_id: " . $e->getMessage());
    }
     
    echo json_encode(['success' => true, 'message' => "Your application has been submitted successfully. We will review it shortly."]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Application Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => "Application submission failed: " . $e->getMessage()]);

} finally {
    if ($conn) {
        $conn->close();
    }
}
exit();
?>