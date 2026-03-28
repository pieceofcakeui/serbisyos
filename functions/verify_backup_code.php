<?php
session_start();
require_once 'db_connection.php';
require_once '2fa_encryption.php';

if (!isset($_SESSION['2fa_pending'], $_SESSION['pre_2fa_user_id'])) {
    $_SESSION['login-error'] = "Session expired or invalid. Please login again.";
    header("Location: ../home");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../home");
    exit();
}

$userId = $_SESSION['pre_2fa_user_id'];
$user_data = $_SESSION['pre_2fa_user_data'];

$backupCode = isset($_POST['backup_code']) ? trim($_POST['backup_code']) : '';
$backupCode = str_replace('-', '', $backupCode);

if (!preg_match('/^\d{8}$/', $backupCode)) {
    $_SESSION['2fa_error'] = "Invalid backup code format. Please enter an 8-digit code.";
    $_SESSION['show_backup_form'] = true; 
    header("Location: ../2fa_verify.php");
    exit();
}

$formattedCode = substr($backupCode, 0, 4) . '-' . substr($backupCode, 4, 4);

$stmt = $conn->prepare("SELECT id, backup_code, encryption_key FROM user_2fa_backup_codes WHERE user_id = ? AND is_used = 0");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$codeFound = false;
$codeId = null;

while ($row = $result->fetch_assoc()) {
    $decryptedCode = EncryptionHelper::decrypt($row['backup_code'], $row['encryption_key']);
    if ($decryptedCode !== false && hash_equals($decryptedCode, $formattedCode)) {
        $codeFound = true;
        $codeId = $row['id'];
        break;
    }
}

if ($codeFound) {
    $updateStmt = $conn->prepare("UPDATE user_2fa_backup_codes SET is_used = 1, used_at = NOW() WHERE id = ?");
    $updateStmt->bind_param("i", $codeId);
    $updateStmt->execute();

    $session_id = bin2hex(random_bytes(32));
    $_SESSION['session_id'] = $session_id;
    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['fullname'] = $user_data['fullname'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['auth_provider'] = $user_data['auth_provider'];

    unset($_SESSION['2fa_pending'], $_SESSION['pre_2fa_user_id'], $_SESSION['pre_2fa_user_data'], $_SESSION['show_backup_form']);

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
            header("Location: ../home");
            exit();
        }
    }

    $redirect = $_SESSION['redirect_after_2fa'] ?? '../account/home.php';
    unset($_SESSION['redirect_after_2fa']);
    header("Location: " . ltrim($redirect, '/'));
    exit();

} else {
    $_SESSION['2fa_error'] = "Invalid or used backup code. Please try again.";
    $_SESSION['show_backup_form'] = true;
    header("Location: ../2fa_verify.php");
    exit();
}
