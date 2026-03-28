<?php
if (!extension_loaded('openssl')) {
    die('OpenSSL extension is required for this application');
}

if (!class_exists('URLSecurity')) {
    class URLSecurity {
        private static $key = 'aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890ab==';
        private static $cipher = 'aes-256-cbc';

        public static function encryptId($id) {
            if (!is_numeric($id) || $id <= 0) {
                return '';
            }

            $iv_length = openssl_cipher_iv_length(self::$cipher);
            $iv = openssl_random_pseudo_bytes($iv_length);
            $encrypted = openssl_encrypt($id, self::$cipher, self::$key, OPENSSL_RAW_DATA, $iv);

            $combined = $iv . $encrypted;

            return rtrim(strtr(base64_encode($combined), '+/', '-_'), '=');
        }

        public static function decryptId($encrypted) {
            if (empty($encrypted)) {
                return 0;
            }

            $decoded = base64_decode(strtr($encrypted, '-_', '+/'));

            if ($decoded === false) {
                error_log("Failed to base64 decode the encrypted ID.");
                return 0;
            }

            $iv_length = openssl_cipher_iv_length(self::$cipher);
            $iv = substr($decoded, 0, $iv_length);
            $encrypted_data = substr($decoded, $iv_length);

            $decrypted = openssl_decrypt($encrypted_data, self::$cipher, self::$key, OPENSSL_RAW_DATA, $iv);

            if ($decrypted === false) {
                error_log("Failed to decrypt the ID.");
                return 0;
            }

            return (int)$decrypted;
        }
    }
}
?>