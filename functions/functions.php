<?php
session_start();
require 'db_connection.php';
include 'encrypt_loc.php';
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

function hashOTP($otp)
{
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
            'sender' => [
                'name' => 'Serbisyos',
                'email' => 'no-reply@serbisyos.com'
            ],
            'to' => [['email' => $email]],
            'replyTo' => [
                'name' => 'Serbisyos',
                'email' => 'no-reply@serbisyos.com'
            ],
            'templateId' => 2,
            'params' => [
                'otp' => $otp,
                'name' => $name,
            ],
        ]);

        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        error_log("Email sent to: $email, OTP: $otp");
        return true;
    } catch (Exception $e) {
        error_log("Brevo API Exception: " . $e->getMessage());
        error_log("Failed to send to: $email, Template: 2, Name: $name");
        return false;
    }
}


$flask_url = $_ENV['FLASK_OTP_URL'] . "/verify_otp";

// Handle OTP Verification
if (isset($_POST['verify_otp'])) {
    $user_id = $_SESSION['temp_user_id'];
    $email = $_SESSION['temp_email'];
    $otp = $_POST['otp'];

    $data = json_encode([
        "user_id" => $user_id,
        "email" => $email,
        "otp" => $otp
    ]);

    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($http_code == 200 && isset($result['success']) && $result['success'] === true) {
        $_SESSION['user_id'] = $user_id;
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_email']);

        if (isset($_SESSION['redirect_after_otp'])) {
            $redirect = $_SESSION['redirect_after_otp'];
            unset($_SESSION['redirect_after_otp']);
            header("Location: ../" . ltrim($redirect, '/'));
            exit();
        } else {
            header("Location: ../account/home.php");
            exit();
        }
    } else {
        $_SESSION['verify-error'] = $result['message'] ?? "OTP verification failed!";
        header("Location: ../verify_otp.php");
        exit();
    }
}

