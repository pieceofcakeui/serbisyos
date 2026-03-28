<?php
include 'db_connection.php';
include 'encrypt_loc.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$maxDistance = isset($_GET['maxDistance']) ? floatval($_GET['maxDistance']) : 10;

try {
    $stmt = $conn->prepare("SELECT latitude, longitude, full_address FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('User not found');
    }
    
    $user = $result->fetch_assoc();
    
    if (empty($user['latitude']) || empty($user['longitude'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No location data found. Please enable location services.'
        ]);
        exit;
    }
    
    $userLat = decryptData($user['latitude']);
    $userLng = decryptData($user['longitude']);
    $userAddress = !empty($user['full_address']) ? decryptData($user['full_address']) : 'Address not available';

    $shopStmt = $conn->prepare("
        SELECT sa.*, 
               COALESCE(AVG(sr.rating), 0) as average_rating,
               COUNT(sr.id) as rating_count
        FROM shop_applications sa 
        LEFT JOIN shop_ratings sr ON sa.id = sr.shop_id
        WHERE sa.status = 'Approved' 
        GROUP BY sa.id
    ");
    
    $shopStmt->execute();
    $shopResult = $shopStmt->get_result();
    
    $nearbyShops = [];

    while ($shop = $shopResult->fetch_assoc()) {
        if (!empty($shop['latitude']) && !empty($shop['longitude'])) {
            $shopLat = decryptData($shop['latitude']);
            $shopLng = decryptData($shop['longitude']);
            
            $distance = calculateDistance($userLat, $userLng, $shopLat, $shopLng);
            
            if ($distance <= $maxDistance) {
                $shop['distance'] = round($distance, 2);
                $nearbyShops[] = $shop;
            }
        }
    }
    
    usort($nearbyShops, function($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });
    
    echo json_encode([
        'success' => true,
        'user_location' => [
            'latitude' => $userLat,
            'longitude' => $userLng,
            'address' => $userAddress
        ],
        'shops' => $nearbyShops,
        'count' => count($nearbyShops)
    ]);
    
} catch (Exception $e) {
    error_log("Nearby shops error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371;
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) + 
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earthRadius * $c;
}

if (isset($stmt)) $stmt->close();
if (isset($shopStmt)) $shopStmt->close();
$conn->close();
?>