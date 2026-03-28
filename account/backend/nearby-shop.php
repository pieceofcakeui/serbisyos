<?php
include 'db_connection.php';
include 'encrypt_loc.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die(json_encode(['error' => 'User not authenticated']));
}

$user_query = "SELECT street, barangay, town, province, latitude, longitude FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

if (!$user_data) {
    die(json_encode(['error' => 'User not found']));
}

$user_street = !empty($user_data['street']) ? decryptData($user_data['street']) : '';
$user_barangay = !empty($user_data['barangay']) ? decryptData($user_data['barangay']) : '';
$user_town = !empty($user_data['town']) ? decryptData($user_data['town']) : '';
$user_province = !empty($user_data['province']) ? decryptData($user_data['province']) : '';
$user_lat = !empty($user_data['latitude']) ? (float)decryptData($user_data['latitude']) : null;
$user_lng = !empty($user_data['longitude']) ? (float)decryptData($user_data['longitude']) : null;

$shops = [];

if ($user_lat && $user_lng) {
   $nearby_query = "
    SELECT 
        sa.id as shop_id,
        sa.shop_name,
        sa.shop_logo,
        sa.barangay,
        sa.town_city,
        sa.province,
        sa.postal_code,
        sa.shop_location,
        sa.country,
        sa.latitude,
        sa.longitude,
        sa.services_offered,
        COALESCE(AVG(sr.rating), 0) as avg_rating,
        COUNT(sr.id) as review_count,
        (6371 * acos(
            cos(radians(?)) * 
            cos(radians(sa.latitude)) * 
            cos(radians(sa.longitude) - radians(?)) + 
            sin(radians(?)) * 
            sin(radians(sa.latitude))
        )) as distance_km
    FROM 
        shop_applications sa
    LEFT JOIN 
        shop_ratings sr ON sa.id = sr.shop_id
    WHERE 
        sa.status = 'Approved' AND
        sa.user_id != ? AND
        sa.latitude IS NOT NULL AND 
        sa.longitude IS NOT NULL AND
        (6371 * acos(
            cos(radians(?)) * 
            cos(radians(sa.latitude)) * 
            cos(radians(sa.longitude) - radians(?)) + 
            sin(radians(?)) * 
            sin(radians(sa.latitude))
        )) <= 10
    GROUP BY 
        sa.id
    ORDER BY 
        distance_km ASC,
        avg_rating DESC
    LIMIT 20";

    $nearby_stmt = $conn->prepare($nearby_query);
    $nearby_stmt->bind_param("dddiddd", 
        $user_lat, $user_lng, $user_lat, $user_id, 
        $user_lat, $user_lng, $user_lat
    );
    
    if ($nearby_stmt->execute()) {
        $nearby_result = $nearby_stmt->get_result();
        while ($row = $nearby_result->fetch_assoc()) {
            $row['search_method'] = 'distance';
            $row['distance'] = $row['distance_km'];
            $shops[] = $row;
        }
    }
}

if (empty($shops) && !empty($user_town) && !empty($user_province)) {
    $town_query = "
    SELECT 
        sa.id as shop_id,
        sa.shop_name,
        sa.shop_logo,
        sa.barangay,
        sa.town_city,
        sa.province,
        sa.postal_code,
        sa.shop_location,
        sa.country,
        sa.latitude,
        sa.longitude,
        sa.services_offered,
        COALESCE(AVG(sr.rating), 0) as avg_rating,
        COUNT(sr.id) as review_count
    FROM 
        shop_applications sa
    LEFT JOIN 
        shop_ratings sr ON sa.id = sr.shop_id
    WHERE 
        sa.status = 'Approved' AND
        sa.user_id != ? AND
        sa.town_city = ? AND 
        sa.province = ?
    GROUP BY 
        sa.id
    ORDER BY 
        avg_rating DESC
    LIMIT 20";
    
    $town_stmt = $conn->prepare($town_query);
    $town_stmt->bind_param("iss", $user_id, $user_town, $user_province);
    
    if ($town_stmt->execute()) {
        $town_result = $town_stmt->get_result();
        while ($row = $town_result->fetch_assoc()) {
            if ($user_lat && $user_lng && $row['latitude'] && $row['longitude']) {
                $row['distance'] = calculateDistance(
                    $user_lat, $user_lng,
                    $row['latitude'], $row['longitude']
                );
            } else {
                $row['distance'] = null;
            }
            $row['search_method'] = 'town';
            $shops[] = $row;
        }
    }
}

if (empty($shops) && !empty($user_province)) {
    $province_query = "
    SELECT 
        sa.id as shop_id,
        sa.shop_name,
        sa.shop_logo,
        sa.barangay,
        sa.town_city,
        sa.province,
        sa.postal_code,
        sa.shop_location,
        sa.country,
        sa.latitude,
        sa.longitude,
        sa.services_offered,
        COALESCE(AVG(sr.rating), 0) as avg_rating,
        COUNT(sr.id) as review_count
    FROM 
        shop_applications sa
    LEFT JOIN 
        shop_ratings sr ON sa.id = sr.shop_id
    WHERE 
        sa.status = 'Approved' AND
        sa.user_id != ? AND
        sa.province = ?
    GROUP BY 
        sa.id
    ORDER BY 
        avg_rating DESC
    LIMIT 20";
    
    $province_stmt = $conn->prepare($province_query);
    $province_stmt->bind_param("is", $user_id, $user_province);
    
    if ($province_stmt->execute()) {
        $province_result = $province_stmt->get_result();
        while ($row = $province_result->fetch_assoc()) {
            if ($user_lat && $user_lng && $row['latitude'] && $row['longitude']) {
                $row['distance'] = calculateDistance(
                    $user_lat, $user_lng,
                    $row['latitude'], $row['longitude']
                );
            } else {
                $row['distance'] = null;
            }
            $row['search_method'] = 'province';
            $shops[] = $row;
        }
    }
}

if (!empty($shops)) {
    $search_method = $shops[0]['search_method'] ?? '';
    
    if ($search_method === 'town' || $search_method === 'province') {
        usort($shops, function($a, $b) {
            if (isset($a['distance']) && isset($b['distance']) && 
                $a['distance'] !== null && $b['distance'] !== null) {
                $distance_comparison = $a['distance'] <=> $b['distance'];
                if ($distance_comparison !== 0) {
                    return $distance_comparison;
                }
            }
            return $b['avg_rating'] <=> $a['avg_rating'];
        });
    }
}

if (!function_exists('calculateDistance')) {
    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371;
        
        $lat1_rad = deg2rad($lat1);
        $lon1_rad = deg2rad($lon1);
        $lat2_rad = deg2rad($lat2);
        $lon2_rad = deg2rad($lon2);
        
        $delta_lat = $lat2_rad - $lat1_rad;
        $delta_lon = $lon2_rad - $lon1_rad;
        
        $a = sin($delta_lat / 2) * sin($delta_lat / 2) +
             cos($lat1_rad) * cos($lat2_rad) *
             sin($delta_lon / 2) * sin($delta_lon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earth_radius * $c;
    }
}


?>