// Handle User Signup
if (isset($_POST['sign_up'])) {

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

        if (!$responseData->success || $responseData->score < 0.5 || $responseData->action != 'signup') {
            $_SESSION['signup-error'] = "reCAPTCHA verification failed. Please try again.";
            header("Location: ../signup.php");
            exit();
        }
    } else {
        $_SESSION['signup-error'] = "reCAPTCHA token missing. Please try again.";
        header("Location: ../signup.php");
        exit();
    }

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $auth_provider = 'manual';
    $account_state = 'Active';

    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['signup-error'] = "Invalid email format!";
        header("Location: ../signup.php");
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['signup-error'] = "Password must be at least 8 characters!";
        header("Location: ../signup.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['signup-error'] = "Email already exists!";
        header("Location: ../signup.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $session_id = bin2hex(random_bytes(32));
    $verification_expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $stmt = $conn->prepare("INSERT INTO users 
        (fullname, email, password, status, profile_type, auth_provider, account_state, verification_expiry) 
        VALUES (?, ?, ?, 'unverified', 'user', ?, ?, ?)");

    $stmt->bind_param(
        "ssssss",
        $fullname,
        $email,
        $hashed_password,
        $auth_provider,
        $account_state,
        $verification_expiry
    );

    if (!$stmt->execute()) {
        $_SESSION['signup-error'] = "Failed to create account! Please try again.";
        header("Location: ../signup.php");
        exit();
    }

    $user_id = $stmt->insert_id;

    $activity_type = 'SIGNUP';
    $activity_stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, device_info, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $activity_stmt->bind_param("issss", $user_id, $activity_type, $user_agent, $ip_address, $user_agent);
    $activity_stmt->execute();

    $session_stmt = $conn->prepare("INSERT INTO active_sessions (user_id, session_id, device_info, ip_address, user_agent, login_time, last_activity, is_current) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), TRUE)");
    $session_stmt->bind_param("issss", $user_id, $session_id, $user_agent, $ip_address, $user_agent);
    $session_stmt->execute();

    $otp = generateOTP();
    $hashedOTP = hashOTP($otp);
    $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $otp_stmt = $conn->prepare("INSERT INTO otp_verifications (user_id, email, otp, expires_at) VALUES (?, ?, ?, ?)");
    $otp_stmt->bind_param("isss", $user_id, $email, $hashedOTP, $expires_at);

    if ($otp_stmt->execute()) {
        if (sendOTP($email, $otp)) {
            $_SESSION['session_id'] = $session_id;
            $_SESSION['temp_user_id'] = $user_id;
            $_SESSION['temp_email'] = $email;
            $_SESSION['otp_sent'] = true;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['auth_provider'] = $auth_provider;

            if (isset($_SESSION['redirect_after_signup'])) {
                $_SESSION['redirect_after_otp'] = $_SESSION['redirect_after_signup'];
                unset($_SESSION['redirect_after_signup']);
            }

            header("Location: ../verify_otp.php");
            exit();
        } else {
            $conn->query("DELETE FROM users WHERE id = $user_id");
            
            $_SESSION['signup-error'] = "Failed to send OTP. Please try again.";
            header("Location: ../signup.php");
            exit();
        }
    } else {
        $conn->query("DELETE FROM users WHERE id = $user_id");

        $_SESSION['signup-error'] = "Failed to create OTP record. Please try again.";
        header("Location: ../signup.php");
        exit();
    }
}

$max_attempts = 5;
$lockout_time = 3 * 60 * 60;

// Handle Sign In
if (isset($_POST['signin'])) {

    if (isset($_POST['recaptcha_token'])) {
        $token = $_POST['recaptcha_token'];
        $secretKey = $_ENV['RECAPTCHA_SECRET_KEY'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => $secretKey, 'response' => $token]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $verifyResponse = curl_exec($ch);
        curl_close($ch);
        $responseData = json_decode($verifyResponse);
        if (!$responseData->success || $responseData->score < 0.5 || $responseData->action != 'login') {
            $_SESSION['login-error'] = "reCAPTCHA verification failed. Please try again.";
            header("Location: ../login.php");
            exit();
        }
    } else {
        $_SESSION['login-error'] = "reCAPTCHA token missing. Please try again.";
        header("Location: ../login.php");
        exit();
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    $stmt = $conn->prepare("SELECT id, fullname, email, password, login_attempts, last_attempt, status, auth_provider, account_state FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['account_state'] === 'Inactive') {
            $_SESSION['login-error'] = "Your account is inactive. Please contact admin.";
            header("Location: ../login.php");
            exit();
        }

        $current_time = time();
        $last_attempt_time = strtotime($user['last_attempt']);
        if ($user['login_attempts'] >= MAX_LOGIN_ATTEMPTS && ($current_time - $last_attempt_time) < (LOCKOUT_HOURS * 3600)) {
            $time_left = (LOCKOUT_HOURS * 3600) - ($current_time - $last_attempt_time);
            $time_format = gmdate("H:i:s", $time_left);
            $_SESSION['login-error'] = "Account locked. Time remaining: $time_format";
            header("Location: ../login.php");
            exit();
        }
        
        if ($user['auth_provider'] === 'google') {
            $_SESSION['login-error'] = "This email is registered using Google Sign-In. Please click 'Sign In with Google'.";
            header("Location: ../login.php");
            exit();
        }
        
        if (password_verify($password, $user['password'])) {
            $reset_stmt = $conn->prepare("UPDATE users SET login_attempts = 0, last_login = NOW() WHERE id = ?");
            $reset_stmt->bind_param("i", $user['id']);
            $reset_stmt->execute();
            
            $activity_type = 'LOGIN SUCCESS';
            $activity_stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, device_info, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
            $activity_stmt->bind_param("issss", $user['id'], $activity_type, $user_agent, $ip_address, $user_agent);
            $activity_stmt->execute();
            
            $session_id = bin2hex(random_bytes(32));
            $update_old_sessions = $conn->prepare("UPDATE active_sessions SET is_current = FALSE WHERE user_id = ?");
            $update_old_sessions->bind_param("i", $user['id']);
            $update_old_sessions->execute();

            $twofa_stmt = $conn->prepare("SELECT is_enabled FROM user_2fa WHERE user_id = ?");
            $twofa_stmt->bind_param("i", $user['id']);
            $twofa_stmt->execute();
            $twofa_result = $twofa_stmt->get_result();
            $twofa_enabled = false;

            if ($twofa_result->num_rows > 0 && $twofa_result->fetch_assoc()['is_enabled'] == 1) {
                $twofa_enabled = true;
            }

            $is_2fa_verified = $twofa_enabled ? 0 : 1;
            $is_current = 1;

            $session_stmt = $conn->prepare("INSERT INTO active_sessions (user_id, session_id, device_info, ip_address, user_agent, is_2fa_verified, is_current, login_time, last_activity) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $session_stmt->bind_param("issssii", $user['id'], $session_id, $user_agent, $ip_address, $user_agent, $is_2fa_verified, $is_current);
            $session_stmt->execute();

            if ($twofa_enabled) {
                $_SESSION['pre_2fa_user_id'] = $user['id'];
                $_SESSION['pre_2fa_user_data'] = [
                    'id'            => $user['id'],
                    'session_id'    => $session_id,
                    'fullname'      => $user['fullname'],
                    'email'         => $user['email'],
                    'auth_provider' => $user['auth_provider'],
                    'status'        => $user['status'],
                    'remember_me'   => $remember_me
                ];
                $_SESSION['2fa_pending'] = true;
                if (isset($_SESSION['redirect_after_login'])) {
                    $_SESSION['redirect_after_2fa'] = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                }
                header("Location: ../2fa_verify.php");
                exit();
            } else {
                $_SESSION['session_id'] = $session_id;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['auth_provider'] = $user['auth_provider'];
                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                    $token_stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token, expires_at, device_info) VALUES (?, ?, ?, ?)");
                    $token_stmt->bind_param("isss", $user['id'], $token, $expires, $user_agent);
                    $token_stmt->execute();
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);
                }
                if ($user['status'] !== 'verified') {
                    $otp = generateOTP();
                    $hashedOTP = hashOTP($otp);
                    $expires_at = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));
                    $otp_stmt = $conn->prepare("INSERT INTO otp_verifications (user_id, email, otp, expires_at) VALUES (?, ?, ?, ?)");
                    $otp_stmt->bind_param("isss", $user['id'], $email, $hashedOTP, $expires_at);
                    $otp_stmt->execute();
                    if (sendOTP($email, $otp)) {
                        $_SESSION['temp_user_id'] = $user['id'];
                        $_SESSION['temp_email'] = $email;
                        if (isset($_SESSION['redirect_after_login'])) {
                            $_SESSION['redirect_after_otp'] = $_SESSION['redirect_after_login'];
                        }
                        header("Location: ../verify_otp.php");
                        exit();
                    } else {
                        $_SESSION['login-error'] = "Login successful, but failed to send verification code.";
                        header("Location: ../login.php");
                        exit();
                    }
                }
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: ../" . ltrim($redirect, '/'));
                } else {
                    header("Location: ../account/home.php");
                }
                exit();
            }
        } else {
            $new_attempts = $user['login_attempts'] + 1;
            $remaining_attempts = max(0, MAX_LOGIN_ATTEMPTS - $new_attempts);
            $update_stmt = $conn->prepare("UPDATE users SET login_attempts = ?, last_attempt = NOW() WHERE id = ?");
            $update_stmt->bind_param("ii", $new_attempts, $user['id']);
            $update_stmt->execute();
            if ($remaining_attempts > 0) {
                $_SESSION['login-error'] = "Invalid password! You have $remaining_attempts attempt(s) left.";
            } else {
                $_SESSION['login-error'] = "Account locked for " . LOCKOUT_HOURS . " hours due to too many failed attempts.";
            }
            header("Location: ../login.php");
            exit();
        }
    } else {
        $_SESSION['login-error'] = "Email not found!";
        header("Location: ../login.php");
        exit();
    }
}

