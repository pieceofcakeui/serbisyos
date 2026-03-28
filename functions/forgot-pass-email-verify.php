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
define('OTP_EXPIRY_MINUTES', 5);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_HOURS', 3);
define('OTP_LENGTH', 6);

if (!isset($_ENV['OTP_SECRET_KEY']) || strlen($_ENV['OTP_SECRET_KEY']) < 32) {
    die('Error: OTP_SECRET_KEY not configured properly');
}

function generateOTP(): string
{
    $min = pow(10, OTP_LENGTH - 1);
    $max = pow(10, OTP_LENGTH) - 1;
    return strval(random_int($min, $max));
}

function hashOTP($otp) {
    return hash_hmac('sha256', $otp, OTP_SECRET_KEY);
}

function sendOTP($email, $otp)
{
    try {
        $config = Brevo\Client\Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
        
        $apiInstance = new TransactionalEmailsApi(
            new Client(), 
            $config
        );

        $name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'User';
 
        $sendSmtpEmail = new SendSmtpEmail([
            'to' => [['email' => $email]],
            'templateId' => 4,
            'params' => [
                'otp' => $otp,
                'name' => $name,
            ],
        ]);

        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['forgot_password'])) {
    
    if (isset($_POST['recaptcha_token'])) {
        $token = $_POST['recaptcha_token'];
        $secretKey = $_ENV['RECAPTCHA_SECRET_KEY'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $secretKey,
            'response' => $token
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $verifyResponse = curl_exec($ch);
        curl_close($ch);
        
        $responseData = json_decode($verifyResponse);

        if (!$responseData->success || $responseData->score < 0.5 || $responseData->action != 'forgot_password') {
            $_SESSION['forgot-password-error'] = "reCAPTCHA verification failed. Please try again.";
            header("Location: ../forgot-password.php");
            exit();
        }
    } else {
        $_SESSION['forgot-password-error'] = "reCAPTCHA token missing. Please try again.";
        header("Location: ../forgot-password.php");
        exit();
    }

    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id, auth_provider FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['auth_provider'] !== 'manual') {
            $_SESSION['forgot-password-error'] = "Password reset is not allowed for accounts signed up with Google. Please use Google to sign in.";
            header("Location: ../forgot-password.php");
            exit();
        }
        
        $otp = generateOTP();
        $hashed_otp = hashOTP($otp);

        $current_time_manila = date('Y-m-d H:i:s');
        $expiry_time_manila = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

        $stmt = $conn->prepare("INSERT INTO otp_verifications (user_id, email, otp, expires_at, created_at, purpose) VALUES (?, ?, ?, ?, ?, 'verification')");
        $stmt->bind_param("issss", $user['id'], $email, $hashed_otp, $expiry_time_manila, $current_time_manila);
        $stmt->execute();

        if (sendOTP($email, $otp)) {
            $_SESSION['temp_user_id'] = $user['id'];
            $_SESSION['temp_email'] = $email;
            header("Location: ../verify_otp_reset.php");
            exit();
        } else {
            $_SESSION['forgot-password-error'] = "Failed to send reset code. Please try again.";
            header("Location: ../forgot-password.php");
            exit();
        }
    } else {
        $_SESSION['forgot-password-error'] = "Email not found!";
        header("Location: ../forgot-password.php");
        exit();
    }
}
?>