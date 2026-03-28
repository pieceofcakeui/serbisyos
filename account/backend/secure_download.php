<?php
session_start();
require_once 'db_connection.php';
require_once 'auth.php';
require_once 'security_helper.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die('Unauthorized access');
}

$user_id = $_SESSION['user_id'];
$encrypted_id = $_GET['id'] ?? null;

if (!$encrypted_id) {
    http_response_code(400);
    die('Invalid request ID');
}

$request_id = URLSecurity::decryptId($encrypted_id);

if (!$request_id || $request_id <= 0) {
    http_response_code(400);
    die('Invalid or corrupted request ID');
}

$stmt = $conn->prepare("SELECT download_url, status, user_id, completion_date FROM data_download_requests WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    die('Request not found');
}

$request = $result->fetch_assoc();

if ($request['user_id'] != $user_id) {
    http_response_code(403);
    die('Access denied - This is not your request');
}

if ($request['status'] !== 'completed') {
    http_response_code(400);
    die('Request not completed yet');
}

if (!empty($request['completion_date'])) {
    $completion_time = strtotime($request['completion_date']);
    $expiry_time = $completion_time + (7 * 24 * 60 * 60); // 7 days
    
    if (time() > $expiry_time) {
        $filepath = dirname(__DIR__) . '/' . $request['download_url'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        $update_stmt = $conn->prepare("UPDATE data_download_requests SET status = 'expired', download_url = NULL WHERE id = ?");
        $update_stmt->bind_param("i", $request_id);
        $update_stmt->execute();
        
        http_response_code(410);
        die('Download has expired (7 days from completion date)');
    }
}

$filepath = dirname(__DIR__) . '/' . $request['download_url'];

if (!file_exists($filepath)) {
    http_response_code(404);
    die('File not found');
}

error_log("Download accessed: User {$user_id} downloaded request {$request_id}");

$filename = basename($filepath);

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($filepath);

unlink($filepath);
$update_stmt = $conn->prepare("UPDATE data_download_requests SET download_url = NULL WHERE id = ?");
$update_stmt->bind_param("i", $request_id);
$update_stmt->execute();

exit;
?>