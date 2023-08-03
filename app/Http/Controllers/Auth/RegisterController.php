<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Tools\AuthJwt;
use App\Tools\Crypter;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Services\Validations;
use Illuminate\Http\Response;
use App\Http\Controllers\ControllerExt;

class RegisterController extends ControllerExt
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            Validations::ClientRegister($request);

            $id = Cliente::CrearCliente($request);

            $cliente = Cliente::find($id);

            $data = [
                'user'=> $cliente->user,
                'ds'  => $cliente->name,
                'id'  => $cliente->id,
                'rol' => $cliente->rol
            ];

            $token = AuthJwt::SignInTokenSimple($data, 9000);

            return $this->responseOk(
                ['id' => $id, 'token' => $token],
                'Creado exitosamente.',
                Response::HTTP_CREATED
            );


        } catch (\Exception $e) {
            return $this->badResponse($e);
        }
    }
}
