<?php
include 'db_connection.php';

header('Content-Type: application/json');

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = trim($_GET['query']);
    $query = "%$query%";
    
    $sql = "SELECT DISTINCT 
            town_city as name,
            'city' as type
            FROM shop_applications 
            WHERE town_city LIKE ? AND status = 'Approved'
            
            UNION
            
            SELECT DISTINCT 
            barangay as name,
            'barangay' as type
            FROM shop_applications 
            WHERE barangay LIKE ? AND status = 'Approved'
            
            UNION
            
            SELECT DISTINCT 
            shop_location as name,
            'address' as type
            FROM shop_applications 
            WHERE shop_location LIKE ? AND status = 'Approved'
            
            ORDER BY name
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $query, $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $locations = [];
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
    
    echo json_encode($locations);
} else {
    echo json_encode([]);
}
?>