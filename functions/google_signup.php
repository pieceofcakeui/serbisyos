<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'db_connection.php';
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$clientID = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
$redirectUri = $_ENV['GOOGLE_REDIRECT_URI_SIGNUP'];

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

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    $_SESSION['google-signup-error'] = "Google authentication failed. Please try again.";
    header("Location: ../signup.php");
    exit();
}

$client->setAccessToken($token['access_token']);
$google_oauth = new Google_Service_Oauth2($client);
$google_account_info = $google_oauth->userinfo->get();

$email = $google_account_info->email;
$name = $google_account_info->name;

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['google-signup-error'] = "You already signed up. Please log in.";
    header("Location: ../login.php");
    exit();
}

$auth_provider = 'google';
$profile_type = 'user';
$status = 'verified';
$account_state = 'Active';
$dummy_password = password_hash('GoogleAuth', PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (fullname, email, password, status, profile_type, auth_provider, account_state) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $name, $email, $dummy_password, $status, $profile_type, $auth_provider, $account_state);

if ($stmt->execute()) {
    $user_id = $conn->insert_id;

    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $session_id = bin2hex(random_bytes(32));

    $activity_stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, device_info, ip_address, user_agent) VALUES (?, 'GOOGLE SIGNUP', ?, ?, ?)");
    $activity_stmt->bind_param("isss", $user_id, $user_agent, $ip_address, $user_agent);
    $activity_stmt->execute();

    $update_current = $conn->prepare("UPDATE active_sessions SET is_current = FALSE WHERE user_id = ?");
    $update_current->bind_param("i", $user_id);
    $update_current->execute();

    $session_stmt = $conn->prepare("INSERT INTO active_sessions (user_id, session_id, device_info, ip_address, user_agent, login_time, last_activity, is_current) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), TRUE)");
    $session_stmt->bind_param("issss", $user_id, $session_id, $user_agent, $ip_address, $user_agent);
    $session_stmt->execute();

    $_SESSION['session_id'] = $session_id;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['fullname'] = $name;
    $_SESSION['auth_provider'] = 'google';

    $_SESSION['notification_tracker'] = [
        'viewed_at' => null,
        'count_at_view' => 0,
        'current_count' => 0
    ];


    if (isset($_SESSION['redirect_after_signup'])) {
        $redirect = $_SESSION['redirect_after_signup'];
        unset($_SESSION['redirect_after_signup']);
        header("Location: ../$redirect");
    } else {
        $_SESSION['google-signup-error'] = "Google signup failed. Please try again.";
        header("Location: ../account/home.php");
    }
    exit();
}
