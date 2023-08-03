<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

trait ResponsesTrait
{
    protected int $errorCode = 400;

    protected array $errors = [];

    protected function responseVoid(): Response
    {
        return response('', Response::HTTP_NO_CONTENT);
    }

    protected function responseOk(
        array $data = [],
        string $message = 'Data found',
        int $statusCode = 200): \Illuminate\Http\JsonResponse
    {

        $statusCode = $statusCode > 199 && $statusCode < 600 ? $statusCode : 200;
        $response = response()->json([
            'status' => 'ok',
            'message' => $message,
            'data' => $data,
        ], $statusCode);

        return $response;
    }

    /**
     * Version extendida con la capacidad de recibir excepciones con los errores seliarizados
     * * Ej: throw new Exception('SerializedError::' . serialize($arrayErrores), 423);
     *
     * @param \Throwable $e
     * @param array $errors
     * @param string $title >
     * @return JsonResponse
     */
    protected function badResponse(\Throwable $e, array $errors = [], $title = ''): \Illuminate\Http\JsonResponse
    {
        $messageError = $e->getMessage() ?? 'unknown error';

        if (Str::contains($messageError, 'SerializedError::')) {
            $errors = $this->unSerializeErrors($messageError);
        }

        $errorCode = ($e->getCode() > 300 || $e->getCode() == 204) && $e->getCode() < 600 ? $e->getCode() : $this->errorCode;

        if ($errorCode < 200 || $errorCode > 599) {
            $errorCode = 500;
        }

        if (Str::contains($messageError, 'SQLSTATE')) {
            // $messageError = 'Error con la base de datos';
            $errorCode = 409;
        }

        $origin = explode('api-auditoria',$e->getFile() .", Line:".$e->getLine());
        $payloadError = [
            'errors' => [
                [
                    'title' => ! empty($title) ? $title : 'Error control',
                    'detail' => $this->traduceNameErrors($title, $messageError),
                    'origin' => isset($origin[1]) ? $origin[1] : $origin[0]
                ],
            ],
        ];

        if (count($errors)) {
            $payloadError['errors'] = [];
            foreach ($errors as $k => $err) {
                if (is_array($err)) {
                    foreach ($err as $val) {
                        $payloadError['errors'][] = [
                            'title' => $k,
                            'detail' => $this->traduceNameErrors($k, $val),
                        ];
                    }
                } else {
                    $payloadError['errors'][] = [
                        'title' => $k,
                        'detail' => $this->traduceNameErrors($k, $err),
                    ];
                }
            }
        }

        if ($e->getMessage() == 'Expired token') {
            $errorCode = 401;
        }

        $response = response()->json($payloadError, $errorCode);

        return $response;
    }

    private function traduceNameErrors(string $key, string $error)
    {
        if (empty($key)) {
            return $error;
        }
        $replace = $this->traduceError($key);
        return Str::replace($key, $replace, $error);
    }

    private function traduceError(string &$key)
    {
        $values = [
            'reasonForReopening' => 'motivo para reabrir el anexo',
            'identifier' => 'identificador',
            'name' => 'nombre',
            'phone' => 'telefono',
            'password' => 'contraseña',
            'address' => 'direccion',
            'city' => 'ciudad',
            'fullName' => 'nombres',
            'lastname' => 'apellidos'
        ];

        // casos donde hay que cambiar un poco mas que el nombre del campo
        $specialCases = [
            'reasonForReopening' => 'campo reason for reopening'
        ];

        $keyClone = $key;

        // si el campo existe en el array de valores, lo cambiamos
        if (isset($values[$keyClone])) {
            // si el campo existe en el array de casos especiales, cambiamos la $key
            if (isset($specialCases[$keyClone])) {
                $key = $specialCases[$keyClone];
            }

            return $values[$keyClone];
        }

        return $keyClone;
    }

    private function unSerializeErrors(string $errorSerialized): array
    {
        $split = explode('::', $errorSerialized);
        return (array)unserialize($split[1]);
    }

}
