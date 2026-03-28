<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connection.php';

if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_email']) || !isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $user_id = $_SESSION['temp_user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['reset-pass-error'] = "Passwords do not match!";
        header("Location: ../reset_password.php");
        exit();
    } 

    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    if (!preg_match($pattern, $new_password)) {
         $_SESSION['reset-pass-error'] = "Password is not strong enough. Please follow the requirements.";
         header("Location: ../reset_password.php");
         exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_email']);
        unset($_SESSION['otp_verified']);

        $_SESSION['reset-pass-success'] = "Password reset successfully! Redirecting to login...";
        header("Location: ../reset_password.php?success=1");
        exit();
    } else {
        $_SESSION['reset-pass-error'] = "Something went wrong. Please try again!";
        header("Location: ../reset_password.php");
        exit();
    }
} else {
    header("Location: ../forgot-password.php");
    exit();
}
?>