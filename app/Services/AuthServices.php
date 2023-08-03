<?php

namespace App\Services;

use App\Classes\Entities\AuthorizationEntity;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;
use TypeError;

class AuthServices
{
    private static string $secret_key = 'secret_Hipper';

    private static string $encryptSSL = 'RS256';

    private static string $passphrase = 'ultra_save';

    public static function AudStrict(): string
    {
        $aud = '';
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return md5($aud);
    }

    public static function getData(string $token): AuthorizationEntity
    {
        if (file_get_contents(__DIR__.'/../keys/key.public.pem')) {

            $keyPublic = openssl_get_publickey(file_get_contents(__DIR__.'/../keys/key.public.pem'));

            if ($keyPublic) {

                $dataToken = JWT::decode($token, new Key($keyPublic, self::$encryptSSL));
                $data = self::secured_decrypt((string)$dataToken->data);
                return new AuthorizationEntity($data);
            }
            throw new Exception('No found key public');
        }
        throw new Exception('No found file : keys/key.public.pem');
    }

    private static function secured_decrypt(string $inputData): array
    {
        $first_key = base64_decode(self::$secret_key);
        $second_key = base64_decode(self::$passphrase);
        $cypherMethod = 'AES-256-CBC';

        $mix = base64_decode($inputData);
        $iv_length = intval(openssl_cipher_iv_length($cypherMethod));
        $ivX = intval(substr($mix, 0, $iv_length));
        $second_encryptedX = substr($mix, $iv_length, 32);
        $first_encryptedX = substr($mix, $iv_length + 32);

        $data = openssl_decrypt($first_encryptedX, $cypherMethod, $first_key, OPENSSL_RAW_DATA, (string)$ivX);
        $second_encrypted_new = hash_hmac('sha256', utf8_encode($first_encryptedX), $second_key, true);

        if ($second_encryptedX == $second_encrypted_new && $data) {
            return (array)unserialize($data);
        }

        return [];
    }
}
