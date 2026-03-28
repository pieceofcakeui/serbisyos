<?php
date_default_timezone_set('UTC');

if (php_sapi_name() !== 'cli' && !isset($_GET['cron_key'])) {

     if (php_sapi_name() !== 'cli') {
        die("This script can only be run from the command line (CLI).");
     }
}

require_once 'backend/db_connection.php';

echo "--- Starting Expired Data Cleanup (" . date('Y-m-d H:i:s') . " UTC) ---\n";

$cleanup_count = 0;
$error_count = 0;
$current_utc_time = (new DateTime('now'))->format('Y-m-d H:i:s');

$sql = "SELECT id, download_url FROM data_download_requests WHERE status = 'completed' AND expires_at IS NOT NULL AND expires_at < ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Error preparing select statement: " . $conn->error . "\n";
    exit;
}

$stmt->bind_param("s", $current_utc_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No expired requests found to clean up.\n";
    $conn->close();
    exit;
}

echo "Found " . $result->num_rows . " expired request(s).\n";

$update_sql = "UPDATE data_download_requests SET status = 'expired', download_url = NULL WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
if (!$update_stmt) {
    echo "Error preparing update statement: " . $conn->error . "\n";
    $conn->close();
    exit;
}

while ($row = $result->fetch_assoc()) {
    $request_id = $row['id'];
    $download_url = $row['download_url'];

    echo "Processing Request #$request_id...\n";

    if (!empty($download_url)) {
        $filepath = __DIR__ . '/' . $download_url;
        
        if (file_exists($filepath)) {
            if (unlink($filepath)) {
                echo "  [SUCCESS] Deleted file: $filepath\n";
            } else {
                echo "  [ERROR] Failed to delete file: $filepath\n";
                $error_count++;
            }
        } else {
            echo "  [INFO] File not found (already deleted?): $filepath\n";
        }
    } else {
        echo "  [INFO] No download_url associated. Skipping file delete.\n";
    }

    $update_stmt->bind_param("i", $request_id);
    if ($update_stmt->execute()) {
        echo "  [SUCCESS] Marked request #$request_id as 'expired' in DB.\n";
        $cleanup_count++;
    } else {
        echo "  [ERROR] Failed to update DB for request #$request_id: " . $update_stmt->error . "\n";
        $error_count++;
    }
}

$stmt->close();
$update_stmt->close();
$conn->close();

echo "--- Cleanup Finished ---\n";
echo "Total processed: $cleanup_count\n";
echo "Total errors: $error_count\n";
?>