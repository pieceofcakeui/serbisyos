<?php
include 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_GET['shop_id'])) {
    echo json_encode(['error' => 'Shop ID is required']);
    exit;
}

$shop_id = (int)$_GET['shop_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 5;
$offset = ($page - 1) * $per_page;

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

$shop_owner_sql = "SELECT user_id FROM shop_applications WHERE id = ?";
$stmt = $conn->prepare($shop_owner_sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$shop_owner_result = $stmt->get_result();
$shop_owner = $shop_owner_result->fetch_assoc();
$shop_owner_id = $shop_owner['user_id'] ?? 0;

$sql = "SELECT 
            sr.id,
            sr.rating,
            sr.comment,
            sr.created_at,
            u.fullname,
            u.profile_picture,
            rr.response as owner_response,
            rr.created_at as response_date,
            shop_owner.fullname as owner_name,
            sr.user_id,
            rr.shop_owner_id
        FROM shop_ratings sr
        JOIN users u ON sr.user_id = u.id
        LEFT JOIN respond_reviews rr ON sr.id = rr.review_id
        LEFT JOIN users shop_owner ON rr.shop_owner_id = shop_owner.id
        WHERE sr.shop_id = ?";

switch ($sort) {
    case 'highest':
        $sql .= " ORDER BY sr.rating DESC";
        break;
    case 'lowest':
        $sql .= " ORDER BY sr.rating ASC";
        break;
    case 'oldest':
        $sql .= " ORDER BY sr.created_at ASC";
        break;
    default:
        $sql .= " ORDER BY sr.created_at DESC";
}

$sql .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $shop_id, $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode([
    'success' => true,
    'reviews' => $reviews,
    'page' => $page,
    'per_page' => $per_page,
    'shop_owner_id' => $shop_owner_id
]);
?>