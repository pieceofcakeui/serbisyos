<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

if (isset($_POST['set_password'])) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $_SESSION['set-password-error'] = "Passwords do not match.";
        header("Location: ../set_password.php");
        exit();
    }

    if (strlen($new_password) < 6) {
        $_SESSION['set-password-error'] = "Password must be at least 6 characters.";
        header("Location: ../set_password.php");
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET password = ?, auth_provider = 'manual' WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        $_SESSION['auth_provider'] = 'manual';
        $_SESSION['set-password-success'] = "Password set successfully. You can now log in manually.";
    } else {
        $_SESSION['set-password-error'] = "Failed to set password.";
    }

    header("Location: ../set_password.php");
    exit();
}
?>
