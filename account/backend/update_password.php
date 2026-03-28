<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['change-pass-error'] = "You must be logged in to update your password.";
    header("Location: /login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$currentPassword = $_POST['currentPassword'];
$newPassword = $_POST['newPassword'];
$confirmPassword = $_POST['confirmPassword'];

if ($newPassword !== $confirmPassword) {
    $_SESSION['change-pass-error'] = "New passwords do not match!";
    header("Location: ../settings-and-privacy.php");
    exit();
}

$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!password_verify($currentPassword, $user['password'])) {
    $_SESSION['change-pass-error'] = "Incorrect current password!";
    header("Location: ../settings-and-privacy.php");
    exit();
}

$newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $newHashedPassword, $user_id);

if ($stmt->execute()) {
    $_SESSION['change-pass-success'] = "Password updated successfully!";
} else {
    $_SESSION['error'] = "Failed to update password. Please try again.";
}

header("Location: ../settings-and-privacy.php");
exit();
?>
