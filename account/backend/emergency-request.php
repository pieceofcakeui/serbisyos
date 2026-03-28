<?php
include 'db_connection.php';
include 'encrypt_loc.php';


$user_id = $_SESSION['user_id'];

if ($user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shop = $shopResult->fetch_assoc();

    if ($shop) {
        $shop_id = $shop['id'];

        $emergencyQuery = $conn->prepare("
            SELECT er.id, u.fullname, er.issue_description, er.created_at 
            FROM emergency_requests er
            JOIN users u ON er.user_id = u.id
            WHERE er.shop_id = ? AND er.seen_emergency_request = 0
            ORDER BY er.created_at DESC LIMIT 1
        ");
        $emergencyQuery->bind_param("i", $shop_id);
        $emergencyQuery->execute();
        $emergencyResult = $emergencyQuery->get_result();
        $emergency = $emergencyResult->fetch_assoc();

        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
        }
    }
}


function getProfilePicturePath($profile_picture) {
    $default_pic = '../assets/img/profile/profile-user.png';

    if (empty($profile_picture)) {
        return $default_pic;
    }

    $possible_directories = [
        '../uploads/profiles/',
        '../assets/img/profiles/',
        '../assets/img/profile/',
        '../uploads/',
    ];

    if (strpos($profile_picture, '/') !== false) {
        return $profile_picture;
    }

    foreach ($possible_directories as $dir) {
        $full_path = $dir . $profile_picture;
        if (file_exists($full_path)) {
            return $full_path;
        }
    }

    return '../uploads/profiles/' . $profile_picture;
}

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please login.");
}

$user_id = $_SESSION['user_id'];

$status_counts = [
    'all' => 0,
    'pending' => 0,
    'accepted' => 0,
    'completed' => 0,
    'rejected' => 0,
    'cancelled' => 0
];

try {
    $profile_check = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
    $profile_check->bind_param("i", $user_id);
    $profile_check->execute();
    $profile_result = $profile_check->get_result();
    
    if ($profile_result->num_rows === 0) {
        die("User not found.");
    }
    
    $user_profile = $profile_result->fetch_assoc();
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

if ($user_profile['profile_type'] === 'owner') {
    try {
        $emergency_query = "SELECT 
    er.id, 
    er.issue_description, 
    er.created_at,
    er.completed_at,
    er.user_id,
    u.fullname AS requester_name, 
    u.profile_picture,
    er.contact_number,
    er.vehicle_type, 
    er.vehicle_model, 
    er.status,
    er.latitude, 
    er.longitude, 
    er.full_address
    FROM emergency_requests er
    JOIN users u ON er.user_id = u.id
    WHERE er.shop_user_id = ? AND (er.hidden IS NULL OR er.hidden = 0)
    ORDER BY er.created_at DESC";

        $stmt = $conn->prepare($emergency_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $emergency_result = $stmt->get_result();

        $status_counts = [
            'all' => 0,
            'pending' => 0,
            'accepted' => 0,
            'completed' => 0,
            'rejected' => 0,
            'cancelled' => 0
        ];
        
        if ($emergency_result->num_rows > 0) {
            $status_counts['all'] = $emergency_result->num_rows;
            $emergency_result->data_seek(0);
            
            while ($request = $emergency_result->fetch_assoc()) {
                $status = !empty($request['status']) ? strtolower(trim($request['status'])) : 'pending';
                if (isset($status_counts[$status])) {
                    $status_counts[$status]++;
                }
            }
            $emergency_result->data_seek(0);
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        $emergency_result = false;
    }
}
?>