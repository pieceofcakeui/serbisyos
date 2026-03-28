<?php
include 'db_connection.php';
session_start();


header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT vehicle_type FROM shop_applications WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Shop not found']);
    exit;
}

$row = $result->fetch_assoc();
$vehicleTypes = !empty($row['vehicle_type']) ? array_map('trim', explode(',', $row['vehicle_type'])) : [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['success' => true, 'vehicleTypes' => $vehicleTypes]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $vehicleType = trim($_POST['vehicleType'] ?? '');
    $index = $_POST['index'] ?? null;

    switch ($action) {
        case 'add':
            if (empty($vehicleType)) {
                echo json_encode(['success' => false, 'message' => 'Vehicle type cannot be empty']);
                exit;
            }
            $vehicleTypes[] = $vehicleType;
            break;
            
        case 'edit':
            if (!is_numeric($index) || $index < 0 || $index >= count($vehicleTypes) || empty($vehicleType)) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters for editing']);
                exit;
            }
            $vehicleTypes[$index] = $vehicleType;
            break;
            
        case 'delete':
            if (!is_numeric($index) || $index < 0 || $index >= count($vehicleTypes)) {
                echo json_encode(['success' => false, 'message' => 'Invalid index for deletion']);
                exit;
            }
            array_splice($vehicleTypes, $index, 1);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action specified']);
            exit;
    }

    $vehicleTypes_str = implode(',', $vehicleTypes);
    $update_stmt = $conn->prepare("UPDATE shop_applications SET vehicle_type = ? WHERE user_id = ?");
    $update_stmt->bind_param("si", $vehicleTypes_str, $user_id);
    
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'vehicleTypes' => $vehicleTypes]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update vehicle types']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>