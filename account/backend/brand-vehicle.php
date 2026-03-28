<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT brands_serviced FROM shop_applications WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $brands = !empty($row['brands_serviced']) ? explode(',', $row['brands_serviced']) : [];
        echo json_encode(['success' => true, 'brands' => $brands]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Shop not found']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $brand = trim($_POST['brand'] ?? '');
    $index = $_POST['index'] ?? null;

    $stmt = $conn->prepare("SELECT brands_serviced FROM shop_applications WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Shop not found']);
        exit;
    }
    
    $row = $result->fetch_assoc();
    $brands = !empty($row['brands_serviced']) ? explode(',', $row['brands_serviced']) : [];

    switch ($action) {
        case 'add':
            if (empty($brand)) {
                echo json_encode(['success' => false, 'message' => 'Brand cannot be empty']);
                exit;
            }
            $brands[] = $brand;
            break;
            
        case 'edit':
            if (!is_numeric($index) || $index < 0 || $index >= count($brands) || empty($brand)) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
                exit;
            }
            $brands[$index] = $brand;
            break;
            
        case 'delete':
            if (!is_numeric($index) || $index < 0 || $index >= count($brands)) {
                echo json_encode(['success' => false, 'message' => 'Invalid index']);
                exit;
            }
            array_splice($brands, $index, 1);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }

    $brands_str = implode(',', $brands);
    $update_stmt = $conn->prepare("UPDATE shop_applications SET brands_serviced = ? WHERE user_id = ?");
    $update_stmt->bind_param("si", $brands_str, $user_id);
    
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'brands' => $brands]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update brands']);
    }
    exit;
}

header("HTTP/1.1 400 Bad Request");
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>