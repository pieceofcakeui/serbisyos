<?php
session_start();
require_once 'backend/db_connection.php';
require_once 'backend/security_helper.php';
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

date_default_timezone_set('UTC');

define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY']);
define('ENCRYPTION_METHOD', 'AES-256-CBC');

function decryptData($data) {
    if (empty($data) || strpos($data, ':') === false) return $data;
    $parts = explode(':', $data, 2);
    if (count($parts) !== 2) return false;
    $iv = base64_decode($parts[0]);
    $encrypted = base64_decode($parts[1]);
    if ($iv === false || $encrypted === false) return false;
    return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}

function formatColumnHeader($column_name) {
    return ucwords(str_replace('_', ' ', $column_name));
}

function decryptLocationData($data, $fields = ['barangay', 'town', 'province']) {
    foreach ($fields as $field) {
        if (isset($data[$field]) && !empty($data[$field])) {
            $data[$field] = decryptData($data[$field]);
        }
    }
    return $data;
}

function generateHtmlFormat($data, $user_email) {
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Your Personal Data Export</title><style>body{font-family:Arial,sans-serif;margin:20px;color:#333;}.header{background:#4CAF50;color:white;padding:20px;text-align:center;margin-bottom:30px;}.section{margin-bottom:30px;}.section-title{background:#f8f9fa;padding:10px;border-left:4px solid #4CAF50;margin-bottom:15px;}table{width:100%;border-collapse:collapse;margin-bottom:20px;}th,td{padding:10px;text-align:left;border-bottom:1px solid #ddd;}th{background-color:#f8f9fa;font-weight:bold;}.generated-date{text-align:center;color:#666;margin-top:30px;font-size:12px;}</style></head><body><div class="header"><h1>Personal Data Export</h1><p>Export for: ' . htmlspecialchars($user_email) . '</p></div>';
    if (isset($data['personal'])) {
        $html .= '<div class="section"><h2 class="section-title">Personal Information</h2><table><tr><th>Field</th><th>Value</th></tr>';
        foreach ($data['personal'] as $key => $value) {
            $html .= '<tr><td>' . htmlspecialchars(formatColumnHeader($key)) . '</td><td>' . htmlspecialchars($value ?? 'N/A') . '</td></tr>';
        }
        $html .= '</table></div>';
    }
    if (isset($data['shop_applications']) && !empty($data['shop_applications'])) {
        $html .= '<div class="section"><h2 class="section-title">Shop Applications</h2>';
        foreach ($data['shop_applications'] as $index => $application) {
            $html .= '<h3>Application ' . ($index + 1) . '</h3><table><tr><th>Field</th><th>Value</th></tr>';
            foreach ($application as $key => $value) {
                $html .= '<tr><td>' . htmlspecialchars(formatColumnHeader($key)) . '</td><td>' . htmlspecialchars($value ?? 'N/A') . '</td></tr>';
            }
            $html .= '</table>';
        }
        $html .= '</div>';
    }
    $manila_time = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('F d, Y g:i A');
    $html .= '<div class="generated-date">Generated on: ' . $manila_time . ' (Asia/Manila)</div></body></html>';
    return $html;
}

function sendDataReadyEmail($user_email, $user_name, $download_url, $format) {
    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );

    $manilaTimeZone = new DateTimeZone('Asia/Manila');
    $generatedDate = (new DateTime('now', $manilaTimeZone))->format('F d, Y g:i A');
    $expirationDate = (new DateTime('+7 days', $manilaTimeZone))->format('F d, Y g:i A');

    $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();
    $sendSmtpEmail->setTemplateId(12);
    $sendSmtpEmail->setTo([['email' => $user_email, 'name' => $user_name]]);

    $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
    $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);

    $sendSmtpEmail->setParams([
        'NAME' => $user_name,
        'FORMAT' => strtoupper($format),
        'DOWNLOAD_URL' => $download_url,
        'GENERATED_DATE' => $generatedDate,
        'EXPIRATION_DATE' => $expirationDate,
    ]);

    try {
        $apiInstance->sendTransacEmail($sendSmtpEmail);
        return true;
    } catch (Exception $e) {
        error_log("Brevo API Error (Template 12): " . $e->getMessage());
        return false;
    }
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: ../login.php');
    exit;
}
$conn->query("SET time_zone = '+00:00'");

