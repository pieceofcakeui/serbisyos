<?php
require __DIR__ . '/../vendor/autoload.php';
include 'backend/db_connection.php';

function findNearbyShops($userLat, $userLng, $radiusKm = 5) {
    global $conn;
    
    $query = "SELECT id, shop_name, latitude, longitude, 
              (6371 * ACOS(
                  COS(RADIANS(?)) * COS(RADIANS(latitude)) * 
                  COS(RADIANS(longitude) - RADIANS(?)) + 
                  SIN(RADIANS(?)) * SIN(RADIANS(latitude))
              )) AS distance
              FROM shop_applications 
              WHERE status = 'Approved'
              HAVING distance < ?
              ORDER BY distance ASC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dddd", $userLat, $userLng, $userLat, $radiusKm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createNotification($userId, $shopId, $message) {
    global $conn;
    
    $query = "INSERT INTO notifications (user_id, shop_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $userId, $shopId, $message);
    $stmt->execute();
    
    return $stmt->affected_rows > 0;
}

function sendEmailNotification($email, $subject, $body) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.example.com';
        $mail->SMTPAuth   = true;
        $mail->Username = "theycallmesayyyy@gmail.com";
        $mail->Password = "nvkt qkqp aozi fhmc";
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('theycallmesayyyy@gmail.com', 'Serbisyos');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>