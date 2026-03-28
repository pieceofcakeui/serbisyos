<?php
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "User not logged in"
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$longitude = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$street = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_STRING);
$barangay = filter_input(INPUT_POST, 'barangay', FILTER_SANITIZE_STRING);
$town = filter_input(INPUT_POST, 'town', FILTER_SANITIZE_STRING);
$province = filter_input(INPUT_POST, 'province', FILTER_SANITIZE_STRING);
$postal_code = filter_input(INPUT_POST, 'postal_code', FILTER_SANITIZE_STRING);

if (!$latitude || !$longitude) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid location coordinates"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE users SET 
        address = ?,
        latitude = ?,
        longitude = ?,
        street = ?,
        barangay = ?,
        town = ?,
        province = ?,
        postal_code = ?
        WHERE id = ?");

    $stmt->bind_param("sddsssssi",
        $address,
        $latitude,
        $longitude,
        $street,
        $barangay,
        $town,
        $province,
        $postal_code,
        $user_id
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Location updated successfully."
        ]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error updating location: " . $e->getMessage()
    ]);
} finally {
    $stmt->close();
    $conn->close();
}
?>