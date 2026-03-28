<?php
require './backend/db_connection.php';

header('Content-Type: application/json');

if (!isset($_GET['search'])) {
    echo json_encode(['error' => 'Missing search parameter']);
    exit();
}

$searchTerm = trim($_GET['search']);

if (empty($searchTerm)) {
    echo json_encode(['error' => 'Empty search term']);
    exit();
}

$searchTerm = $conn->real_escape_string($searchTerm);
$searchWildcard = "%{$searchTerm}%";

$query = "SELECT shop_name, services_offered, barangay, town_city, province 
          FROM shop_applications 
          WHERE shop_name LIKE ? OR services_offered LIKE ? OR barangay LIKE ? OR town_city LIKE ? OR province LIKE ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit();
}

$stmt->bind_param("sssss", $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard);
$stmt->execute();
$result = $stmt->get_result();

$shops = [];
while ($row = $result->fetch_assoc()) {
    $shops[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($shops);
exit();
?>
