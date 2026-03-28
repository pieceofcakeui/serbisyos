<?php
session_start();
header('Content-Type: application/json');
include 'db_connection.php';

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

define('ENCRYPTION_KEY',  $_ENV['ENCRYPTION_KEY']);
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('ENCRYPTION_IV_LENGTH', openssl_cipher_iv_length(ENCRYPTION_METHOD));

function decryptData($data)
{
    if (empty($data)) {
        error_log("DecryptData: Empty input received");
        return null;
    }

    try {
        $decoded = base64_decode($data);
        if ($decoded === false) {
            error_log("Base64 decoding failed for data: " . substr($data, 0, 20) . "...");
            return null;
        }

        if (strlen($decoded) < ENCRYPTION_IV_LENGTH) {
            error_log("Data too short for decryption. Length: " . strlen($decoded) .
                ", needed: " . ENCRYPTION_IV_LENGTH);
            return null;
        }

        $iv = substr($decoded, 0, ENCRYPTION_IV_LENGTH);
        $encrypted = substr($decoded, ENCRYPTION_IV_LENGTH);

        $decrypted = openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);

        if ($decrypted === false) {
            $error = openssl_error_string();
            error_log("OpenSSL decryption failed: $error");
            return null;
        }

        return $decrypted;
    } catch (Exception $e) {
        error_log("Decryption exception: " . $e->getMessage());
        return null;
    }
}

if (!isset($_GET['request_id']) || !is_numeric($_GET['request_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid request ID is required']);
    exit;
}

$requestId = (int) $_GET['request_id'];

try {
    $query = "SELECT 
                er.*, 
                u.fullname AS requester_name,
                u.profile_picture AS requester_photo
              FROM emergency_requests er
              JOIN users u ON er.user_id = u.id
              WHERE er.id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Request not found']);
        exit;
    }

    $request = $result->fetch_assoc();

    $decrypted_data = [
        'full_address' => decryptData($request['full_address']) ?? '[Address unavailable]',
        'contact_number' => decryptData($request['contact_number']) ?? '[Contact unavailable]',
        'latitude' => decryptData($request['latitude']) ?? 0,
        'longitude' => decryptData($request['longitude']) ?? 0
    ];

    $video = null;
    if (!empty($request['video'])) {
        $videoFilenames = json_decode($request['video'], true);
        if (is_array($videoFilenames) && !empty($videoFilenames)) {
            $video = $videoFilenames[0];
        } else if (is_string($request['video']) && !empty($request['video'])) {
            $video = $request['video'];
        }
    }

    $response = [
        'success' => true,
        'request' => [
            'id' => $request['id'],
            'requester_name' => $request['requester_name'],
            'requester_photo' => $request['requester_photo'] ?? null,
            'vehicle_type' => $request['vehicle_type'],
            'vehicle_model' => $request['vehicle_model'],
            'issue_description' => $request['issue_description'],
            'full_address' => $decrypted_data['full_address'],
            'contact_number' => $decrypted_data['contact_number'],
            'urgent' => (bool) $request['urgent'],
            'status' => $request['status'],
            'video' => $video,
            'coordinates' => [
                'latitude' => (float) $decrypted_data['latitude'],
                'longitude' => (float) $decrypted_data['longitude']
            ],
            'created_at' => $request['created_at'],
            'updated_at' => $request['updated_at'] ?? null
        ]
    ];

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    error_log("API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred',
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

?>