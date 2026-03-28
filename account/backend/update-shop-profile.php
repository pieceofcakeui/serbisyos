<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

$user_stmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user || $user['profile_type'] !== 'owner') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

$shop_stmt = $conn->prepare("SELECT * FROM shop_applications WHERE user_id = ? AND status = 'Approved'");
$shop_stmt->bind_param("i", $user_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();
$shop = $shop_result->fetch_assoc();

if (!$shop) {
    echo json_encode(['success' => false, 'message' => 'No approved shop found for this user.']);
    exit();
}

$shop_id = $shop['id'];

$required_fields = ['shop_name', 'email', 'phone', 'opening_time_am', 'closing_time_am'];
foreach ($required_fields as $field) {
    if (empty(trim($_POST[$field]))) {
        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.']);
        exit();
    }
}

if (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit();
}

$new_shop_name = trim($_POST['shop_name']);
$new_email = trim($_POST['email']);
$new_phone = trim($_POST['phone']);
$new_postal_code = trim($_POST['postal_code'] ?? '');
$new_barangay = trim($_POST['barangay'] ?? '');
$new_town_city = trim($_POST['town_city'] ?? '');
$new_province = trim($_POST['province'] ?? '');
$new_description = trim($_POST['description'] ?? '');
$new_facebook = trim($_POST['facebook'] ?? '');
$new_instagram = trim($_POST['instagram'] ?? '');
$new_website = trim($_POST['website'] ?? '');
$new_years_operation = isset($_POST['years_operation']) && $_POST['years_operation'] !== '' ? (int)$_POST['years_operation'] : NULL;
$new_opening_time_am = trim($_POST['opening_time_am']);
$new_closing_time_am = trim($_POST['closing_time_am']);
$new_opening_time_pm = !empty($_POST['opening_time_pm']) ? trim($_POST['opening_time_pm']) : NULL;
$new_closing_time_pm = !empty($_POST['closing_time_pm']) ? trim($_POST['closing_time_pm']) : NULL;
$new_days_open = isset($_POST['days_open']) && is_array($_POST['days_open']) ? implode(',', $_POST['days_open']) : '';

$new_shop_logo = $shop['shop_logo'];

if (isset($_FILES['shop_logo']) && $_FILES['shop_logo']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "../uploads/shop_logo/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_info = pathinfo($_FILES["shop_logo"]["name"]);
    $file_extension = strtolower($file_info['extension']);
    $allowed_ext = ["jpg", "jpeg", "png", "gif", "webp"];
    
    if (in_array($file_extension, $allowed_ext) && $_FILES["shop_logo"]["size"] <= 5000000) {
        $new_filename = uniqid('logo_', true) . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["shop_logo"]["tmp_name"], $target_file)) {
            if (!empty($shop['shop_logo']) && file_exists($target_dir . $shop['shop_logo'])) {
                if ($shop['shop_logo'] !== 'logo.jpg') {
                     unlink($target_dir . $shop['shop_logo']);
                }
            }
            $new_shop_logo = $new_filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid file type or size (Max 5MB). Allowed: JPG, JPEG, PNG, GIF, WEBP.']);
        exit();
    }
}

$sql = "UPDATE shop_applications SET
            shop_name = ?, email = ?, phone = ?, postal_code = ?, barangay = ?,
            town_city = ?, province = ?, description = ?, shop_logo = ?,
            opening_time_am = ?, closing_time_am = ?, opening_time_pm = ?,
            closing_time_pm = ?, days_open = ?, facebook = ?, instagram = ?, website = ?,
            years_operation = ?
        WHERE id = ? AND user_id = ?";

$update_stmt = $conn->prepare($sql);

if (!$update_stmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
    exit();
}

$update_stmt->bind_param("sssssssssssssssssiii",
    $new_shop_name, $new_email, $new_phone, $new_postal_code, $new_barangay,
    $new_town_city, $new_province, $new_description, $new_shop_logo,
    $new_opening_time_am, $new_closing_time_am, $new_opening_time_pm,
    $new_closing_time_pm, $new_days_open, $new_facebook, $new_instagram, $new_website,
    $new_years_operation,
    $shop_id, $user_id
);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Shop profile updated successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating shop profile: ' . $update_stmt->error]);
}

$update_stmt->close();
$conn->close();
?>