<?php

namespace App\Tools;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthJwt {

    private static $secret_key = 'secret_key11';
    private static $encrypt = 'HS512';
    private static $encryptSSL = 'RS256';
    private static $passphrase = 'aguapanelaconlimon';

    public static function SignInCertificated($data, $hours = 24)
    {
        $keySSL = openssl_get_privatekey(file_get_contents(dirname(__FILE__).'/../keys/privateKey1024.pem'));
        $time = time();

        $dataSecure = self::secured_encrypt($data);

        $token = array(
            'iss' => $_SERVER["HTTP_HOST"] ?? 'localhost',
            'exp' => $time + (60 * 60 * $hours),
            'aud' => self::Aud(),
            'data' => $dataSecure,
            'usuario' => $data['usuario'],
            'statusCliente' => isset($data['statusCliente']) ? $data['statusCliente'] : '',
            'rol' => isset($data['rol']) ? $data['rol'] : ''
        );
        if (isset($data['i_idusuario'])){
            $token['i_idusuario'] = $data['i_idusuario'];
        }
        if (isset($data['i_idempresa'])){
            $token['enterprise'] = $data['i_idempresa'];
        }
        if (isset($data['nombreCompleto'])){
            $token['nombreCompleto'] = $data['nombreCompleto'];
        }

        return JWT::encode($token, $keySSL, self::$encryptSSL);
    }

    public static function SignInTokenSimple($data, $hours = 24)  {
        $keySSL = openssl_get_privatekey(file_get_contents(dirname(__FILE__).'/../keys/privateKey1024.pem'));
        $time = time();

        $token = [
            'iss' => $_SERVER["HTTP_HOST"] ?? '',
            'exp' => $time + (60 * 60 * $hours),
            'aud' => self::Aud(),
            'user'=> $data['user'],
            'ds'  => $data['ds'],
            'id'  => $data['id'],
            'rol' => $data['rol']
        ];
        return JWT::encode($token, $keySSL, self::$encryptSSL);

    }

    public static function Check($token)
    {
        $keyPublic = openssl_get_publickey(file_get_contents(__DIR__.'/key.public.pem'));

        if (empty($token)) { throw new Exception("Se requiere el token en Headers"); }
        $decode = JWT::decode($token, new Key($keyPublic, self::$encryptSSL ));
        return $decode;
    }

    public static function GetData($token, $ignoreOrigen = false)
    {
        $keyPublic = openssl_get_publickey(file_get_contents(__DIR__.'/key.public.pem'));
        $dataToken = JWT::decode($token, new Key($keyPublic, self::$encryptSSL ));

        if ($ignoreOrigen) {
           return $dataToken;
        }

        $data = self::secured_decrypt($dataToken->data);
        // if ($dataToken->aud !== self::Aud()) {
        //     throw new Exception("Origen de Token desconocido");
        // }

        return $data;
    }
    /**
     * Solo para extraer datos del token
     */
    public static function GetDataSimple($token)
    {
        $keyPublic = openssl_get_publickey(file_get_contents(dirname(__FILE__).'/../keys/publicKey1024.pem'));

        $dataToken = JWT::decode($token, new Key($keyPublic, self::$encryptSSL ));

        return $dataToken;
    }

    private static function Aud() {
        $aud = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        }
        // elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //     $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        // }
        // else {
        //     $aud = $_SERVER['REMOTE_ADDR'];
        // }

        // $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return md5($aud);
    }

    private static function secured_encrypt($data)
    {
        $first_key = base64_decode(self::$secret_key);
        $second_key = base64_decode(self::$passphrase);
        $cypherMethod = 'AES-256-CBC';

        $dataToEncrypt = serialize($data);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cypherMethod));

        $first_encrypted = openssl_encrypt($dataToEncrypt, $cypherMethod, $first_key, OPENSSL_RAW_DATA, $iv);
        $second_encrypted = hash_hmac('sha256', utf8_encode($first_encrypted), $second_key, TRUE);
        $output = base64_encode($iv.$second_encrypted.$first_encrypted);
        return $output;
    }

    static function secured_decrypt($inputData)
    {
        $first_key = base64_decode(self::$secret_key);
        $second_key = base64_decode(self::$passphrase);
        $cypherMethod = 'AES-256-CBC';

        $mix = base64_decode($inputData);
        $iv_length = openssl_cipher_iv_length($cypherMethod);
        $ivX = substr($mix,0,$iv_length);
        $second_encryptedX = substr($mix,$iv_length,32);
        $first_encryptedX = substr($mix,$iv_length+32);

        $data = openssl_decrypt($first_encryptedX, $cypherMethod,$first_key ,OPENSSL_RAW_DATA ,$ivX);
        $second_encrypted_new = hash_hmac('sha256', utf8_encode($first_encryptedX), $second_key, TRUE);

        if ($second_encryptedX == $second_encrypted_new) {
            return unserialize($data);
        }
        return false;
    }

}
