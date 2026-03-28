<?php
header('Content-Type: application/json');

require_once 'db_connection.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $service = isset($_POST['service']) ? trim($_POST['service']) : '';
    $brand = isset($_POST['brand']) ? trim($_POST['brand']) : '';
    
    $sql = "SELECT * FROM shop_applications WHERE status = 'Approved'";
    $params = [];

    if (!empty($service)) {
        $sql .= " AND services_offered LIKE :service";
        $params[':service'] = "%$service%";
    }
 
    if (!empty($brand)) {
        $sql .= " AND brand_serviced LIKE :brand";
        $params[':brand'] = "%$brand%";
    }
    
    $sql .= " LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    $shops = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($shops);
    
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>