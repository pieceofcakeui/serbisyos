<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

include 'db_connection.php';

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$currentAdminId = $_SESSION['id'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT id, full_name, email, created_at FROM admins";

if (!empty($search)) {
    $searchTerm = mysqli_real_escape_string($conn, $search);
    $query .= " WHERE (full_name LIKE '%$searchTerm%' OR email LIKE '%$searchTerm%')";
}

$query .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]);
    exit();
}

$admins = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['is_current'] = ($row['id'] == $currentAdminId);
    $admins[] = $row;
}

echo json_encode($admins);
mysqli_close($conn);
?>