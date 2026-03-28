<?php
class MessageEncryption {
    private $encryption_key;
    private $cipher_method = 'AES-256-CBC';
    
    public function __construct($key = null) {
        if ($key) {
            $this->encryption_key = $key;
        } else {
            $this->encryption_key = $this->getOrCreateEncryptionKey();
        }
    }

    public function encrypt($message) {
        if (empty($message)) {
            return '';
        }
        
        try {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher_method));

            $encrypted = openssl_encrypt($message, $this->cipher_method, $this->encryption_key, 0, $iv);
            
            if ($encrypted === false) {
                throw new Exception('Encryption failed');
            }

            return base64_encode($iv . $encrypted);
            
        } catch (Exception $e) {
            error_log('Encryption error: ' . $e->getMessage());
            throw new Exception('Failed to encrypt message');
        }
    }

    public function decrypt($encryptedMessage) {
        if (empty($encryptedMessage)) {
            return '';
        }
        
        try {
            $data = base64_decode($encryptedMessage);
            
            if ($data === false) {
                throw new Exception('Invalid encrypted data format');
            }

            $iv_length = openssl_cipher_iv_length($this->cipher_method);
            $iv = substr($data, 0, $iv_length);
            $encrypted = substr($data, $iv_length);

            $decrypted = openssl_decrypt($encrypted, $this->cipher_method, $this->encryption_key, 0, $iv);
            
            if ($decrypted === false) {
                throw new Exception('Decryption failed');
            }
            
            return $decrypted;
            
        } catch (Exception $e) {
            error_log('Decryption error: ' . $e->getMessage());
            return $encryptedMessage;
        }
    }

    private function getOrCreateEncryptionKey() {
        $keyFile = __DIR__ . '/encryption_key.txt';

        if (file_exists($keyFile)) {
            $key = file_get_contents($keyFile);
            if ($key && strlen($key) >= 32) {
                return $key;
            }
        }

        $key = bin2hex(random_bytes(32));

        if (file_put_contents($keyFile, $key) === false) {
            throw new Exception('Cannot create encryption key file');
        }

        chmod($keyFile, 0600);
        
        return $key;
    }

    public function test() {
        $testMessage = "Hello, this is a test message with special characters: àáâãäå!@#$%^&*()";
        $encrypted = $this->encrypt($testMessage);
        $decrypted = $this->decrypt($encrypted);
        
        return [
            'original' => $testMessage,
            'encrypted' => $encrypted,
            'decrypted' => $decrypted,
            'success' => ($testMessage === $decrypted)
        ];
    }
}
?>