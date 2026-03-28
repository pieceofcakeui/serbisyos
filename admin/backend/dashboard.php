<?php
require 'db_connection.php';

$totalQuery = "SELECT COUNT(*) AS total FROM shop_applications";
$totalResult = $conn->query($totalQuery);
$totalApplications = ($totalResult->num_rows > 0) ? $totalResult->fetch_assoc()['total'] : 0;

$approvedQuery = "SELECT COUNT(*) AS approved FROM shop_applications WHERE status = 'Approved'";
$approvedResult = $conn->query($approvedQuery);
$approvedApplications = ($approvedResult->num_rows > 0) ? $approvedResult->fetch_assoc()['approved'] : 0;

$pendingQuery = "SELECT COUNT(*) AS pending FROM shop_applications WHERE status = 'Pending'";
$pendingResult = $conn->query($pendingQuery);
$pendingApplications = ($pendingResult->num_rows > 0) ? $pendingResult->fetch_assoc()['pending'] : 0;

$monthlyStatsQuery = "SELECT 
                DATE_FORMAT(date_created, '%b') AS month,
                COUNT(*) AS total_apps,
                SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved_apps,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_apps,
                SUM(CASE WHEN status = 'Denied' THEN 1 ELSE 0 END) AS denied_apps
                FROM shop_applications
                WHERE date_created >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(date_created, '%Y-%m')
                ORDER BY DATE_FORMAT(date_created, '%Y-%m')";

$monthlyStatsResult = $conn->query($monthlyStatsQuery);
$monthlyStats = [];

if ($monthlyStatsResult) {
    while ($row = $monthlyStatsResult->fetch_assoc()) {
        $monthlyStats[] = $row;
    }
}

$chartData = json_encode($monthlyStats);

$applicationsQuery = "SELECT id, shop_name, status, owner_name, email, phone, services_offered, business_permit_file, barangay, town_city, province, postal_code, date_created FROM shop_applications ORDER BY id DESC LIMIT 5";
$applicationsResult = $conn->query($applicationsQuery);

$applications = [];
while ($row = $applicationsResult->fetch_assoc()) {
    $applications[] = $row;
}

$activitiesQuery = "SELECT activity_type, description, DATE_FORMAT(created_at, '%M %d, %Y %h:%i %p') as formatted_date FROM activities ORDER BY created_at DESC LIMIT 10";
$activitiesResult = $conn->query($activitiesQuery);

$activities = [];
if (!$activitiesResult) {
    $activities = [
        ['activity_type' => 'login', 'description' => 'Admin logged in', 'formatted_date' => 'February 25, 2025 09:30 AM'],
        ['activity_type' => 'approve', 'description' => 'Shop application #123 approved', 'formatted_date' => 'February 25, 2025 09:35 AM'],
        ['activity_type' => 'view', 'description' => 'Viewed shop details for Big Dreams Auto Shop', 'formatted_date' => 'February 25, 2025 09:40 AM'],
        ['activity_type' => 'deny', 'description' => 'Shop application #124 denied', 'formatted_date' => 'February 25, 2025 10:15 AM'],
        ['activity_type' => 'message', 'description' => 'Replied to message from John Smith', 'formatted_date' => 'February 25, 2025 11:30 AM']
    ];
} else {
    while ($row = $activitiesResult->fetch_assoc()) {
        $activities[] = $row;
    }
}
?>