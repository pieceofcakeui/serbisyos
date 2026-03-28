<?php
require_once 'db_connection.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$current_user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $review_id = filter_var($data['review_id'], FILTER_SANITIZE_NUMBER_INT);
    $review_owner_id = filter_var($data['review_owner_id'], FILTER_SANITIZE_NUMBER_INT);
    $action = filter_var($data['action'], FILTER_SANITIZE_STRING);

    if (empty($review_id) || empty($review_owner_id) || !in_array($action, ['like', 'unlike'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    try {
        $conn->begin_transaction();

        if ($action === 'like') {
            $stmt = $conn->prepare("SELECT id FROM review_likes WHERE review_id = ? AND liked_by_user_id = ?");
            $stmt->bind_param("ii", $review_id, $current_user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Already liked']);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO review_likes (review_id, liked_by_user_id, review_owner_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $review_id, $current_user_id, $review_owner_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("DELETE FROM review_likes WHERE review_id = ? AND liked_by_user_id = ?");
            $stmt->bind_param("ii", $review_id, $current_user_id);
            $stmt->execute();
        }

        $stmt = $conn->prepare("SELECT COUNT(*) as like_count FROM review_likes WHERE review_id = ?");
        $stmt->bind_param("i", $review_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $like_count = $result->fetch_assoc()['like_count'];

        $stmt = $conn->prepare("SELECT COUNT(*) as total_user_likes FROM review_likes WHERE review_owner_id = ?");
        $stmt->bind_param("i", $review_owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_user_likes = $result->fetch_assoc()['total_user_likes'];

        $conn->commit();

        echo json_encode([
            'success' => true,
            'action' => $action,
            'review_like_count' => $like_count,
            'total_user_likes' => $total_user_likes
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}