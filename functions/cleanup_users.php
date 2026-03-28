<?php
require 'db_connection.php'; 

$now = date('Y-m-d H:i:s');

$stmt = $conn->prepare("DELETE FROM users WHERE status = 'unverified' AND verification_expiry IS NOT NULL AND verification_expiry < ?");
$stmt->bind_param("s", $now);

if ($stmt->execute()) {
    $deleted_count = $stmt->affected_rows;
    error_log("$deleted_count expired unverified user(s) deleted.");
} else {
    error_log("Error during cleanup of unverified users: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>