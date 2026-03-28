<?php
include 'db_connection.php';
include 'security_helper.php';

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ? AND status = 'Approved' LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No approved shop found']);
        exit;
    }
    
    $shop = $result->fetch_assoc();
    $shop_id = $shop['id'];

    $response = ['success' => false, 'message' => 'Invalid action'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'upload':
                $response = handleImageUpload($conn, $shop_id);
                break;
                
            case 'delete':
                $response = handleImageDelete($conn, $shop_id);
                break;
                
            default:
                $response = ['success' => false, 'message' => 'Invalid action'];
        }
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Gallery handler error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}

function handleImageUpload($conn, $shop_id) {
    $maxFileSize = 5 * 1024 * 1024;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $uploadDir = '../uploads/shop_gallery/';
    $maxImages = 3;
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $currentImages = getCurrentGalleryImages($conn, $shop_id);
    $currentCount = count($currentImages);

    if ($currentCount >= $maxImages) {
        return ['success' => false, 'message' => "You cannot upload more than {$maxImages} images. Please delete an existing image first."];
    }

    if ($currentCount == 1) {
        $allowedNew = 2; 
    } elseif ($currentCount == 2) {
        $allowedNew = 1; 
    } else {
        $allowedNew = 3;
    }
    
    if (empty($_FILES['images']['name'][0])) {
        return ['success' => false, 'message' => 'No files uploaded'];
    }

    $attemptingToUpload = count(array_filter($_FILES['images']['name']));

    if ($attemptingToUpload > $allowedNew) {
        $message = $currentCount == 1 
            ? "You can upload 2 more images only (you already have 1)." 
            : "You can upload only 1 more image (you already have 2).";
        return ['success' => false, 'message' => $message];
    }

    $newImages = [];
    $uploadCount = 0;

    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
        if ($uploadCount >= $allowedNew) {
            break; 
        }

        $fileName = $_FILES['images']['name'][$key];
        $fileSize = $_FILES['images']['size'][$key];
        $fileType = $_FILES['images']['type'][$key];
        $fileError = $_FILES['images']['error'][$key];

        if ($fileError !== UPLOAD_ERR_OK) {
            continue;
        }
        if ($fileSize > $maxFileSize) {
            continue;
        }
        if (!in_array($fileType, $allowedTypes)) {
            continue;
        }

        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'shop_' . $shop_id . '_' . uniqid() . '.' . $fileExt;
        $destination = $uploadDir . $newFileName;
        
        if (move_uploaded_file($tmpName, $destination)) {
            $newImages[] = 'uploads/shop_gallery/' . $newFileName;
            $uploadCount++;
        }
    }
    
    if (empty($newImages)) {
        return ['success' => false, 'message' => 'No valid images were uploaded'];
    }

    $updatedImages = array_merge($currentImages, $newImages);

    if (updateGalleryInDatabase($conn, $shop_id, $updatedImages)) {
        return [
            'success' => true,
            'message' => count($newImages) . ' image(s) uploaded successfully',
            'images' => $updatedImages
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to update gallery'];
}

function handleImageDelete($conn, $shop_id) {
    if (!isset($_POST['image_path'])) {
        return ['success' => false, 'message' => 'No image specified'];
    }
    
    $imagePath = $_POST['image_path'];
    $fullPath = '../' . ltrim($imagePath, '/');

    $currentImages = getCurrentGalleryImages($conn, $shop_id);

    $updatedImages = array_filter($currentImages, function($img) use ($imagePath) {
        return $img !== $imagePath;
    });

    if (updateGalleryInDatabase($conn, $shop_id, $updatedImages)) {
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        return [
            'success' => true,
            'message' => 'Image deleted successfully',
            'images' => array_values($updatedImages)
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to delete image'];
}

function getCurrentGalleryImages($conn, $shop_id) {
    $stmt = $conn->prepare("SELECT shop_gallery_images FROM shop_applications WHERE id = ?");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [];
    }
    
    $shop = $result->fetch_assoc();
    $galleryImages = json_decode($shop['shop_gallery_images'], true) ?? [];
    
    return is_array($galleryImages) ? $galleryImages : [];
}

function updateGalleryInDatabase($conn, $shop_id, $images) {
    $imagesJson = json_encode(array_values($images));
    $stmt = $conn->prepare("UPDATE shop_applications SET shop_gallery_images = ? WHERE id = ?");
    $stmt->bind_param("si", $imagesJson, $shop_id);
    return $stmt->execute();
}
?>