if (isset($_POST['update_profile'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['update-profile-error'] = "Invalid form submission. Please try again.";
        header("Location: settings-and-privacy");
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Session expired. Please login again.";
        header("Location: ../login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $fullname = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);
    $barangay = filter_input(INPUT_POST, 'barangay', FILTER_SANITIZE_STRING);
    $municipality = filter_input(INPUT_POST, 'municipality', FILTER_SANITIZE_STRING);
    $province = filter_input(INPUT_POST, 'province', FILTER_SANITIZE_STRING);
    $postal_code = filter_input(INPUT_POST, 'postal_code', FILTER_SANITIZE_STRING);

    $latitude = 0;
    $longitude = 0;

    if (!empty($barangay) && !empty($municipality) && !empty($province)) {
        $address = "$barangay, $municipality, $province, Philippines";
        if (!empty($postal_code)) {
            $address .= " $postal_code";
        }

        $geo_data = geocodeAddress($address);
        if ($geo_data) {
            $latitude = $geo_data['lat'];
            $longitude = $geo_data['lon'];
        }
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['update-profile-error'] = "Email already exists!";
        header("Location: settings-and-privacy");
        exit;
    }

    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($filetype, $allowed)) {
            $_SESSION['update-profile-error'] = "Only JPG, JPEG, PNG & WEBP files are allowed.";
            header("Location: settings-and-privacy");
            exit;
        }

        $new_filename = uniqid() . '.' . $filetype;
        $upload_path = '../assets/img/profile/' . $new_filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $old_picture = $result->fetch_assoc()['profile_picture'];

            if ($old_picture && $old_picture != 'profile-user.png' && file_exists('../assets/img/profile/' . $old_picture)) {
                unlink('../assets/img/profile/' . $old_picture);
            }

            $profile_picture = $new_filename;
        } else {
            $_SESSION['update-profile-error'] = "Failed to upload profile picture.";
            header("Location: settings-and-privacy");
            exit;
        }
    }

    if ($profile_picture) {
        $stmt = $conn->prepare("UPDATE users SET fullname=?, email=?, contact_number=?, barangay=?, town=?, province=?, postal_code=?, latitude=?, longitude=?, profile_picture=? WHERE id=?");
        $stmt->bind_param("sssssssddsi", $fullname, $email, $contact_number, $barangay, $municipality, $province, $postal_code, $latitude, $longitude, $profile_picture, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname=?, email=?, contact_number=?, barangay=?, town=?, province=?, postal_code=?, latitude=?, longitude=? WHERE id=?");
        $stmt->bind_param("sssssssddi", $fullname, $email, $contact_number, $barangay, $municipality, $province, $postal_code, $latitude, $longitude, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['fullname'] = $fullname;
        $_SESSION['email'] = $email;

        $_SESSION['update-profile-success'] = "Profile updated successfully!";
    } else {
        $_SESSION['update-profile-error'] = "Failed to update profile: " . $conn->error;
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header("Location: settings-and-privacy");
    exit;
}
/**
 * Geocode an address using Google Maps API
 * 
 * @param string $address
 * @return array|false
 */
function geocodeAddress($address)
{
    $apiKey = $_ENV['GOOGLE_MAPS_API_KEY'];
    $encodedAddress = urlencode($address);

    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedAddress}&key={$apiKey}&components=country:PH";

    try {
        $response = file_get_contents($url);
        if ($response === false) {
            error_log("Failed to connect to Google Maps API");
            return false;
        }

        $data = json_decode($response, true);

        if ($data['status'] !== 'OK' || empty($data['results'])) {
            error_log("Google Maps geocoding failed with status: " . $data['status']);
            return false;
        }

        $location = $data['results'][0]['geometry']['location'];
        return [
            'lat' => (float) $location['lat'],
            'lon' => (float) $location['lng']
        ];
    } catch (Exception $e) {
        error_log("Google Maps Geocoding error: " . $e->getMessage());
        return false;
    }
}

// Handle OTP Verification
if (isset($_POST['otp'])) {
    $user_id = $_SESSION['temp_user_id'];
    $email = $_SESSION['temp_email'];
    $otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT * FROM otp_verifications WHERE user_id = ? AND email = ? AND verified = 0 ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("is", $user_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $otpData = $result->fetch_assoc();
        
       $current_time_utc = new DateTime("now", new DateTimeZone('UTC'));
        $expiry_time_utc = new DateTime($otpData['expires_at'], new DateTimeZone('UTC'));

        if ($current_time_utc < $expiry_time_utc) {
            $hashed_otp = hash_hmac('sha256', $otp, OTP_SECRET_KEY);

            if (hash_equals($hashed_otp, $otpData['otp'])) {
                $stmt = $conn->prepare("UPDATE otp_verifications SET verified = 1 WHERE id = ?");
                $stmt->bind_param("i", $otpData['id']);
                $stmt->execute();

                $_SESSION['otp_verified'] = true;
                header("Location: ../reset_password.php");
                exit();
            } else {
                $_SESSION['otp-error'] = "Invalid verification code!";
            }
        } else {
            $_SESSION['otp-error'] = "Verification code has expired!";
        }
    } else {
        $_SESSION['otp-error'] = "Invalid verification code!";
    }
    header("Location: ../verify_otp_reset.php");
    exit();
}

// Handle Password Reset
if (isset($_POST['reset_password'])) {
    $user_id = $_SESSION['temp_user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['reset-password-error'] = "Passwords do not match!";
        header("Location: ../reset_password.php");
        exit();
    }

    if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
        $_SESSION['reset-password-error'] = "OTP verification required!";
        header("Location: ../verify_otp_reset.php");
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        $stmt = $conn->prepare("UPDATE otp_verifications SET verified = 1 WHERE user_id = ? AND email = ?");
        $stmt->bind_param("is", $user_id, $_SESSION['temp_email']);
        $stmt->execute();

        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_email']);
        unset($_SESSION['otp_verified']);

        $_SESSION['reset-password-success'] = "Your password has been reset successfully!";
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['reset-password-error'] = "Failed to reset password. Please try again.";
        header("Location: ../reset_password.php");
        exit();
    }
}
