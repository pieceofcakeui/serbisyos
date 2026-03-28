<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
    exit;
}

require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (isset($_POST['current_password'])) {
    $admin_id = $_SESSION['id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'New password and confirmation password do not match.']);
        exit;
    }

    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit;
    }

    if (!preg_match('/[A-Z]/', $new_password)) {
        echo json_encode(['success' => false, 'message' => 'Password must contain at least one uppercase letter.']);
        exit;
    }

    if (!preg_match('/[a-z]/', $new_password)) {
        echo json_encode(['success' => false, 'message' => 'Password must contain at least one lowercase letter.']);
        exit;
    }

    if (!preg_match('/[0-9]/', $new_password)) {
        echo json_encode(['success' => false, 'message' => 'Password must contain at least one number.']);
        exit;
    }

    if (!preg_match('/[\W]/', $new_password)) {
        echo json_encode(['success' => false, 'message' => 'Password must contain at least one special character.']);
        exit;
    }

    $query = "SELECT password FROM admins WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    $user = $result->fetch_assoc();
    $current_db_password = $user['password'];

    if ($current_password !== $current_db_password) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit;
    }

    if ($new_password === $current_db_password) {
        echo json_encode(['success' => false, 'message' => 'New password cannot be the same as current password.']);
        exit;
    }

    $update_query = "UPDATE admins SET password = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_password, $admin_id);

    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password. Please try again.']);
    }

    $update_stmt->close();
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Password data not received.']);
}

$conn->close();
?>