<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

include 'db_connection.php';
include 'encrypt_loc.php';

define('GOOGLE_API_KEY', $_ENV['GOOGLE_MAPS_API_KEY']);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo "Database connection failed.";
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "User not logged in. Cannot store location.";
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['latitude']) || !isset($data['longitude'])) {
    http_response_code(400);
    echo "Invalid coordinates provided.";
    exit();
}

$latitude = (double) $data['latitude'];
$longitude = (double) $data['longitude'];
$encrypted_latitude = encryptData((string)$latitude);
$encrypted_longitude = encryptData((string)$longitude);

$street = '';
$barangay = '';
$town = '';
$province = '';
$country = '';
$postal_code = '';
$full_address = '';

if (isset($data['address']) && !empty($data['address'])) {
    $address = trim($data['address']);
    $parsed_address = parseAddress($address);
    
    $street = $parsed_address['street'];
    $barangay = $parsed_address['barangay'];
    $town = $parsed_address['town'];
    $province = $parsed_address['province'];
    $country = $parsed_address['country'];
    $postal_code = $parsed_address['postal_code'];
} else {
    $geocoded_data = reverseGeocode($latitude, $longitude);
    
    if ($geocoded_data) {
        $street = $geocoded_data['street'];
        $barangay = $geocoded_data['barangay'];
        $town = $geocoded_data['town'];
        $province = $geocoded_data['province'];
        $country = $geocoded_data['country'];
        $postal_code = $geocoded_data['postal_code'];
        $address = $geocoded_data['formatted_address'];
    }
}

$full_address_components = [];
if (!empty($street)) $full_address_components[] = $street;
if (!empty($barangay)) $full_address_components[] = $barangay;
if (!empty($town)) $full_address_components[] = $town;
if (!empty($province)) $full_address_components[] = $province;
if (!empty($country)) $full_address_components[] = $country;

$full_address = implode(', ', $full_address_components);

if (!empty($postal_code)) {
    $full_address .= ' ' . $postal_code;
}

if (empty($full_address) && isset($address)) {
    $full_address = $address;
}

$encrypted_street = encryptData($street);
$encrypted_barangay = encryptData($barangay);
$encrypted_town = encryptData($town);
$encrypted_province = encryptData($province);
$encrypted_full_address = encryptData($full_address);

$sql = "UPDATE users SET 
        street = ?,
        barangay = ?, 
        town = ?, 
        province = ?, 
        country = ?,
        postal_code = ?,
        latitude = ?, 
        longitude = ?,
        full_address = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    error_log("SQL prepare failed: " . $conn->error);
    http_response_code(500);
    echo "Failed to prepare database statement.";
    exit();
}

$stmt->bind_param("sssssssssi", 
    $encrypted_street,
    $encrypted_barangay,
    $encrypted_town,
    $encrypted_province,
    $country,
    $postal_code,
    $encrypted_latitude,
    $encrypted_longitude,
    $encrypted_full_address,
    $user_id
);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'data' => [
            'street' => $street,
            'barangay' => $barangay,
            'town' => $town,
            'province' => $province,
            'country' => $country,
            'postal_code' => $postal_code,
            'coordinates' => [$latitude, $longitude],
            'full_address' => $full_address
        ]
    ]);
} else {
    error_log("Error updating location: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error updating location']);
}

$stmt->close();
$conn->close();

function parseAddress($address) {
    $result = [
        'street' => '',
        'barangay' => '',
        'town' => '',
        'province' => '',
        'country' => '',
        'postal_code' => ''
    ];

    $address = trim($address);

    if (preg_match('/\b(\d{4})\b/', $address, $matches)) {
        $result['postal_code'] = $matches[1];
        $address = preg_replace('/\b' . $matches[1] . '\b/', '', $address);
    }

    $parts = array_map('trim', explode(',', $address));
    $parts = array_filter($parts, function($part) {
        return !empty($part);
    });

    $parts = array_reverse($parts);
    
    if (count($parts) >= 1) {
        $result['country'] = $parts[0];
    }
    if (count($parts) >= 2) {
        $result['province'] = $parts[1];
    }
    if (count($parts) >= 3) {
        $result['town'] = $parts[2];
    }
    if (count($parts) >= 4) {
        $result['barangay'] = $parts[3];
    }

    if (count($parts) >= 5) {
        $street_parts = array_slice($parts, 4);
        $result['street'] = implode(', ', array_reverse($street_parts));
    }

    if (!empty($result['barangay'])) {
        $result['barangay'] = preg_replace('/\b\d{4}\b/', '', $result['barangay']);
        $result['barangay'] = trim($result['barangay']);
    }
    
    return $result;
}

function reverseGeocode($latitude, $longitude) {
    $api_key = GOOGLE_API_KEY;
    
    if (empty($api_key) || $api_key === $_ENV['GOOGLE_MAPS_API_KEY_PLACEHOLDER']) {
        error_log("Google API key not configured or is set to placeholder");
        return false;
    }
    
    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$api_key}&result_type=street_address|route|neighborhood|sublocality|locality|administrative_area_level_2|administrative_area_level_1|postal_code";
    
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: LocationApp/1.0\r\n",
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        error_log("Failed to get Google reverse geocoding data");
        return false;
    }
    
    $data = json_decode($response, true);
    
    if (!$data || $data['status'] !== 'OK' || empty($data['results'])) {
        error_log("Invalid Google reverse geocoding response");
        return false;
    }
    
    $result = [
        'street' => '',
        'barangay' => '',
        'town' => '',
        'province' => '',
        'country' => 'Philippines',
        'postal_code' => '',
        'formatted_address' => $data['results'][0]['formatted_address'] ?? ''
    ];

    foreach ($data['results'][0]['address_components'] as $component) {
        if (in_array('street_number', $component['types'])) {
            $result['street'] = $component['long_name'] . ' ';
        } elseif (in_array('route', $component['types'])) {
            $result['street'] .= $component['long_name'];
        } elseif (in_array('neighborhood', $component['types'])) {
            $result['barangay'] = $component['long_name'];
        } elseif (in_array('sublocality', $component['types'])) {
            $result['barangay'] = $result['barangay'] ?: $component['long_name'];
        } elseif (in_array('locality', $component['types'])) {
            $result['town'] = $component['long_name'];
        } elseif (in_array('administrative_area_level_2', $component['types'])) {
            $result['town'] = $result['town'] ?: $component['long_name'];
        } elseif (in_array('administrative_area_level_1', $component['types'])) {
            $result['province'] = $component['long_name'];
        } elseif (in_array('country', $component['types'])) {
            $result['country'] = $component['long_name'];
        } elseif (in_array('postal_code', $component['types'])) {
            $result['postal_code'] = $component['long_name'];
        }
    }

    $result['street'] = trim($result['street']);

    if (empty($result['barangay']) && !empty($result['town'])) {
        $result['barangay'] = $result['town'];
    }

    if ($result['country'] === 'Philippines') {
        if (empty($result['barangay']) && !empty($result['formatted_address'])) {
            if (preg_match('/(Barangay|Brgy\.?)\s*(\w+)/i', $result['formatted_address'], $matches)) {
                $result['barangay'] = trim($matches[0]);
            }
        }
    }

    return $result;
}
?>