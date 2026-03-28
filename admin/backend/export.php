<?php
require 'db_connection.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=approved_applications_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'ID', 
    'Shop Name', 
    'Status', 
    'Owner Name', 
    'Email', 
    'Phone', 
    'Services Offered', 
    'Barangay', 
    'Town/City', 
    'Province', 
    'Postal Code', 
    'Date Created',
    'Opening Time',
    'Closing Time',
    'Open 24/7',
    'Days Open',
    'Business Hours Summary'
]);

$services_map = [];
$services_query = "SELECT ss.application_id, s.name 
                   FROM shop_services ss
                   JOIN services s ON ss.service_id = s.id
                   JOIN shop_applications sa ON ss.application_id = sa.id
                   WHERE LOWER(sa.status) = 'approved'
                   ORDER BY ss.application_id, s.name";
$services_result = $conn->query($services_query);
if ($services_result) {
    while ($service_row = $services_result->fetch_assoc()) {
        $app_id = $service_row['application_id'];
        if (!isset($services_map[$app_id])) {
            $services_map[$app_id] = [];
        }
        $services_map[$app_id][] = $service_row['name'];
    }
}

$query = "SELECT 
            id, 
            shop_name, 
            status, 
            owner_name, 
            email, 
            phone, 
            barangay, 
            town_city, 
            province, 
            postal_code, 
            date_created,
            TIME_FORMAT(opening_time, '%H:%i') as opening_time,
            TIME_FORMAT(closing_time, '%H:%i') as closing_time,
            open_24_7,
            days_open
          FROM shop_applications
          WHERE LOWER(status) = 'approved'";
          
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $business_hours_summary = '';
        if ($row['open_24_7']) {
            $business_hours_summary = 'Open 24/7';
        } elseif (!empty($row['opening_time']) && !empty($row['closing_time'])) {
            $business_hours_summary = $row['opening_time'] . ' - ' . $row['closing_time'];
        }

        $days_open = !empty($row['days_open']) ? str_replace(',', ', ', $row['days_open']) : '';

        $application_id = $row['id'];
        $services_string = isset($services_map[$application_id]) ? implode('; ', $services_map[$application_id]) : 'N/A';

        $csv_row = [
            $row['id'],
            $row['shop_name'],
            $row['status'],
            $row['owner_name'],
            $row['email'],
            $row['phone'],
            $services_string,
            $row['barangay'],
            $row['town_city'],
            $row['province'],
            $row['postal_code'],
            $row['date_created'],
            $row['opening_time'],
            $row['closing_time'],
            $row['open_24_7'] ? 'Yes' : 'No',
            $days_open,
            $business_hours_summary
        ];
        
        fputcsv($output, $csv_row);
    }
}

fclose($output);
exit;
?>