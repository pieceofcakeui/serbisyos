<?php
session_start();
header('Content-Type: application/json');
require_once 'db_connection.php';
include 'encrypt_loc.php';

error_log("Received location save request: " . file_get_contents('php://input'));

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['barangay'], $input['town'], $input['province'], $input['country'], 
    $input['postal_code'], $input['latitude'], $input['longitude'], $input['full_address'])) {
    error_log("Missing required fields in location save request");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (!is_numeric($input['latitude']) || !is_numeric($input['longitude'])) {
    error_log("Invalid latitude/longitude values");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid coordinates']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    error_log("User not authenticated for location save");
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

$encryptedBarangay     = encryptData($input['barangay']);
$encryptedTown         = encryptData($input['town']);
$encryptedProvince     = encryptData($input['province']);
$encryptedFullAddress  = encryptData($input['full_address']);
$encryptedLatitude     = encryptData(strval($input['latitude']));
$encryptedLongitude    = encryptData(strval($input['longitude']));

try {
    $stmt = $conn->prepare("
        UPDATE users 
        SET 
            barangay = ?,
            town = ?,
            province = ?,
            country = ?,
            postal_code = ?,
            latitude = ?,
            longitude = ?,
            full_address = ?,
            location_updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssssssssi",
        $encryptedBarangay,
        $encryptedTown,
        $encryptedProvince,
        $input['country'],
        $input['postal_code'],
        $encryptedLatitude,
        $encryptedLongitude,
        $encryptedFullAddress,
        $userId
    );

    if ($stmt->execute()) {
        error_log("Location saved successfully for user $userId");
        echo json_encode(['success' => true, 'message' => 'Location saved successfully']);
    } else {
        error_log("Database error saving location: " . $stmt->error);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save location']);
    }
} catch (Exception $e) {
    error_log("Exception saving location: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>