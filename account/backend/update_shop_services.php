<?php
session_start();
include 'db_connection.php';


header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$shop_id = $_POST['shop_id'] ?? 0;
$service_id = $_POST['service_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (empty($shop_id) || empty($service_id) || empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit();
}

$stmt = $conn->prepare("SELECT user_id FROM shop_applications WHERE id = ?");
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_assoc();

if (!$shop || $shop['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Permission denied.']);
    exit();
}

$conn->begin_transaction();

try {
    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO shop_services (application_id, service_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $shop_id, $service_id);
    } elseif ($action === 'remove') {
        $stmt = $conn->prepare("DELETE FROM shop_services WHERE application_id = ? AND service_id = ?");
        $stmt->bind_param("ii", $shop_id, $service_id);
    } else {
        throw new Exception('Invalid action.');
    }

    if (!$stmt->execute()) {
        throw new Exception('Database operation failed.');
    }
    
    $conn->commit();

    $new_services_list = [];
    $result = $conn->query("SELECT service_id FROM shop_services WHERE application_id = $shop_id");
    while($row = $result->fetch_assoc()) {
        $new_services_list[] = $row['service_id'];
    }

    echo json_encode(['success' => true, 'services' => array_map('strval', $new_services_list)]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>