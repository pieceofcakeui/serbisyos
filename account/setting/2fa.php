<?php
include '../backend/db_connection.php';

if (!isset($user_id)) {
    $user_id = $_SESSION['user_id'] ?? 0;
}

$twofa_enabled = false;
$stmt = $conn->prepare("SELECT is_enabled FROM user_2fa WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $twofa_enabled = $result->fetch_assoc()['is_enabled'] == 1;
}
?>