<?php

namespace App\Security;

/**
 * OpenSSL encryption helper for sensitive application values.
 */
class Encrypter
{
    private string $key;

    public function __construct(string $key)
    {
        if ($key === '') {
            throw new \InvalidArgumentException('Encryption key cannot be empty.');
        }

        $this->key = hash('sha256', $key, true);
    }

    /**
     * Encrypt a string value.
     *
     * @param string $value Plain text.
     * @return string Base64 encoded payload.
     */
    public function encrypt(string $value): string
    {
        $iv = random_bytes(16);
        $cipherText = openssl_encrypt($value, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $iv);

        if ($cipherText === false) {
            throw new \RuntimeException('Encryption failed.');
        }

        return base64_encode($iv . $cipherText);
    }

    /**
     * Decrypt an encrypted payload.
     *
     * @param string $payload Base64 encoded payload.
     * @return string Plain text.
     */
    public function decrypt(string $payload): string
    {
        $decoded = base64_decode($payload, true);

        if ($decoded === false || strlen($decoded) < 17) {
            throw new \InvalidArgumentException('Invalid encrypted payload.');
        }

        $iv = substr($decoded, 0, 16);
        $cipherText = substr($decoded, 16);
        $plainText = openssl_decrypt($cipherText, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $iv);

        if ($plainText === false) {
            throw new \RuntimeException('Decryption failed.');
        }

        return $plainText;
    }
}
