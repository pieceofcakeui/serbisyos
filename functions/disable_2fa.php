<?php
ini_set('display_errors', 0);

require_once 'auth.php';
require_once 'db_connection.php';
require_once '../account/include/totp.php';
require_once '2fa_encryption.php';
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

date_default_timezone_set('Asia/Manila');

$response = ['success' => false, 'message' => 'Unknown error'];

try {
    if (!isset($_SESSION['user_id'])) {
        if (isset($_POST['action'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Session expired']);
            exit();
        }
        header("Location: ../../login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    if (isset($_POST['action'])) {
        header('Content-Type: application/json');

        $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();

        if ($user_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit();
        }

        $user = $user_result->fetch_assoc();
        $user_email = $user['email'];
        $user_fullname = $user['fullname'] ?? $user_email; 

        if ($_POST['action'] === 'send_otp') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                exit();
            }

            if ($email !== $user_email) {
                echo json_encode(['success' => false, 'message' => 'Email does not match your account']);
                exit();
            }

            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

            $cleanup_stmt = $conn->prepare("DELETE FROM otp_verifications WHERE user_id = ? AND purpose = '2fa_disable'");
            $cleanup_stmt->bind_param("i", $user_id);
            $cleanup_stmt->execute();

            $stmt = $conn->prepare("INSERT INTO otp_verifications (user_id, email, otp, purpose, expires_at, verified) VALUES (?, ?, ?, '2fa_disable', ?, 0)");
            $stmt->bind_param("isss", $user_id, $email, $otp, $expires_at);

            if (!$stmt->execute()) {
                $db_error = $stmt->error;
                error_log("Failed to insert new OTP: " . $db_error);
                echo json_encode(['success' => false, 'message' => 'Failed to generate OTP. DB Error: ' . $db_error]);
                exit();
            }

            $timestamp = time();
            $unique_id = substr(md5($email . $timestamp), 0, 8);
            $generated_date = date('F j, Y g:i A');

            $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
            $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
                new GuzzleHttp\Client(),
                $config
            );
            $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();

            try {
                $sendSmtpEmail->setTemplateId(13);
                $sendSmtpEmail->setTo([['email' => $email, 'name' => $user_fullname]]);
                $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
                $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos']);
                
                $sendSmtpEmail->setParams([
                    'OTP_CODE' => $otp,
                    'UNIQUE_ID' => $unique_id,
                    'GENERATED_DATE' => $generated_date,
                    'TIMESTAMP_REF' => $timestamp
                ]);

                $apiInstance->sendTransacEmail($sendSmtpEmail);

                echo json_encode(['success' => true, 'message' => 'OTP sent successfully to your email']);

            } catch (Exception $e) {
                error_log("Brevo API Error (Template 13): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again.']);
            }
            exit();
        }

        if ($_POST['action'] === 'verify_otp') {
            $otp = $_POST['otp'] ?? '';
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (!$otp || strlen($otp) !== 6 || !ctype_digit($otp)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid 6-digit OTP']);
                exit();
            }

            if (!$email || $email !== $user_email) {
                echo json_encode(['success' => false, 'message' => 'Invalid email']);
                exit();
            }

            $stmt = $conn->prepare("SELECT id FROM otp_verifications WHERE user_id = ? AND email = ? AND otp = ? AND purpose = '2fa_disable' AND verified = 0 AND expires_at > NOW()");
            $stmt->bind_param("iss", $user_id, $email, $otp);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                $update_stmt = $conn->prepare("UPDATE otp_verifications SET verified = 1 WHERE id = ?");
                $update_stmt->bind_param("i", $row['id']);
                $update_stmt->execute();

                $_SESSION['otp_verified'] = true;
                $_SESSION['otp_verified_time'] = time();

                echo json_encode(['success' => true, 'message' => 'OTP verified successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid OTP or expired. Please try again.']);
            }
            exit();
        }

        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        exit();
    }

    $stmt = $conn->prepare("SELECT email, auth_provider FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $auth_provider = $user['auth_provider'];
    $user_email = $user['email'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
        if ($auth_provider !== 'manual') {
            if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
                $_SESSION['2fa_disable_error'] = "Please verify your email with OTP first";
                header("Location: ../account/2fa_disable.php");
                exit();
            }

            if (isset($_SESSION['otp_verified_time']) && (time() - $_SESSION['otp_verified_time']) > 600) {
                unset($_SESSION['otp_verified'], $_SESSION['otp_verified_time']);
                $_SESSION['2fa_disable_error'] = "OTP verification expired. Please verify your email again.";
                header("Location: ../account/2fa_disable.php");
                exit();
            }
        }

        if ($auth_provider === 'manual') {
            $password = $_POST['password'] ?? '';
            if (empty($password)) {
                $_SESSION['2fa_disable_error'] = "Password is required";
                header("Location: ../account/2fa_disable.php");
                exit();
            }

            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user_data = $stmt->get_result()->fetch_assoc();

            if (!password_verify($password, $user_data['password'])) {
                $_SESSION['2fa_disable_error'] = "Invalid password";
                header("Location: ../account/2fa_disable.php");
                exit();
            }
        }

        $verification_code = $_POST['verification_code'] ?? '';
        if (empty($verification_code) || strlen($verification_code) !== 6 || !ctype_digit($verification_code)) {
            $_SESSION['2fa_disable_error'] = "Please enter a valid 6-digit verification code";
            header("Location: ../account/2fa_disable.php");
            exit();
        }

        $stmt = $conn->prepare("SELECT secret_key, encryption_key FROM user_2fa WHERE user_id = ? AND is_enabled = 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $_SESSION['error_message'] = "2FA is not enabled for your account";
            header("Location: ../account/settings-and-privacy.php");
            exit();
        }

        $row = $result->fetch_assoc();
        $secret = EncryptionHelper::decrypt($row['secret_key'], $row['encryption_key']);
        $totp = new TOTP($secret);

        if ($totp->verifyCode($verification_code)) {
            $conn->begin_transaction();

            try {
                $stmt = $conn->prepare("UPDATE user_2fa SET is_enabled = 0 WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM user_2fa_backup_codes WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM otp_verifications WHERE user_id = ? AND purpose = '2fa_disable'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                $conn->commit();

                $name_stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
                $name_stmt->bind_param("i", $user_id);
                $name_stmt->execute();
                $name_result = $name_stmt->get_result();
                $user_details = $name_result->fetch_assoc();
                
                $user_fullname_email = $user_details['fullname'] ?? $user_details['email'];
                $user_email_for_notif = $user_details['email'];
                
                $disabled_date = date('F j, Y g:i A');
                $reset_link = "https://www.serbisyos.com/login";

                $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
                $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
                    new GuzzleHttp\Client(),
                    $config
                );
                $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();

                try {
                    $sendSmtpEmail->setTemplateId(15);
                    $sendSmtpEmail->setTo([['email' => $user_email_for_notif, 'name' => $user_fullname_email]]);
                    $sendSmtpEmail->setSender(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos Security']);
                    $sendSmtpEmail->setReplyTo(['email' => $_ENV['NO_REPLY_EMAIL'], 'name' => 'Serbisyos Security']);
                    
                    $sendSmtpEmail->setParams([
                        'USER_NAME' => $user_fullname_email,
                        'USER_EMAIL' => $user_email_for_notif,
                        'DISABLED_DATE' => $disabled_date,
                        'RESET_LINK' => $reset_link,
                        'LOGO_URL' => BASE_URL . '/assets/img/logo.png',
                        'BASE_URL' => BASE_URL
                    ]);

                    $apiInstance->sendTransacEmail($sendSmtpEmail);

                } catch (Exception $e) {
                    error_log("Brevo API Error (Template 15): " . $e->getMessage());
                }

                unset($_SESSION['otp_verified'], $_SESSION['otp_verified_time']);

                $_SESSION['success_message'] = "Two-Factor Authentication has been disabled successfully";
                header("Location: ../account/settings-and-privacy.php");
                exit();

            } catch (Exception $e) {
                $conn->rollback();
                error_log("2FA Disable DB Error: " . $e->getMessage());
                $_SESSION['2fa_disable_error'] = "An error occurred while disabling 2FA. Please try again.";
                header("Location: ../account/2fa_disable.php");
                exit();
            }
        } else {
            $_SESSION['2fa_disable_error'] = "Invalid verification code. Please check your authenticator app and try again.";
            header("Location: ../account/2fa_disable.php");
            exit();
        }
    }

} catch (Exception $e) {
    error_log("2FA Disable General Error: " . $e->getMessage());

    if (isset($_POST['action'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again.']);
        exit();
    }

    $_SESSION['error_message'] = "An unexpected error occurred. Please try again.";
    header("Location: ../account/settings-and-privacy.php");
    exit();
}
?>

