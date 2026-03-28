<?php
include 'db_connection.php';

if (!isset($_GET['shop']) || empty($_GET['shop'])) {
    header("Location: ../emergency-provider.php?error=invalid_shop");
    exit();
}

$shop_slug = $_GET['shop'];
$shop = [];

$stmt = $conn->prepare("SELECT sa.*, u.fullname as shop_owner, sa.phone
    FROM shop_applications sa
    JOIN users u ON sa.user_id = u.id
    WHERE sa.shop_slug = ? AND sa.status = 'Approved'");
if ($stmt) {
    $stmt->bind_param("s", $shop_slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $shop = $result->fetch_assoc();
    $stmt->close();
}

if (empty($shop)) {
    error_log("Invalid shop access attempt - Slug: $shop_slug");
    header("Location: ../emergency-provider.php?error=invalid_shop");
    exit();
}

$emergency_hours = [];
$stmt = $conn->prepare("SELECT emergency_hours FROM shop_emergency_config WHERE shop_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $shop['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $emergency_hours = json_decode($row['emergency_hours'], true);
    }
    $stmt->close();
}

$user_id = $_SESSION['user_id'];
$current_request = null;
$can_request = true;

$stmt = $conn->prepare("SELECT status FROM emergency_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_request = $result->fetch_assoc();
    $stmt->close();
}

if ($current_request) {
    if (in_array($current_request['status'], ['pending', 'accepted'])) {
        $can_request = false;
    }
}
?>