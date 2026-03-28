<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

$rateLimitWindow = 60;
$maxRequests = 10;
$ip = $_SERVER['REMOTE_ADDR'];

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    http_response_code(500);
    echo json_encode(['error' => ['message' => 'Configuration error: .env file not found.']]);
    exit;
}

$apiKey = $_ENV['GEMINI_API_KEY'];

if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => ['message' => 'Configuration error: GEMINI_API_KEY is not set in your .env file.']]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => ['message' => 'Method Not Allowed']]);
    exit;
}

$rateLimitKey = 'rate_limit_' . $ip;
$currentTime = time();

if (!isset($_SESSION[$rateLimitKey])) {
    $_SESSION[$rateLimitKey] = [
        'count' => 1,
        'timestamp' => $currentTime
    ];
} else {
    $rateData = $_SESSION[$rateLimitKey];
    
    if (($currentTime - $rateData['timestamp']) > $rateLimitWindow) {
        $_SESSION[$rateLimitKey] = [
            'count' => 1,
            'timestamp' => $currentTime
        ];
    } else {
        if ($rateData['count'] >= $maxRequests) {
            http_response_code(429);
            echo json_encode([
                'error' => [
                    'message' => 'Too many requests. Please wait a moment and try again.',
                    'retry_after' => $rateLimitWindow - ($currentTime - $rateData['timestamp'])
                ]
            ]);
            exit;
        }
        
        $_SESSION[$rateLimitKey]['count']++;
    }
}

$json_data = file_get_contents('php://input');
$requestData = json_decode($json_data, true);
$requestSize = strlen($json_data);

if ($requestSize > 4000000) {
    http_response_code(413);
    echo json_encode(['error' => ['message' => 'Request too large. Please reduce image size or text length.']]);
    exit;
}

$geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $geminiApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    error_log('Gemini API cURL Error: ' . $error_msg);
    
    http_response_code(500);
    echo json_encode(['error' => ['message' => 'Service temporarily unavailable: ' . $error_msg]]);
} else {
    http_response_code($httpcode);

    if ($httpcode !== 200) {
        $responseData = json_decode($response, true);
        if (!isset($responseData['error'])) {
            $responseData = ['error' => ['message' => $response]];
        }
        echo json_encode($responseData);
    } else {
        echo $response;
    }
}

curl_close($ch);