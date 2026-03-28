<?php
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['shop_id'])) {
    $shop_id = intval($_POST['shop_id']);
    
    $checkQuery = "SELECT * FROM save_shops WHERE user_id = ? AND shop_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $user_id, $shop_id);
    $checkStmt->execute();
    $exists = $checkStmt->get_result()->num_rows > 0;
    
    if ($exists) {
        echo "Shop already saved.";
    } else {
        $insertQuery = "INSERT INTO save_shops (user_id, shop_id) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $user_id, $shop_id);
        echo $insertStmt->execute() ? "Shop saved successfully." : "Failed to save shop.";
        $insertStmt->close();
    }
    
    $checkStmt->close();
    $conn->close();
    exit;
}

$sql = "SELECT 
            sa.id, 
            sa.shop_name, 
            sa.shop_logo, 
            sa.town_city, 
            sa.province, 
            sa.country, 
            sa.shop_location,
            sa.shop_slug,
            COUNT(sr.id) AS rating_count, 
            COALESCE(AVG(sr.rating), 0) AS average_rating
        FROM save_shops ss 
        INNER JOIN shop_applications sa ON ss.shop_id = sa.id 
        LEFT JOIN shop_ratings sr ON sa.id = sr.shop_id
        WHERE ss.user_id = ?
        GROUP BY sa.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$savedShops = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>