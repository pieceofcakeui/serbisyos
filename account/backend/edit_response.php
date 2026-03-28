<?php
include 'db_connection.php';
session_start();

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
    $check_sql = "SELECT rr.id 
                  FROM respond_reviews rr
                  WHERE rr.review_id = ? AND rr.shop_owner_id = ?";

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
        echo json_encode(['success' => false, 'error' => 'Response not found or not authorized']);
        exit;
    }

    $update_sql = "UPDATE respond_reviews 
                   SET response = ?, created_at = NOW()
                   WHERE review_id = ? AND shop_owner_id = ?";

    $stmt = $conn->prepare($update_sql);
    if (!$stmt) {
        throw new Exception('Database error');
    }

    $stmt->bind_param("sii", $response_text, $review_id, $_SESSION['user_id']);

    if (!$stmt->execute()) {
        throw new Exception('Database error');
    }

    if ($stmt->affected_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'No changes made']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Response updated successfully'
    ]);

} catch (Exception $e) {
    error_log("Error in edit_response.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>