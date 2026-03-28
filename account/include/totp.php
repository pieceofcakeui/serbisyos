<?php
class TOTP
{
    private $secret;
    private $digits;
    private $period;
    private $algorithm;

    /**
     * Constructor
     * 
     * @param string $secret
     * @param int $digits
     * @param int $period
     * @param string $algorithm
     */
    public function __construct($secret = null, $digits = 6, $period = 30, $algorithm = 'sha1')
    {
        $this->digits = $digits;
        $this->period = $period;
        $this->algorithm = $algorithm;

        if ($secret) {
            $this->secret = $secret;
        } else {
            $this->secret = $this->generateSecret();
        }
    }

    /**
     * Generate a random secret key
     * 
     * @param int $length
     * @return string
     */
    public function generateSecret($length = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[ord($bytes[$i]) % strlen($chars)];
        }

        return $secret;
    }

    /**
     * Get the current secret key
     * 
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Generate a TOTP code
     * 
     * @param int|null $timestamp
     * @return string
     */
    public function generateCode($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        $counter = floor($timestamp / $this->period);

        $secret = $this->base32Decode($this->secret);

        $binary = pack('N*', 0, $counter);

        $hash = hash_hmac($this->algorithm, $binary, $secret, true);

        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
        $value = ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF);

        $otp = $value % pow(10, $this->digits);

        return str_pad($otp, $this->digits, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a TOTP code
     * 
     * @param string $code
     * @param int $window
     * @param int|null $timestamp
     * @return bool
     */
    public function verifyCode($code, $window = 1, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        $code = (int) $code;

        for ($i = -$window; $i <= $window; $i++) {
            $check_timestamp = $timestamp + ($i * $this->period);
            $check_code = (int) $this->generateCode($check_timestamp);

            if ($check_code === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a QR code URL for the secret key
     * 
     * @param string $issuer
     * @param string $account
     * @return string
     */
    public function getQRCodeUrl($issuer, $account)
    {
        $issuer = rawurlencode($issuer);
        $account = rawurlencode($account);
        $secret = rawurlencode($this->secret);

        $url = "otpauth://totp/{$issuer}:{$account}?secret={$secret}&issuer={$issuer}";
        $url .= "&digits={$this->digits}&period={$this->period}&algorithm={$this->algorithm}";

        return $url;
    }

    /**
     * Generate backup codes
     * 
     * @param int $count
     * @param int $length
     * @return array
     */
    public function generateBackupCodes($count = 8, $length = 8)
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = $this->generateBackupCode($length);
        }

        return $codes;
    }

    /**
     * Generate a single backup code
     * 
     * @param int $length
     * @return string
     */
    private function generateBackupCode($length = 8)
    {
        $chars = '0123456789';
        $code = '';

        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[ord($bytes[$i]) % strlen($chars)];
        }

        if ($length >= 6) {
            $middle = floor($length / 2);
            $code = substr($code, 0, $middle) . '-' . substr($code, $middle);
        }

        return $code;
    }

    /**
     * Base32 decode
     * 
     * @param string $string
     * @return string
     */
    private function base32Decode($string)
    {
        $string = strtoupper($string);
        $string = str_replace('=', '', $string);

        $lookup = [
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
            'E' => 4,
            'F' => 5,
            'G' => 6,
            'H' => 7,
            'I' => 8,
            'J' => 9,
            'K' => 10,
            'L' => 11,
            'M' => 12,
            'N' => 13,
            'O' => 14,
            'P' => 15,
            'Q' => 16,
            'R' => 17,
            'S' => 18,
            'T' => 19,
            'U' => 20,
            'V' => 21,
            'W' => 22,
            'X' => 23,
            'Y' => 24,
            'Z' => 25,
            '2' => 26,
            '3' => 27,
            '4' => 28,
            '5' => 29,
            '6' => 30,
            '7' => 31
        ];

        $result = '';
        $buffer = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($string); $i++) {
            $buffer = ($buffer << 5) | $lookup[$string[$i]];
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $result .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
                $bitsLeft -= 8;
            }
        }

        return $result;
    }
}
?>