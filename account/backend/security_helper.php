<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


if (!extension_loaded('openssl')) {
    die('OpenSSL extension is required for this application');
}

if (!class_exists('URLSecurity')) {
    class URLSecurity {
        private static $key = null;
        private static $cipher = 'aes-256-cbc';

        private static function getKey() {
            if (self::$key === null) {
                self::$key = $_ENV['SERVICES_ENCRYPTION_KEY'] ?? null;
            }
            return self::$key;
        }

        public static function encryptId($id) {
            $key = self::getKey();

            if (!is_numeric($id) || $id <= 0 || empty($key)) {
                error_log("Encryption failed: Invalid ID or key is missing.");
                return '';
            }

            $iv_length = openssl_cipher_iv_length(self::$cipher);
            $iv = openssl_random_pseudo_bytes($iv_length);
            $encrypted = openssl_encrypt($id, self::$cipher, $key, OPENSSL_RAW_DATA, $iv);

            $combined = $iv . $encrypted;

            return rtrim(strtr(base64_encode($combined), '+/', '-_'), '=');
        }

        public static function decryptId($encrypted) {
            $key = self::getKey();

            if (empty($encrypted) || empty($key)) {
                error_log("Decryption failed: Input is empty or key is missing.");
                return 0;
            }

            $decoded = base64_decode(strtr($encrypted, '-_', '+/'));

            if ($decoded === false) {
                error_log("Failed to base64 decode the encrypted ID.");
                return 0;
            }

            $iv_length = openssl_cipher_iv_length(self::$cipher);
            
            if (mb_strlen($decoded, '8bit') < $iv_length) {
                error_log("Decryption failed: Decoded data is shorter than IV length.");
                return 0;
            }
            
            $iv = substr($decoded, 0, $iv_length);
            $encrypted_data = substr($decoded, $iv_length);

            $decrypted = openssl_decrypt($encrypted_data, self::$cipher, $key, OPENSSL_RAW_DATA, $iv);

            if ($decrypted === false) {
                error_log("Failed to decrypt the ID.");
                return 0;
            }

            return (int)$decrypted;
        }
    }
}
?>