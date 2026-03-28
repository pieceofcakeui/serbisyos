<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'db_connection.php';
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$clientID = $_ENV['GOOGLE_CLIENT_ID']; 
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
$redirectUri = $_ENV['GOOGLE_REDIRECT_URI_LOGIN'];

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

if (!isset($_GET['code'])) {
    header("Location: " . $client->createAuthUrl());
    exit();
}

try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        $_SESSION['google-login-error'] = "Google authentication failed: " . $token['error_description'];
        header("Location: ../login.php");
        exit();
    }
    $client->setAccessToken($token['access_token']);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $google_id = $google_account_info->id;

    $stmt = $conn->prepare("SELECT id, fullname, auth_provider, account_state FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['account_state'] === 'Inactive') {
            $_SESSION['google-login-error'] = "Your account is inactive. Please contact admin.";
            header("Location: ../login.php");
            exit();
        }

        if ($user['auth_provider'] !== 'google') {
            $_SESSION['google-login-error'] = "This email is registered with a password. Please use manual login.";
            header("Location: ../login.php");
            exit();
        }

        $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update_stmt->bind_param("i", $user['id']);
        $update_stmt->execute();

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $activity_type = 'GOOGLE LOGIN';
        $activity_stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, device_info, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $activity_stmt->bind_param("issss", $user['id'], $activity_type, $user_agent, $ip_address, $user_agent);
        $activity_stmt->execute();

        $session_id = bin2hex(random_bytes(32));
        $update_current = $conn->prepare("UPDATE active_sessions SET is_current = FALSE WHERE user_id = ?");
        $update_current->bind_param("i", $user['id']);
        $update_current->execute();

        $stmt_2fa = $conn->prepare("SELECT is_enabled FROM user_2fa WHERE user_id = ?");
        $stmt_2fa->bind_param("i", $user['id']);
        $stmt_2fa->execute();
        $result_2fa = $stmt_2fa->get_result();

        $_SESSION['session_id'] = $session_id;
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['auth_provider'] = $user['auth_provider'];

        if ($result_2fa->num_rows > 0 && $result_2fa->fetch_assoc()['is_enabled'] == 1) {
            $stmt = $conn->prepare("INSERT INTO active_sessions (user_id, session_id, device_info, ip_address, user_agent, login_time, last_activity, is_current) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), FALSE)");
            $stmt->bind_param("issss", $user['id'], $session_id, $user_agent, $ip_address, $user_agent);
            $stmt->execute();


            $_SESSION['2fa_pending'] = true;
            $_SESSION['pre_2fa_user_id'] = $user['id'];
            $_SESSION['pre_2fa_user_data'] = [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'email' => $email,
                'auth_provider' => $user['auth_provider'],
                'remember_me' => false,
                'status' => 'verified'
            ];

            header("Location: ../2fa_verify.php");
            exit();

        }

        $stmt = $conn->prepare("INSERT INTO active_sessions (user_id, session_id, device_info, ip_address, user_agent, login_time, last_activity, is_current) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), TRUE)");
        $stmt->bind_param("issss", $user['id'], $session_id, $user_agent, $ip_address, $user_agent);
        $stmt->execute();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['notification_tracker'] = ['viewed_at' => null, 'count_at_view' => 0, 'current_count' => 0];

        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: ../$redirect");
        } else {
            header("Location: ../account/home.php");
        }
        exit();

    } else {

        $_SESSION['google-login-error'] = "Account not found. Please complete the sign-up process first.";

        $_SESSION['google_signup_info'] = [
            'email' => $email,
            'name' => $name,
            'google_id' => $google_id
        ];

        header("Location: ../signup.php");
        exit();

    }
} catch (Exception $e) {
    $_SESSION['google-login-error'] = "An unexpected error occurred. Please try again.";
    header("Location: ../login.php");
    exit();
}