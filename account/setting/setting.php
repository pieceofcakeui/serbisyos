<?php
include '../backend/db_connection.php';

$googleUser = false;
$manualUser = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT fullname, auth_provider, profile_picture, email, contact_number, barangay, town as municipality, province, postal_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $googleUser = ($user['auth_provider'] === 'google');
        $manualUser = ($user['auth_provider'] === 'manual');
    }

    $stmt->close();
}
?>