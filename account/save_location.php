<?php
session_start();
include 'backend/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['latitude']) || !isset($data['longitude'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Latitude and longitude are required']);
    exit;
}

$latitude = floatval($data['latitude']);
$longitude = floatval($data['longitude']);
$user_id = $_SESSION['user_id'];

if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid coordinates']);
    exit;
}

$mapbox_token = 'pk.eyJ1IjoibGluZ3hydWFuIiwiYSI6ImNtOXpoYWI1ajF0eTYyaXBzMHBnYWJuNHEifQ.BY0MZAOZCRv96vQuy1cspQ';
$geocode_url = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$longitude},{$latitude}.json?access_token={$mapbox_token}&types=address";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'method' => 'GET'
    ]
]);

$response = @file_get_contents($geocode_url, false, $context);

if ($response === false) {
    error_log("Mapbox geocoding failed for coordinates: {$latitude}, {$longitude}");

    $stmt = $conn->prepare("UPDATE users SET latitude = ?, longitude = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ddi", $latitude, $longitude, $user_id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Location coordinates saved successfully',
            'address_resolved' => false
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update location']);
    }

    $stmt->close();
    exit;
}

$geocode_data = json_decode($response, true);

$barangay = null;
$town_city = null;
$province = null;

if (isset($geocode_data['features']) && !empty($geocode_data['features'])) {
    $feature = $geocode_data['features'][0];

    if (isset($feature['context'])) {
        foreach ($feature['context'] as $context_item) {
            $id = $context_item['id'];
            $text = $context_item['text'];

            if (strpos($id, 'locality') !== false || strpos($id, 'neighborhood') !== false) {
                $barangay = $text;
            } elseif (strpos($id, 'place') !== false) {
                $town_city = $text;
            } elseif (strpos($id, 'region') !== false) {
                $province = $text;
            }
        }
    }

    if (empty($barangay) && isset($feature['place_name'])) {
        $place_parts = explode(',', $feature['place_name']);
        if (count($place_parts) > 0) {
            $barangay = trim($place_parts[0]);
        }
    }

    if (empty($town_city) && isset($feature['place_name'])) {
        $place_parts = explode(',', $feature['place_name']);
        if (count($place_parts) > 1) {
            $town_city = trim($place_parts[1]);
        }
    }
}

$barangay = $barangay ? cleanAddressComponent($barangay) : null;
$town_city = $town_city ? cleanAddressComponent($town_city) : null;
$province = $province ? cleanAddressComponent($province) : null;

$stmt = $conn->prepare("UPDATE users SET barangay = ?, town = ?, province = ?, latitude = ?, longitude = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("sssddi", $barangay, $town_city, $province, $latitude, $longitude, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Location saved successfully',
        'address_resolved' => true,
        'address' => [
            'barangay' => $barangay,
            'town_city' => $town_city,
            'province' => $province,
            'full_address' => trim(implode(', ', array_filter([$barangay, $town_city, $province])))
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update location in database']);
}

$stmt->close();
$conn->close();

function cleanAddressComponent($component)
{
    if (empty($component))
        return null;

    $prefixes = ['Barangay ', 'Brgy. ', 'Brgy ', 'City of ', 'Province of '];
    foreach ($prefixes as $prefix) {
        if (stripos($component, $prefix) === 0) {
            $component = substr($component, strlen($prefix));
        }
    }

    $suffixes = [' City', ' Municipality', ' Province'];
    foreach ($suffixes as $suffix) {
        if (stripos($component, $suffix) === (strlen($component) - strlen($suffix))) {
            $component = substr($component, 0, -strlen($suffix));
        }
    }

    return trim($component);
}
?>