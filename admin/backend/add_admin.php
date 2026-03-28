<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create-admin'])) {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['modal_error'] = "All fields are required.";
        header("Location: ../create-admin.php#addAdminModal");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['modal_error'] = "Invalid email format.";
        header("Location: ../create-admin.php#addAdminModal");
        exit();
    }

    if ($password !== $confirmPassword) {
        $_SESSION['modal_error'] = "Passwords do not match.";
        header("Location: ../create-admin.php#addAdminModal");
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['modal_error'] = "Email already exists.";
        header("Location: ../create-admin.php#addAdminModal");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO admins (full_name, email, password) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $fullName, $email, $hashedPassword);

    if ($insert->execute()) {
        $_SESSION['modal_success'] = "Admin created successfully.";
    } else {
        $_SESSION['modal_error'] = "Failed to create admin.";
    }

    header("Location: ../create-admin.php#addAdminModal");
    exit();
} else {
    header("Location: ../create-admin.php");
    exit();
}
