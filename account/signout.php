<?php
session_start();
require_once 'backend/db_connection.php';

$preservedVars = [
    'initial_pending' => $_SESSION['initial_pending'] ?? null,
    'initial_emergency' => $_SESSION['initial_emergency'] ?? null,
    'login_counts_set' => $_SESSION['login_counts_set'] ?? null
];

if (isset($_SESSION['user_id'])) {
    $activity_type = 'LOGOUT';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $activity_stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, device_info, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $activity_stmt->bind_param("issss", $_SESSION['user_id'], $activity_type, $user_agent, $ip_address, $user_agent);
    $activity_stmt->execute();

    if (isset($_SESSION['session_id'])) {
        $update_stmt = $conn->prepare("UPDATE active_sessions 
                                     SET last_activity = NOW(), is_current = FALSE, logout_time = NOW() 
                                     WHERE session_id = ?");
        $update_stmt->bind_param("s", $_SESSION['session_id']);
        $update_stmt->execute();
    }
}

$_SESSION = [];

foreach ($preservedVars as $key => $value) {
    if ($value !== null) {
        $_SESSION[$key] = $value;
    }
}

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

session_destroy();

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, "/", "", true, true);
}

header("Location: ../home");
exit();
?>