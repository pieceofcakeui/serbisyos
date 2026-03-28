<?php
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
    exit;
}

$review_id = isset($data['review_id']) ? (int) $data['review_id'] : 0;
$response_text = isset($data['response']) ? trim($data['response']) : '';

if ($review_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid review ID']);
    exit;
}

if (empty($response_text)) {
    echo json_encode(['success' => false, 'error' => 'Response cannot be empty']);
    exit;
}

try {
    $check_sql = "SELECT sr.id, sr.user_id 
                  FROM shop_ratings sr
                  JOIN shop_applications sa ON sr.shop_id = sa.id
                  WHERE sr.id = ? AND sa.user_id = ?";

    $stmt = $conn->prepare($check_sql);
    if (!$stmt) {
        throw new Exception('Database error');
    }

    $stmt->bind_param("ii", $review_id, $_SESSION['user_id']);
    if (!$stmt->execute()) {
        throw new Exception('Database error');
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Review not found or not authorized']);
        exit;
    }

    $review_data = $result->fetch_assoc();
    $reviewer_user_id = $review_data['user_id'];

    $check_response_sql = "SELECT id FROM respond_reviews WHERE review_id = ?";
    $stmt = $conn->prepare($check_response_sql);
    if (!$stmt) {
        throw new Exception('Database error');
    }

    $stmt->bind_param("i", $review_id);
    if (!$stmt->execute()) {
        throw new Exception('Database error');
    }

    $existing_response = $stmt->get_result();

    if ($existing_response->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'You have already responded to this review']);
        exit;
    }

    $insert_sql = "INSERT INTO respond_reviews (review_id, shop_owner_id, user_id, response, created_at) 
                   VALUES (?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        throw new Exception('Database error');
    }

    $stmt->bind_param("iiis", $review_id, $_SESSION['user_id'], $reviewer_user_id, $response_text);

    if (!$stmt->execute()) {
        throw new Exception('Database error');
    }

    $response_id = $conn->insert_id;

    $select_sql = "SELECT rr.*, u.fullname as shop_owner_name
                   FROM respond_reviews rr
                   JOIN users u ON rr.shop_owner_id = u.id
                   WHERE rr.id = ?";

    $stmt = $conn->prepare($select_sql);
    if (!$stmt) {
        throw new Exception('Database error');
    }

    $stmt->bind_param("i", $response_id);
    if (!$stmt->execute()) {
        throw new Exception('Database error');
    }

    $response_data = $stmt->get_result()->fetch_assoc();

    echo json_encode([
        'success' => true,
        'message' => 'Response submitted successfully',
        'response' => [
            'response' => $response_data['response'],
            'created_at' => $response_data['created_at'],
            'shop_owner_name' => $response_data['shop_owner_name']
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in respond_to_review.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>