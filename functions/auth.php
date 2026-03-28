<?php
include 'base-path.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {

    require_once 'db_connection.php'; 
    
    if (isset($_SESSION['session_id']) && $conn) {
        try {
            $stmt = $conn->prepare("UPDATE active_sessions SET last_activity = NOW() WHERE session_id = ? AND user_id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $_SESSION['session_id'], $_SESSION['user_id']);
                $stmt->execute();
            }
        } catch (Exception $e) {
            error_log("Failed to update last_activity: " . $e->getMessage());
        }
    }
    
    return;
}

if (isset($_COOKIE['remember_token'])) {

    require_once 'db_connection.php'; 
    
    $token = $_COOKIE['remember_token'];

    $stmt = $conn->prepare("SELECT * FROM remember_tokens WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $token_data = $result->fetch_assoc();
        $user_id = $token_data['user_id'];

        $user_stmt = $conn->prepare("SELECT id, fullname, email, status, auth_provider, account_state FROM users WHERE id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows === 1) {
            $user = $user_result->fetch_assoc();

            if ($user['account_state'] === 'Active') {

                session_regenerate_id(true);

                $session_id = bin2hex(random_bytes(32));
                $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

                $update_old_sessions = $conn->prepare("UPDATE active_sessions SET is_current = FALSE WHERE user_id = ?");
                $update_old_sessions->bind_param("i", $user['id']);
                $update_old_sessions->execute();

                $is_2fa_verified = 1; 
                $is_current = 1;

                $session_stmt = $conn->prepare("INSERT INTO active_sessions (user_id, session_id, device_info, ip_address, user_agent, is_2fa_verified, is_current, login_time, last_activity) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $session_stmt->bind_param("issssii", $user['id'], $session_id, $user_agent, $ip_address, $user_agent, $is_2fa_verified, $is_current);
                $session_stmt->execute();

                $_SESSION['session_id'] = $session_id;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['auth_provider'] = $user['auth_provider'];

                $new_token = bin2hex(random_bytes(32));
                $new_expires = date('Y-m-d H:i:s', strtotime('+30 days'));

                $update_token_stmt = $conn->prepare("UPDATE remember_tokens SET token = ?, expires_at = ? WHERE id = ?");
                $update_token_stmt->bind_param("ssi", $new_token, $new_expires, $token_data['id']);
                $update_token_stmt->execute();

                setcookie('remember_token', $new_token, time() + (30 * 24 * 60 * 60), "/", "", false, true);

                return;
            }
        }
    }

    setcookie('remember_token', '', time() - 3600, "/", "", false, true);
    unset($_COOKIE['remember_token']);
}

$_SESSION['flash_message'] = [
    'type'  => 'info',
    'title' => 'Session Expired',
    'body'  => 'Your session has expired. Please log in again.'
];

header("Location: " . BASE_URL . "/login.php"); 
exit();
?>