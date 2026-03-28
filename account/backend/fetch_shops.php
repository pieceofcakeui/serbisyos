<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'db_connection.php';

$input = json_decode(file_get_contents('php://input'), true);

$location       = trim($input['location'] ?? '');
$searchType     = $input['searchType'] ?? 'both';
$townCity       = $input['townCity'] ?? '';
$province       = $input['province'] ?? '';
$requireBooking   = isset($input['requireBooking']) ? (bool)$input['requireBooking'] : false;
$requireEmergency = isset($input['requireEmergency']) ? (bool)$input['requireEmergency'] : false;

try {
    $sql = "SELECT 
                sa.id,
                sa.shop_name,
                sa.shop_slug,
                sa.shop_location,
                sa.shop_logo,
                sa.services_offered,
                sa.show_book_now,
                sa.show_emergency,
                sa.town_city,
                sa.province,
                sa.country,
                sa.postal_code,
                COALESCE(AVG(sr.rating), 0) AS rating
            FROM shop_applications sa
            LEFT JOIN shop_ratings sr ON sa.id = sr.shop_id
            WHERE sa.status = 'Approved'
               AND sa.shop_status NOT IN ('permanently_closed', 'temporarily_closed')";
    
    $types = '';
    $params = [];

    if (!empty($location) && empty($townCity) && empty($province)) {
        $sql .= " AND (LOWER(sa.shop_location) LIKE LOWER(?) OR LOWER(sa.town_city) LIKE LOWER(?) OR LOWER(sa.province) LIKE LOWER(?))";
        $locationParam = '%' . strtolower($location) . '%';
        $params[] = $locationParam;
        $params[] = $locationParam;
        $params[] = $locationParam;
        $types .= 'sss';
    } else {
        if ($searchType === 'town_city' && !empty($townCity)) {
            $sql .= " AND LOWER(sa.town_city) LIKE LOWER(?)";
            $params[] = '%' . strtolower($townCity) . '%';
            $types .= 's';
        } elseif ($searchType === 'province' && !empty($province)) {
            $sql .= " AND LOWER(sa.province) LIKE LOWER(?)";
            $params[] = '%' . strtolower($province) . '%';
            $types .= 's';
        } elseif ($searchType === 'both') {
            if (!empty($townCity)) {
                $sql .= " AND LOWER(sa.town_city) LIKE LOWER(?)";
                $params[] = '%' . strtolower($townCity) . '%';
                $types .= 's';
            }
            if (!empty($province)) {
                $sql .= " AND LOWER(sa.province) LIKE LOWER(?)";
                $params[] = '%' . strtolower($province) . '%';
                $types .= 's';
            }
        }
    }

    if ($requireBooking) {
        $sql .= " AND sa.show_book_now = 1";
    }

    if ($requireEmergency) {
        $sql .= " AND sa.show_emergency = 1";
    }

    $sql .= " GROUP BY sa.id
              ORDER BY 
                  sa.show_emergency DESC, 
                  sa.show_book_now DESC, 
                  rating DESC, 
                  sa.shop_name ASC
              LIMIT 10";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("SQL preparation failed: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $shops = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($shops as &$shop) {
        $shop['rating'] = $shop['rating'] > 0 ? round($shop['rating'], 1) : null;
        $shop['services_offered'] = $shop['services_offered'] ?: 'General auto repair services';
        $shop['shop_name'] = $shop['shop_name'] ?? '';

        $shop['full_address'] = $shop['shop_location'] ?? '';
        if (!empty($shop['town_city'])) {
            $shop['full_address'] .= (!empty($shop['full_address']) ? ', ' : '') . $shop['town_city'];
        }
        if (!empty($shop['province'])) {
            $shop['full_address'] .= (!empty($shop['full_address']) ? ', ' : '') . $shop['province'];
        }
        if (!empty($shop['country'])) {
            $shop['full_address'] .= (!empty($shop['full_address']) ? ', ' : '') . $shop['country'];
        }
        if (!empty($shop['postal_code'])) {
            $shop['full_address'] .= (!empty($shop['full_address']) ? ', ' : '') . $shop['postal_code'];
        }
        
        if (empty($shop['full_address'])) {
            $shop['full_address'] = 'Address not available';
        }

        $logo_filename = !empty($shop['shop_logo']) ? $shop['shop_logo'] : 'logo.jpg';
        $shop['logo_url'] = 'uploads/shop_logo/' . $logo_filename;
    }
    unset($shop);

    echo json_encode([
        'success' => true,
        'count' => count($shops),
        'data' => $shops
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Query failed: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>