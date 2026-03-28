<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_email'])) {
    $_SESSION['reset-password-error'] = "Session expired or invalid. Please try again.";
    header("Location: ../home");
    exit();
}

require_once 'db_connection.php';

$flask_url = $_ENV['FLASK_OTP_URL'] . "/verify_otp_reset";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['otp'])) {
        $user_id = $_SESSION['temp_user_id'];
        $email = $_SESSION['temp_email'];
        $otp = trim($_POST['otp']);

        if (!preg_match('/^\d{6}$/', $otp)) {
            $_SESSION['reset-password-error'] = "Invalid OTP format. Must be exactly 6 digits.";
            header("Location: /verify_otp_reset");
            exit();
        }

        $data = [
            "user_id" => $user_id,
            "email" => $email,
            "otp" => $otp
        ];

        $ch = curl_init($flask_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json"
            ],
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $_SESSION['reset-password-error'] = "Connection error: " . curl_error($ch);
            header("Location: /verify_otp_reset");
            exit();
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if ($http_code == 200 && isset($result['success']) && $result['success']) {
            $_SESSION['otp_verified'] = true;
            $_SESSION['reset-password-success'] = $result['message'] ?? "OTP verified successfully!";

            header("Location: ./reset_password.php");
            exit();
        } else {
            $error_message = $result['message'] ?? "OTP verification failed. Please try again.";
            $_SESSION['reset-password-error'] = $error_message;
            header("Location: /verify_otp_reset");
            exit();
        }
    } elseif (isset($_POST['resend_otp'])) {
        require_once 'forgot-pass-email-verify.php';
        
        $email = $_SESSION['temp_email'];
        $otp = generateOTP();
        $hashed_otp = hashOTP($otp);

        if (!defined('OTP_EXPIRY_MINUTES')) {
            define('OTP_EXPIRY_MINUTES', 5);
        }

        $current_time_manila = date('Y-m-d H:i:s');
        $expiry_time_manila = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

        $stmt = $conn->prepare("INSERT INTO otp_verifications (user_id, email, otp, expires_at, created_at, purpose) VALUES (?, ?, ?, ?, ?, 'reset')");
        $stmt->bind_param("issss", $_SESSION['temp_user_id'], $email, $hashed_otp, $expiry_time_manila, $current_time_manila);
        $stmt->execute();

        if (sendOTP($email, $otp)) {
            $_SESSION['reset-password-success'] = "New verification code sent to your email!";
        } else {
            $_SESSION['reset-password-error'] = "Failed to send new code. Please try again.";
        }
        header("Location: /verify_otp_reset");
        exit();
    }
}
?>