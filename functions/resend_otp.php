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

function generateOTP(): string
{
    $min = pow(10, OTP_LENGTH - 1);
    $max = pow(10, OTP_LENGTH) - 1;
    return strval(random_int($min, $max));
}

function hashOTP(string $otp): string
{
    return hash_hmac('sha256', $otp, OTP_SECRET_KEY);
}

function verifyOTP(string $otp, string $hashedOtp): bool
{
    $calculatedHash = hash_hmac('sha256', $otp, OTP_SECRET_KEY);
    return hash_equals($calculatedHash, $hashedOtp);
}

function sendOTP($email, $otp): bool
{
    try {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', $_ENV['BREVO_API_KEY']);

        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );

        $name = $_SESSION['fullname'] ?? 'User';

        $sendSmtpEmail = new SendSmtpEmail([
            'to' => [['email' => $email]],
            'templateId' => 3,
            'params' => [
                'otp' => $otp,
                'name' => $name,
            ],
            'sender' => [
                'name' => 'Serbisyos',
                'email' => 'no-reply@serbisyos.com'
            ],
            'replyTo' => [
                'name' => 'Serbisyos',
                'email' => 'no-reply@serbisyos.com'
            ]
        ]);

        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

header('Content-Type: application/json');

if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_email'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please sign up again.']);
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

$expiry_time_manila = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

$stmt = $conn->prepare(
    "UPDATE otp_verifications 
     SET otp = ?, expires_at = ?
     WHERE user_id = ? AND email = ? AND verified = 0 
     ORDER BY created_at DESC LIMIT 1"
);

$stmt->bind_param("ssis", $hashedOTP, $expiry_time_manila, $user_id, $email);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        if (sendOTP($email, $otp)) {
            $_SESSION['last_otp_sent'] = time();
            echo json_encode(['success' => true, 'message' => 'A new OTP has been sent to your email.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send the new OTP. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not find an active OTP record to update. Please try signing up again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update OTP in the database.']);
}

exit();
?>