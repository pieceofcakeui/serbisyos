<?php
require "db_connection.php";

header("Content-Type: application/json");

$sql = "SELECT shop_name, barangay, town_city, province, postal_code, latitude, longitude FROM shop_applications";
$result = $conn->query($sql);

$shops = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!empty($row["latitude"]) && !empty($row["longitude"])) {
            $shops[] = [
                "shop_name" => $row["shop_name"],
                "barangay" => $row["barangay"],
                "town_city" => $row["town_city"],
                "province" => $row["province"],
                "postal_code" => $row["postal_code"],
                "latitude" => (float)$row["latitude"],
                "longitude" => (float)$row["longitude"]
            ];
        }
    }
}

echo json_encode($shops);
$conn->close();
?>
