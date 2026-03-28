<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if (empty($_ENV['ENCRYPTION_KEY'])) {
    die('Encryption key is not set in the .env file.');
}

define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY']);
define('ENCRYPTION_METHOD', 'AES-256-CBC');

function encryptData($data) {
    if ($data === null || $data === '') {
        return $data;
    }
    
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    
    return base64_encode($iv . $encrypted);
}

function decryptData($data) {
    if ($data === null || $data === '') {
        return $data;
    }

    $decodedData = base64_decode($data, true);
    if ($decodedData === false) {
        return false;
    }

    $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    if (strlen($decodedData) < $ivLength) {
        return false;
    }
    
    $iv = substr($decodedData, 0, $ivLength);
    $encrypted = substr($decodedData, $ivLength);

    return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}