$activeTab = '#privacy';
if (!empty($_POST['active_tab'])) {
    $tab = $_POST['active_tab'];
    if (in_array($tab, ['#security', '#notifications', '#privacy'])) {
        $activeTab = $tab;
    }
} elseif (!empty($_GET['active_tab'])) {
    $tab = $_GET['active_tab'];
    if (in_array($tab, ['#security', '#notifications', '#privacy'])) {
        $activeTab = $tab;
    }
}

if (isset($_GET['delete_request'])) {
    $request_id = (int)$_GET['delete_request'];
    $stmt = $conn->prepare("SELECT user_id, download_url FROM data_download_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $request = $result->fetch_assoc();
        if ($request['user_id'] == $user_id) {
            if (!empty($request['download_url'])) {
                $filepath = __DIR__ . '/' . $request['download_url'];
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }
            $delete_stmt = $conn->prepare("DELETE FROM data_download_requests WHERE id = ?");
            $delete_stmt->bind_param("i", $request_id);
            if ($delete_stmt->execute()) {
                header("Location: settings-and-privacy.php?request_deleted=1" . $activeTab);
            } else {
                $_SESSION['toast_error'] = "Error deleting request.";
                header("Location: settings-and-privacy.php" . $activeTab);
            }
            $delete_stmt->close();
        } else {
            $_SESSION['toast_error'] = "You are not authorized to delete this request.";
            header("Location: settings-and-privacy.php" . $activeTab);
        }
    } else {
        $_SESSION['toast_error'] = "Request not found.";
        header("Location: settings-and-privacy.php" . $activeTab);
    }
    $stmt->close();
    exit;
}

