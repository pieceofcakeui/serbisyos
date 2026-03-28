<?php
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a review.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$shop_id = isset($_POST['shop_id']) ? trim($_POST['shop_id']) : null;
$shop_name = isset($_POST['shop_name']) ? trim($_POST['shop_name']) : '';
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$reviewer_name = isset($_POST['reviewer_name']) ? trim($_POST['reviewer_name']) : '';

if (empty($reviewer_name)) {
    $user_query = "SELECT fullname FROM users WHERE id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    
    if ($user_row = $user_result->fetch_assoc()) {
        $reviewer_name = !empty($user_row['fullname']) ? $user_row['fullname'] : 'User #' . $user_id;
    } else {
        $reviewer_name = 'User #' . $user_id;
    }
    $stmt->close();
}

if (!$shop_id) {
    echo json_encode(['success' => false, 'message' => 'Error: Shop ID is missing.']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => "Error: Please select a valid rating (1-5 stars). [Received: $rating]"]);
    exit;
}

if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Error: Please write a review comment.']);
    exit;
}

$shop_id = (int)$shop_id;

$sql = "INSERT INTO shop_ratings (user_id, shop_id, rating, comment, reviewer_name, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiss", $user_id, $shop_id, $rating, $comment, $reviewer_name);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully!', 'shop_id' => $shop_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
}
?>