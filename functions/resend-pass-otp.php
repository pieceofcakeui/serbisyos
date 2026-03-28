<?php
session_start();
require_once 'db_connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Configuration;
use GuzzleHttp\Client;

date_default_timezone_set('Asia/Manila');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

define('OTP_SECRET_KEY', $_ENV['OTP_SECRET_KEY']);
define('OTP_LENGTH', 6);
define('OTP_EXPIRY_MINUTES', 5);
define('RESEND_COOLDOWN', 120);

if (!isset($_ENV['OTP_SECRET_KEY']) || strlen($_ENV['OTP_SECRET_KEY']) < 32) {
    die('Error: OTP_SECRET_KEY not configured properly');
}

function generateOTP(): string {
    $min = 10 ** (OTP_LENGTH - 1);
    $max = (10 ** OTP_LENGTH) - 1;
    return strval(random_int($min, $max));
}

function hashOTP(string $otp): string {
    return hash_hmac('sha256', $otp, OTP_SECRET_KEY);
}

function sendOTP(string $email, string $otp): bool {
    try {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
        
        $apiInstance = new TransactionalEmailsApi(new Client(), $config);
        $name = $_SESSION['fullname'] ?? 'User';

        $sendSmtpEmail = new SendSmtpEmail([
            'to' => [['email' => $email]],
            'templateId' => 9,
            'params' => [
                'otp' => $otp,
                'name' => $name,
            ],
        ]);

        $apiInstance->sendTransacEmail($sendSmtpEmail);
        error_log("Password reset OTP sent to: $email");
        return true;
    } catch (Exception $e) {
        error_log("Brevo API Exception: " . $e->getMessage());
        error_log("Failed to send password reset OTP to: $email");
        return false;
    }
}

header('Content-Type: application/json');

if (!isset($_SESSION['temp_user_id'], $_SESSION['temp_email'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please try again.']);
    exit();
}

if (isset($_SESSION['last_otp_sent']) && (time() - $_SESSION['last_otp_sent']) < RESEND_COOLDOWN) {
    $remaining = RESEND_COOLDOWN - (time() - $_SESSION['last_otp_sent']);
    echo json_encode(['success' => false, 'message' => "Please wait for {$remaining} more seconds."]);
    exit();
}

$otp = generateOTP();
$hashedOTP = hashOTP($otp);
$user_id = $_SESSION['temp_user_id'];
$email = $_SESSION['temp_email'];

error_log("=== OTP RESEND START ===");

$tz_check = $conn->query("SELECT @@global.time_zone as global_tz, @@session.time_zone as session_tz");
$tz_info = $tz_check->fetch_assoc();
error_log("MySQL GLOBAL timezone: " . $tz_info['global_tz']);
error_log("MySQL SESSION timezone: " . $tz_info['session_tz']);

$current_time_manila = date('Y-m-d H:i:s');
$expiry_time_manila = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

error_log("PHP Timezone: " . date_default_timezone_get());
error_log("Current time (Manila): " . $current_time_manila);
error_log("Expiry time (Manila): " . $expiry_time_manila);

$sql = "INSERT INTO otp_verifications (user_id, email, otp, expires_at, created_at, purpose) VALUES (?, ?, ?, ?, ?, 'reset')";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("PREPARE ERROR: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database preparation error.']);
    exit();
}

$stmt->bind_param("issss", $user_id, $email, $hashedOTP, $expiry_time_manila, $current_time_manila);

if ($stmt->execute()) {
    $inserted_id = $stmt->insert_id;
    error_log("INSERT SUCCESS - ID: " . $inserted_id);
    
    $verify = $conn->query("SELECT created_at, expires_at FROM otp_verifications WHERE id = {$inserted_id}");
    $verify_data = $verify->fetch_assoc();
    error_log("DB stored created_at: " . $verify_data['created_at']);
    error_log("DB stored expires_at: " . $verify_data['expires_at']);
    
    if (sendOTP($email, $otp)) {
        $_SESSION['last_otp_sent'] = time();
        error_log("OTP SENT SUCCESS");
        echo json_encode(['success' => true, 'message' => 'New OTP sent successfully.']);
    } else {
        error_log("OTP SEND FAILED");
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
    }
} else {
    error_log("EXECUTE ERROR: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Database execution error.']);
}

exit();
?>