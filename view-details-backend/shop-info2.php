<?php
include './functions/db_connection.php';
$user_id = $_SESSION['user_id'] ?? null;

$is_saved = false;
$save_count = 0;
if ($user_id) {
    $check_saved_sql = "SELECT * FROM save_shops WHERE user_id = ? AND shop_id = ?";
    $stmt = $conn->prepare($check_saved_sql);
    $stmt->bind_param("ii", $user_id, $shop['id']);
    $stmt->execute();
    $saved_result = $stmt->get_result();
    $is_saved = $saved_result->num_rows > 0;

    $count_sql = "SELECT COUNT(*) as save_count FROM save_shops WHERE shop_id = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $shop['id']);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $save_data = $count_result->fetch_assoc();
    $save_count = $save_data['save_count'] ?? 0;
}

$latitude = $shop['latitude'] ?? null;
$longitude = $shop['longitude'] ?? null;
$full_address = htmlspecialchars($shop['barangay'] . ', ' . $shop['town_city'] . ', ' . $shop['province']);
$encodedAddress = urlencode($full_address);
?>