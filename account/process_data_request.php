<?php
date_default_timezone_set('Asia/Manila');

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require_once 'backend/db_connection.php';

set_time_limit(300);

/**
 * 
 * 
 * @param mysqli $conn
 * @param int $user_id
 * @param array $types
 * @return array
 */
function fetch_user_data($conn, $user_id, $types)
{
    $data = [];

    if (in_array('personal', $types)) {
        $stmt = $conn->prepare("SELECT fullname, email, profile_type, contact_number, barangay, town, province, postal_code, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $data['personal'] = $res->fetch_assoc();
        $stmt->close();
    }

    if (in_array('shop_applications', $types)) {
        $check = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();
        $check->close();

        if ($result && $result['profile_type'] === 'owner') {
           // process_data_request.php (Line 301)
$stmt = $conn->prepare("SELECT 
    shop_name, business_type, owner_name, years_operation, email, phone, 
    website, instagram, facebook, business_permit, tax_id, barangay, 
    town_city, province, postal_code, latitude, longitude, services_offered, 
    brands_serviced, applied_at, approved_at, description, business_hours,
    opening_time, closing_time, open_24_7, days_open
FROM shop_applications WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $data['shop_applications'] = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
    }

    return $data;
}

$old_time = date('Y-m-d H:i:s', time() - 1800);
$update_stuck = $conn->prepare("UPDATE data_download_requests SET status = 'failed' WHERE status = 'processing' AND request_date < ?");
$update_stuck->bind_param("s", $old_time);
$update_stuck->execute();
$update_stuck->close();

$update_stmt = $conn->prepare("UPDATE data_download_requests SET status = 'processing' WHERE status = 'pending'");
$update_stmt->execute();
$update_stmt->close();

$sql = "SELECT * FROM data_download_requests WHERE status = 'processing'";
$result = $conn->query($sql);

$processed_count = 0;
$failed_count = 0;

while ($request = $result->fetch_assoc()) {
    try {
        $user_id = $request['user_id'];
        $request_id = $request['id'];

        $data_types_raw = json_decode($request['data_types'], true);

        if (isset($data_types_raw['types'])) {
            $types = $data_types_raw['types'];
        } else {
            $types = $data_types_raw;
        }

        if (!is_array($types)) {
            throw new Exception("Invalid data types format for request #$request_id");
        }

        $data = fetch_user_data($conn, $user_id, $types);

        $download_dir = __DIR__ . "/downloads";
        if (!is_dir($download_dir)) {
            if (!mkdir($download_dir, 0755, true)) {
                throw new Exception("Failed to create download directory");
            }

            $htaccess = "Order deny,allow\nDeny from all\nAllow from 127.0.0.1\n";
            file_put_contents($download_dir . "/.htaccess", $htaccess);
        }

        $timestamp = time();
        $filename = "userdata_" . $user_id . "_" . $request_id . "_" . $timestamp . ".json";
        $filepath = "$download_dir/$filename";

        if (file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
            throw new Exception("Failed to write data file");
        }

        $zipfile = str_replace(".json", ".zip", $filepath);
        $zip = new ZipArchive();
        if ($zip->open($zipfile, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($filepath, basename($filename));
            $zip->close();
            unlink($filepath);
            $final_file = basename($zipfile);
        } else {
            $final_file = basename($filename);
        }

        $download_url = "downloads/" . $final_file;
        $completion_date = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("UPDATE data_download_requests SET status = 'completed', download_url = ?, completion_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $download_url, $completion_date, $request_id);
        $stmt->execute();
        $stmt->close();

        $processed_count++;

    } catch (Exception $e) {
        error_log("Error processing request #$request_id: " . $e->getMessage());

        $stmt = $conn->prepare("UPDATE data_download_requests SET status = 'failed', completion_date = ? WHERE id = ?");
        $completion_date = date('Y-m-d H:i:s');
        $stmt->bind_param("si", $completion_date, $request_id);
        $stmt->execute();
        $stmt->close();

        $failed_count++;
    }
}

echo "Processing completed. Processed: $processed_count, Failed: $failed_count\n";
$conn->close();
?>