<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once 'db_connection.php';

$user_id = $_SESSION['user_id'];

if ($user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shop = $shopResult->fetch_assoc();

    if ($shop) {
        $shop_id = $shop['id'];

        $emergencyQuery = $conn->prepare("
            SELECT er.id, u.fullname, er.issue_description, er.created_at 
            FROM emergency_requests er
            JOIN users u ON er.user_id = u.id
            WHERE er.shop_id = ? AND er.seen_emergency_request = 0
            ORDER BY er.created_at DESC LIMIT 1
        ");
        $emergencyQuery->bind_param("i", $shop_id);
        $emergencyQuery->execute();
        $emergencyResult = $emergencyQuery->get_result();
        $emergency = $emergencyResult->fetch_assoc();

        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
        }
    }
}

$stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $_SESSION['email'] = $user['email'];
    $_SESSION['fullname'] = $user['fullname'];
}

$qr_url = $_GET['qr'];
$secret = $_GET['secret'];
$otpauth_url = $_GET['otpauth'];
$error = isset($_GET['error']);

$qrCodeImage = '';
$qrCodeError = false;

if (!empty($qr_url)) {
    $qrCodeImage = $qr_url;
} elseif (!empty($otpauth_url)) {
    $qrCodeImage = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauth_url);
} elseif (!empty($secret)) {
    $app_name = urlencode("Serbisyos");
    $user_email = $_SESSION['email'] ?? '';
    $full_name = $_SESSION['fullname'] ?? '';
    $account_name = urlencode($full_name . ' (' . $user_email . ')');
    $issuer = $app_name;
    
    $otpauth_url = "otpauth://totp/{$issuer}:{$account_name}?secret={$secret}&issuer={$issuer}";
    $qrCodeImage = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauth_url);
} else {
    $qrCodeError = true;
}

$alternativeQRServices = [
    'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=',
    'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl='
];
?>