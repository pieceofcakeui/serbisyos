<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
    exit;
}

require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (isset($_POST['full_name']) || isset($_POST['email']) || isset($_POST['phone_number']) || isset($_FILES['profile_pic'])) {
    $admin_id = $_SESSION['id'];
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $profile_pic_name = null;

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $profile_pic = $_FILES['profile_pic'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($profile_pic['type'], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Please upload a valid image file (JPG, PNG, GIF, or WebP).']);
            exit;
        }

        if ($profile_pic['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Image file size must be less than 5MB.']);
            exit;
        }

        $ext = pathinfo($profile_pic['name'], PATHINFO_EXTENSION);
        $profile_pic_name = 'admin_' . $admin_id . '_' . time() . '.' . $ext;

        $upload_dir = '../img/profile/'; 
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $upload_path = $upload_dir . $profile_pic_name;
        if (!move_uploaded_file($profile_pic['tmp_name'], $upload_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload profile image.']);
            exit;
        }
    }

    if (!empty($email)) {
        $check_email_query = "SELECT id FROM admins WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_email_query);
        $check_stmt->bind_param("si", $email, $admin_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email address is already in use by another admin.']);
            exit;
        }
        $check_stmt->close();
    }

    $fields = [];
    $params = [];
    $types = '';
    
    if (!empty($full_name)) {
        $fields[] = "full_name = ?";
        $params[] = $full_name;
        $types .= 's';
    }
    
    if (!empty($email)) {
        $fields[] = "email = ?";
        $params[] = $email;
        $types .= 's';
    }
    
    if (!empty($phone_number)) {
        $fields[] = "phone_number = ?";
        $params[] = $phone_number;
        $types .= 's';
    }
    
    if ($profile_pic_name) {
        $fields[] = "profile_pic = ?";
        $params[] = $profile_pic_name;
        $types .= 's';
    }

    if (empty($fields)) {
        echo json_encode(['success' => false, 'message' => 'No fields were provided for update.']);
        exit;
    }

    $params[] = $admin_id;
    $types .= 'i';

    $query = "UPDATE admins SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        if (!empty($full_name)) {
            $_SESSION['admin_name'] = $full_name;
        }
        
        $response = [
            'success' => true, 
            'message' => 'Profile updated successfully!'
        ];
        
        if ($profile_pic_name) {
            $response['profile_pic'] = $profile_pic_name;
        }
        
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No form data received.']);
}

$conn->close();
?>