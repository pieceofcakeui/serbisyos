<?php
session_start();
require_once 'db_connection.php';
require_once '../account/include/totp.php';
require_once '2fa_encryption.php';

require_once '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        $_SESSION['2fa_error'] = "User not found.";
        header("Location: ../account/settings-and-privacy.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM user_2fa WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['is_enabled']) {
            $_SESSION['2fa_error'] = "Two-factor authentication is already enabled.";
            header("Location: ../account/settings-and-privacy.php");
            exit();
        }

        $secret = EncryptionHelper::decrypt($row['secret_key'], $row['encryption_key']);
        $totp = new TOTP($secret);
    } else {
        $totp = new TOTP();
        $secret = $totp->getSecret();

        $encryption_key = '';
        $encrypted_secret = EncryptionHelper::encrypt($secret, $encryption_key);

        $stmt = $conn->prepare("INSERT INTO user_2fa (user_id, secret_key, encryption_key, is_enabled) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("iss", $user_id, $encrypted_secret, $encryption_key);
        $stmt->execute();
    }

    $app_name = 'Serbisyos';
    $user_email = $user['email'];
    $user_name = $user['fullname'] ?? $user_email;

    $issuer = urlencode($app_name);
    $account_name = urlencode($user_name . ' (' . $user_email . ')');
    $otpauth_url = "otpauth://totp/{$issuer}:{$account_name}?secret={$secret}&issuer={$issuer}";

    $qr_url = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauth_url);

    $_SESSION['2fa_setup'] = true;
    $_SESSION['2fa_secret'] = $secret;
    $_SESSION['2fa_user_email'] = $user_email;
    $_SESSION['2fa_user_name'] = $user_name;

    header("Location: ../account/2fa_setup.php?qr=" . urlencode($qr_url) . "&secret=" . urlencode($secret) . "&otpauth=" . urlencode($otpauth_url));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['2fa_setup']) || !isset($_SESSION['2fa_secret'])) {
        $_SESSION['2fa_error'] = "Invalid 2FA setup session.";
        header("Location: ../account/settings-and-privacy.php");
        exit();
    }

    $verification_code = $_POST['verification_code'] ?? '';
    $secret = $_SESSION['2fa_secret'];

    $totp = new TOTP($secret);

    if ($totp->verifyCode($verification_code)) {
        $backup_codes = $totp->generateBackupCodes();

        $stmt = $conn->prepare("INSERT INTO user_2fa_backup_codes (user_id, backup_code, encryption_key) VALUES (?, ?, ?)");

        foreach ($backup_codes as $code) {
            $encryption_key = '';
            $encrypted_code = EncryptionHelper::encrypt($code, $encryption_key);
            $stmt->bind_param("iss", $user_id, $encrypted_code, $encryption_key);
            $stmt->execute();
        }

        $encryption_key = '';
        $encrypted_secret = EncryptionHelper::encrypt($secret, $encryption_key);

        $stmt = $conn->prepare("UPDATE user_2fa SET secret_key = ?, encryption_key = ?, is_enabled = 1 WHERE user_id = ?");
        $stmt->bind_param("ssi", $encrypted_secret, $encryption_key, $user_id);
        $stmt->execute();

        $user_email = $_SESSION['2fa_user_email'];
        $user_name = $_SESSION['2fa_user_name'];
        $enabled_date = date('F j, Y g:i A');

        $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
        $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
            new GuzzleHttp\Client(),
            $config
        );
        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();

        try {
            $sendSmtpEmail->setTemplateId(14);
            $sendSmtpEmail->setTo([['email' => $user_email, 'name' => $user_name]]);
            $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos Security']);
            $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos Security']);
            
            $sendSmtpEmail->setParams([
                'NAME' => $user_name,
                'ENABLED_DATE' => $enabled_date,
            ]);

            $apiInstance->sendTransacEmail($sendSmtpEmail);

        } catch (Exception $e) {
            error_log("Brevo API Error (Template 14): " . $e->getMessage());
        }

        unset($_SESSION['2fa_setup']);
        unset($_SESSION['2fa_secret']);
        unset($_SESSION['2fa_user_email']);
        unset($_SESSION['2fa_user_name']);

        $_SESSION['backup_codes'] = $backup_codes;

        $_SESSION['success_message'] = "Two-factor authentication has been enabled successfully.";
        header("Location: ../account/2fa_success.php");
        exit();
    } else {

        $user_email = $_SESSION['2fa_user_email'];
        $user_name = $_SESSION['2fa_user_name'];

        $app_name = 'Serbisyos';
        $issuer = urlencode($app_name);
        $account_name = urlencode($user_name . ' (' . $user_email . ')');
        $otpauth_url = "otpauth://totp/{$issuer}:{$account_name}?secret={$secret}&issuer={$issuer}";
        $qr_url = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauth_url);

        header("Location: ../account/2fa_setup.php?qr=" . urlencode($qr_url) . "&secret=" . urlencode($secret) . "&otpauth=" . urlencode($otpauth_url) . "&error=1");
        exit();
    }
}
?>
