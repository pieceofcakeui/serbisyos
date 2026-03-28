<?php 

$sql = "SELECT 
            SUM(CASE WHEN profile_type = 'user' THEN 1 ELSE 0 END) AS total_users,
            SUM(CASE WHEN profile_type = 'owner' THEN 1 ELSE 0 END) AS total_owners
        FROM users";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalUsers = $row['total_users'];
    $totalOwners = $row['total_owners'];
} else {
    $totalUsers = 0;
    $totalOwners = 0;
}

$sql = "SELECT id, fullname, email, profile_type, status FROM users";
$result = $conn->query($sql);
$conn->close();
?>