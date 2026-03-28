<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_user_id = $_SESSION['user_id'];

if ($current_user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $current_user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shopData = $shopResult->fetch_assoc();

    if ($shopData) {
        $shop_id_for_emergency = $shopData['id'];
        $emergencyQuery = $conn->prepare("
            SELECT er.id, u.fullname, er.issue_description, er.created_at 
            FROM emergency_requests er
            JOIN users u ON er.user_id = u.id
            WHERE er.shop_id = ? AND er.seen_emergency_request = 0
            ORDER BY er.created_at DESC LIMIT 1
        ");
        $emergencyQuery->bind_param("i", $shop_id_for_emergency);
        $emergencyQuery->execute();
        $emergencyResult = $emergencyQuery->get_result();
        $emergency = $emergencyResult->fetch_assoc();

        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
            $emergency_request = $emergency;
        }
    }
}

$shop_slug = isset($_GET['name']) ? urldecode(trim($_GET['name'])) : '';

if (empty($shop_slug)) {
    header('Location: home.php');
    exit;
}

$average_rating = "0.0";
$total_reviews = 0;
$reviews_result = null;
$rating_distribution = [];
$shop = null;

$shop_sql = "SELECT sa.*, u.fullname as owner_name 
            FROM shop_applications sa 
            JOIN users u ON sa.user_id = u.id 
            WHERE sa.status = 'Approved' AND 
                    sa.shop_slug = ? AND
                    sa.user_id = ? 
            LIMIT 1";

$stmt = $conn->prepare($shop_sql);
$stmt->bind_param("si", $shop_slug, $current_user_id);
$stmt->execute();
$shop_result = $stmt->get_result();

if ($shop_result->num_rows === 0) {
    echo "<script>alert('Shop not found, not approved, or you are not the owner.'); window.location.href = './home.php';</script>";
    exit;
}

$shop = $shop_result->fetch_assoc();
$shop_id = $shop['id'];
$shopname = $shop['shop_name'];

$reviews_sql = "SELECT 
                    sr.id, sr.rating, sr.comment, sr.created_at,
                    u.fullname, u.profile_picture,
                    rr.response as owner_response, rr.created_at as response_date
                FROM shop_ratings sr
                JOIN users u ON sr.user_id = u.id
                LEFT JOIN respond_reviews rr ON sr.id = rr.review_id
                WHERE sr.shop_id = ?
                ORDER BY sr.created_at DESC
                LIMIT 5";
$stmt = $conn->prepare($reviews_sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$reviews_result = $stmt->get_result();

$stats_sql = "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as avg_rating
                FROM shop_ratings
                WHERE shop_id = ?";
$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$stats_result = $stmt->get_result();

if ($stats_result->num_rows > 0) {
    $stats = $stats_result->fetch_assoc();
    $total_reviews = $stats['total_reviews'];
    $average_rating = number_format((float)$stats['avg_rating'], 1);
}

$rating_sql = "SELECT 
                    rating, COUNT(*) as count
                FROM shop_ratings 
                WHERE shop_id = ?
                GROUP BY rating 
                ORDER BY rating DESC";
$stmt = $conn->prepare($rating_sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$rating_result = $stmt->get_result();

while ($row = $rating_result->fetch_assoc()) {
    $rating_distribution[$row['rating']] = $row['count'];
}

$stmt->close();
$conn->close();
?>