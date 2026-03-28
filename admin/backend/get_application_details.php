<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM shop_applications WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            "success" => true,
            "data" => $row
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Application not found."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "ID not provided."
    ]);
}
?>
