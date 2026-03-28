<?php
session_start();
include 'db_connection.php';
include 'security_helper.php';

header('Content-Type: application/json');

if (isset($_GET['query'])) {
    $search_term = $_GET['query'];

    try {
        if (!$conn) {
            echo json_encode(array('error' => 'Database connection failed'));
            exit;
        }

        $sql = "SELECT s.id, s.shop_name, s.shop_logo, s.user_id 
                FROM shop_applications s 
                WHERE s.status = 'Approved' 
                AND LOWER(s.shop_name) LIKE LOWER(?)
                ORDER BY s.shop_name
                LIMIT 10";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(array('error' => 'SQL prepare error'));
            exit;
        }

        $searchParam = "{$search_term}%";
        $bindResult = $stmt->bind_param("s", $searchParam);
        if (!$bindResult) {
            echo json_encode(array('error' => 'Parameter binding error'));
            exit;
        }

        $executeResult = $stmt->execute();
        if (!$executeResult) {
            echo json_encode(array('error' => 'SQL execution error'));
            exit;
        }

        $result = $stmt->get_result();
        if (!$result) {
            echo json_encode(array('error' => 'Result retrieval error'));
            exit;
        }

        $shops = array();
        while ($row = $result->fetch_assoc()) {
            $shop_logo = '';
            if (!empty($row['shop_logo'])) {
                $shop_logo = $row['shop_logo'];
                if (strpos($shop_logo, 'uploads/shop_logo/') === false) {
                    $shop_logo = 'uploads/shop_logo/' . $shop_logo;
                }
            } else {
                $shop_logo = 'uploads/shop_logo/logo.jpg';
            }

            $shops[] = array(
                'id' => $row['user_id'],
                'shop_id' => $row['id'],
                'name' => htmlspecialchars($row['shop_name']),
                'logo' => $shop_logo,
                'encrypted_id' => URLSecurity::encryptId($row['id'])
            );
        }

        echo json_encode($shops);

    } catch (Exception $e) {
        echo json_encode(array('error' => 'Database error occurred'));
    }
} else {
    echo json_encode(array('error' => 'No search query provided'));
}

exit;
?>