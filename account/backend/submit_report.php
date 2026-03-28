<?php
session_start();
include 'db_connection.php';

date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['toastr'] = [
        'type' => 'error',
        'message' => 'You must be logged in to report a shop'
    ];
    echo json_encode(['success' => false]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['report_error'] = 'You must be logged in to report a shop';
    echo json_encode(['success' => false, 'message' => 'You must be logged in to report a shop']);
    exit;
}

if (!isset($_POST['shop_id']) || !isset($_POST['reason'])) {
    $_SESSION['report_error'] = 'Invalid input';
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$shop_id = intval($_POST['shop_id']);
$user_id = $_SESSION['user_id'];
$reason = htmlspecialchars(trim($_POST['reason']));
$description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;

$stmt = $conn->prepare("
    SELECT id, created_at 
    FROM reports 
    WHERE user_id = ? AND shop_id = ? 
    AND CONVERT_TZ(created_at, @@session.time_zone, '+08:00') >= DATE_SUB(CONVERT_TZ(NOW(), @@session.time_zone, '+08:00'), INTERVAL 24 HOUR)
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->bind_param("ii", $user_id, $shop_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $last_report = $result->fetch_assoc();
    $last_report_time = new DateTime($last_report['created_at']);
    $current_time = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $time_passed = $current_time->getTimestamp() - $last_report_time->getTimestamp();
    $time_left = 86400 - $time_passed;
    
    if ($time_left > 0) {
        $hours_left = floor($time_left / 3600);
        $minutes_left = floor(($time_left % 3600) / 60);
        $message = 'You have already reported this shop recently. Please wait ' . $hours_left . ' hours and ' . $minutes_left . ' minutes before submitting another report.';
        $_SESSION['report_error'] = $message;
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}

$stmt = $conn->prepare("INSERT INTO reports (user_id, shop_id, reason, description) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $user_id, $shop_id, $reason, $description);

if ($stmt->execute()) {
    $_SESSION['report_success'] = 'Report submitted successfully';
    echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
} else {
    $_SESSION['report_error'] = 'Error submitting report';
    echo json_encode(['success' => false, 'message' => 'Error submitting report']);
}

$stmt->close();
$conn->close();
?>