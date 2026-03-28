<?php
include 'db_connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'emergencies' => []];

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

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
        ORDER BY er.created_at DESC
    ");
    $emergencyQuery->bind_param("i", $shop_id);
    $emergencyQuery->execute();
    $emergencyResult = $emergencyQuery->get_result();
    
    $emergencies = [];
    $emergencyIds = [];
    
    while ($emergency = $emergencyResult->fetch_assoc()) {
        $emergencies[] = $emergency;
        $emergencyIds[] = $emergency['id'];
    }
    
    if (!empty($emergencyIds)) {
        $placeholders = implode(',', array_fill(0, count($emergencyIds), '?'));
        $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id IN ($placeholders)");
        $updateQuery->bind_param(str_repeat('i', count($emergencyIds)), ...$emergencyIds);
        $updateQuery->execute();
        
        $response['success'] = true;
        $response['emergencies'] = $emergencies;
    }
}

echo json_encode($response);
?>