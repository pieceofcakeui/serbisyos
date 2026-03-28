<?php
class EncryptionHelper
{
    private static $method = 'aes-256-cbc';

    public static function encrypt($data, &$encryption_key)
    {
        if (empty($data))
            return $data;

        if (empty($encryption_key)) {
            $encryption_key = bin2hex(random_bytes(32));
        }

        $iv = random_bytes(openssl_cipher_iv_length(self::$method));
        $encrypted = openssl_encrypt($data, self::$method, hex2bin($encryption_key), 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data, $encryption_key)
    {
        if (empty($data) || empty($encryption_key))
            return $data;

        $data = base64_decode($data);
        $iv_length = openssl_cipher_iv_length(self::$method);
        $iv = substr($data, 0, $iv_length);
        $encrypted = substr($data, $iv_length);

        return openssl_decrypt($encrypted, self::$method, hex2bin($encryption_key), 0, $iv);
    }
}
?>