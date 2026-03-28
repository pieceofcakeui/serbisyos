<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once 'db_connection.php';

$user_id = $_SESSION['user_id'];
$is_verified = 0;
$profile_type = '';

$stmt = $conn->prepare("SELECT is_verified, profile_type FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header('Location: ../../login.php');
    exit();
}

if ($user['profile_type'] === 'owner') {
    $_SESSION['toast_message'] = 'This page is for customer accounts only.';
    $_SESSION['toast_type'] = 'warning';
    header('Location: home.php');
    exit();
}

if ($user['is_verified'] != 1) {
    $_SESSION['toast_message'] = 'You must be a fully verified user to access this page.';
    $_SESSION['toast_type'] = 'error';

    $script_name = basename($_SERVER['SCRIPT_NAME']);
    if ($script_name == 'book-now.php') {
        header('Location: booking-provider.php');
    } else {
        header('Location: home.php');
    }
    exit();
}
?>
