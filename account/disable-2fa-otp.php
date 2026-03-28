<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
include 'db_connection.php';
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

ob_clean();
header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN'); 

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    $headers = getallheaders();
    if (!isset($headers['X-CSRF-TOKEN']) || !isset($_SESSION['csrf_token']) || $headers['X-CSRF-TOKEN'] !== $_SESSION['csrf_token']) {
        throw new Exception('CSRF token validation failed');
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized');
    }

    $user_id = $_SESSION['user_id'];
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }

    if (isset($input['action']) && $input['action'] === 'verify_email') {
        if (!isset($input['email']) || empty($input['email'])) {
            throw new Exception('Email is required');
        }
        
        $entered_email = trim($input['email']);
        
        $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        $registered_email = $user['email'];
        $name = $user['fullname'] ?? $user['email'];
        
        if (strtolower($entered_email) !== strtolower($registered_email)) {
            throw new Exception('Email does not match your registered email address');
        }
        
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires_at = date('Y-m-d H:i:s', time() + 300);
        $purpose = 'disable_2fa';
        
        $stmt = $conn->prepare("DELETE FROM otp_verifications WHERE user_id = ? AND purpose = ?");
        $stmt->bind_param("is", $user_id, $purpose);
        $stmt->execute();
        
        $stmt = $conn->prepare("INSERT INTO otp_verifications 
                               (user_id, email, otp, created_at, expires_at, verified, purpose) 
                               VALUES (?, ?, ?, NOW(), ?, 0, ?)");
        $stmt->bind_param("issss", $user_id, $registered_email, $otp, $expires_at, $purpose);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to generate OTP');
        }
        
        $otp_id = $conn->insert_id;

        $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
        $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
            new GuzzleHttp\Client(),
            $config
        );
        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();

        try {
            $sendSmtpEmail->setTemplateId(21);
            $sendSmtpEmail->setTo([['email' => $registered_email, 'name' => $name]]);
            $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
            $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos Support']);
            
            $sendSmtpEmail->setParams([
                'USER_NAME' => $name,
                'OTP_CODE' => $otp,
                'LOGO_URL' => BASE_URL . '/assets/img/logo.png',
                'BASE_URL' => BASE_URL
            ]);

            $apiInstance->sendTransacEmail($sendSmtpEmail);

            $_SESSION['otp_verification_id'] = $otp_id;
            $_SESSION['verified_email'] = $registered_email;
            $_SESSION['otp_attempts'] = 0;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Email verified! OTP has been sent to your email address.',
                'email' => $registered_email
            ]);
            
        } catch (Exception $e) {
            $conn->query("DELETE FROM otp_verifications WHERE id = $otp_id");
            error_log("Brevo API Error (Template 21): " . $e->getMessage());
            throw new Exception('Failed to send OTP email. Please try again.');
        }
        
        exit();
    }

    if (isset($input['action']) && $input['action'] === 'verify_otp') {
        if (!isset($_SESSION['otp_verification_id']) || !isset($_SESSION['verified_email'])) {
            throw new Exception('Please verify your email first');
        }
        
        if (!isset($input['otp']) || empty($input['otp'])) {
            throw new Exception('OTP is required');
        }
        
        $entered_otp = trim($input['otp']);
        $verification_id = $_SESSION['otp_verification_id'];
        
        if (($_SESSION['otp_attempts'] ?? 0) >= 3) {
            throw new Exception('Too many attempts. Please request a new OTP.');
        }
        
        $stmt = $conn->prepare("SELECT id, otp, expires_at FROM otp_verifications 
                               WHERE id = ? AND user_id = ? AND email = ? AND verified = 0 
                               AND purpose = 'disable_2fa'");
        $stmt->bind_param("iis", $verification_id, $user_id, $_SESSION['verified_email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $otp_record = $result->fetch_assoc();
        
        if (!$otp_record) {
            throw new Exception('OTP not found or already used. Please request a new one.');
        }
        
        if (strtotime($otp_record['expires_at']) < time()) {
            throw new Exception('OTP has expired. Please request a new one.');
        }
        
        if ($otp_record['otp'] === $entered_otp) {
            $stmt = $conn->prepare("UPDATE otp_verifications SET verified = 1 WHERE id = ?");
            $stmt->bind_param("i", $verification_id);
            $stmt->execute();
            
            $_SESSION['otp_verified'] = true;
            $_SESSION['email_verification_complete'] = time();
            
            echo json_encode([
                'success' => true, 
                'message' => 'OTP verified successfully! You can now proceed with 2FA authentication.'
            ]);
        } else {
            $_SESSION['otp_attempts'] = ($_SESSION['otp_attempts'] ?? 0) + 1;
            $remaining_attempts = 3 - $_SESSION['otp_attempts'];
            
            if ($_SESSION['otp_attempts'] >= 3) {
                $stmt = $conn->prepare("UPDATE otp_verifications SET verified = -1 
                                       WHERE user_id = ? AND purpose = 'disable_2fa' AND verified = 0");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                
                unset($_SESSION['verified_email'], $_SESSION['otp_attempts'], $_SESSION['otp_verification_id'], $_SESSION['otp_verified']);
                
                throw new Exception('Too many incorrect attempts. Please start over with email verification.');
            } else {
                throw new Exception("Invalid OTP. You have $remaining_attempts attempts remaining.");
            }
        }
        exit();
    }

    throw new Exception('Invalid request');

} catch (Exception $e) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Error $e) {
    ob_clean();
    http_response_code(500);
    error_log("Server Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
} finally {
    ob_end_flush();
}