function processDataRequest($conn, $request_id, $format = 'json') {
    try {
        $stmt = $conn->prepare("SELECT * FROM data_download_requests WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$request) throw new Exception("Request not found");
        $user_id = $request['user_id'];
        $data_types_raw = json_decode($request['data_types'], true);
        $types = isset($data_types_raw['types']) ? $data_types_raw['types'] : $data_types_raw;
        $stmt = $conn->prepare("UPDATE data_download_requests SET status = 'processing' WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $data = [];
        if (in_array('personal', $types)) {
            $stmt = $conn->prepare("SELECT u.fullname, u.email, u.profile_type, u.contact_number, u.created_at, vs.gender, vs.birthday, vs.address_barangay, vs.address_town_city, vs.address_province, vs.address_postal_code FROM users u LEFT JOIN verification_submissions vs ON u.id = vs.user_id WHERE u.id = ? ORDER BY vs.submission_date DESC LIMIT 1");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $personal_data = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($personal_data) {
                $personal_data['birthday'] = decryptData($personal_data['birthday']);
                $personal_data['address_barangay'] = decryptData($personal_data['address_barangay']);
                $personal_data['address_town_city'] = decryptData($personal_data['address_town_city']);
                $personal_data['address_province'] = decryptData($personal_data['address_province']);
                $personal_data['address_postal_code'] = decryptData($personal_data['address_postal_code']);
                $personal_data['country'] = 'Philippines';
                $data['personal'] = $personal_data;
            }
        }
        if (in_array('shop_applications', $types)) {
            $check = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
            $check->bind_param("i", $user_id);
            $check->execute();
            $result = $check->get_result()->fetch_assoc();
            $check->close();
            if ($result && $result['profile_type'] === 'owner') {
                $stmt = $conn->prepare("SELECT shop_name, business_type, owner_name, years_operation, email, phone, website, instagram, facebook, business_permit, tax_id, barangay, town_city, province, postal_code, latitude, longitude, services_offered, brands_serviced, applied_at, approved_at, description, business_hours, opening_time, closing_time, open_24_7, days_open FROM shop_applications WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $shop_applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                foreach ($shop_applications as &$application) {
                    $application = decryptLocationData($application, ['barangay', 'province']);
                    if (isset($application['town_city'])) $application['town_city'] = decryptData($application['town_city']);
                    $application['country'] = 'Philippines';
                }
                $data['shop_applications'] = $shop_applications;
            }
        }
        $download_dir = __DIR__ . "/downloads";
        if (!is_dir($download_dir)) {
            if (!mkdir($download_dir, 0755, true)) throw new Exception("Failed to create download directory");
            file_put_contents($download_dir . "/.htaccess", "Order deny,allow\nDeny from all\nAllow from 127.0.0.1\n");
        }
        $base_filename = "data_" . bin2hex(random_bytes(16)) . "_" . time();
        if ($format === 'html') {
            $filename = $base_filename . ".html";
            $content = generateHtmlFormat($data, $user_info['email']);
        } else {
            $filename = $base_filename . ".json";
            $content = json_encode($data, JSON_PRETTY_PRINT);
        }
        $filepath = "$download_dir/$filename";
        if (file_put_contents($filepath, $content) === false) throw new Exception("Failed to write data file");
        $zipfile = str_replace("." . pathinfo($filename, PATHINFO_EXTENSION), ".zip", $filepath);
        $zip = new ZipArchive();
        if ($zip->open($zipfile, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($filepath, basename($filename));
            $zip->close();
            unlink($filepath);
            $final_file = basename($zipfile);
        } else {
            $final_file = basename($filename);
        }
        $download_url = "downloads/" . $final_file;
        $completion_date = date('Y-m-d H:i:s');
        $expires_at = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60));
        $stmt = $conn->prepare("UPDATE data_download_requests SET status = 'completed', download_url = ?, completion_date = ?, expires_at = ?, format = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $download_url, $completion_date, $expires_at, $format, $request_id);
        $stmt->execute();
        $stmt->close();
        $encrypted_id_for_email = URLSecurity::encryptId($request_id);
        $secure_download_url = "https://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . "/account/backend/secure_download.php?id=" . $encrypted_id_for_email;
        sendDataReadyEmail($user_info['email'], $user_info['fullname'], $secure_download_url, $format);
        return true;
    } catch (Exception $e) {
        error_log("Error processing request #$request_id: " . $e->getMessage());
        $stmt = $conn->prepare("UPDATE data_download_requests SET status = 'failed', completion_date = ? WHERE id = ?");
        $completion_date = date('Y-m-d H:i:s');
        $stmt->bind_param("si", $completion_date, $request_id);
        $stmt->execute();
        $stmt->close();
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data_types'])) {
    $selected_data_types = $_POST['data_types'];
    $format = $_POST['format'] ?? 'json';
    $allowed_types = ['personal', 'shop_applications'];
    $allowed_formats = ['json', 'html'];

    $data_types_filtered = array_values(array_filter($selected_data_types, function($type) use ($allowed_types) {
        return in_array($type, $allowed_types);
    }));

    if (!in_array($format, $allowed_formats)) $format = 'json';
    if (empty($data_types_filtered)) {
        $_SESSION['toast_error'] = "Please select at least one valid data type.";
        header("Location: settings-and-privacy.php" . $activeTab);
        exit;
    }
    $stmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_profile = $stmt->get_result()->fetch_assoc();
    $profile_type = $user_profile['profile_type'] ?? '';
    $stmt->close();
    if ($profile_type !== 'owner') {
        $data_types_filtered = array_values(array_diff($data_types_filtered, ['shop_applications']));
    }
    $check_stmt = $conn->prepare("SELECT id FROM data_download_requests WHERE user_id = ? AND status IN ('pending', 'processing') LIMIT 1");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $check_stmt->close();
        $_SESSION['toast_error'] = "You already have a pending request. Please wait until it is completed.";
        header("Location: settings-and-privacy.php" . $activeTab);
        exit;
    }
    $check_stmt->close();
    $data_types_json = json_encode($data_types_filtered);
    $current_utc_time = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO data_download_requests (user_id, request_type, data_types, status, format, request_date) VALUES (?, 'custom', ?, 'pending', ?, ?)");
    $stmt->bind_param("isss", $user_id, $data_types_json, $format, $current_utc_time);
    if ($stmt->execute()) {
        $request_id = $conn->insert_id;
        $stmt->close();
        if (processDataRequest($conn, $request_id, $format)) {
            header("Location: settings-and-privacy.php?data_request_completed=1" . $activeTab);
        } else {
            header("Location: settings-and-privacy.php?data_request_failed=1" . $activeTab);
        }
        exit;
    } else {
        $_SESSION['toast_error'] = "Error creating request: " . htmlspecialchars($conn->error);
        header("Location: settings-and-privacy.php" . $activeTab);
        exit;
    }
} else {
    header("Location: settings-and-privacy.php" . $activeTab);
    exit();
}
$conn->close();
?>
