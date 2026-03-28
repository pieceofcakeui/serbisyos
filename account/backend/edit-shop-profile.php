<?php
include 'db_connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

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

$stmt = $conn->prepare("SELECT id, fullname, email, profile_picture, contact_number, postal_code, profile_type, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['profile_type'] == 'user') {
    header("Location: profile.php");
    exit();
}

$shop_stmt = $conn->prepare("SELECT * FROM shop_applications WHERE user_id = ? AND status = 'Approved'");
$shop_stmt->bind_param("i", $user_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();
$shop = $shop_result->fetch_assoc();

$shop_id = $shop['id'] ?? 0;
$shop_name = $shop['shop_name'] ?? '';
$shop_postal_code = $shop['postal_code'] ?? '';
$barangay = $shop['barangay'] ?? '';
$town_city = $shop['town_city'] ?? '';
$province = $shop['province'] ?? '';
$shop_location = $shop['shop_location'] ?? '';
$shop_hours = $shop['business_hours'] ?? '';
$shop_description = $shop['description'] ?? '';
$shop_email = $shop['email'] ?? $user['email'];
$shop_phone = $shop['phone'] ?? $user['contact_number'];
$facebook = $shop['facebook'] ?? '';
$instagram = $shop['instagram'] ?? '';
$website = $shop['website'] ?? '';
$years_operation = $shop['years_operation'] ?? '';

$shop_logo = !empty($shop['shop_logo']) ? $shop['shop_logo'] : 'uploads/shop_logo/logo.jpg';
if (!empty($shop_logo) && !str_starts_with($shop_logo, 'uploads/shop_logo/')) {
    $shop_logo = 'uploads/shop_logo/' . $shop_logo;
}
if (!file_exists($shop_logo)) {
    $shop_logo = 'uploads/shop_logo/logo.jpg';
}
?>