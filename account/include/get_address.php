<?php
session_start();
header('Content-Type: application/json');

require './backend/db_connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log("Database connection failed in get_address.php");
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if (isset($_POST['latitude']) && isset($_POST['longitude']) && isset($_POST['address'])) {
    $latitude = filter_var($_POST['latitude'], FILTER_VALIDATE_FLOAT);
    $longitude = filter_var($_POST['longitude'], FILTER_VALIDATE_FLOAT);
    $address = $_POST['address'];

    if ($latitude === false || $longitude === false) {
        echo json_encode(["status" => "error", "message" => "Invalid coordinates"]);
        exit;
    }

    try {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            $stmt = $pdo->prepare("
                UPDATE users SET 
                    latitude = :latitude, 
                    longitude = :longitude, 
                    address = :address
                WHERE id = :user_id
            ");

            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':latitude', $latitude);
            $stmt->bindParam(':longitude', $longitude);
            $stmt->bindParam(':address', $address);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    "status" => "OK", 
                    "message" => "User location updated successfully"
                ]);
                exit;
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO users (
                fullname, email, password, status,
                latitude, longitude, address, 
                created_at
            ) VALUES (
                'Temporary User', :temp_email, 'temporary', 'unverified',
                :latitude, :longitude, :address,
                CURRENT_TIMESTAMP
            )
        ");

        $temp_email = 'temp_' . time() . '@example.com';
        $stmt->bindParam(':temp_email', $temp_email);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':address', $address);

        $stmt->execute();

        echo json_encode([
            "status" => "OK", 
            "message" => "Location saved successfully"
        ]);

    } catch (PDOException $e) {
        error_log("Database error in get_address.php: " . $e->getMessage());
        echo json_encode([
            "status" => "error", 
            "message" => "Database error: " . $e->getMessage(),
            "sql_state" => $e->errorInfo[0],
            "error_code" => $e->errorInfo[1],
            "error_message" => $e->errorInfo[2]
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing required data"]);
}
?>