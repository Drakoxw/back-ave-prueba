<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Tools\AuthJwt;
use App\Tools\Crypter;
use Illuminate\Http\Request;
use App\Services\Validations;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerExt;
use App\Models\Cliente;

class LoginController extends ControllerExt
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            Validations::UserLogin($request);

            $nickname = $request->input('user');
            $password = Crypter::encryptAES($request->input('password'));

            // buscar usuario
            $user = User::where('user', $nickname)->where('password', $password)->first();

            $data = [];
            if ($user) {
                // encontro un usuario
                $data = [
                    'user'=> $user->user,
                    'ds'  => $user->name,
                    'id'  => $user->id,
                    'rol' => $user->rol
                ];
            } else {
                // no encontro usuario esta buscando cliente
                $client = Cliente::where('user', $nickname)->where('password', $password)->first();

                if ($client == null) {
                    // no encontro cliente ni usuario
                    throw new \Exception('Usuario o contraseña incorrectos', Response::HTTP_NOT_FOUND);
                }

                $data = [
                    'user'=> $client->user,
                    'ds'  => $client->name,
                    'id'  => $client->id,
                    'rol' => $client->rol
                ];
            }
            // creacion del token
            $token = AuthJwt::SignInTokenSimple($data, 9000);

            return $this->responseOk([ 'token' => $token ]);

        } catch (\Exception $e) {
            return $this->badResponse($e);
        }
    }
}
