<?php
session_start();
require '../backend/db_connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log("Rate experience request received");
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION data: " . print_r($_SESSION, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $shop_id = isset($_POST['shop_id']) ? intval($_POST['shop_id']) : 0;
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    $rating = 0;
    if (isset($_POST['rating']) && is_numeric($_POST['rating'])) {
        $rating = intval($_POST['rating']);
    }
    
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    error_log("Processed data - shop_id: $shop_id, user_id: $user_id, rating: $rating, comment length: " . strlen($comment));

    if ($shop_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid shop ID. Please try again.', 'show_modal' => true]);
        exit;
    }
    
    if ($user_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to submit a review.', 'show_modal' => true]);
        exit;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Please select a rating from 1 to 5 stars. (Received: ' . $rating . ')', 'show_modal' => true]);
        exit;
    }
    
    if (empty($comment)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a comment for your review.', 'show_modal' => true]);
        exit;
    }

    $user_sql = "SELECT fullname FROM users WHERE id = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User account not found. Please log in again.', 'show_modal' => true]);
        exit;
    }

    $user = $user_result->fetch_assoc();
    $reviewer_name = $user['fullname'];

    $insert_sql = "
        INSERT INTO shop_ratings (shop_id, user_id, reviewer_name, rating, comment, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iisss", $shop_id, $user_id, $reviewer_name, $rating, $comment);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Review submitted successfully.', 'show_modal' => true]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit review: ' . $stmt->error, 'show_modal' => true]);
    }

    exit;
}
?>
