<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$user_id = $_SESSION['user_id'];

$shop_stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ? AND status = 'Approved' LIMIT 1");
$shop_stmt->bind_param("i", $user_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();

if ($shop_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No approved shop found for this user.']);
    $shop_stmt->close();
    $conn->close();
    exit();
}

$shop = $shop_result->fetch_assoc();
$shop_id = $shop['id'];
$shop_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $services_structure = [];
    $sql_services = "
        SELECT 
            ec.name AS category_name,
            esc.name AS subcategory_name,
            es.name AS service_display_name,
            es.value AS service_machine_value
        FROM 
            emergency_assistance_services AS es
        JOIN 
            emergency_subcategories AS esc ON es.subcategory_id = esc.id
        JOIN 
            emergency_categories AS ec ON esc.category_id = ec.id
        ORDER BY 
            ec.display_order, ec.name, esc.name, es.name
    ";
    $result_services = $conn->query($sql_services);
    if ($result_services && $result_services->num_rows > 0) {
        while ($row = $result_services->fetch_assoc()) {
            $services_structure[$row['category_name']][$row['subcategory_name']][] = [
                'name' => $row['service_display_name'],
                'value' => $row['service_machine_value']
            ];
        }
    }
    
    // --- START: Added Code to Fetch Booking Services ---
    $all_booking_services = [];
    $categories_result = $conn->query("SELECT * FROM service_categories ORDER BY display_order, name");
    while ($cat = $categories_result->fetch_assoc()) {
        $all_booking_services[$cat['id']] = $cat;
        $all_booking_services[$cat['id']]['subcategories'] = [];
    }
    $subcategories_result = $conn->query("SELECT * FROM service_subcategories ORDER BY name");
    while ($sub = $subcategories_result->fetch_assoc()) {
        if (isset($all_booking_services[$sub['category_id']])) {
            $all_booking_services[$sub['category_id']]['subcategories'][$sub['id']] = $sub;
            $all_booking_services[$sub['category_id']]['subcategories'][$sub['id']]['services'] = [];
        }
    }
    $services_result = $conn->query("SELECT * FROM services ORDER BY name");
    while ($ser = $services_result->fetch_assoc()) {
        foreach ($all_booking_services as &$category) {
            if (isset($category['subcategories'][$ser['subcategory_id']])) {
                $category['subcategories'][$ser['subcategory_id']]['services'][] = $ser;
                break;
            }
        }
    }
    // --- END: Added Code ---

    $response = [
        'shop_status' => 'open',
        'show_book_now' => false,
        'show_emergency' => false,
        'bookingConfig' => [
            'serviceTypes' => [], 'transmissionTypes' => [], 'fuelTypes' => [],
            'vehicleTypes' => [], 'preferredDateTimes' => []
        ],
        'emergencyConfig' => [
            'emergencyHours' => [], 'offeredServices' => []
        ],
        'all_emergency_services' => $services_structure,
        'all_booking_services' => $all_booking_services // Added this line
    ];

    $stmt = $conn->prepare("SELECT show_book_now, show_emergency, shop_status FROM shop_applications WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $shop_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $shop_data = $result->fetch_assoc();
            $response['show_book_now'] = (bool)($shop_data['show_book_now'] ?? false);
            $response['show_emergency'] = (bool)($shop_data['show_emergency'] ?? false);
            $response['shop_status'] = $shop_data['shop_status'] ?? 'open';
        }
        $stmt->close();
    }

    $booking_stmt = $conn->prepare("SELECT service_types, transmission_types, fuel_types, vehicle_types, business_hours FROM shop_booking_form WHERE shop_id = ?");
    if ($booking_stmt) {
        $booking_stmt->bind_param("i", $shop_id);
        $booking_stmt->execute();
        $booking_result = $booking_stmt->get_result();
        if ($booking_result->num_rows > 0) {
            $booking_config = $booking_result->fetch_assoc();
            $response['bookingConfig']['serviceTypes'] = $booking_config['service_types'] ? json_decode($booking_config['service_types'], true) : [];
            $response['bookingConfig']['transmissionTypes'] = $booking_config['transmission_types'] ? json_decode($booking_config['transmission_types'], true) : [];
            $response['bookingConfig']['fuelTypes'] = $booking_config['fuel_types'] ? json_decode($booking_config['fuel_types'], true) : [];
            $response['bookingConfig']['vehicleTypes'] = $booking_config['vehicle_types'] ? json_decode($booking_config['vehicle_types'], true) : [];
            $response['bookingConfig']['preferredDateTimes'] = $booking_config['business_hours'] ? json_decode($booking_config['business_hours'], true) : [];
        }
        $booking_stmt->close();
    }
    
    $emergency_stmt = $conn->prepare("SELECT emergency_hours, offered_services FROM shop_emergency_config WHERE shop_id = ?");
    if ($emergency_stmt) {
        $emergency_stmt->bind_param("i", $shop_id);
        $emergency_stmt->execute();
        $emergency_result = $emergency_stmt->get_result();
        if ($emergency_result->num_rows > 0) {
            $emergency_config = $emergency_result->fetch_assoc();
            $response['emergencyConfig']['emergencyHours'] = $emergency_config['emergency_hours'] ? json_decode($emergency_config['emergency_hours'], true) : [];
            $response['emergencyConfig']['offeredServices'] = $emergency_config['offered_services'] ? json_decode($emergency_config['offered_services'], true) : [];
        }
        $emergency_stmt->close();
    }

    echo json_encode(['success' => true, 'settings' => $response]);
    exit();
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
        exit();
    }

    $conn->begin_transaction();

    try {

        $show_book_now = isset($data['show_book_now']) ? (int)$data['show_book_now'] : 0;
        $show_emergency = isset($data['show_emergency']) ? (int)$data['show_emergency'] : 0;
        $shop_status = $data['shop_status'] ?? 'open';

        $allowed_statuses = ['open', 'temporarily_closed', 'permanently_closed'];
        if (!in_array($shop_status, $allowed_statuses)) {
            throw new Exception("Invalid shop status provided.");
        }

        $status_check_stmt = $conn->prepare("SELECT shop_status FROM shop_applications WHERE id = ?");
        $status_check_stmt->bind_param("i", $shop_id);
        $status_check_stmt->execute();
        $current_shop = $status_check_stmt->get_result()->fetch_assoc();
        $status_check_stmt->close();

        if ($current_shop && $current_shop['shop_status'] === 'permanently_closed' && $shop_status !== 'permanently_closed') {
            throw new Exception("A permanently closed shop cannot be reopened.");
        }

        $stmt = $conn->prepare("UPDATE shop_applications SET show_book_now = ?, show_emergency = ?, shop_status = ? WHERE id = ?");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        
        $stmt->bind_param("iisi", $show_book_now, $show_emergency, $shop_status, $shop_id);
        if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
        $stmt->close();

        if (isset($data['bookingConfig'])) {
            $config = $data['bookingConfig'];
            $service_types = json_encode($config['serviceTypes'] ?? []);
            $transmission_types = json_encode($config['transmissionTypes'] ?? []);
            $fuel_types = json_encode($config['fuelTypes'] ?? []);
            $vehicle_types = json_encode($config['vehicleTypes'] ?? []);
            $business_hours = json_encode($config['preferredDateTimes'] ?? []);

            $check_stmt = $conn->prepare("SELECT id FROM shop_booking_form WHERE shop_id = ?");
            $check_stmt->bind_param("i", $shop_id);
            $check_stmt->execute();
            $exists = $check_stmt->get_result()->num_rows > 0;
            $check_stmt->close();

            if ($exists) {
                $stmt = $conn->prepare("UPDATE shop_booking_form SET service_types = ?, transmission_types = ?, fuel_types = ?, vehicle_types = ?, business_hours = ? WHERE shop_id = ?");
                $stmt->bind_param("sssssi", $service_types, $transmission_types, $fuel_types, $vehicle_types, $business_hours, $shop_id);
            } else {
                $stmt = $conn->prepare("INSERT INTO shop_booking_form (user_id, shop_id, service_types, transmission_types, fuel_types, vehicle_types, business_hours) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iisssss", $user_id, $shop_id, $service_types, $transmission_types, $fuel_types, $vehicle_types, $business_hours);
            }
            if (!$stmt->execute()) throw new Exception("Booking config update failed: " . $stmt->error);
            $stmt->close();
        }

        if (isset($data['emergencyConfig'])) {
            $emergency_hours = json_encode($data['emergencyConfig']['emergencyHours'] ?? []);
            $offered_services = json_encode($data['emergencyConfig']['offeredServices'] ?? []);

            $check_stmt = $conn->prepare("SELECT id FROM shop_emergency_config WHERE shop_id = ?");
            $check_stmt->bind_param("i", $shop_id);
            $check_stmt->execute();
            $exists = $check_stmt->get_result()->num_rows > 0;
            $check_stmt->close();

            if ($exists) {
                $stmt = $conn->prepare("UPDATE shop_emergency_config SET emergency_hours = ?, offered_services = ? WHERE shop_id = ?");
                $stmt->bind_param("ssi", $emergency_hours, $offered_services, $shop_id);
            } else {
                $stmt = $conn->prepare("INSERT INTO shop_emergency_config (user_id, shop_id, emergency_hours, offered_services) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $user_id, $shop_id, $emergency_hours, $offered_services);
            }
            if (!$stmt->execute()) throw new Exception("Emergency config update failed: " . $stmt->error);
            $stmt->close();
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Settings updated successfully.']);

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Shop settings update error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
$conn->close();
?>