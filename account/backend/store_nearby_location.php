<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

include 'db_connection.php';
include 'encrypt_loc.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;

if ($latitude === null || $longitude === null || !is_numeric($latitude) || !is_numeric($longitude)) {
    echo json_encode(['success' => false, 'message' => 'Invalid coordinates provided']);
    exit;
}

if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
    echo json_encode(['success' => false, 'message' => 'Invalid coordinate values']);
    exit;
}

try {
    $apiKey = $_ENV['SERVER_KEY_SEARCH'];

    if (empty($apiKey)) {
        throw new Exception('Google API key is not configured.');
    }

    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=$apiKey";
    
    $response = file_get_contents($url);
    if ($response === false) {
        throw new Exception('Failed to connect to geocoding service');
    }
    
    $data = json_decode($response, true);
    if (!$data || $data['status'] != 'OK') {
        throw new Exception('Geocoding failed: ' . ($data['error_message'] ?? 'Unknown error'));
    }

    $fullAddress = $data['results'][0]['formatted_address'];

    $encryptedLatitude     = encryptData((string)$latitude);
    $encryptedLongitude    = encryptData((string)$longitude);
    $encryptedFullAddress  = encryptData($fullAddress);

    $stmt = $conn->prepare("UPDATE users SET 
                                latitude = ?, 
                                longitude = ?, 
                                full_address = ?
                                WHERE id = ?");
    
    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param("sssi", 
        $encryptedLatitude, 
        $encryptedLongitude,
        $encryptedFullAddress,
        $userId
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Database update failed: ' . $stmt->error);
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Location storage error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

if (isset($stmt)) $stmt->close();
$conn->close();
?>