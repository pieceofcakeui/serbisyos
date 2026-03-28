<?php
session_start();
include 'db_connection.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all-users';

$whereClause = "";
$params = [];
$types = "";

if (!empty($search)) {
    $searchTerm = "%$search%";
    $whereClause = "WHERE (fullname LIKE ? OR email LIKE ? OR profile_type LIKE ? OR account_state LIKE ?)";
    $types = "ssss";
    $params = array_fill(0, 4, $searchTerm);
}

if ($tab === 'owners') {
    $whereClause .= empty($whereClause) ? "WHERE profile_type = 'owner'" : " AND profile_type = 'owner'";
}

$sql = "SELECT u.id, u.fullname, u.email, u.profile_type, u.account_state, u.last_login, u.created_at, u.profile_picture, s.shop_logo 
        FROM users u 
        LEFT JOIN shop_applications s ON u.id = s.user_id 
        $whereClause";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$users = [];

function getDaysInactive($lastActivity, $createdAt) {
    if ($lastActivity) {
        $lastActivityDate = new DateTime($lastActivity);
    } else if ($createdAt) {
        $lastActivityDate = new DateTime($createdAt);
    } else {
        return 0;
    }
    
    $now = new DateTime();
    return $now->diff($lastActivityDate)->days;
}

while ($row = $result->fetch_assoc()) {
    $daysInactive = getDaysInactive($row['last_login'], $row['created_at']);
    
    $users[] = [
        'id' => $row['id'],
        'fullname' => $row['fullname'],
        'email' => $row['email'],
        'profile_type' => $row['profile_type'],
        'account_state' => $row['account_state'],
        'days_inactive' => $daysInactive,
        'last_activity' => $row['last_login'] ?: $row['created_at'],
        'profile_picture' => $row['profile_picture'],
        'shop_logo' => $row['shop_logo']
    ];
}

header('Content-Type: application/json');
echo json_encode($users);

$conn->close();
?>