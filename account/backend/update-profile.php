<?php
session_start();
include 'db_connection.php';

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

function geocodeAddress($address)
{
    $apiKey = $_ENV['GOOGLE_MAPS_API_KEY'];
    $encodedAddress = urlencode($address);

    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedAddress}&key={$apiKey}&components=country:PH";

    try {
        $response = file_get_contents($url);
        if ($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if ($data['status'] !== 'OK' || empty($data['results'])) {
            return false;
        }

        $location = $data['results'][0]['geometry']['location'];
        return [
            'lat' => (float) $location['lat'],
            'lon' => (float) $location['lng']
        ];
    } catch (Exception $e) {
        error_log("Geocoding error: " . $e->getMessage());
        return false;
    }
}

if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $barangay = $_POST['barangay'] ?? '';
    $municipality = $_POST['municipality'] ?? '';
    $province = $_POST['province'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';

    $latitude = 0;
    $longitude = 0;

    if (!empty($barangay) && !empty($municipality) && !empty($province)) {
        $address = "$barangay, $municipality, $province, Philippines";
        if (!empty($postal_code)) {
            $address .= " $postal_code";
        }

        $geo_data = geocodeAddress($address);
        if ($geo_data) {
            $latitude = $geo_data['lat'];
            $longitude = $geo_data['lon'];
            error_log("Geocoded coordinates: LAT=$latitude, LON=$longitude");
        }
    }

    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Email already exists!");
        }

        $profile_picture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = mime_content_type($file['tmp_name']);
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Only JPG, PNG, GIF, and WEBP images are allowed");
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("File size should be less than 5MB");
            }

            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
            $upload_path = '../assets/img/profile/' . $new_filename;

            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                throw new Exception("Failed to upload profile picture");
            }

            $profile_picture = $new_filename;

            $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!empty($user['profile_picture']) && $user['profile_picture'] !== 'profile-user.png') {
                $old_file = '../assets/img/profile/' . $user['profile_picture'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
        }

        if ($profile_picture) {
            $sql = "UPDATE users SET 
                        fullname = ?, 
                        email = ?, 
                        contact_number = ?, 
                        barangay = ?, 
                        town = ?, 
                        province = ?, 
                        postal_code = ?, 
                        latitude = ?, 
                        longitude = ?, 
                        profile_picture = ? 
                        WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssssssddsi",
                $full_name,
                $email,
                $contact_number,
                $barangay,
                $municipality,
                $province,
                $postal_code,
                $latitude,
                $longitude,
                $profile_picture,
                $user_id
            );
        } else {
            $sql = "UPDATE users SET 
                        fullname = ?, 
                        email = ?, 
                        contact_number = ?, 
                        barangay = ?, 
                        town = ?, 
                        province = ?, 
                        postal_code = ?, 
                        latitude = ?, 
                        longitude = ? 
                        WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssssssddi",
                $full_name,
                $email,
                $contact_number,
                $barangay,
                $municipality,
                $province,
                $postal_code,
                $latitude,
                $longitude,
                $user_id
            );
        }

        if ($stmt->execute()) {
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            if ($profile_picture) {
                $_SESSION['user_profile'] = $profile_picture;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'profile_picture' => $profile_picture
            ]);
        } else {
            throw new Exception("Failed to update profile: " . $conn->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>