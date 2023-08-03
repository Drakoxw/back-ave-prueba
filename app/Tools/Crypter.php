<?php

namespace App\Tools;

class Crypter
{
    private static $secret_key = 'IGfMGndsjs22sA0GCSqhdhsdKBg';

    private static $secret_iv = '8901234651234567';

    private static $method = 'AES-256-CBC';

    public static function decryptRSA(string $value): string
    {
        $decrypted = '';
        $privateGet = openssl_get_privatekey(file_get_contents(dirname(__FILE__).'/../keys/privateKey1024.pem'));
        $private_key = openssl_pkey_get_private($privateGet);
        $encrypted = base64_decode($value);
        openssl_private_decrypt($encrypted, $decrypted, $private_key);

        return $decrypted;
    }

    public static function encryptRSA(string $value): string
    {
        $key_resource = openssl_get_publickey(file_get_contents(dirname(__FILE__).'/../keys/publicKey1024.pem'));
        openssl_public_encrypt($value, $crypttext, $key_resource);
        $encrypted = base64_encode($crypttext);

        return $encrypted;
    }

    public static function encryptAES(string $value): string
    {
        $iv = substr(hash('sha256', self::$secret_iv), 0, 16);
        $key = hash('sha256', self::$secret_key);

        return base64_encode(openssl_encrypt($value, self::$method, $key, 0, $iv));
    }

    public static function decryptAES(string $value): string
    {
        $iv = substr(hash('sha256', self::$secret_iv), 0, 16);
        $key = hash('sha256', self::$secret_key);
        $output = openssl_decrypt(base64_decode($value), self::$method, $key, 0, $iv);
        if ($output == false) {
            $output = '';
        }

        return $output;
    }
}
