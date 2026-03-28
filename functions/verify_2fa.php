<?php
session_start();
require_once 'db_connection.php';
require_once '../account/include/totp.php';
require_once '2fa_encryption.php';

if (!isset($_SESSION['2fa_pending'], $_SESSION['pre_2fa_user_id'])) {
    $_SESSION['login-error'] = "Session expired or invalid. Please login again.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['pre_2fa_user_id'];
$user_data = $_SESSION['pre_2fa_user_data'];

$verification_code = '';
for ($i = 1; $i <= 6; $i++) {
    $verification_code .= $_POST["digit$i"] ?? '';
}

if (strlen($verification_code) !== 6 || !ctype_digit($verification_code)) {
    $_SESSION['2fa_error'] = "Please enter a valid 6-digit code.";
    header("Location: ../2fa_verify.php");
    exit();
}

$stmt = $conn->prepare("SELECT secret_key, encryption_key FROM user_2fa WHERE user_id = ? AND is_enabled = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login-error'] = "Two-factor authentication is not enabled for this account.";
    header("Location: ../login.php");
    exit();
}

$row = $result->fetch_assoc();
$secret = EncryptionHelper::decrypt($row['secret_key'], $row['encryption_key']);

if ($secret === false) {
    $_SESSION['login-error'] = "Authentication error. Please contact support.";
    header("Location: ../login.php");
    exit();
}

$totp = new TOTP($secret);
$verified = $totp->verifyCode($verification_code, 30);

if ($verified) {

    $session_id = bin2hex(random_bytes(32));
    $_SESSION['session_id'] = $session_id;
    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['fullname'] = $user_data['fullname'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['auth_provider'] = $user_data['auth_provider'];

    unset($_SESSION['2fa_pending'], $_SESSION['pre_2fa_user_id'], $_SESSION['pre_2fa_user_data']);

    if ($user_data['remember_me']) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        $token_stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token, expires_at, device_info) VALUES (?, ?, ?, ?)");
        $token_stmt->bind_param("isss", $user_data['id'], $token, $expires, $user_agent);
        $token_stmt->execute();
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);
    }

    if ($user_data['status'] !== 'verified') {
        $otp = generateOTP();
        $hashedOTP = hashOTP($otp);
        $expires_at = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

        $otp_stmt = $conn->prepare("INSERT INTO otp_verifications (user_id, email, otp, expires_at) VALUES (?, ?, ?, ?)");
        $otp_stmt->bind_param("isss", $user_data['id'], $user_data['email'], $hashedOTP, $expires_at);
        $otp_stmt->execute();

        if (sendOTP($user_data['email'], $otp)) {
            $_SESSION['temp_user_id'] = $user_data['id'];
            $_SESSION['temp_email'] = $user_data['email'];
            
            if (isset($_SESSION['redirect_after_2fa'])) {
                $_SESSION['redirect_after_otp'] = $_SESSION['redirect_after_2fa'];
                unset($_SESSION['redirect_after_2fa']);
            }
            header("Location: ../verify_otp.php");
            exit();
        } else {
            $_SESSION['login-error'] = "Login successful, but failed to send verification code.";
            header("Location: ../login.php");
            exit();
        }
    }

    $redirect = $_SESSION['redirect_after_2fa'] ?? '../account/home.php';
    unset($_SESSION['redirect_after_2fa']);
    header("Location: " . ltrim($redirect, '/'));
    exit();

} else {
    $_SESSION['2fa_error'] = "Invalid verification code. Please try again.";
    unset($_SESSION['show_backup_form']);
    header("Location: ../2fa_verify.php");
    exit();
}
?>
