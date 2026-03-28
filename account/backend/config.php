<?php
if (!defined('ENCRYPTION_KEY')) {
    define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY'] ?? null);
}

if (!defined('ENCRYPTION_METHOD')) {
    define('ENCRYPTION_METHOD', 'AES-256-CBC');
}

if (!function_exists('encryptData')) {
    function encryptData($data) {
        if (empty($data) || !ENCRYPTION_KEY) {
            return $data;
        }
        $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
}

if (!function_exists('decryptData')) {
    function decryptData($data) {
        if (empty($data) || !ENCRYPTION_KEY) {
            return $data;
        }
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    }
}
?>