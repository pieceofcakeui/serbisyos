<?php
include '../backend/db_connection.php';

$profile_type = '';
if (isset($user_id) && $user_id) {
    $stmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    $profile_type = $profile['profile_type'] ?? '';
    $stmt->close();
}
?>