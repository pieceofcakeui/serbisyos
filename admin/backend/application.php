<?php
include 'db_connection.php';
include 'config.php';

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : '';
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : '';

if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="shop_applications.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, [
        'ID',
        'Shop Name',
        'Business Type',
        'Owner Name',
        'Email',
        'Status',
        'Shop Status',
        'Services Offered',
        'Phone',
        'Town/City',
        'Province',
        'Country',
        'Postal Code',
        'Years in Operation',
        'Business Permit',
        'Tax ID',
        'Applied At',
        'Opening Time',
        'Closing Time',
        'Open 24/7',
        'Days Open'
    ]);

    $where = [];
    if ($status_filter != 'all') {
        $where[] = "status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
    }
    if (!empty($date_start)) {
        $where[] = "applied_at >= '" . mysqli_real_escape_string($conn, $date_start) . " 00:00:00'";
    }
    if (!empty($date_end)) {
        $where[] = "applied_at <= '" . mysqli_real_escape_string($conn, $date_end) . " 23:59:59'";
    }
    $where_clause = !empty($where) ? " WHERE " . implode(' AND ', $where) : "";

    $services_map = [];
    $services_query = "SELECT ss.application_id, s.name 
                         FROM shop_services ss
                         JOIN services s ON ss.service_id = s.id
                         JOIN shop_applications sa ON ss.application_id = sa.id" . $where_clause;
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
                 id, shop_name, business_type, owner_name, email, status, 
                 shop_status,
                 phone, town_city, province, country,
                 postal_code, years_operation,
                 business_permit, tax_id, applied_at,
                 TIME_FORMAT(opening_time, '%H:%i') as opening_time,
                 TIME_FORMAT(closing_time, '%H:%i') as closing_time,
                 open_24_7,
                 days_open
               FROM shop_applications" . $where_clause . " ORDER BY id ASC";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $decrypted_business_permit = decryptData($row['business_permit']);
            $decrypted_tax_id = decryptData($row['tax_id']);
            $application_id = $row['id'];
            $services_string = isset($services_map[$application_id]) ? implode('; ', $services_map[$application_id]) : '';

            $csv_row = [
                $row['id'],
                $row['shop_name'],
                $row['business_type'],
                $row['owner_name'],
                $row['email'],
                $row['status'],
                $row['shop_status'] ?? 'N/A',
                $services_string,
                $row['phone'],
                $row['town_city'],
                $row['province'],
                $row['country'],
                $row['postal_code'],
                $row['years_operation'],
                $decrypted_business_permit,
                $decrypted_tax_id,
                $row['applied_at'],
                $row['opening_time'],
                $row['closing_time'],
                $row['open_24_7'] ? 'Yes' : 'No',
                str_replace(',', ', ', $row['days_open'])
            ];
            fputcsv($output, $csv_row);
        }
    }
    fclose($output);
    exit();
}
?>