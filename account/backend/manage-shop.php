<?php
include 'db_connection.php';

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

$stmt = $conn->prepare("SELECT id, fullname, email, profile_picture, contact_number, profile_type, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['profile_type'] == 'user') {
    header("Location: ../profile.php");
    exit();
}

$shop_stmt = $conn->prepare("SELECT * FROM shop_applications WHERE user_id = ? AND status = 'Approved'");
$shop_stmt->bind_param("i", $user_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();
$shop = $shop_result->fetch_assoc();

$shop_id = $shop['id'] ?? 0;
$shop_name = $shop['shop_name'] ?? 'No Shop Registered';
$shop_address = (!empty($shop)) ? $shop['barangay'] . ', ' . $shop['town_city'] . ', ' . $shop['province'] : 'No Address Available';
$shop_hours = $shop['business_hours'] ?? 'Not specified';
$services = !empty($shop['services_offered']) ? explode(",", $shop['services_offered']) : [];
$shop_description = $shop['description'] ?? 'No description available';
$shop_email = $shop['email'] ?? $user['email'];
$shop_phone = $shop['phone'] ?? $user['contact_number'];
$join_date = date('F Y', strtotime($user['created_at']));

$default_logo = 'uploads/shop_logo/logo.jpg';

if (!empty($shop['shop_logo'])) {
    if (strpos($shop['shop_logo'], '../') === 0) {
        $shop_logo = $shop['shop_logo'];
    } else {
        if (file_exists('uploads/shop_logo/' . $shop['shop_logo'])) {
            $shop_logo = 'uploads/shop_logo/' . $shop['shop_logo'];
        } elseif (file_exists('uploads/shop_logo/' . $shop['shop_logo'])) {
            $shop_logo = 'uploads/shop_logo/' . $shop['shop_logo'];
        } else {
            $shop_logo = $shop['shop_logo'];
        }
    }

    if (!file_exists($shop_logo)) {
        $shop_logo = $default_logo;
    }
} else {
    $shop_logo = $default_logo;
}

$total_services = count($services);

$average_rating = "0.0";
$total_reviews = 0;

if ($shop_id > 0) {
    $avg_sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews FROM shop_ratings WHERE shop_id = ?";
    $stmt = $conn->prepare($avg_sql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $avg_result = $stmt->get_result();

    if ($avg_result->num_rows > 0) {
        $rating_data = $avg_result->fetch_assoc();
        $average_rating = $rating_data['average_rating'] ? number_format($rating_data['average_rating'], 1) : "0.0";
        $total_reviews = $rating_data['total_reviews'] ? $rating_data['total_reviews'] : 0;
    }
}
?>