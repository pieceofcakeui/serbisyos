<?php
session_start();
include 'db_connection.php';
require '../../vendor/autoload.php';
require_once '../../account/backend/utilities.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

header('Content-Type: application/json');

function sendJsonResponse($success, $message, $data = [], $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit;
}

function logError($message, $context = []) {
    error_log("Booking Error: " . $message . " | Context: " . json_encode($context));
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Invalid request method', [], 405);
    }

    if (empty($_POST['shop_id'])) {
        sendJsonResponse(false, 'Session expired. Please refresh the page and try again.', [], 400);
    }

    $required_fields = [
        'customer_name' => 'Full name',
        'customer_phone' => 'Phone number',
        'customer_email' => 'Email address',
        'vehicle_make' => 'Vehicle make',
        'vehicle_model' => 'Vehicle model',
        'plate_number' => 'Plate number',
        'vehicle_year' => 'Vehicle year',
        'transmission_type' => 'Transmission type',
        'fuel_type' => 'Fuel type',
        'preferred_datetime' => 'Preferred date and time',
        'vehicle_type' => 'Vehicle type'
    ];

    $errors = [];
    $input_data = [];

    foreach ($required_fields as $field => $friendly_name) {
        if (empty($_POST[$field])) {
            $errors[] = "$friendly_name is required";
        } else {
            $input_data[$field] = trim($_POST[$field]);
        }
    }
    
    $input_data['vehicle_issues'] = isset($_POST['vehicle_issues']) ? trim($_POST['vehicle_issues']) : null;

    if (!empty($input_data['customer_email']) && !filter_var($input_data['customer_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    if (!empty($input_data['customer_phone']) && !preg_match('/^\d{11}$/', $input_data['customer_phone'])) {
        $errors[] = "Phone number must be exactly 11 digits";
    }

    if (!empty($input_data['vehicle_year'])) {
        $year = intval($input_data['vehicle_year']);
        if ($year < 1990 || $year > date('Y')) {
            $errors[] = "Please select a valid vehicle year (1990-" . date('Y') . ")";
        }
    }

    $selected_services = [];
    if (!empty($_POST['selected_services'])) {
        if (is_array($_POST['selected_services'])) {
            $selected_services = $_POST['selected_services'];
        } else {
            $decoded_services = json_decode($_POST['selected_services'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "Invalid service selection format";
            } else {
                $selected_services = $decoded_services;
            }
        }
    }

    if (empty($selected_services)) {
        $errors[] = "Please select at least one service";
    }

    if (!empty($errors)) {
        sendJsonResponse(false, 'Please correct the following issues:', ['errors' => $errors], 400);
    }

    if (!$conn) {
        logError("Database connection failed", ['error' => mysqli_connect_error()]);
        sendJsonResponse(false, 'Database connection error. Please try again later.', [], 500);
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $shop_id = intval($_POST['shop_id']);
    $customer_notes = isset($_POST['additional_notes']) ? trim($_POST['additional_notes']) : null;
    $services_json = json_encode($selected_services);
    $selected_day = isset($_POST['selected_day']) ? $_POST['selected_day'] : null;
    $selected_start_time = isset($_POST['selected_start_time']) ? $_POST['selected_start_time'] : null;
    $selected_end_time = isset($_POST['selected_end_time']) ? $_POST['selected_end_time'] : null;

    $conn->begin_transaction();

    $check_stmt = $conn->prepare("SELECT business_hours FROM shop_booking_form WHERE shop_id = ?");
    if (!$check_stmt) {
        throw new Exception("Database query preparation failed");
    }
    
    $check_stmt->bind_param("i", $shop_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $check_stmt->close();
        throw new Exception("This shop is currently not accepting online bookings. Please contact the shop directly.");
    }
    
    $settings = $check_result->fetch_assoc();
    $business_hours = json_decode($settings['business_hours'], true);
    $check_stmt->close();

    $slot_available = false;
    $current_slots = 0;
    
    foreach ($business_hours as $day_slot) {
        if (preg_match('/([^,]+),\s*([\d:]+)\s*-\s*([\d:]+)\s*\((\d+)\s*slots\)/', $day_slot, $matches)) {
            $day = trim($matches[1]);
            $start_time = trim($matches[2]);
            $end_time = trim($matches[3]);
            $slots = (int)$matches[4];
            
            if ($day === $selected_day && $start_time === $selected_start_time && $end_time === $selected_end_time) {
                if ($slots > 0) {
                    $slot_available = true;
                    $current_slots = $slots;
                }
                break;
            }
        }
    }
    
    if (!$slot_available) {
        throw new Exception("Sorry, this time slot is no longer available. Please refresh the page and select another time slot.");
    }

   $stmt = $conn->prepare("
        INSERT INTO services_booking (
            user_id, shop_id, customer_name, customer_phone, customer_email,
            vehicle_make, vehicle_model, plate_number, vehicle_year, vehicle_type, transmission_type, fuel_type,
            preferred_datetime, vehicle_issues, customer_notes, booking_status,
            service_type
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)
    ");
    
    if (!$stmt) {
        logError("Failed to prepare booking statement", ['error' => $conn->error]);
        throw new Exception("Failed to prepare booking statement: " . $conn->error);
    }
    
    $bind_result = $stmt->bind_param(
        "iissssssisssssss",
        $user_id,
        $shop_id,
        $input_data['customer_name'],
        $input_data['customer_phone'],
        $input_data['customer_email'],
        $input_data['vehicle_make'],
        $input_data['vehicle_model'],
        $input_data['plate_number'],
        $input_data['vehicle_year'],
        $input_data['vehicle_type'],
        $input_data['transmission_type'],
        $input_data['fuel_type'],
        $input_data['preferred_datetime'],
        $input_data['vehicle_issues'],
        $customer_notes,
        $services_json
    );
    
    if (!$bind_result) {
        throw new Exception("Failed to bind booking parameters");
    }
    
    if (!$stmt->execute()) {
        logError("Failed to execute booking insert", ['error' => $stmt->error]);
        throw new Exception("Failed to create booking. Please try again.");
    }
    
    $booking_id = $conn->insert_id;
    $stmt->close();

    if ($selected_day && $selected_start_time && $selected_end_time) {
        $updated_hours = [];
        $slot_updated = false;
        
        foreach ($business_hours as $day_slot) {
            if (preg_match('/([^,]+),\s*([\d:]+)\s*-\s*([\d:]+)\s*\((\d+)\s*slots\)/', $day_slot, $matches)) {
                $day = trim($matches[1]);
                $start_time = trim($matches[2]);
                $end_time = trim($matches[3]);  
                $slots = (int)$matches[4];

                if ($day === $selected_day && $start_time === $selected_start_time && $end_time === $selected_end_time) {
                    $new_slots = max(0, $slots - 1);
                    $updated_slot = "$day, $start_time - $end_time ($new_slots slots)";
                    $updated_hours[] = $updated_slot;
                    $slot_updated = true;
                } else {
                    $updated_hours[] = $day_slot;
                }
            } else {
                $updated_hours[] = $day_slot;
            }
        }
        
        if ($slot_updated) {
            $update_stmt = $conn->prepare("UPDATE shop_booking_form SET business_hours = ? WHERE shop_id = ?");
            if (!$update_stmt) {
                throw new Exception("Failed to prepare slot update statement");
            }
            
            $updated_business_hours = json_encode($updated_hours);
            $update_stmt->bind_param("si", $updated_business_hours, $shop_id);
            
            if (!$update_stmt->execute()) {
                logError("Failed to update slot count", ['error' => $update_stmt->error]);
                throw new Exception("Failed to update time slot availability");
            }
            $update_stmt->close();
        } else {
            throw new Exception("Could not update time slot. Please try again.");
        }
    }

    $conn->commit();

    $shop_owner_id = 0;
    $owner_stmt = $conn->prepare("SELECT user_id FROM shop_applications WHERE id = ?");
    if ($owner_stmt) {
        $owner_stmt->bind_param("i", $shop_id);
        $owner_stmt->execute();
        $owner_result = $owner_stmt->get_result();
        if ($owner_row = $owner_result->fetch_assoc()) {
            $shop_owner_id = $owner_row['user_id'];
        }
        $owner_stmt->close();
    }

    if ($user_id) {
        $notif_customer_stmt = $conn->prepare("
            INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status) 
            VALUES (?, ?, 'booking_sent', ?, 'Pending')
        ");
        if ($notif_customer_stmt) {
            $notif_customer_stmt->bind_param("iii", $user_id, $shop_id, $booking_id);
            $notif_customer_stmt->execute();
            $notif_customer_stmt->close();
        }
    }

    if ($shop_owner_id > 0) {
        $notif_owner_stmt = $conn->prepare("
            INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status) 
            VALUES (?, ?, 'booking_received', ?, 'Pending')
        ");
        if ($notif_owner_stmt) {
            $notif_owner_stmt->bind_param("iii", $shop_owner_id, $shop_id, $booking_id);
            $notif_owner_stmt->execute();
            $notif_owner_stmt->close();
        }

        try {
            $push_title = 'New Booking Request!';
            $push_body = 'You have received a new booking from ' . htmlspecialchars($input_data['customer_name']) . '.';
            $push_url = "/account/booking";
            sendPushNotification($conn, $shop_owner_id, $push_title, $push_body, $push_url);
        } catch (Exception $e) {
            logError("Web Push Notification Error (New Booking): " . $e->getMessage());
        }
    }

    $response_data = [
        'booking_id' => $booking_id,
        'details' => [
            'date' => $input_data['preferred_datetime'],
            'time' => '',
           'vehicle' => $input_data['vehicle_make'] . ' ' . $input_data['vehicle_model'] . ' (Plate: ' . $input_data['plate_number'] . ')',
            'services' => $selected_services
        ],
        'slots_remaining' => max(0, $current_slots - 1)
    ];
    
    sendJsonResponse(true, 'Booking successfully created! We will contact you soon to confirm your appointment.', $response_data);
    
} catch (Exception $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }

    logError($e->getMessage(), [
        'shop_id' => isset($shop_id) ? $shop_id : 'unknown',
        'user_id' => isset($user_id) ? $user_id : 'unknown'
    ]);

    $error_message = $e->getMessage();

    if (strpos($error_message, 'MySQL') !== false || strpos($error_message, 'database') !== false) {
        $error_message = 'Database connection error. Please try again in a few moments.';
    }
    
    sendJsonResponse(false, $error_message, [], 500);
    
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>